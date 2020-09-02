<?php
	error_reporting(0);
	$id=$_GET['id'];

	include('conexao.php'); 
	$stmt="update pessoas set situacao='1' where pessoa_id=$id ";
	$sth = pg_query($stmt);
	header("location: clientes.php?id=".$id);
