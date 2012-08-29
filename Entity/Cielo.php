<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BFOS\GatewayLocawebBundle\Entity\Cielo
 *
 * @ORM\Table(name="bfos_locaweb_pagamento")
 * @ORM\Entity
 */
class Cielo extends Pagamento
{
    //-------------------------- OS PARÂMETROS OBRIGATÓRIOS QUE DEVERÃO SER PASSADOS VIA POST --------------------------

    /**
     * Define a ação que será executada. Utilizar: Registro
     *
     * Presença: Obrigatória.
     * Tipo: String.
     * Formato: Livre. Deve ser Registro, com o limite de 10 caracteres
     *
     * @var string $operacao
     *
     * @ORM\Column(name="operacao", type="string", length=10)
     *
     */
    private $operacao;

    /**
     * Valor total da transação sem pontuação – os últimos dois dígitos representam sempre os centavos.
     * Utilizar: 100 para R$ 1,00
     *
     * Presença: Obrigatória.
     * Tipo: Número.
     * Formato: Um número com o limite de 12 dígitos.
     *
     * @var integer $valor
     *
     * @ORM\Column(name="valor_transacao", type="integer")
     *
     */
    private $valor;

    /**
     * Número do pedido para controle interno da sua loja.
     *
     * Presença: Obrigatória.
     * Tipo: String.
     * Formato: Livre, com o limite de 20 caracteres.
     *
     * @var string $pedido
     *
     * @ORM\Column(name="pedido", type="string", length=20)
     *
     */
    private $pedido;

    /**
     * Bandeira
     *
     * Presença: Obrigatória.
     * Tipo: String.
     * Formato: Livre, Deve ser visa ou mastercard em minúsculo e com o limite de 10 caracteres.
     *
     * @var string $bandeira
     *
     * @ORM\Column(name="bandeira", type="string", length=10)
     *
     */
    private $bandeira;

    /**
     * Forma de pagamento.
     * Utilizar:
     *        1 (Crédito à Vista),
     *        2 (Parcelado loja),
     *        3 (Parcelado administradora),
     *        A (Débito)
     *
     * Presença: Obrigatória.
     * Tipo: Número.
     * Formato: Um número com o limite de 1 dígito.
     *
     * @var integer $formaPagamento
     *
     * @ORM\Column(name="forma_pagamento", type="integer")
     *
     */
    private $formaPagamento;

    /**
     * Número de parcelas.
     * Para transação à vista ou débito utilizar: 1
     *
     * Presença: Obrigatória.
     * Tipo: Número.
     * Formato: Um número com o limite de 1 dígito.
     *
     * @var integer $parcelas
     *
     * @ORM\Column(name="parcelas", type="integer")
     *
     */
    private $parcelas;

    /**
     * Indicador de autorização automática.
     * Utilizar:
     *        0 (não autorizar),
     *        1 (autorizar somente se autenticada),
     *        2 (autorizar autenticada e não-autenticada),
     *        3 (autorizar sem passar por autenticação – válido somente para crédito)
     *
     * Presença: Obrigatória.
     * Tipo: Número.
     * Formato: Um número com o limite de 1 dígito.
     *
     * @var integer $autorizar
     *
     * @ORM\Column(name="autorizar", type="integer")
     *
     */
    private $autorizar;

    /**
     * Captura automática da transação caso seja autorizada.
     * Utilizar: true ou false
     *
     * Presença: Obrigatória.
     * Tipo: String.
     * Formato: Livre, com o limite de 5 caracteres.
     *
     * @var string $capturar
     *
     * @ORM\Column(name="capturar", type="string", length=5)
     *
     */
    private $capturar;

    //-------------------------- OS PARÂMETROS QUE NÃO SÃO OBRIGATÓRIOS QUE DEVERÃO SER PASSADOS VIA POST --------------------------

    /**
     * Seis primeiros números do cartão.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número com o limite de 6 dígitos.
     *
     * @var integer $binCartao
     *
     * @ORM\Column(name="bin_cartao", type="integer")
     *
     */
    private $binCartao;

    /**
     * Idioma do pedido.
     * Utilizar:
     *        PT (português),
     *        EN (inglês) ou
     *        ES (espanhol)
     *
     * Presença: Opcional.
     * Tipo: String.
     * Formato: Livre, com o limite de 2 caracteres.
     *
     * @var string $idioma
     *
     * @ORM\Column(name="idioma", type="string", length=2)
     *
     */
    private $idioma;

    /**
     * Breve descrição do pedido.
     *
     * Presença: Opcional.
     * Tipo: String.
     * Formato: Livre, com o limite de 1024 caracteres.
     *
     * @var string $descricao
     *
     * @ORM\Column(name="descricao", type="string", length=1024)
     *
     */
    private $descricao;

    /**
     * Campo livre.
     *
     * Presença: Opcional.
     * Tipo: String.
     * Formato: Livre, com o limite de 128 caracteres.
     *
     * @var string $campoLivre
     *
     * @ORM\Column(name="campo_livre_cartao", type="string", length=128)
     *
     */
    private $campoLivre;


    /**
     * Set operacao
     *
     * @param string $operacao
     * @return Cielo
     */
    public function setOperacao($operacao)
    {
        $this->operacao = $operacao;
        return $this;
    }

    /**
     * Get operacao
     *
     * @return string 
     */
    public function getOperacao()
    {
        return $this->operacao;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     * @return Cielo
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    /**
     * Get valor
     *
     * @return integer 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set pedido
     *
     * @param string $pedido
     * @return Cielo
     */
    public function setPedido($pedido)
    {
        $this->pedido = $pedido;
        return $this;
    }

    /**
     * Get pedido
     *
     * @return string 
     */
    public function getPedido()
    {
        return $this->pedido;
    }

    /**
     * Set bandeira
     *
     * @param string $bandeira
     * @return Cielo
     */
    public function setBandeira($bandeira)
    {
        $this->bandeira = $bandeira;
        return $this;
    }

    /**
     * Get bandeira
     *
     * @return string 
     */
    public function getBandeira()
    {
        return $this->bandeira;
    }

    /**
     * Set formaPagamento
     *
     * @param integer $formaPagamento
     * @return Cielo
     */
    public function setFormaPagamento($formaPagamento)
    {
        $this->formaPagamento = $formaPagamento;
        return $this;
    }

    /**
     * Get formaPagamento
     *
     * @return integer 
     */
    public function getFormaPagamento()
    {
        return $this->formaPagamento;
    }

    /**
     * Set parcelas
     *
     * @param integer $parcelas
     * @return Cielo
     */
    public function setParcelas($parcelas)
    {
        $this->parcelas = $parcelas;
        return $this;
    }

    /**
     * Get parcelas
     *
     * @return integer 
     */
    public function getParcelas()
    {
        return $this->parcelas;
    }

    /**
     * Set autorizar
     *
     * @param integer $autorizar
     * @return Cielo
     */
    public function setAutorizar($autorizar)
    {
        $this->autorizar = $autorizar;
        return $this;
    }

    /**
     * Get autorizar
     *
     * @return integer 
     */
    public function getAutorizar()
    {
        return $this->autorizar;
    }

    /**
     * Set capturar
     *
     * @param string $capturar
     * @return Cielo
     */
    public function setCapturar($capturar)
    {
        $this->capturar = $capturar;
        return $this;
    }

    /**
     * Get capturar
     *
     * @return string 
     */
    public function getCapturar()
    {
        return $this->capturar;
    }

    /**
     * Set binCartao
     *
     * @param integer $binCartao
     * @return Cielo
     */
    public function setBinCartao($binCartao)
    {
        $this->binCartao = $binCartao;
        return $this;
    }

    /**
     * Get binCartao
     *
     * @return integer 
     */
    public function getBinCartao()
    {
        return $this->binCartao;
    }

    /**
     * Set idioma
     *
     * @param string $idioma
     * @return Cielo
     */
    public function setIdioma($idioma)
    {
        $this->idioma = $idioma;
        return $this;
    }

    /**
     * Get idioma
     *
     * @return string 
     */
    public function getIdioma()
    {
        return $this->idioma;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return Cielo
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Get descricao
     *
     * @return string 
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set campoLivre
     *
     * @param string $campoLivre
     * @return Cielo
     */
    public function setCampoLivre($campoLivre)
    {
        $this->campoLivre = $campoLivre;
        return $this;
    }

    /**
     * Get campoLivre
     *
     * @return string 
     */
    public function getCampoLivre()
    {
        return $this->campoLivre;
    }

    // retorna a variável com os dados da request
    public function getParametrosDaTransacao()
    {
        $request  = 'identificacao=' . self::getIdentificacao();
        $request .= '&modulo=' . self::getModulo();
        $request .= '&operacao=' . self::getOperacao();
        $request .= '&ambiente=' . self::getAmbiente();
        $request .= '&bin_cartao=' . $this->getBinCartao();
        $request .= '&idioma=' . $this->getIdioma();
        $request .= '&valor=' . $this->getValor();
        $request .= '&pedido=' . $this->getPedido();
        $request .= '&descricao=' . $this->getDescricao();
        $request .= '&bandeira=' . $this->getBandeira();
        $request .= '&forma_pagamento=' . $this->getFormaPagamento();
        $request .= '&parcelas=' . $this->getParcelas();
        $request .= '&autorizar=' . $this->getAutorizar();
        $request .= '&capturar=' . $this->getCapturar();
        $request .= '&campo_livre=' . $this->getCampoLivre();

        return utf8_decode($request);
    }

}