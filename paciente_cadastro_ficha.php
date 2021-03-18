<?php

include 'conexao.php';
include 'verifica.php';

$sql = 'update atendimentos set paciente_id = ' . $_GET['id'] . ' where transacao = ' . $_GET['atendimento'];
$result = pg_query($sql) or die($sql);

$data = date('Y-m-d');
				$hora = date('H:i');
$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
						values ('$usuario','ALTEROU O PACIENTE VINCULADO AO ATENDIMENTO','" . $_GET['atendimento'] . "','$data','$hora')";
				$sthLogs = pg_query($stmtLogs) or die($stmtLogs);
