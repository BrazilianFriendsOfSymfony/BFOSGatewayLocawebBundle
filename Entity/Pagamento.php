<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BFOS\GatewayLocawebBundle\Entity\Pagamento
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\Table(name="bfos_locaweb_pagamento")
 * @ORM\DiscriminatorColumn(name="tipo_pagamento", type="string")
 * @ORM\DiscriminatorMap({"boleto" = "Boleto", "cielo" = "Cielo"})
 */

class Pagamento
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    //-------------------------- OS PARÂMETROS OBRIGATÓRIOS QUE DEVERÃO SER PASSADOS --------------------------

    /**
    * Código do serviço de Comércio Eletrônico junto à Locaweb.
    *
    * Presença: Obrigatória.
    * Tipo: Número.
    * Formato: Um número de identificação de comércio eletrônico na locaweb, com o limite de 30 dígitos.
    *
    * @var integer $identificacao
    *
    * @ORM\Column(name="identificacao", type="integer")
    *
    */
    private $identificacao;

    /**
     * Nome do módulo de pagamento utilizado
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: Livre. Deve ser BOLETOLOCAWEB ou CIELO, com o limite de 15 caracteres.
     *
     * @var string $modulo
     *
     * @ORM\Column(name="modulo", type="string", length=15)
     *
     */
    private $modulo;

    /**
     * Nome do ambiente utilizado para emissão do boleto.
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: Livre. Deve ser PRODUCAO ou TESTE, com o limite de 10 caracteres.
     *
     * @var string $ambiente
     *
     * @ORM\Column(name="ambiente", type="string", length=10)
     *
     */
    private $ambiente;

    /**
     *Código do erro. Identifica a natureza do erro encontrado e permite o tratamento do erro pelo seu sistema.
     *
     * Formato: Veja a tabela de erros no manual de integração da Cielo
     *
     * @var string $erro
     *
     * @ORM\Column(name="codigo_erro", type="string", nullable=true)
     */
    private $erro;

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
     * Set identificacao
     *
     * @param integer $identificacao
     * @return Pagamento
     */
    public function setIdentificacao($identificacao)
    {
        $this->identificacao = $identificacao;
        return $this;
    }

    /**
     * Get identificacao
     *
     * @return integer
     */
    public function getIdentificacao()
    {
        return $this->identificacao;
    }

    /**
     * Set modulo
     *
     * @param string $modulo
     * @return Pagamento
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
        return $this;
    }

    /**
     * Get modulo
     *
     * @return string
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set ambiente
     *
     * @param string $ambiente
     * @return Pagamento
     */
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
        return $this;
    }

    /**
     * Get ambiente
     *
     * @return string
     */
    public function getAmbiente()
    {
        return $this->ambiente;
    }

    /**
     * @param string $erro
     */
    public function setErro($erro)
    {
        $this->erro = $erro;
    }

    /**
     * @return string
     */
    public function getErro()
    {
        return $this->erro;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Pagamento
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Pagamento
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    // retorna a URL de redirecionamento para o site da Cielo
    public function getUrlCielo(){
        if($this->getErro()){
            return null;
        } else {
            return 'https://comercio.locaweb.com.br/comercio.comp';
        }
    }

}