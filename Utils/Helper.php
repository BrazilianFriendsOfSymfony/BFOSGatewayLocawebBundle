<?php
namespace BFOS\GatewayLocawebBundle\Utils;

class Helper
{

    static $status_transacao = array(
         0 => 'Criada',
         1 => 'Em andamento',
         2 => 'Autenticada',
         3 => 'Não Autenticada',
         4 => 'Autorizada ou pendente de captura',
         5 => 'Não autorizada',
         6 => 'Capturada',
         8 => 'Não capturada',
         9 => 'Cancelada',
        10 => 'Em autenticação'
    );

    static $seguranca_transacao_visa = array(
         5 => 'Portador autenticado com sucesso',
         6 => 'Portador não realizou autenticação, pois o Emissor não forneceu mecanismos de autenticação',
         7 => 'Portador não se autenticou com sucesso ou a loja optou por autorizar sem passar pela autenticação'
    );

    static $seguranca_transacao_mastercard = array(
         2 => 'Portador autenticado com sucesso',
         1 => 'Portador não realizou autenticação, pois o Emissor não forneceu mecanismos de autenticação',
         0 => 'Portador não se autenticou com sucesso ou a loja optou por autorizar sem passar pela autenticação'
    );

    static $erros_transacao = array(
         1 => 'Criada',
         2 => 'Credenciais inválidas',
         3 => 'Transação inexistente',
         9 => 'Cancelamento por timeout do usuário',
        10 => 'Inconsistência no envio do cartão',
        11 => 'Modalidade não habilitada',
        12 => 'Número de parcelas inválido',
        20 => 'Status não permite autorização',
        21 => 'Prazo de autorização vencido',
        22 => 'EC não autorizado',
        30 => 'Transação não autorizada para captura',
        31 => 'Prazo de captura vencido',
        32 => 'Valor de captura inválido',
        33 => 'Falha ao capturar',
        40 => 'Prazo de cancelamento vencido',
        41 => 'Status não permite cancelamento',
        42 => 'Falha ao cancelar',
        99 => 'Erro inesperado'
    );

}
