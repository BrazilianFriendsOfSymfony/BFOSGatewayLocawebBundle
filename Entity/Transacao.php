<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BFOS\GatewayLocawebBundle\Entity\Transacao
 * BFOSGatewayLocawebBundle:Transacao
 *
 * @ORM\Table(name="bfos_locaweb_transacao")
 * @ORM\Entity(repositoryClass="BFOS\GatewayLocawebBundle\Entity\TransacaoRepository")
 */
class Transacao
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Pagamento $pagamento
     *
     * @ORM\OneToOne(targetEntity="Pagamento", inversedBy="transacao")
     * @ORM\JoinColumn(name="pagamento_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private  $pagamento;

    //-------------------------- PARÂMETROS DE RETORNO DO XML DO REGISTRO DA TRANSAÇÃO --------------------------

    /**
     * Código de identificação da transação.
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 40 caracteres
     *
     * @var string $tid
     *
     * @ORM\Column(name="tid", type="string", length=40)
     *
     */
    private $tid;

    /**
     * Hash do número do cartão do portador.
     *
     * Tipo: String.
     *
     * @var integer $pan
     *
     * @ORM\Column(name="pan", type="string",length=40, nullable=true)
     *
     */
    private $pan;

    /**
     * Status da transação.
     * Ver item 10 do guia de implementação.
     * Possíveis status de transação.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 2 dígitos.
     *
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer")
     *
     */
    private $status;

    /**
     * URL de redirecionamento a Cielo para processamento da transação.
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 256 caracteres.
     *
     * @var string $urlAutenticacao
     *
     * @ORM\Column(name="url_autenticacao", type="string", length=256)
     *
     */
    private $urlAutenticacao;

    /**
     * Número do pedido para controle interno da loja.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 20 dígitos.
     *
     * @var integer $pedido
     *
     * @ORM\Column(name="pedido", type="integer")
     *
     */
    private $pedido;

    /**
     * Valor total da transação
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 12 dígitos.
     *
     * @var integer $valor
     *
     * @ORM\Column(name="valor", type="integer")
     *
     */
    private $valor;

    /**
     * Código numérico da moeda.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 3 dígitos.
     *
     * @var integer $moeda
     *
     * @ORM\Column(name="moeda", type="integer")
     *
     */
    private $moeda;

    /**
     * Data e hora do pedido.
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 19 caracteres.
     *
     * @var string $dataHora
     *
     * @ORM\Column(name="data_hora", type="string", length=19)
     *
     */
    private $dataHora;

    /**
     * Breve descrição do pedido.
     *
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
     * Idioma do pedido.
     *
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
     * Bandeira do cartão
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 10 caracteres.
     *
     * @var string $bandeira
     *
     * @ORM\Column(name="bandeira", type="string", length=10)
     *
     */
    private $bandeira;

    /**
     * Forma de pagamento.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 1 dígito.
     *
     * @var integer $produto
     *
     * @ORM\Column(name="produto", type="integer")
     *
     */
    private $produto;

    /**
     * Número de parcelas.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 3 dígitos.
     *
     * @var integer $parcelas
     *
     * @ORM\Column(name="parcelas", type="integer")
     *
     */
    private $parcelas;

    /**
     * Código do erro.
     * Ver o item 12 do guia de implementação.
     * Possíveis retornos de erro.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 3 dígitos.
     *
     * @var integer $codigoErro
     *
     * @ORM\Column(name="codigo_erro", type="integer", nullable=true)
     *
     */
    private $codigoErro;

    /**
     * Descrição do erro
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 255 caracteres.
     *
     * @var string $descricaoErro
     *
     * @ORM\Column(name="descricao_erro", type="string", length=255, nullable=true)
     *
     */
    private $descricaoErro;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $situacoes
     *
     * @ORM\OneToMany(targetEntity="\BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao", mappedBy="transacao")
     */
    private $situacoes;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="date")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tid
     *
     * @param string $tid
     * @return Transacao
     */
    public function setTid($tid)
    {
        $this->tid = $tid;
        return $this;
    }

    /**
     * Get tid
     *
     * @return string 
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Transacao
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set urlAutenticacao
     *
     * @param string $urlAutenticacao
     * @return Transacao
     */
    public function setUrlAutenticacao($urlAutenticacao)
    {
        $this->urlAutenticacao = $urlAutenticacao;
        return $this;
    }

    /**
     * Get urlAutenticacao
     *
     * @return string 
     */
    public function getUrlAutenticacao()
    {
        return $this->urlAutenticacao;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return Transacao
     */
    public function setPedido($numero)
    {
        $this->pedido = $numero;
        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getPedido()
    {
        return $this->pedido;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     * @return Transacao
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
     * Set moeda
     *
     * @param integer $moeda
     * @return Transacao
     */
    public function setMoeda($moeda)
    {
        $this->moeda = $moeda;
        return $this;
    }

    /**
     * Get moeda
     *
     * @return integer 
     */
    public function getMoeda()
    {
        return $this->moeda;
    }

    /**
     * Set dataHora
     *
     * @param string $dataHora
     * @return Transacao
     */
    public function setDataHora($dataHora)
    {
        $this->dataHora = $dataHora;
        return $this;
    }

    /**
     * Get dataHora
     *
     * @return string 
     */
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     * @return Transacao
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
     * Set idioma
     *
     * @param string $idioma
     * @return Transacao
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
     * Set bandeira
     *
     * @param string $bandeira
     * @return Transacao
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
     * Set produto
     *
     * @param integer $produto
     * @return Transacao
     */
    public function setProduto($produto)
    {
        $this->produto = $produto;
        return $this;
    }

    /**
     * Get produto
     *
     * @return integer 
     */
    public function getProduto()
    {
        return $this->produto;
    }

    /**
     * Set parcelas
     *
     * @param integer $parcelas
     * @return Transacao
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
     * Set codigoErro
     *
     * @param integer $codigoErro
     * @return Transacao
     */
    public function setCodigoErro($codigoErro)
    {
        $this->codigoErro = $codigoErro;
        return $this;
    }

    /**
     * Get codigoErro
     *
     * @return integer 
     */
    public function getCodigoErro()
    {
        return $this->codigoErro;
    }

    /**
     * Set descricaoErro
     *
     * @param string $descricaoErro
     * @return Transacao
     */
    public function setDescricaoErro($descricaoErro)
    {
        $this->descricaoErro = $descricaoErro;
        return $this;
    }

    /**
     * Get descricaoErro
     *
     * @return string 
     */
    public function getDescricaoErro()
    {
        return $this->descricaoErro;
    }

    /**
     * Add situacoes
     *
     * @param BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao $situacoes
     * @return Transacao
     */
    public function addSituacao(TransacaoSituacao $situacao){
        $situacao->setTransacao($this);
        $this->situacoes[] = $situacao;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $situacoes
     *
     */
    public function setSituacoes($situacoes)
    {
        $this->situacoes = $situacoes;
    }

    /**
     * Get situacoes
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSituacoes()
    {
        return $this->situacoes;
    }

    /**
     * Set created
     *
     * @param date $created
     * @return Transacao
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return date
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     * @return Transacao
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->situacoes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set pagamento
     *
     * @param \BFOS\GatewayLocawebBundle\Entity\Pagamento $pagamento
     * @return Transacao
     */
    public function setPagamento(\BFOS\GatewayLocawebBundle\Entity\Pagamento $pagamento)
    {
        $this->pagamento = $pagamento;
    
        return $this;
    }

    /**
     * Get pagamento
     *
     * @return \BFOS\GatewayLocawebBundle\Entity\Pagamento
     */
    public function getPagamento()
    {
        return $this->pagamento;
    }

    /**
     * Add situacoes
     *
     * @param \BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao $situacoes
     * @return Transacao
     */
    public function addSituacoe(\BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao $situacoes)
    {
        $this->situacoes[] = $situacoes;
    
        return $this;
    }

    /**
     * Remove situacoes
     *
     * @param \BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao $situacoes
     */
    public function removeSituacoe(\BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao $situacoes)
    {
        $this->situacoes->removeElement($situacoes);
    }

    /**
     * Set pan
     *
     * @param string $pan
     * @return Transacao
     */
    public function setPan($pan)
    {
        $this->pan = $pan;
    
        return $this;
    }

    /**
     * Get pan
     *
     * @return string 
     */
    public function getPan()
    {
        return $this->pan;
    }
}