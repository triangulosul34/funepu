<?php

include 'conexao.php';
include 'tsul_ssl.php';

$sql = 'select * from sindrome_gripal where sindrome_gripal_id in (1699,1690)';
$result = pg_query($sql);
while ($row = pg_fetch_object($result)) {
	$sqlu = "update sindrome_gripal set nome_mae ='" . ts_decodifica($row->nome_mae) . "' where pessoa_id = $row->sindrome_gripal_id";
	$resultu = pg_query($sqlu) or die($sqlu);
}
