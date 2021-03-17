<?php

include 'conexao.php';
$estado = $_GET['cid'];
$sql = "SELECT * FROM cid10 WHERE cid = '$estado' ORDER BY descricao";
$res = pg_query($sql);
$num = pg_num_rows($res);
for ($i = 0; $i < $num; $i++) {
	$dados = pg_fetch_array($res);
	echo $dados['descricao'];
}
