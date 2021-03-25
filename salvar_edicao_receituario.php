<?php

$medicamento = pg_escape_string(stripslashes($_POST['medicamento']));
$quantidade = pg_escape_string(stripslashes($_POST['quantidade']));
$usar = pg_escape_string(stripslashes($_POST['usar']));
$cuidados = pg_escape_string(stripslashes($_POST['cuidados']));
$transacao = pg_escape_string(stripslashes($_POST['transacao']));

include 'conexao.php';
$stmt = "update receituario_remedio set medicamentos = '$medicamento', quantidade = '$quantidade', modo_usar = '$usar' where transacao = $transacao";
$sth = pg_query($stmt) or die($stmt);
