<?php

error_reporting(0);
include 'verifica.php';
include 'funcoes.php';
include 'conexao.php';
$edita = false;
date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$id = $_GET['id'];
	$parametros = explode(',', $id);
	include 'conexao.php';
	$stmt = 'select procedimento_id, laudo_padrao from procedimentos where procedimento_id=' . $parametros[0];
	$sth = pg_query($stmt) or die($stmt);
	$row = pg_fetch_object($sth);
	$laudo = $row->laudo_padrao;
	include 'conexao.php';
	$stmt = "update itenspedidos set resultado = '$laudo', laudo_sel=" . $parametros[0] . ' where exame_nro=' . $parametros[1];
	$sth = pg_query($stmt) or die($stmt);
	include 'conexao.php';
	$datalog = date('Y-m-d H:i:s');
	$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($parametros[1], 'Trocou Laudo', '$usuario', '$datalog' )";
	$sth = pg_query($stmtx) or die($stmtx);

	header("location: emitelaudos.php?id=$parametros[1]");
}
