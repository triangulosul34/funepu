<?php
error_reporting(0);

$transacao = $_GET['transacaoMod'];
$status = $_GET['situacaoMod'];

include('conexao.php');
$stmt1 = "UPDATE atendimentos SET status = '$status', destino_paciente = (case when destino_paciente = '09' then null else destino_paciente end) WHERE transacao = $transacao";


if (pg_query($stmt1) or die($stmt1)) {
	echo "Situação alterarda com sucesso! ";
}
