<?php
include('verifica.php');
$id=$_GET['id'];
include ('conexao.php');
$stmt = "update itenspedidos set situacao='Realizado', tecnico='$usuario' where exame_nro='$id'";

$sth = pg_query ( $stmt ) or die ( $stmt );

header('Location:painel_rx.php');



?>