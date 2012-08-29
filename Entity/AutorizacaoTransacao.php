<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BFOS\GatewayLocawebBundle\Entity\AutorizacaoTransacao
 *
 * @ORM\Table(name="bfos_locaweb_autorizacao_transacao")
 * @ORM\Entity
 */
class AutorizacaoTransacao
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    //-------------------------- PARÂMETROS DE RETORNO DO XML DA AUTORIZAÇÃO DA TRANSAÇÃO --------------------------

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
     * Formato: Livre, com o limite de 40 caracteres.
     *
     * @var string $pan
     *
     * @ORM\Column(name="pan", type="string", length=40)
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

    //-------------------------- AUTORIZAÇÃO --------------------------

    /**
     * Código do processamento.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 2 dígitos.
     *
     * @var integer $codigoProcessamento
     *
     * @ORM\Column(name="codigo_processamento", type="integer")
     *
     */
    private $codigoProcessamento;

    /**
     * Detalhe do processamento
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 100 caracteres.
     *
     * @var string $mensagemProcessamento
     *
     * @ORM\Column(name="mensagem_processamento", type="string", length=100)
     *
     */
    private $mensagemProcessamento;

    /**
     * Data hora do processamento
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 19 caracteres.
     *
     * @var string $dataHoraProcessamento
     *
     * @ORM\Column(name="data_hora_processamento", type="string", length=19)
     *
     */
    private $dataHoraProcessamento;

    /**
     * Valor do processamento sem pontuação.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 12 dígitos.
     *
     * @var integer $valorProcessamento
     *
     * @ORM\Column(name="valor_processamento", type="integer")
     *
     */
    private $valorProcessamento;

    /**
     * Retorno da autorização.
     *
     * Tipo: Número.
     * Formato: Um número com o limite de 2 dígitos.
     *
     * @var integer $retornoAutorizacao
     *
     * @ORM\Column(name="retorno_autorizacao", type="integer")
     *
     */
    private $retornoAutorizacao;

    /**
     * Código da autorização caso a transação tenha sido autorizada com sucesso.
     *
     * Tipo: String.
     * Formato: Livre, com o limite de 6 caracteres.
     *
     * @var string $codigoAutorizacao
     *
     * @ORM\Column(name="codigo_autorizacao", type="string", length=6)
     *
     */
    private $codigoAutorizacao;

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
     * @return AutorizacaoTransacao
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
     * Set pan
     *
     * @param string $pan
     * @return AutorizacaoTransacao
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

    /**
     * Set status
     *
     * @param integer $status
     * @return AutorizacaoTransacao
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
     * @return AutorizacaoTransacao
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
     * Set codigoProcessamento
     *
     * @param integer $codigoProcessamento
     * @return AutorizacaoTransacao
     */
    public function setCodigoProcessamento($codigoProcessamento)
    {
        $this->codigoProcessamento = $codigoProcessamento;
        return $this;
    }

    /**
     * Get codigoProcessamento
     *
     * @return integer 
     */
    public function getCodigoProcessamento()
    {
        return $this->codigoProcessamento;
    }

    /**
     * Set mensagemProcessamento
     *
     * @param string $mensagemProcessamento
     * @return AutorizacaoTransacao
     */
    public function setMensagemProcessamento($mensagemProcessamento)
    {
        $this->mensagemProcessamento = $mensagemProcessamento;
        return $this;
    }

    /**
     * Get mensagemProcessamento
     *
     * @return string 
     */
    public function getMensagemProcessamento()
    {
        return $this->mensagemProcessamento;
    }

    /**
     * Set dataHoraProcessamento
     *
     * @param string $dataHoraProcessamento
     * @return AutorizacaoTransacao
     */
    public function setDataHoraProcessamento($dataHoraProcessamento)
    {
        $this->dataHoraProcessamento = $dataHoraProcessamento;
        return $this;
    }

    /**
     * Get dataHoraProcessamento
     *
     * @return string 
     */
    public function getDataHoraProcessamento()
    {
        return $this->dataHoraProcessamento;
    }

    /**
     * Set valorProcessamento
     *
     * @param integer $valorProcessamento
     * @return AutorizacaoTransacao
     */
    public function setValorProcessamento($valorProcessamento)
    {
        $this->valorProcessamento = $valorProcessamento;
        return $this;
    }

    /**
     * Get valorProcessamento
     *
     * @return integer 
     */
    public function getValorProcessamento()
    {
        return $this->valorProcessamento;
    }

    /**
     * Set retornoAutorizacao
     *
     * @param integer $retornoAutorizacao
     * @return AutorizacaoTransacao
     */
    public function setRetornoAutorizacao($retornoAutorizacao)
    {
        $this->retornoAutorizacao = $retornoAutorizacao;
        return $this;
    }

    /**
     * Get retornoAutorizacao
     *
     * @return integer 
     */
    public function getRetornoAutorizacao()
    {
        return $this->retornoAutorizacao;
    }

    /**
     * Set codigoAutorizacao
     *
     * @param string $codigoAutorizacao
     * @return AutorizacaoTransacao
     */
    public function setCodigoAutorizacao($codigoAutorizacao)
    {
        $this->codigoAutorizacao = $codigoAutorizacao;
        return $this;
    }

    /**
     * Get codigoAutorizacao
     *
     * @return string 
     */
    public function getCodigoAutorizacao()
    {
        return $this->codigoAutorizacao;
    }

    /**
     * Set codigoErro
     *
     * @param integer $codigoErro
     * @return AutorizacaoTransacao
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
     * @return AutorizacaoTransacao
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
     * Set created
     *
     * @param date $created
     * @return AutorizacaoTransacao
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
     * @return AutorizacaoTransacao
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