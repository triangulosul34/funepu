<?php

$id = $_GET['a'];
$arquivo = $_GET['b'];

include 'conexao.php';
$sql = "update excel_notificacao set controle = 0 where arquivo = '$arquivo' and excel_notificacao = $id";
   $result = pg_query($sql) or die($sql);
include 'conexao2.php';
$sql = "update excel_notificacao set controle = 0 where arquivo = '$arquivo' and excel_notificacao = $id";
   $result = pg_query($sql) or die($sql);
