<?php

include 'conexao.php';

$sql = "select * from pessoas where nome like '%W%'";
$result = pg_query($sql);
while ($row = pg_fetch_object($result)) {
	$sqlu = "update pessoas set nome='" . str_replace('W', 'DDD', $row->nome) . "' where pessoa_id = $row->pessoa_id";
	$resultu = pg_query($sqlu) or die($sqlu);
}

header('Location: index.php');
