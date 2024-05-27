<?php

require_once __DIR__ . '/../../../api_core/config.php';
require_once __DIR__ . '/../../../api_core/response.php';

$dadoRecebido = file_get_contents('php://input');
$dadoRecebido = json_decode($dadoRecebido, true);

$cxMysqli = new mysqli('localhost', 'root', '', 'gestao_ascsacb');

if ($cxMysqli->connect_error) {
    die("Connection failed: " . $cxMysqli->connect_error);
}

$cmdSql = "SELECT * FROM contatos";

if (isset($dadoRecebido['txtBusca'])) {
    $busca = $dadoRecebido['txtBusca'];
    $cmdSql = "SELECT * FROM contatos WHERE campanha LIKE ?";
}

$cxPrepare = $cxMysqli->prepare($cmdSql);

if ($cxPrepare) {
    // Se um parâmetro de campanha foi enviado, vincular o valor
    if (isset($busca)) {
        $likeBusca = '%' . $busca . '%';
        $cxPrepare->bind_param('s', $likeBusca);
    }

    // Executar a consulta e capturar os resultados
    $dados = array(
        'result' => false,
        'values'=> array(),
    );

    if ($cxPrepare->execute()) {
        $result = $cxPrepare->get_result();
        if ($result->num_rows > 0) {
            $dados['result'] = true;
            while ($row = $result->fetch_assoc()) {
                $dados['values'][] = $row;
            }
            $dados['values'] = $result->fetch_all();
        }
    }

    // Fechar a declaração preparada
    $cxPrepare->close();
} else {
    // Tratar erro de preparação da consulta
    die("Preparation failed: " . $cxMysqli->error);
}

// Fechar a conexão
$cxMysqli->close();

// Retornar a resposta JSON
echo Response::json(200, 'success', $dados);
