<?php

namespace BFOS\GatewayLocawebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \BFOS\GatewayLocawebBundle\Entity\Boleto;

class BoletoController extends Controller
{
    /**
     * @Route("admin/boleto")
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

        $boleto = new Boleto();

        $boleto->setIdentificacao($key_secret);
        $boleto->setModulo('boletolocaweb');
        $boleto->setAmbiente('teste');
        $boleto->setValor('105,50');
        $boleto->setNumDoc(18095);
        $boleto->setSacado('Paulo Rogério de Barros');
        $boleto->setCgcCpf('137.917.358-33');
        $boleto->setEndereco('Rua Raphael Perissinotto');
        $boleto->setNumero('103');
        $boleto->setComplemento('Ap');
        $boleto->setBairro('João Aranha');
        $boleto->setCidade('Paulínia');
        $boleto->setUf('SP');
        $boleto->setCep('13140-000');
        $boleto->setData(date('d/m/Y'));
        $boleto->setVencimento(date('d/m/Y'));

        $boleto->setInstrucao1('Sr. caixa não receber após o vencimento');
        $boleto->setInstrucao2('Boleto referênte ao boleto da parcela 30 do carro');
        $boleto->setInstrucao3('Boleto referênte ao boleto da parcela 31 do carro');
        $boleto->setInstrucao4('Boleto referênte ao boleto da parcela 32 do carro');
        $boleto->setInstrucao5('Entrega no condomínio João Vieira - Apto 3 - BL G');

        return $mpagamento->registrarPagamentoBoleto($boleto);

    }
}
