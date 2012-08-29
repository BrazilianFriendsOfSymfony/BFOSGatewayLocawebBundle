<?php

namespace BFOS\GatewayLocawebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BFOS\GatewayLocawebBundle\Entity\Boleto
 *
 * @ORM\Table(name="bfos_locaweb_pagamento")
 * @ORM\Entity
 */
class Boleto extends Pagamento
{
    //-------------------------- OS PARÂMETROS OBRIGATÓRIOS QUE DEVERÃO SER PASSADOS --------------------------

    /**
     * Valor do documento.
     *
     * Presença: Obrigatória.
     * Tipo: String.
     * Formato: 9999999999999,99 e com o limite de 15 caracteres.
     *
     * @var string $valor
     *
     * @ORM\Column(name="valor_boleto", type="string", length=15)
     *
     */
    private $valor;

    /**
     * Número do documento. Geralmente, igual ao número do pedido na loja.
     *
     * Presença: Obrigatória.
     * Tipo: Número.
     * Formato: Um número com o limite de 17 dígitos.
     *
     * @var integer $numDoc
     *
     * @ORM\Column(name="numdoc", type="integer")
     *
     */
    private $numDoc;

    /**
     * Data de emissão do documento.
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: DD/MM/AAAA, com o limite de 10 caracteres.
     *
     * @var $data
     *
     * @ORM\Column(name="data_boleto", type="string", length=10)
     *
     */
    private $data;

    /**
     * Data de vencimento do boleto.
     * Para exibição do texto "C/ APRESENTACAO" utilize com o valor “ca”.
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: DD/MM/AAAA, com o limite de 10 caracteres.
     *
     * @var string $vencimento
     *
     * @ORM\Column(name="vencimento_boleto", type="string", length=10)
     *
     */
    private $vencimento;

    //-------------------------- OS PARÂMETROS ADICIONAIS DEVEM SER POSTADOS DE ACORDO COM A NECESSIDADE DO BOLETO E DISPONIBILIDADE DESSA OPÇÃO PELO BANCO --------------------------

    /**
     * Nome do sacado.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 45 caracteres.
     *
     * @var string $sacado
     *
     * @ORM\Column(name="sacado", type="string", length=45, nullable=true)
     *
     */

    private $sacado;

    /**
     * CGC ou CPF do sacado.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 20 caracteres.
     *
     * @var string $cgcCpf
     *
     * @ORM\Column(name="cgccpf", type="string", length=20, nullable=true)
     *
     */
    private $cgcCpf;

    /**
     * Código de cobrança sem registro. Endereço do sacado
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 100 caracteres.
     *
     * @var string $endereco
     *
     * @ORM\Column(name="endereco", type="string", length=100, nullable=true)
     *
     */
    private $endereco;

    /**
     * Número da residência do sacado
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 10 caracteres.
     *
     * @var string $numero
     *
     * @ORM\Column(name="numero", type="string", length=10, nullable=true)
     *
     */
    private $numero;

    /**
     * Complemento da residência do sacado
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 2 caracteres.
     *
     * @var string $complemento
     *
     * @ORM\Column(name="complemento", type="string", length=2, nullable=true)
     *
     */
    private $complemento;

    /**
     * Bairro do sacado
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 50 caracteres.
     *
     * @var string $bairro
     *
     * @ORM\Column(name="bairro", type="string", length=50, nullable=true)
     *
     */
    private $bairro;

    /**
     * Cep do sacado
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 10 caracteres.
     *
     * @var string $cep
     *
     * @ORM\Column(name="cep", type="string", length=10, nullable=true)
     *
     */
    private $cep;

    /**
     * Cidade do sacado
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 50 caracteres.
     *
     * @var string $cidade
     *
     * @ORM\Column(name="cidade", type="string", length=50, nullable=true)
     *
     */
    private $cidade;

    /**
     * Estado do sacado
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 2 caracteres.
     *
     * @var string $uf
     *
     * @ORM\Column(name="uf", type="string", length=2, nullable=true)
     *
     */
    private $uf;

    /**
     * Número do documento fixo passado.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número com o limite de 17 dígitos.
     *
     * @var integer $numDocFixo
     *
     * @ORM\Column(name="numero_doc_fixo", type="integer", nullable=true)
     *
     */
    private $numDocFixo;

    /**
     * Nosso número fixo.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número com o limite de 17 dígitos.
     *
     * @var integer $nossoNumero
     *
     * @ORM\Column(name="nosso_numero", type="integer", nullable=true)
     */
    private $nossoNumero;

    /**
     * Campo livre especial.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número com o limite de 25 dígitos.
     *
     * @var integer $campoLivre
     *
     * @ORM\Column(name="campo_livre", type="integer", nullable=true)
     *
     */
    private $campoLivre;

    /**
     * Layout do boleto. O padrão é 240.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número com o limite de 3 dígitos.
     *
     * @var integer $cnab
     *
     * @ORM\Column(name="cnab", type="integer", nullable=true)
     *
     */
    private $cnab;

    /**
     * Linhas de instruções.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 67 caracteres.
     *
     * @var string $instrucao1
     *
     * @ORM\Column(name="instrucao1", type="string", length=67, nullable=true)
     *
     */
    private $instrucao1;

    /**
     * Linhas de instruções.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 67 caracteres.
     *
     * @var string $instrucao2
     *
     * @ORM\Column(name="instrucao2", type="string", length=67, nullable=true)
     *
     */
    private $instrucao2;

    /**
     * Linhas de instruções.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 67 caracteres.
     *
     * @var string $instrucao3
     *
     * @ORM\Column(name="instrucao3", type="string", length=67, nullable=true)
     *
     */
    private $instrucao3;

    /**
     * Linhas de instruções.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 67 caracteres.
     *
     * @var string $instrucao4
     *
     * @ORM\Column(name="instrucao4", type="string", length=67, nullable=true)
     *
     */
    private $instrucao4;

    /**
     * Linhas de instruções.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 67 caracteres.
     *
     * @var string $instrucao5
     *
     * @ORM\Column(name="instrucao5", type="string", length=67, nullable=true)
     *
     */
    private $instrucao5;

    //-------------------------- OS PARÂMETROS DE CUSTOMIZAÇÃO DO BOLETO QUE PODEM SER PASSADOS --------------------------

    /**
     * URL de seu logotipo para exibição no cabeçalho do boleto.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Uma URL válida, com o limite de 255 caracteres.
     *
     * @var string $logotipo
     *
     * @ORM\Column(name="logotipo", type="string", length=255, nullable=true)
     *
     */
    private $logotipo;

    /**
     * Exibe o nome da loja no título do navegador,na emissão do boleto.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 95 caracteres.
     *
     * @var string $loja
     *
     * @ORM\Column(name="loja", type="string", length=95, nullable=true)
     *
     */
    private $loja;

    /**
     * Exibe os botões para a impressão do boleto e o fechamento da janela.
     *
     * Assume:
     *      0 – oculta;
     *      1 – exibe.
     *
     * Presença: Opcional.
     * Tipo: Integer.
     * Formato: Número. Um número com o limite de 1 dígito.
     *
     * @var integer $botoesBoleto
     *
     * @ORM\Column(name="botoes_boleto", type="integer", nullable=true)
     *
     */
    private $botoesBoleto;

    /**
     * URL de sua página para exibição no cabeçalho do boleto.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 250 caracteres.
     *
     * @var string $url
     *
     * @ORM\Column(name="url_loja", type="string", length=250, nullable=true)
     *
     */
    private $url;

    /**
     * Exibe as informações do cabeçalho do boleto.
     *
     * Assume:
     *      0 – exibe;
     *      1 – oculta.
     *
     * Presença: Opcional.
     * Tipo: Integer.
     * Formato: Número. Um número com o limite de 1 dígito.
     *
     * @var integer $cabecalho
     *
     * @ORM\Column(name="cabecalho", type="integer", nullable=true)
     *
     */
    private $cabecalho;


    function __construct()
    {
        $this->botoesBoleto = 1;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return Pagamento
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set numDoc
     *
     * @param integer $numDoc
     * @return Pagamento
     */
    public function setNumDoc($numDoc)
    {
        $this->numDoc = $numDoc;
        return $this;
    }

    /**
     * Get numDoc
     *
     * @return integer
     */
    public function getNumDoc()
    {
        return $this->numDoc;
    }

    /**
     * Set sacado
     *
     * @param string $sacado
     * @return Pagamento
     */
    public function setSacado($sacado)
    {
        $this->sacado = $sacado;
        return $this;
    }

    /**
     * Get sacado
     *
     * @return string
     */
    public function getSacado()
    {
        return $this->sacado;
    }

    /**
     * Set cgcCpf
     *
     * @param string $cgcCpf
     * @return Pagamento
     */
    public function setCgcCpf($cgcCpf)
    {
        $this->cgcCpf = $cgcCpf;
        return $this;
    }

    /**
     * Get cgcCpf
     *
     * @return string
     */
    public function getCgcCpf()
    {
        return $this->cgcCpf;
    }

    /**
     * Set endereco
     *
     * @param string $endereco
     * @return Pagamento
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
        return $this;
    }

    /**
     * Get endereco
     *
     * @return string
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Pagamento
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set complemento
     *
     * @param string $complemento
     * @return Pagamento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * Get complemento
     *
     * @return string
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * Set bairro
     *
     * @param string $bairro
     * @return Pagamento
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
        return $this;
    }

    /**
     * Get bairro
     *
     * @return string
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * Set cidade
     *
     * @param string $cidade
     * @return Pagamento
     */
    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
        return $this;
    }

    /**
     * Get cidade
     *
     * @return string
     */
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     * Set cep
     *
     * @param string $cep
     * @return Pagamento
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * Get cep
     *
     * @return string
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * Set uf
     *
     * @param string $uf
     * @return Pagamento
     */
    public function setUf($uf)
    {
        $this->uf = $uf;
        return $this;
    }

    /**
     * Get uf
     *
     * @return string
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return Pagamento
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set vencimento
     *
     * @param date $vencimento
     * @return Pagamento
     */
    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;
        return $this;
    }

    /**
     * Get vencimento
     *
     * @return date
     */
    public function getVencimento()
    {
        return $this->vencimento;
    }

    /**
     * Set numDocFixo
     *
     * @param integer $numDocFixo
     * @return Pagamento
     */
    public function setNumDocFixo($numDocFixo)
    {
        $this->numDocFixo = $numDocFixo;
        return $this;
    }

    /**
     * Get numDocFixo
     *
     * @return integer
     */
    public function getNumDocFixo()
    {
        return $this->numDocFixo;
    }

    /**
     * Set nossoNumero
     *
     * @param integer $nossoNumero
     * @return Pagamento
     */
    public function setNossoNumero($nossoNumero)
    {
        $this->nossoNumero = $nossoNumero;
        return $this;
    }

    /**
     * Get nossoNumero
     *
     * @return integer
     */
    public function getNossoNumero()
    {
        return $this->nossoNumero;
    }

    /**
     * Set campoLivre
     *
     * @param integer $campoLivre
     * @return Pagamento
     */
    public function setCampoLivre($campoLivre)
    {
        $this->campoLivre = $campoLivre;
        return $this;
    }

    /**
     * Get campoLivre
     *
     * @return integer
     */
    public function getCampoLivre()
    {
        return $this->campoLivre;
    }

    /**
     * Set cnab
     *
     * @param integer $cnab
     * @return Pagamento
     */
    public function setCnab($cnab)
    {
        $this->cnab = $cnab;
        return $this;
    }

    /**
     * Get cnab
     *
     * @return integer
     */
    public function getCnab()
    {
        return $this->cnab;
    }

    /**
     * Set instrucao1
     *
     * @param string $instrucao1
     * @return Pagamento
     */
    public function setInstrucao1($instrucao1)
    {
        $this->instrucao1 = $instrucao1;
        return $this;
    }

    /**
     * Get instrucao1
     *
     * @return string
     */
    public function getInstrucao1()
    {
        return $this->instrucao1;
    }

    /**
     * Set instrucao2
     *
     * @param string $instrucao2
     * @return Pagamento
     */
    public function setInstrucao2($instrucao2)
    {
        $this->instrucao2 = $instrucao2;
        return $this;
    }

    /**
     * Get instrucao2
     *
     * @return string
     */
    public function getInstrucao2()
    {
        return $this->instrucao2;
    }

    /**
     * Set instrucao3
     *
     * @param string $instrucao3
     * @return Pagamento
     */
    public function setInstrucao3($instrucao3)
    {
        $this->instrucao3 = $instrucao3;
        return $this;
    }

    /**
     * Get instrucao3
     *
     * @return string
     */
    public function getInstrucao3()
    {
        return $this->instrucao3;
    }

    /**
     * Set instrucao4
     *
     * @param string $instrucao4
     * @return Pagamento
     */
    public function setInstrucao4($instrucao4)
    {
        $this->instrucao4 = $instrucao4;
        return $this;
    }

    /**
     * Get instrucao4
     *
     * @return string
     */
    public function getInstrucao4()
    {
        return $this->instrucao4;
    }

    /**
     * Set instrucao5
     *
     * @param string $instrucao5
     * @return Pagamento
     */
    public function setInstrucao5($instrucao5)
    {
        $this->instrucao5 = $instrucao5;
        return $this;
    }

    /**
     * Get instrucao5
     *
     * @return string
     */
    public function getInstrucao5()
    {
        return $this->instrucao5;
    }

    /**
     * Set logotipo
     *
     * @param string $logotipo
     * @return Pagamento
     */
    public function setLogotipo($logotipo)
    {
        $this->logotipo = $logotipo;
        return $this;
    }

    /**
     * Get logotipo
     *
     * @return string
     */
    public function getLogotipo()
    {
        return $this->logotipo;
    }

    /**
     * Set loja
     *
     * @param string $loja
     * @return Pagamento
     */
    public function setLoja($loja)
    {
        $this->loja = $loja;
        return $this;
    }

    /**
     * Get loja
     *
     * @return string
     */
    public function getLoja()
    {
        return $this->loja;
    }

    /**
     * Set botoesBoleto
     *
     * @param integer $botoesBoleto
     * @return Pagamento
     */
    public function setBotoesBoleto($botoesBoleto)
    {
        $this->botoesBoleto = $botoesBoleto;
        return $this;
    }

    /**
     * Get botoesBoleto
     *
     * @return integer
     */
    public function getBotoesBoleto()
    {
        return $this->botoesBoleto;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Pagamento
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set cabecalho
     *
     * @param integer $cabecalho
     * @return Pagamento
     */
    public function setCabecalho($cabecalho)
    {
        $this->cabecalho = $cabecalho;
        return $this;
    }

    /**
     * Get cabecalho
     *
     * @return integer
     */
    public function getCabecalho()
    {
        return $this->cabecalho;
    }

    // retorna a URL de redirecionamento para o site da Locaweb
    public function getUrlGatewayLocaweb()
    {
        $url = 'https://comercio.locaweb.com.br/comercio.comp?';
        $url .= 'identificacao=' . self::getIdentificacao();
        $url .= '&modulo=' . self::getModulo();
        $url .= '&ambiente=' . self::getAmbiente();
        $url .= '&valor=' . $this->getValor();
        $url .= '&numdoc=' . $this->getNumDoc();
        $url .= '&sacado=' . $this->getSacado();
        $url .= '&cgccpfsac=' . $this->getCgcCpf();
        $url .= '&enderecosac=' . $this->getEndereco();
        $url .= '&numeroendsac=' . $this->getNumero();
        $url .= '&complementosac=' . $this->getComplemento();
        $url .= '&bairrosac=' . $this->getBairro();
        $url .= '&cidadesac=' . $this->getCidade();
        $url .= '&cepsac=' . $this->getCep();
        $url .= '&ufsac=' . $this->getUf();
        $url .= '&datadoc=' . $this->getData();
        $url .= '&vencto=' . $this->getVencimento();
        $url .= '&instr1=' . $this->getInstrucao1();
        $url .= '&instr2=' . $this->getInstrucao2();
        $url .= '&instr3=' . $this->getInstrucao3();
        $url .= '&instr4=' . $this->getInstrucao4();
        $url .= '&instr5=' . $this->getInstrucao5();
        $url .= '&numdocespec=' . $this->getNumDocFixo();
        $url .= '&nossonum=' . $this->getNossoNumero();
        $url .= '&cnab=' . $this->getCnab();
        $url .= '&campolivreespec=' . $this->getCampoLivre();
        $url .= '&debug=';
        $url .= '&logoloja=' . $this->getLogotipo();
        $url .= '&tituloloja=' . $this->getLoja();
        $url .= '&botoesboleto=' . $this->getBotoesBoleto();
        $url .= '&urltopoloja=' . $this->getUrl();
        $url .= '&cabecalho=' . $this->getCabecalho();

        return utf8_decode($url);
    }

}