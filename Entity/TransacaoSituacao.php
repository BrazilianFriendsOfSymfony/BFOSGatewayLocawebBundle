<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BFOS\GatewayLocawebBundle\Utils\Helper;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao
 * BFOSGatewayLocawebBundle:TransacaoSituacao
 *
 * @ORM\Table(name="bfos_locaweb_transacao_situacao")
 * @ORM\Entity
 */
class TransacaoSituacao
{

    const ETAPA_REGISTRO = 0;
    const ETAPA_AUTENTICACAO = 1;
    const ETAPA_AUTORIZACAO = 2;
    const ETAPA_CAPTURA = 4;
    const ETAPA_CANCELAMENTO = 8;
    const ETAPA_ERRO = 999;

    static $etapas = array(
        0 => 'Registro',
        1 => 'Autenticação',
        2 => 'Autorização',
        4 => 'Captura',
        8 => 'Cancelamento',
        999 => 'Erro'
    );

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Transacao $transacao
     *
     * @ORM\ManyToOne(targetEntity="\BFOS\GatewayLocawebBundle\Entity\Transacao", inversedBy="situacoes")
     * @ORM\JoinColumn(name="transacao_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $transacao;

    /**
     * @var integer $etapa
     *
     * @ORM\Column(name="etapa", type="smallint", nullable=false)
     */
    private $etapa;

    /**
     * @var integer $codigo
     *
     * @ORM\Column(name="codigo", type="smallint", nullable=true)
     */
    private $codigo;

    /**
     * @var integer $situacao
     *
     * @ORM\Column(name="situacao", type="string", length=100)
     */
    private $situacao;

    /**
     * @var integer $dataHora
     *
     * @ORM\Column(name="data_hora", type="string", length=40, nullable=true)
     */
    private $dataHora;

    /**
     * @var float $valor
     *
     * @ORM\Column(name="valor", type="decimal", scale=2, nullable=true)
     */
    private $valor;

    /**
     * Nível de segurança da transação. Ver item 11. Níveis de segurança da transação.
     *
     * @var int $eci
     *
     * @ORM\Column(name="eci", type="smallint", nullable=true)
     */
    private $eci;

    /**
     * Retorno da autorização.
     *
     * @var int $lr
     *
     * @ORM\Column(name="lr", type="smallint", nullable=true)
     */
    private $lr;

    /**
     * Código da autorização caso a transação tenha sido autorizada com sucesso.
     *
     * @var string $arp
     *
     * @ORM\Column(name="arp", type="string", length=10, nullable=true)
     */
    private $arp;

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
     * Set transacao
     *
     * @param \BFOS\GatewayLocawebBundle\Entity\Transacao $transacao
     * @return TransacaoSituacao
     */
    public function setTransacao(Transacao $transacao)
    {
        $this->transacao = $transacao;
        return $this;
    }

    /**
     * Get transacao
     *
     * @return \BFOS\GatewayLocawebBundle\Entity\Transacao
     */
    public function getTransacao()
    {
        return $this->transacao;
    }

    /**
     * Set situacao
     *
     * @param string $situacao
     * @return TransacaoSituacao
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * Get situacao
     *
     * @return string 
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * Set created
     *
     * @param date $created
     * @return TransacaoSituacao
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
     * @return TransacaoSituacao
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
     * Set etapa
     *
     * @param integer $etapa
     * @return TransacaoSituacao
     */
    public function setEtapa($etapa)
    {
        $this->etapa = $etapa;
    
        return $this;
    }

    /**
     * Get etapa
     *
     * @return integer 
     */
    public function getEtapa()
    {
        return $this->etapa;
    }

    /**
     * Get etapa
     *
     * @return integer
     */
    public function getEtapaLabel()
    {
        if(isset(self::$etapas[$this->etapa])){
            return self::$etapas[$this->etapa];
        }
        return '';
    }

    /**
     * Set dataHora
     *
     * @param string $dataHora
     * @return TransacaoSituacao
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
     * Set valor
     *
     * @param float $valor
     * @return TransacaoSituacao
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    
        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set eci
     *
     * @param integer $eci
     * @return TransacaoSituacao
     */
    public function setEci($eci)
    {
        $this->eci = $eci;
    
        return $this;
    }

    /**
     * Get eci
     *
     * @return integer 
     */
    public function getEci()
    {
        return $this->eci;
    }

    /**
     * Get eci
     *
     * @return integer
     */
    public function getEciLabel()
    {
        if(isset(Helper::$seguranca_transacao[$this->eci])){
            return Helper::$seguranca_transacao[$this->eci];
        }
        return '';
    }


    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return TransacaoSituacao
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set lr
     *
     * @param integer $lr
     * @return TransacaoSituacao
     */
    public function setLr($lr)
    {
        $this->lr = $lr;
    
        return $this;
    }

    /**
     * Get lr
     *
     * @return integer 
     */
    public function getLr()
    {
        return $this->lr;
    }

    /**
     * Set arp
     *
     * @param string $arp
     * @return TransacaoSituacao
     */
    public function setArp($arp)
    {
        $this->arp = $arp;
    
        return $this;
    }

    /**
     * Get arp
     *
     * @return string 
     */
    public function getArp()
    {
        return $this->arp;
    }
}