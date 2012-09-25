<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Operacao deve ser definida")
     */
    private $operacao;

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
     * @Assert\NotBlank(message="Bandeira deve ser definida.")
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
     * @Assert\NotBlank(message="Forma de pagamento deve ser definida")
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
     * @Assert\NotBlank(message="Parcelas deve ser definida.")
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
     * @Assert\NotBlank(message="Autorizar deve ser definido.")
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
     * @Assert\NotBlank(message="Capturar deve ser definido.")
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

    function __construct()
    {
        $this->setModulo('CIELO');
        $this->operacao = 'Registro';
    }


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
        $request .= '&valor=' . number_format($this->getValorTotal(), 2, '', '');
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