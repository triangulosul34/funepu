<?php
include('conexao.php');
$estado = $_GET['cid'];
$sql = "SELECT * FROM cid10 WHERE cid = '$estado' ORDER BY cid";
$res = pg_query($sql);
$num = pg_num_rows($res);
$dados = pg_fetch_array($res);
echo  $dados['descricao'];

?>


