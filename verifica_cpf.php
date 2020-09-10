<?php

include('conexao.php');

$cpf = $_GET['cpf'];
$id = $_GET['id'];

if ($id) {
    $sql = "select * from pessoas where cpf = '$cpf' and pessoa_id <> '$id'";
} else {
    $sql = "select * from pessoas where cpf = '$cpf'";
}
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);

if ($row->cpf) {
    echo "<script>alert('cpf já existe');</script>";
}
