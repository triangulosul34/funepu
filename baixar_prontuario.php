<?php

$transacao = $_GET['a'];

include 'conexao.php';
$sql = "update atendimentos set data_envio = '" . date('Y-m-d') . "' where transacao = $transacao";
$result = pg_query($sql) or die($sql);
