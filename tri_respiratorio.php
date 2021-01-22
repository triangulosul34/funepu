<?php

$id = $_GET['id'];
$crvs = $_GET['crvs'];

include 'conexao.php';
$sql = "update atendimentos set coronavirus = $crvs where transacao = $id";
$result = pg_query($sql) or die($sql);
