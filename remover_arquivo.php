<?php

$arquivo = $_GET['a'];

include 'conexao.php';
$sql = "update excel_notificacao set controle = 0 where arquivo = '$arquivo'";
   $result = pg_query($sql) or die($sql);
