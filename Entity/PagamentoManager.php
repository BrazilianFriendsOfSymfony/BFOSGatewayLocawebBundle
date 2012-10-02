<?php
namespace BFOS\GatewayLocawebBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use BFOS\GatewayLocawebBundle\Utils\Browser;
use BFOS\GatewayLocawebBundle\Utils\Helper;
use \BFOS\GatewayLocawebBundle\Entity\Pagamento;

class PagamentoManager
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var TransacaoRepository $rtransacao
     */
    private $rtransacao;

    /**
     * @var EntityRepository $rcielo
     */
    private $rcielo;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    private $logger;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->rtransacao = $this->em->getRepository('BFOSGatewayLocawebBundle:Transacao');
        $this->rcielo = $this->em->getRepository('BFOSGatewayLocawebBundle:Cielo');
        $this->logger    = $container->get('logger');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getCieloRepository(){
        return $this->rcielo;
    }

    /**
     * @return TransacaoRepository
     */
    public function getTransacaoRepository(){
        return $this->rtransacao;
    }

    /**
     * Obtem o TID a partir do id do pedido.
     *
     * @param int|string $pedido
     * @return string TID
     */
    public function obterTidAPartirDoPedido($pedido){
        /**
         * @var Transacao $transacao
         */
        $transacao = $this->getTransacaoRepository()->findOneBy(array('pedido'=>$pedido));
        if($transacao){
            return $transacao->getTid();
        }
        return null;
    }

    //-------------------------- PAGAMENTO BOLETO --------------------------
    /**
     * Registra o pagamento junto ao gateway da locaweb
     *
     * @param \BFOS\GatewayLocawebBundle\Entity\Pagamento $pagamento
     *
     * @return boolean|\Symfony\Component\Validator\ConstraintViolationList True se o pagamento é valido para ser
     *         submetido a Locaweb e ConstraintViolationList cajo constrário.
     */
    public function registrarPagamentoBoleto(Boleto $pagamento){

        /**
         * @var \Symfony\Component\Validator\Validator $validator
         */
        $validator = $this->container->get('validator');
        $errors = $validator->validate($pagamento);
        if(count($errors)){
            return $errors;
        }

        // tenta persistir as informacoes no BD, para garantir a persistencia do que esta sendo enviado
        $this->em->persist($pagamento);
        $this->em->flush();

        // obtem a montagem da URL do componente
        $urlGatewayLocaweb = $pagamento->getUrlGatewayLocaweb();

        if($urlGatewayLocaweb == null){
            throw new \Exception('Não está configurada a url de redirecionamento para o gateway da Locaweb.');
        } else{
            return new RedirectResponse($urlGatewayLocaweb);
        }

    }

    //-------------------------- PAGAMENTO CIELO --------------------------
    /**
     * Registra o pagamento junto ao gateway da locaweb
     *
     * @param \BFOS\GatewayLocawebBundle\Entity\Pagamento $pagamento
     *
     * @return RedirectResponse|\Symfony\Component\Validator\ConstraintViolationList   RedirectResponse se o pagamento é valido para ser
     *         submetido a Locaweb e ConstraintViolationList (lista de erros) caso contrário .
     */
    public function registrarPagamentoCielo(Cielo $pagamento){

        /**
         * @var \Symfony\Component\Validator\Validator $validator
         */
        $validator = $this->container->get('validator');
        $errors = $validator->validate($pagamento);
        if(count($errors)){
            return $errors;
        }

        // tenta persistir as informacoes no BD, para garantir a persistencia do que esta sendo enviado
        $this->em->persist($pagamento);
        $this->em->flush();

        //obtem a montagem da URL do componente
        $urlCielo     = $pagamento->getUrlCielo();
        $request = $pagamento->getParametrosDaTransacao();
        $retorno = Browser::postUrl($urlCielo, $request);

        //array com os paramentros de retorno do xml do processo da transacao
        $retorno_processo  = array();

        if(isset($retorno['erro_num']) && $retorno['erro_num']){
            $erro_msg = 'Houve um problema de comunicação:  '.$retorno['erro_msg'];
            $pagamento->setErro($erro_msg);
            $this->em->persist($pagamento);
            $this->em->flush();
            throw new \Exception($erro_msg);
        }
        if($retorno['info']['http_code']>=400){
            $erro_msg = 'O gateway da Cielo ecommerce da Locaweb nao autorizou o registro da transacao do com a identificacao  '.$pagamento->getIdentificacao()  .' : ' . $retorno['info']['http_code'] . " | " . $retorno['corpo'];
            $pagamento->setErro($erro_msg);
            $this->em->persist($pagamento);
            $this->em->flush();
            throw new \Exception($erro_msg);
        }

        /**
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;
            $pagamento->setErro($erro_msg);
            $this->em->persist($pagamento);
            $this->em->flush();

            throw new \Exception($erro_msg);
        } else {
            //paramentros de retorno do xml
            $tid      = (string)  @$xml['tid'];
            $status   = (integer) @$xml['status'];
            $url      = (string)  @$xml['url-autenticacao'];

            //dados do pedido
            @$dados_pedido = (array) $xml['dados-pedido'];
            $numero   = (integer) @$dados_pedido['numero'];
            $valor    = (integer) @$dados_pedido['valor'];
            $moeda    = (integer) @$dados_pedido['moeda'];
            $data_hora= (string)  @$dados_pedido['data-hora'];
            $descricao= (string)  @$dados_pedido['descricao'];
            $idioma   = (string)  @$dados_pedido['idioma'];

            //forma de pagamento
            @$forma_pagamento = (array) $xml['forma-pagamento'];
            $bandeira = (string)  @$forma_pagamento['bandeira'];
            $produto  = (integer) @$forma_pagamento['produto'];
            $parcelas = (integer) @$forma_pagamento['parcelas'];

            //registra a transacao
            $transacao = new Transacao();
            $transacao->setTid($tid);
            $transacao->setStatus($status);
            $transacao->setUrlAutenticacao($url);
            $transacao->setPedido($numero);
            $transacao->setValor(((int)$valor)/100);
            $transacao->setMoeda($moeda);
            $transacao->setDataHora($data_hora);
            $transacao->setDescricao($descricao);
            $transacao->setIdioma($idioma);
            $transacao->setBandeira($bandeira);
            $transacao->setProduto($produto);
            $transacao->setParcelas($parcelas);

            $this->em->persist($transacao);
            $this->em->flush();

            //processa o status da transacao
            if($transacao){
                $situacao = new TransacaoSituacao();
                $situacao->setEtapa(TransacaoSituacao::ETAPA_REGISTRO);
                $situacao->setSituacao('Registrado');
                $dataHora = new \DateTime();
                $situacao->setDataHora($dataHora->format('d/m/Y H:i:s'));
                $situacao->setValor( $transacao->getValor() );
                $transacao->addSituacao($situacao);
                $this->em->persist($situacao);
            }
            $this->em->flush();

            //paramentros de retorno do xml do processo da transacao
            $retorno_processo['tid']          = $tid;
            $retorno_processo['status']       = $status;
            $retorno_processo['url']          = $url;
            $retorno_processo['pedido']       = $numero;
            $retorno_processo['valor']        = $valor;
            $retorno_processo['moeda']        = $moeda;
            $retorno_processo['data_hora']    = $data_hora;
            $retorno_processo['descricao']    = $descricao;
            $retorno_processo['idioma']       = $idioma;
            $retorno_processo['bandeira']     = $bandeira;
            $retorno_processo['produto']      = $produto;
            $retorno_processo['parcelas']     = $parcelas;
            $retorno_processo['codigo_erro']  = 0;
            $retorno_processo['mensagem_erro']= '';

        }

        //view de retorno do registro da transacao
        //return $this->container->get('templating')->renderResponse('BFOSGatewayLocawebBundle:Cielo:registra_transacao.html.twig', array('transacao' => $retorno_processo));

        //url de retorno do gateway da cielo ecommerce da locaweb
        if($url == null){
            return null;
            throw new \Exception('Não está configurada a url de redirecionamento para o gateway da Locaweb.');
        } else{
            return new RedirectResponse($url);
        }
    }

    /**
     * Retorno XML da Consulta da transação
     *
     * @param integer $identificacao. Código do serviço de comércio eletrônico junto à Locaweb.
     * @param string $ambiente. Nome do ambiente utilizado para consulta
     * @param string $tid. Código de identificação da transação.
     */
    public function consultaTransacao($identificacao, $ambiente, $tid){
        $url = 'https://comercio.locaweb.com.br/comercio.comp';

        /**
         * @var string $modulo - Nome do módulo de pagamento utilizado. Utilizar: CIELO
         * @var string $operacao - Define a ação que será executada. Utilizar: Consulta
         */
        $modulo = 'CIELO';
        $operacao = 'Consulta';

        //obtem a montagem da URL do componente
        $request  = 'identificacao=' . $identificacao;
        $request .= '&modulo=' . $modulo;
        $request .= '&operacao=' . $operacao;
        $request .= '&ambiente=' . $ambiente;
        $request .= '&tid=' . $tid;

        //obtem o componente
        $retorno = Browser::postUrl($url, $request);

        //array com os paramentros de retorno do xml do processo da transacao
        $retorno_processo  = array();

        if($retorno['info']['http_code']!=200){
            $erro_msg = 'O gateway da Cielo ecommerce da Locaweb nao autorizou a consulta da transacao com a identificacao  '. $identificacao  .' : ' . $retorno['info']['http_code'] . " | " . $retorno['corpo'];
            throw new \Exception($erro_msg);
        }

        /**
         * @var Transacao $transacao
         */
        $transacao = $this->rtransacao->findOneBy(array('tid'=>$tid));

        /**
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;

            if($transacao){
                $situacao = new TransacaoSituacao();
                $situacao->setEtapa(TransacaoSituacao::ETAPA_ERRO);
                $situacao->setSituacao($codigo_erro . '. ' . $mensagem_erro);
                $transacao->addSituacao($situacao);
                $this->em->persist($transacao);
            }

            throw new \Exception($erro_msg);
        } else {

            //paramentros de retorno do xml
            $tid      = (string)  @$xml['tid'];
            $pan      = (string)  @$xml['pan'];
            $status   = (integer) @$xml['status'];
            $url      = (string)  @$xml['url-autenticacao'];

            if($transacao){
                $transacao->setStatus($status);
                $transacao->setUrlAutenticacao($url);
                $transacao->setPan($pan);
                $this->em->persist($transacao);
            }

            //dados do pedido
            @$dados_pedido = (array) $xml['dados-pedido'];
            $numero   = (integer) @$dados_pedido['numero'];
            $valor    = (integer) @$dados_pedido['valor'];
            $moeda    = (integer) @$dados_pedido['moeda'];
            $data_hora= (string)  @$dados_pedido['data-hora'];
            $descricao= (string)  @$dados_pedido['descricao'];
            $idioma   = (string)  @$dados_pedido['idioma'];

            //forma de pagamento
            @$forma_pagamento = (array) $xml['forma-pagamento'];
            $bandeira = (string)  @$forma_pagamento['bandeira'];
            $produto  = (integer) @$forma_pagamento['produto'];
            $parcelas = (integer) @$forma_pagamento['parcelas'];

            //autenticacao
            @$autenticacao = (array) $xml['autenticacao'];
            $codigo_autenticacao    = (integer) @$autenticacao['codigo'];
            $mensagem_autenticacao  = (string)  @$autenticacao['mensagem'];
            $data_hora_autenticacao = (string)  @$autenticacao['data-hora'];
            $valor_autenticacao     = (integer) @$autenticacao['valor'];
            $eci_autenticacao       = (integer) @$autenticacao['eci'];


            if($transacao && $autenticacao){
                $situacao = $this->getTransacaoRepository()->findSituacao($transacao,TransacaoSituacao::ETAPA_AUTENTICACAO);
                if(!$situacao){
                    $situacao = new TransacaoSituacao();
                    $situacao->setEtapa(TransacaoSituacao::ETAPA_AUTENTICACAO);
                    $situacao->setCodigo($codigo_autenticacao);
                    $situacao->setSituacao($mensagem_autenticacao);
                    $situacao->setDataHora($data_hora_autenticacao);
                    $situacao->setValor( ((int) $valor_autenticacao)/100 );
                    $situacao->setEci($eci_autenticacao);
                    $transacao->addSituacao($situacao);
                    $this->em->persist($situacao);
                }
            }

            //autorizacao
            @$autorizacao = (array) $xml['autorizacao'];
            $codigo_autorizacao    = (integer) @$autorizacao['codigo'];
            $mensagem_autorizacao  = (string)  @$autorizacao['mensagem'];
            $data_hora_autorizacao = (string)  @$autorizacao['data-hora'];
            $valor_autorizacao     = (integer) @$autorizacao['valor'];
            $lr_autorizacao        = (integer) @$autorizacao['lr'];
            $arp_autorizacao       = (string)  @$autorizacao['arp'];


            if($transacao && $autorizacao){
                $situacao = $this->getTransacaoRepository()->findSituacao($transacao,TransacaoSituacao::ETAPA_AUTORIZACAO);
                if(!$situacao){
                    $situacao = new TransacaoSituacao();
                    $situacao->setEtapa(TransacaoSituacao::ETAPA_AUTORIZACAO);
                    $situacao->setCodigo($codigo_autorizacao);
                    $situacao->setSituacao($mensagem_autorizacao);
                    $situacao->setDataHora($data_hora_autorizacao);
                    $situacao->setValor( ((int) $valor_autorizacao)/100 );
                    $situacao->setLr($lr_autorizacao);
                    $situacao->setArp($arp_autorizacao);
                    $transacao->addSituacao($situacao);
                    $this->em->persist($situacao);
                }
            }

            //cancelamento
            @$cancelamento = (array) $xml['cancelamento'];
            $codigo_cancelamento    = (integer) @$cancelamento['codigo'];
            $mensagem_cancelamento  = (string)  @$cancelamento['mensagem'];
            $data_hora_cancelamento = (string)  @$cancelamento['data-hora'];
            $valor_cancelamento     = (integer) @$cancelamento['valor'];


            if($transacao && $cancelamento){
                $situacao = $this->getTransacaoRepository()->findSituacao($transacao,TransacaoSituacao::ETAPA_CANCELAMENTO);
                if(!$situacao){
                    $situacao = new TransacaoSituacao();
                    $situacao->setEtapa(TransacaoSituacao::ETAPA_CANCELAMENTO);
                    $situacao->setCodigo($codigo_cancelamento);
                    $situacao->setSituacao($mensagem_cancelamento);
                    $situacao->setDataHora($data_hora_cancelamento);
                    $situacao->setValor( ((int) $valor_cancelamento)/100 );
                    $transacao->addSituacao($situacao);
                    $this->em->persist($situacao);
                }
            }

            //captura
            @$captura = (array) $xml['captura'];
            $codigo_captura    = (integer) @$captura['codigo'];
            $mensagem_captura  = (string)  @$captura['mensagem'];
            $data_hora_captura = (string)  @$captura['data-hora'];
            $valor_captura     = (integer) @$captura['valor'];


            if($transacao && $captura){
                $situacao = $this->getTransacaoRepository()->findSituacao($transacao,TransacaoSituacao::ETAPA_CAPTURA);
                if(!$situacao){
                    $situacao = new TransacaoSituacao();
                    $situacao->setEtapa(TransacaoSituacao::ETAPA_CAPTURA);
                    $situacao->setCodigo($codigo_captura);
                    $situacao->setSituacao($mensagem_captura);
                    $situacao->setDataHora($data_hora_captura);
                    $situacao->setValor( ((int) $valor_captura)/100 );
                    $transacao->addSituacao($situacao);
                    $this->em->persist($situacao);
                }
            }
            $this->em->flush();

            //paramentros de retorno do xml do processo da transacao
            $retorno_processo['tid']          = $tid;
            $retorno_processo['pan']          = $pan;
            $retorno_processo['status']       = $status;
            $retorno_processo['url']          = $url;

            $retorno_processo['dados-pedido'] = $dados_pedido;
            $retorno_processo['pedido']       = $numero;
            $retorno_processo['valor']        = $valor;
            $retorno_processo['moeda']        = $moeda;
            $retorno_processo['data_hora']    = $data_hora;
            $retorno_processo['descricao']    = $descricao;
            $retorno_processo['idioma']       = $idioma;

            $retorno_processo['forma-pagamento'] = $forma_pagamento;
            $retorno_processo['bandeira']        = $bandeira;
            $retorno_processo['produto']         = $produto;
            $retorno_processo['parcelas']        = $parcelas;

            $retorno_processo['autenticacao'] = $autenticacao;
            $retorno_processo['codigo_autenticacao']   = $codigo_autenticacao;
            $retorno_processo['mensagem_autenticacao'] = $mensagem_autenticacao;
            $retorno_processo['data_hora_autenticacao']= $data_hora_autenticacao;
            $retorno_processo['valor_autenticacao']    = $valor_autenticacao;
            $retorno_processo['eci_autenticacao']      = $eci_autenticacao;

            $retorno_processo['autorizacao'] = $autorizacao;
            $retorno_processo['codigo_autorizacao']    = $codigo_autorizacao;
            $retorno_processo['mensagem_autorizacao']  = $mensagem_autorizacao;
            $retorno_processo['data_hora_autorizacao'] = $data_hora_autorizacao;
            $retorno_processo['valor_autorizacao']     = $valor_autorizacao;
            $retorno_processo['lr_autorizacao']        = $lr_autorizacao;
            $retorno_processo['arp_autorizacao']       = $arp_autorizacao;

            $retorno_processo['captura'] = $captura;
            $retorno_processo['codigo_captura']        = $codigo_captura;
            $retorno_processo['mensagem_captura']      = $mensagem_captura;
            $retorno_processo['data_hora_captura']     = $data_hora_captura;
            $retorno_processo['valor_captura']         = $valor_captura;

            $retorno_processo['cancelamento'] = $cancelamento;
            $retorno_processo['codigo_cancelamento']   = $codigo_cancelamento;
            $retorno_processo['mensagem_cancelamento'] = $mensagem_cancelamento;
            $retorno_processo['data_hora_cancelamento']= $data_hora_cancelamento;
            $retorno_processo['valor_cancelamento']    = $valor_cancelamento;

            $retorno_processo['codigo_erro']  = 0;
            $retorno_processo['mensagem_erro']= '';

        }

        return $transacao;

        //url de retorno do gateway da cielo ecommerce da locaweb
        if($url == null){
            //throw new \Exception('Não está configurada a url de redirecionamento para o gateway da Locaweb.');
            //view de retorno da consulta da transacao
            return $this->container->get('templating')->renderResponse('BFOSGatewayLocawebBundle:Cielo:consulta_transacao.html.twig', array('transacao' => $retorno_processo));
        } else{
            return new RedirectResponse($url);
        }
    }

    /**
     * Retorno XML da Captura da transação
     *
     * @param integer $identificacao. Código do serviço de comércio eletrônico junto à Locaweb.
     * @param string $modulo. Nome do módulo de pagamento utilizado. Utilizar: CIELO
     * @param string $operacao. Define a ação que será executada. Utilizar: Captura
     * @param string $ambiente. Nome do ambiente utilizado para consulta
     * @param string $tid. Código de identificação da transação.
     */
    public function capturaTransacao($identificacao, $modulo, $operacao, $ambiente, $tid){
        $url = 'https://comercio.locaweb.com.br/comercio.comp';

        //obtem a montagem da URL do componente
        $request  = 'identificacao=' . $identificacao;
        $request .= '&modulo=' . $modulo;
        $request .= '&operacao=' . $operacao;
        $request .= '&ambiente=' . $ambiente;
        $request .= '&tid=' . $tid;

        //obtem o componente
        $retorno = Browser::postUrl($url, $request);

        //array com os paramentros de retorno do xml do processo da transacao
        $retorno_processo  = array();

        if($retorno['info']['http_code']!=200){
            $erro_msg = 'O gateway da Cielo ecommerce da Locaweb nao autorizou a captura da transacao com a identificacao  '. $identificacao  .' : ' . $retorno['info']['http_code'] . " | " . $retorno['corpo'];
            throw new \Exception($erro_msg);
        }

        /**
         * @var Transacao $transacao
         */
        $transacao = $this->rtransacao->findOneBy(array('tid'=>$tid));

        /**
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;

            //atualiza o situação da transacao
            if($transacao){
                $situacao = new TransacaoSituacao();
                $situacao->setEtapa(TransacaoSituacao::ETAPA_ERRO);
                $situacao->setSituacao($codigo_erro . '. ' . $mensagem_erro);
                $transacao->addSituacao($situacao);
                $this->em->persist($transacao);
            }

            throw new \Exception($erro_msg);
        } else {
            //paramentros de retorno do xml
            $tid      = (string)  @$xml['tid'];
            $pan      = (string)  @$xml['pan'];
            $status   = (integer) @$xml['status'];
            $url      = (string)  @$xml['url-autenticacao'];

            //atualiza o situação da transacao
            $this->atualizaSituacaDaTransacao($tid, $status);

            //dados do pedido
            @$dados_pedido = (array) $xml['dados-pedido'];
            $numero   = (integer) @$dados_pedido['numero'];
            $valor    = (integer) @$dados_pedido['valor'];
            $moeda    = (integer) @$dados_pedido['moeda'];
            $data_hora= (string)  @$dados_pedido['data-hora'];
            $descricao= (string)  @$dados_pedido['descricao'];
            $idioma   = (string)  @$dados_pedido['idioma'];

            //forma de pagamento
            @$forma_pagamento = (array) $xml['forma-pagamento'];
            $bandeira = (string)  @$forma_pagamento['bandeira'];
            $produto  = (integer) @$forma_pagamento['produto'];
            $parcelas = (integer) @$forma_pagamento['parcelas'];

            //captura
            @$captura = (array) $xml['captura'];
            $codigo_captura    = (integer) @$captura['codigo'];
            $mensagem_captura  = (string)  @$captura['mensagem'];
            $data_hora_captura = (string)  @$captura['data-hora'];
            $valor_captura     = (integer) @$captura['valor'];

            //paramentros de retorno do xml do processo da transacao
            $retorno_processo['tid']          = $tid;
            $retorno_processo['pan']          = $pan;
            $retorno_processo['status']       = $status;
            $retorno_processo['url']          = $url;

            $retorno_processo['dados-pedido'] = $dados_pedido;
            $retorno_processo['pedido']       = $numero;
            $retorno_processo['valor']        = $valor;
            $retorno_processo['moeda']        = $moeda;
            $retorno_processo['data_hora']    = $data_hora;
            $retorno_processo['descricao']    = $descricao;
            $retorno_processo['idioma']       = $idioma;

            $retorno_processo['forma-pagamento']   = $forma_pagamento;
            $retorno_processo['bandeira']          = $bandeira;
            $retorno_processo['produto']           = $produto;
            $retorno_processo['parcelas']          = $parcelas;

            $retorno_processo['captura'] = $captura;
            $retorno_processo['codigo_captura']    = $codigo_captura;
            $retorno_processo['mensagem_captura']  = $mensagem_captura;
            $retorno_processo['data_hora_captura'] = $data_hora_captura;
            $retorno_processo['valor_captura']     = $valor_captura;

            $retorno_processo['codigo_erro']  = 0;
            $retorno_processo['mensagem_erro']= '';

        }
        $this->em->flush();

        //url de retorno do gateway da cielo ecommerce da locaweb
        if($url == null){
            //throw new \Exception('Não está configurada a url de redirecionamento para o gateway da Locaweb.');
            //view de retorno da captura da transacao
            return $this->container->get('templating')->renderResponse('BFOSGatewayLocawebBundle:Cielo:captura_transacao.html.twig', array('transacao' => $retorno_processo));
        } else{
            return new RedirectResponse($url);
        }
    }

    /**
     * Retorno XML da Consulta da transação
     *
     * @param integer $identificacao. Código do serviço de comércio eletrônico junto à Locaweb.
     * @param string $modulo. Nome do módulo de pagamento utilizado. Utilizar: CIELO
     * @param string $operacao. Define a ação que será executada. Utilizar: Cancelamento
     * @param string $ambiente. Nome do ambiente utilizado para consulta
     * @param string $tid. Código de identificação da transação.
     */
    public function cancelamentoTransacao($identificacao, $modulo, $operacao, $ambiente, $tid){
        $url = 'https://comercio.locaweb.com.br/comercio.comp';

        //obtem a montagem da URL do componente
        $request  = 'identificacao=' . $identificacao;
        $request .= '&modulo=' . $modulo;
        $request .= '&operacao=' . $operacao;
        $request .= '&ambiente=' . $ambiente;
        $request .= '&tid=' . $tid;

        //obtem o componente
        $retorno = Browser::postUrl($url, $request);

        //array com os paramentros de retorno do xml do processo da transacao
        $retorno_processo  = array();

        if($retorno['info']['http_code']!=200){
            $erro_msg = 'O gateway da Cielo ecommerce da Locaweb nao autorizou o cancelamento da transacao com a identificacao  '. $identificacao  .' : ' . $retorno['info']['http_code'] . " | " . $retorno['corpo'];
            throw new \Exception($erro_msg);
        }

        /**
         * @var Transacao $transacao
         */
        $transacao = $this->rtransacao->findOneBy(array('tid'=>$tid));

        /**
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;

            //atualiza o situação da transacao
            if($transacao){
                $situacao = new TransacaoSituacao();
                $situacao->setEtapa(TransacaoSituacao::ETAPA_ERRO);
                $situacao->setSituacao($codigo_erro . '. ' . $mensagem_erro);
                $transacao->addSituacao($situacao);
                $this->em->persist($transacao);
            }

            throw new \Exception($erro_msg);
        } else {
            //paramentros de retorno do xml
            $tid      = (string)  @$xml['tid'];
            $pan      = (string)  @$xml['pan'];
            $status   = (integer) @$xml['status'];
            $url      = (string)  @$xml['url-autenticacao'];

            //atualiza o situação da transacao
            $this->atualizaSituacaDaTransacao($tid, $status);

            //dados do pedido
            @$dados_pedido = (array) $xml['dados-pedido'];
            $numero   = (integer) @$dados_pedido['numero'];
            $valor    = (integer) @$dados_pedido['valor'];
            $moeda    = (integer) @$dados_pedido['moeda'];
            $data_hora= (string)  @$dados_pedido['data-hora'];
            $descricao= (string)  @$dados_pedido['descricao'];
            $idioma   = (string)  @$dados_pedido['idioma'];

            //forma de pagamento
            @$forma_pagamento = (array) $xml['forma-pagamento'];
            $bandeira = (string)  @$forma_pagamento['bandeira'];
            $produto  = (integer) @$forma_pagamento['produto'];
            $parcelas = (integer) @$forma_pagamento['parcelas'];

            //cancelamento
            @$cancelamento = (array) $xml['cancelamento'];
            $codigo_cancelamento    = (integer) @$cancelamento['codigo'];
            $mensagem_cancelamento  = (string)  @$cancelamento['mensagem'];
            $data_hora_cancelamento = (string)  @$cancelamento['data-hora'];
            $valor_cancelamento     = (integer) @$cancelamento['valor'];

            //paramentros de retorno do xml do processo da transacao
            $retorno_processo['tid']          = $tid;
            $retorno_processo['pan']          = $pan;
            $retorno_processo['status']       = $status;
            $retorno_processo['url']          = $url;

            $retorno_processo['dados-pedido'] = $dados_pedido;
            $retorno_processo['pedido']       = $numero;
            $retorno_processo['valor']        = $valor;
            $retorno_processo['moeda']        = $moeda;
            $retorno_processo['data_hora']    = $data_hora;
            $retorno_processo['descricao']    = $descricao;
            $retorno_processo['idioma']       = $idioma;

            $retorno_processo['forma-pagamento'] = $forma_pagamento;
            $retorno_processo['bandeira']        = $bandeira;
            $retorno_processo['produto']         = $produto;
            $retorno_processo['parcelas']        = $parcelas;

            $retorno_processo['cancelamento'] = $cancelamento;
            $retorno_processo['codigo_cancelamento']   = $codigo_cancelamento;
            $retorno_processo['mensagem_cancelamento'] = $mensagem_cancelamento;
            $retorno_processo['data_hora_cancelamento']= $data_hora_cancelamento;
            $retorno_processo['valor_cancelamento']    = $valor_cancelamento;

            $retorno_processo['codigo_erro']  = 0;
            $retorno_processo['mensagem_erro']= '';

        }
        $this->em->flush();

        //url de retorno do gateway da cielo ecommerce da locaweb
        if($url == null){
            //throw new \Exception('Não está configurada a url de redirecionamento para o gateway da Locaweb.');
            //view de retorno da consulta da transacao
            return $this->container->get('templating')->renderResponse('BFOSGatewayLocawebBundle:Cielo:cancelamento_transacao.html.twig', array('transacao' => $retorno_processo));
        } else{
            return new RedirectResponse($url);
        }
    }

    /**
     * Retorno XML da Autorização da transação
     *
     * @param integer $identificacao. Código do serviço de comércio eletrônico junto à Locaweb.
     * @param string $modulo. Nome do módulo de pagamento utilizado. Utilizar: CIELO
     * @param string $operacao. Define a ação que será executada. Utilizar: Autorizacao
     * @param string $ambiente. Nome do ambiente utilizado para consulta
     * @param string $tid. Código de identificação da transação.
     */
    public function autorizacaoTransacao($identificacao, $modulo, $operacao, $ambiente, $tid){
        $url = 'https://comercio.locaweb.com.br/comercio.comp';

        //obtem a montagem da URL do componente
        $request  = 'identificacao=' . $identificacao;
        $request .= '&modulo=' . $modulo;
        $request .= '&operacao=' . $operacao;
        $request .= '&ambiente=' . $ambiente;
        $request .= '&tid=' . $tid;

        //obtem o componente
        $retorno = Browser::postUrl($url, $request);

        //array com os paramentros de retorno do xml do processo da transacao
        $retorno_processo  = array();

        if($retorno['info']['http_code']!=200){
            $erro_msg = 'O gateway da Cielo ecommerce da Locaweb nao autorizou a autorizacao da transacao com a identificacao  '. $identificacao  .' : ' . $retorno['info']['http_code'] . " | " . $retorno['corpo'];
            throw new \Exception($erro_msg);
        }

        /**
         * @var Transacao $transacao
         */
        $transacao = $this->rtransacao->findOneBy(array('tid'=>$tid));

        /**
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;

            //atualiza o situação da transacao
            if($transacao){
                $situacao = new TransacaoSituacao();
                $situacao->setEtapa(TransacaoSituacao::ETAPA_ERRO);
                $situacao->setSituacao($codigo_erro . '. ' . $mensagem_erro);
                $transacao->addSituacao($situacao);
                $this->em->persist($transacao);
            }

            throw new \Exception($erro_msg);
        } else {
            //paramentros de retorno do xml
            $tid      = (string)  @$xml['tid'];
            $pan      = (string)  @$xml['pan'];
            $status   = (integer) @$xml['status'];
            $url      = (string)  @$xml['url-autenticacao'];

            //atualiza o situação da transacao
            $this->atualizaSituacaDaTransacao($tid, $status);

            //dados do pedido
            @$dados_pedido = (array) $xml['dados-pedido'];
            $numero   = (integer) @$dados_pedido['numero'];
            $valor    = (integer) @$dados_pedido['valor'];
            $moeda    = (integer) @$dados_pedido['moeda'];
            $data_hora= (string)  @$dados_pedido['data-hora'];
            $descricao= (string)  @$dados_pedido['descricao'];
            $idioma   = (string)  @$dados_pedido['idioma'];

            //forma de pagamento
            @$forma_pagamento = (array) $xml['forma-pagamento'];
            $bandeira = (string)  @$forma_pagamento['bandeira'];
            $produto  = (integer) @$forma_pagamento['produto'];
            $parcelas = (integer) @$forma_pagamento['parcelas'];

            //autorizacao
            @$autorizacao = (array) $xml['autorizacao'];
            $codigo_autorizacao    = (integer) @$autorizacao['codigo'];
            $mensagem_autorizacao  = (string)  @$autorizacao['mensagem'];
            $data_hora_autorizacao = (string)  @$autorizacao['data-hora'];
            $valor_autorizacao     = (integer) @$autorizacao['valor'];
            $lr_autorizacao        = (integer) @$autorizacao['lr'];
            $arp_autorizacao       = (string)  @$autorizacao['arp'];

            //registra a autorizacao
            $autorizacaoTransacao = new AutorizacaoTransacao();
            $autorizacaoTransacao->setTid($tid);
            $autorizacaoTransacao->setPan($pan);
            $autorizacaoTransacao->setStatus($status);
            $autorizacaoTransacao->setUrlAutenticacao($url);
            $autorizacaoTransacao->setCodigoProcessamento($codigo_autorizacao);
            $autorizacaoTransacao->setMensagemProcessamento($mensagem_autorizacao);
            $autorizacaoTransacao->setDataHoraProcessamento($data_hora_autorizacao);
            $autorizacaoTransacao->setValorProcessamento($valor_autorizacao);
            $autorizacaoTransacao->setRetornoAutorizacao($lr_autorizacao);
            $autorizacaoTransacao->setCodigoAutorizacao($arp_autorizacao);

            $this->em->persist($autorizacaoTransacao);
            $this->em->flush();

            //paramentros de retorno do xml do processo da transacao
            $retorno_processo['tid']          = $tid;
            $retorno_processo['pan']          = $pan;
            $retorno_processo['status']       = $status;
            $retorno_processo['url']          = $url;

            $retorno_processo['dados-pedido'] = $dados_pedido;
            $retorno_processo['pedido']       = $numero;
            $retorno_processo['valor']        = $valor;
            $retorno_processo['moeda']        = $moeda;
            $retorno_processo['data_hora']    = $data_hora;
            $retorno_processo['descricao']    = $descricao;
            $retorno_processo['idioma']       = $idioma;

            $retorno_processo['forma-pagamento'] = $forma_pagamento;
            $retorno_processo['bandeira']        = $bandeira;
            $retorno_processo['produto']         = $produto;
            $retorno_processo['parcelas']        = $parcelas;

            $retorno_processo['autorizacao'] = $autorizacao;
            $retorno_processo['codigo_autorizacao']    = $codigo_autorizacao;
            $retorno_processo['mensagem_autorizacao']  = $mensagem_autorizacao;
            $retorno_processo['data_hora_autorizacao'] = $data_hora_autorizacao;
            $retorno_processo['valor_autorizacao']     = $valor_autorizacao;
            $retorno_processo['lr_autorizacao']        = $lr_autorizacao;
            $retorno_processo['arp_autorizacao']       = $arp_autorizacao;

            $retorno_processo['codigo_erro']  = 0;
            $retorno_processo['mensagem_erro']= '';

        }
        $this->em->flush();

        //url de retorno do gateway da cielo ecommerce da locaweb
        if($url == null){
            //throw new \Exception('Não está configurada a url de redirecionamento para o gateway da Locaweb.');
            //view de retorno da consulta da transacao
            return $this->container->get('templating')->renderResponse('BFOSGatewayLocawebBundle:Cielo:autorizacao_transacao.html.twig', array('transacao' => $retorno_processo));
        } else{
            return new RedirectResponse($url);
        }

    }

    /**
     *  Retorna um elemento através da chave
     *
     **/
    function array_search_key($key, $stack) {
        if (is_array($stack)) {
            foreach ($stack as $k => $v) {
                if ($k == $key) {
                    return $v;
                }
                elseif (is_array($v)) {
                    return array_search_key($key, $v);
                }
            }
        }
        return false;
    }

}
