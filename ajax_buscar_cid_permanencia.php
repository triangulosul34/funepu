<?php
include('conexao.php');
$estado = $_GET['cid'];
$sql = "SELECT * FROM cid10 a INNER JOIN cid_internacao b ON a.cid = b.cid WHERE a.cid = '$estado' ORDER BY descricao";
$res = pg_query($sql) or die($sql);
$num = pg_num_rows($res);
for ($i = 0; $i < $num; $i++) {
    $dados = pg_fetch_array($res);
    echo $dados['descricao'];
}
