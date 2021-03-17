<?php

include 'conexao.php';

$transacao = $_GET['transacao'];
$resposta = $_GET['resposta'];

$sql = "update atendimentos set convenio = $resposta where transacao = $transacao";
$result = pg_query($sql) or die($sql);
