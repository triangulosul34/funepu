<?php

include('conexao.php');

$cpf = $_GET['cpf'];

$sql = "select * from pessoas where cpf = '$cpf'";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);

if ($row->cpf) {
    echo "<script>alert('cpf já existe');</script>";
}
