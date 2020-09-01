<?php

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

$id = $_GET['id'];
$data = $_GET['data'];

include ("conexao.php");
$sql = "UPDATE destino_paciente SET data = '" . inverteData($data) . "' WHERE destino_id=$id";
$result = pg_query($sql) or die($sql);
