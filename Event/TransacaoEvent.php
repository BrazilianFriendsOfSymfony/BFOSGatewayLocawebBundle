<?php
namespace BFOS\GatewayLocawebBundle\Event;

use \Symfony\Component\EventDispatcher\Event;
use BFOS\GatewayLocawebBundle\Entity\Transacao;

class TransacaoEvent extends Event
{
    /**
     * @var Transacao $transacao
     */
    protected $transacao;

    function __construct(Transacao $transacao)
    {
        $this->transacao = $transacao;
    }


    /**
     * @param \BFOS\GatewayLocawebBundle\Entity\Transacao $transacao
     */
    public function setTransacao($transacao)
    {
        $this->transacao = $transacao;
        return $this;
    }

    /**
     * @return \BFOS\GatewayLocawebBundle\Entity\Transacao
     */
    public function getTransacao()
    {
        return $this->transacao;
    }

}
