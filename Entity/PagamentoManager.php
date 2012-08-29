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
     * @var \BFOS\GatewayLocawebBundle\Entity\Transacao $rtransacao
     */
    private $rtransacao;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    private $logger;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->rtransacao = $this->container->get('doctrine')->getRepository('BFOSGatewayLocawebBundle:Transacao');
        $this->logger    = $container->get('logger');
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

        if(($erros = $this->isValidate($pagamento))!==true){
            return $this->container->get('templating')->renderResponse('BFOSGatewayLocawebBundle:Boleto:boleto.html.twig', array('erro' => $erros));
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
     * @return boolean|\Symfony\Component\Validator\ConstraintViolationList True se o pagamento é valido para ser
     *         submetido a Locaweb e ConstraintViolationList cajo constrário.
     */
    public function registrarPagamentoCielo(Cielo $pagamento){

        if(($erros = $this->isValidate($pagamento))!==true){
            return $this->container->get('templating')->renderResponse('BFOSGatewayLocawebBundle:Cielo:cielo.html.twig', array('erro' => $erros));
        }

        // tenta persistir as informacoes no BD, para garantir a persistencia do que esta sendo enviado
        $this->em->persist($pagamento);
        $this->em->flush();

        //obtem a montagem da URL do componente
        $url     = $pagamento->getUrlCielo();
        $request = $pagamento->getParametrosDaTransacao();
        $retorno = Browser::postUrl($url, $request);

        //array com os paramentros de retorno do xml do processo da transacao
        $retorno_processo  = array();

        if($retorno['info']['http_code']!=200){
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
            $transacao->setNumero($numero);
            $transacao->setValor($valor);
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
            $seguranca_transacao = $this->array_search_key($status, Helper::$status_transacao);

            $transacaoSituacao = new TransacaoSituacao();
            $transacaoSituacao->setSituacao($seguranca_transacao);

            $transacao->addSituacao($transacaoSituacao);

            $this->em->persist($transacaoSituacao);
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
            throw new \Exception('Não está configurada a url de redirecionamento para o gateway da Locaweb.');
        } else{
            return new RedirectResponse($url);
        }
    }

    /**
     * Verifica se o pagamento é valido, ou seja, se já pode ser submetido ao gateway da locaweb
     *
     * @param \BFOS\GatewayLocawebBundle\Entity\Pagamento $pagamento
     **/
    public function isValidate(Pagamento $pagamento){

        $validator = $this->container->get('validator');
        $errors = $validator->validate($pagamento);

        if (count($errors) > 0){
            return $errors;
        } else {
            return true;
        }
    }

    /**
     * Atualiza a situacao da transacao
     *
     * @param string $tid. Código de identificação da transação.
     * @param string $st. Situacação da transação.
     * @param string $rt. Retorno de erro ou de status.
     */
    public function atualizaSituacaDaTransacao($tid, $st, $rt = 'status'){
        if(($t = $this->rtransacao->findOneBy(array('tid'=>$tid)))){
            //processa o status ou erro da transacao
            if ($rt === 'erro') {
                $seguranca_transacao = $this->array_search_key((int)$st, Helper::$erros_transacao);
            }else{
                $seguranca_transacao = $this->array_search_key((int)$st, Helper::$status_transacao);
            }

            $ts = new TransacaoSituacao();
            $ts->setSituacao($seguranca_transacao);

            $t->addSituacao($ts);
            $t->setStatus($st);

            $this->em->persist($ts);
            $this->em->flush();

            return true;
        }else{
            return false;
        }
    }

    /**
     * Retorno XML da Consulta da transação
     *
     * @param integer $identificacao. Código do serviço de comércio eletrônico junto à Locaweb.
     * @param string $modulo. Nome do módulo de pagamento utilizado. Utilizar: CIELO
     * @param string $operacao. Define a ação que será executada. Utilizar: Consulta
     * @param string $ambiente. Nome do ambiente utilizado para consulta
     * @param string $tid. Código de identificação da transação.
     */
    public function consultaTransacao($identificacao, $modulo, $operacao, $ambiente, $tid){
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
            $erro_msg = 'O gateway da Cielo ecommerce da Locaweb nao autorizou a consulta da transacao com a identificacao  '. $identificacao  .' : ' . $retorno['info']['http_code'] . " | " . $retorno['corpo'];
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

            //atualiza o situação da transacao
            $this->atualizaSituacaDaTransacao($tid, $codigo_erro, 'erro');

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

            //autenticacao
            @$autenticacao = (array) $xml['autenticacao'];
            $codigo_autenticacao    = (integer) @$autenticacao['codigo'];
            $mensagem_autenticacao  = (string)  @$autenticacao['mensagem'];
            $data_hora_autenticacao = (string)  @$autenticacao['data-hora'];
            $valor_autenticacao     = (integer) @$autenticacao['valor'];
            $eci_autenticacao       = (integer) @$autenticacao['eci'];

            //autorizacao
            @$autorizacao = (array) $xml['autorizacao'];
            $codigo_autorizacao    = (integer) @$autorizacao['codigo'];
            $mensagem_autorizacao  = (string)  @$autorizacao['mensagem'];
            $data_hora_autorizacao = (string)  @$autorizacao['data-hora'];
            $valor_autorizacao     = (integer) @$autorizacao['valor'];
            $lr_autorizacao        = (integer) @$autorizacao['lr'];
            $arp_autorizacao       = (string)  @$autorizacao['arp'];

            //cancelamento
            @$cancelamento = (array) $xml['cancelamento'];
            $codigo_cancelamento    = (integer) @$cancelamento['codigo'];
            $mensagem_cancelamento  = (string)  @$cancelamento['mensagem'];
            $data_hora_cancelamento = (string)  @$cancelamento['data-hora'];
            $valor_cancelamento     = (integer) @$cancelamento['valor'];

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
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;

            //atualiza o situação da transacao
            $this->atualizaSituacaDaTransacao($tid, $codigo_erro, 'erro');

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
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;

            //atualiza o situação da transacao
            $this->atualizaSituacaDaTransacao($tid, $codigo_erro, 'erro');

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
         * @var \SimpleXMLElement $xml
         **/
        @$xml = (array) simplexml_load_string($retorno['corpo']);

        if(@$xml['codigo']){
            //pagseguro retornou xml com erro
            $codigo_erro   = (integer) $xml['codigo'];
            $mensagem_erro = (string) $xml['mensagem'];
            $erro_msg = 'O gateway da locaweb retornou o seguinte erro:' . $codigo_erro . '. ' . $mensagem_erro;

            //atualiza o situação da transacao
            $this->atualizaSituacaDaTransacao($tid, $codigo_erro, 'erro');

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
