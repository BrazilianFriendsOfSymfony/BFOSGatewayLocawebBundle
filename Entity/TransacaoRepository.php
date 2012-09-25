<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\AbstractQuery;

/**
 * TransacaoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransacaoRepository extends EntityRepository
{
    /**
     * @var EntityRepository $rtransacaoSituacao
     */
    private $rtransacaoSituacao;

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getTransacaoSituacaoRepository(){
        if(is_null($this->rtransacaoSituacao)){
            $this->rtransacaoSituacao = $this->_em->getRepository('BFOSGatewayLocawebBundle:TransacaoSituacao');
        }
        return $this->rtransacaoSituacao;
    }

    public function findSituacao(Transacao $transacao, $etapa){
        $situacoes = $transacao->getSituacoes();
        $transacaoSituacao = null;
        if($situacoes){
            /**
             * @var TransacaoSituacao $situacao
             */
            foreach ($situacoes as $situacao) {
                if($situacao->getEtapa()==$etapa){
                    $transacaoSituacao = $situacao;
                    break;
                }
            }

        }
        return $transacaoSituacao;
    }
}