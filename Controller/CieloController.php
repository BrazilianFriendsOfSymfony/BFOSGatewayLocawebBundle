<?php

namespace BFOS\GatewayLocawebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use \BFOS\GatewayLocawebBundle\Entity\Cielo;
use BFOS\GatewayLocawebBundle\Entity\PagamentoManager;
use BFOS\GatewayLocawebBundle\Entity\Pagamento;

class CieloController extends Controller
{
    /**
     * @Route("admin/bfos-gateway-locaweb/cielo")
     * @Template()
     */
    public function indexAction()
    {

        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getEntityManager();

        /**
         * manager do pedido bundle
         */
        $mpagamento = $this->get('gateway_locaweb.manager');

        $key_secret = $this->container->getParameter('key_secret_service_locaweb');

        $cielo = new Cielo();

        $cielo->setIdentificacao($key_secret);
        $cielo->setModulo('cielo');
        $cielo->setOperacao('registro');
        $cielo->setAmbiente('teste');
        $cielo->setValor(1000);
        $cielo->setPedido('10');
        $cielo->setBandeira('visa');
        $cielo->setFormaPagamento(1);
        $cielo->setParcelas(1);
        $cielo->setAutorizar(2);
        $cielo->setCapturar('false');

        return $mpagamento->registrarPagamentoCielo($cielo);

    }

    /**
     * @Route("/{pedido}/consulta", name="bfos_gateway_locaweb_cielo_consulta")
     * @Template()
     * @Method("get")
     */
    public function consultaAction($pedido)
    {

        /**
         * @var PagamentoManager $mpagamento
         */
        $mpagamento = $this->get('bfos_gateway_locaweb.pagamento_manager');
        /**
         * @var Pagamento $pagamento
         */
        $pagamento = $mpagamento->getCieloRepository()->findOneBy(array('pedido'=>$pedido));

        //dados do processo
        $identificacao = $this->container->getParameter('key_secret_service_locaweb');
        $modulo   = $pagamento->getModulo();
        $operacao = 'consulta';
        $ambiente = $pagamento->getAmbiente();

        //dados do pedido
        if($pagamento->getTransacao()){
            $tid      = $pagamento->getTransacao()->getTid();
        } else {
            $transacao = $mpagamento->getTransacaoRepository()->findOneBy(array('pedido'=>$pedido));
            $tid = $transacao->getTid();
        }

        return $mpagamento->consultaTransacao($identificacao, $modulo, $operacao, $ambiente, $tid);

    }

    /**
     * @Route("admin/captura/cielo")
     * @Template()
     */
    public function capturaAction()
    {

        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getEntityManager();

        /**
         * manager do pedido bundle
         */
        $mpagamento = $this->get('gateway_locaweb.manager');

        //dados do processo
        $identificacao = $this->container->getParameter('key_secret_service_locaweb');
        $modulo   = 'cielo';
        $operacao = 'consulta';
        $ambiente = 'teste';

        //dados do pedido
        $tid      = '10017348980885221001';

        return $mpagamento->capturaTransacao($identificacao, $modulo, $operacao, $ambiente, $tid);

    }

    /**
     * @Route("admin/cancelamento/cielo")
     * @Template()
     */
    public function cancelamentoAction()
    {

        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getEntityManager();

        /**
         * manager do pedido bundle
         */
        $mpagamento = $this->get('gateway_locaweb.manager');

        //dados do processo
        $identificacao = $this->container->getParameter('key_secret_service_locaweb');
        $modulo   = 'cielo';
        $operacao = 'consulta';
        $ambiente = 'teste';

        //dados do pedido
        $tid      = '10017348980885221001';

        return $mpagamento->cancelamentoTransacao($identificacao, $modulo, $operacao, $ambiente, $tid);

    }

    /**
     * @Route("admin/autorizacao/cielo")
     * @Template()
     */
    public function autorizacaoAction()
    {

        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getEntityManager();

        /**
         * manager do pedido bundle
         */
        $mpagamento = $this->get('gateway_locaweb.manager');

        //dados do processo
        $identificacao = $this->container->getParameter('key_secret_service_locaweb');
        $modulo   = 'cielo';
        $operacao = 'autorizacao';
        $ambiente = 'teste';

        //dados do pedido
        $tid      = '10017348980885221001';

        return $mpagamento->autorizacaoTransacao($identificacao, $modulo, $operacao, $ambiente, $tid);

    }

}
