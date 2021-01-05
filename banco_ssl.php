<?php

set_time_limit(3600);

include 'conexao.php';
include 'tsul_ssl.php';

$sql = 'select * from pessoas';
$result = pg_query($sql);
while ($row = pg_fetch_object($result)) {
	$sqlu = "update pessoas set nome='" . ts_codifica($row->nome) . "',cpf='" . ts_codifica($row->cpf) . "',nome_pai='" . ts_codifica($row->nome_pai) . "',nome_mae='" . ts_codifica($row->nome_mae) . "',identidade='" . ts_codifica($row->identidade) . "', criptografia = 'c' where pessoa_id = $row->pessoa_id and criptografia is null";
	$resultu = pg_query($sqlu) or die($sqlu);
}

header('Location: index.php');
