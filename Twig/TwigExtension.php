<?php

namespace BFOS\GatewayLocawebBundle\Twig;

use \Symfony\Component\Translation\TranslatorInterface;
use \Symfony\Component\Routing\RouterInterface;
use \Symfony\Component\DependencyInjection\Container;


use \Doctrine\ORM\EntityManager;


class TwigExtension extends \Twig_Extension
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Twig_Environment
     */
    protected $env;

    /**
     *
     * @var  \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->env = $environment;
    }

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getEntityManager();
    }


    public function getFunctions()
    {
        return array(
            'bfos_gateway_locaweb_pagamento_boleto_detalhes' => new \Twig_Function_Method($this, 'pagamentoBoletoDetalhes', array('is_safe' => array('html'))),
            'bfos_gateway_locaweb_pagamento_cielo_detalhes' => new \Twig_Function_Method($this, 'pagamentoCieloDetalhes', array('is_safe' => array('html'))),
            'bfos_gateway_locaweb_pagamento_cielo_barra_acoes' => new \Twig_Function_Method($this, 'pagamentoCieloBarraAcoes', array('is_safe' => array('html'))),
        );
    }


    public function pagamentoCieloDetalhes($pedido) {
        return $this->pagamentoDetalhes($pedido, 'cielo');
    }

    public function pagamentoBoletoDetalhes($pedido) {
        return $this->pagamentoDetalhes($pedido, 'boleto');
    }
    public function pagamentoDetalhes($pedido, $metodo){

        $criteria_pagamento = array('pedido' => $pedido);
        $criteria_transacao = array('pedido' => $pedido);
        if($metodo=='cielo'){
            $rpagamento   = $this->container->get('doctrine')->getRepository('BFOSGatewayLocawebBundle:Cielo');
            $pagamento = $rpagamento->findOneBy($criteria_pagamento);
        } elseif($metodo=='boleto'){
            $rpagamento   = $this->container->get('doctrine')->getRepository('BFOSGatewayLocawebBundle:Boleto');
            $pagamento = $rpagamento->findOneBy($criteria_pagamento);
        }


        $rtransacao   = $this->container->get('doctrine')->getRepository('BFOSGatewayLocawebBundle:Transacao');
        $transacao = $rtransacao->findOneBy($criteria_transacao);

        return $this->env->render('BFOSGatewayLocawebBundle:Cielo:pagamentoDetalhes.html.twig',
            array(
                'pagamento' => $pagamento,
                'transacao' => $transacao
            )
        );
    }

    public function pagamentoCieloBarraAcoes($pedido){
        return $this->env->render('BFOSGatewayLocawebBundle:Cielo:barra_acoes.html.twig',
            array(
                'pedido' => $pedido
            )
        );
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bfos_gateway_locaweb';
    }




}
