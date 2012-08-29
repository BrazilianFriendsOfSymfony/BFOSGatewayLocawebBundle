<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BFOS\GatewayLocawebBundle\Entity\TransacaoSituacao
 *
 * @ORM\Table(name="bfos_locaweb_transacao_situacao")
 * @ORM\Entity
 */
class TransacaoSituacao
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
     * @var Transacao $transacao
     *
     * @ORM\ManyToOne(targetEntity="\BFOS\GatewayLocawebBundle\Entity\Transacao", inversedBy="situacoes")
     * @ORM\JoinColumn(name="transacao_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $transacao;

    /**
     * @var integer $situacao
     *
     * @ORM\Column(name="situacao", type="string", length=100)
     */
    private $situacao;

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
     * @param BFOS\GatewayLocawebBundle\Entity\Transacao $transacao
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
     * @return BFOS\GatewayLocawebBundle\Entity\Transacao
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

}