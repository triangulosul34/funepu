<?php
include('verifica.php');
$id=$_GET['id'];
include ('conexao.php');
$stmt = "update atendimentos set solicitacao_sisreg_dt=now(), username_solicitacao_sisreg='$usuario' where transacao='$id'";

$sth = pg_query ( $stmt ) or die ( $stmt );

header('Location:painel_sisreg.php');



?>