<?php

error_reporting(0);
include('verifica.php');
$transacao = $_POST['transacaoModal'];
$consultorio = $_POST['consultorioModal'];
$prioridade = $_POST['prioridadeModal'];
$observacao = $_POST['observacao'];
$discriminador = $_POST['discriminador'];
$fluxograma = $_POST['fluxograma'];
$pa_sis = $_POST['pa_sis'];
$pa_dist = $_POST['pa_dist'];
$dor = $_POST['dor'];
$temperatura = $_POST['temperatura'];
$queixa = $_POST['queixa'];
$fimclassificacao = date("Y-m-d H:i:s");
$horatriagem = date("H:i");
$peso =  $_POST['peso'];
$oxigenio = $_POST['oxigenio'];
$pulso = $_POST['pulso'];
$glicose = $_POST['glicose'];


include('conexao.php');
$stmtdiscriminador = "SELECT discriminador FROM classificacao where cast(atendimento_id as integer) = $transacao";
$sthdiscriminador = pg_query($stmtdiscriminador) or die($stmtdiscriminador);
$rowdiscriminador = pg_fetch_object($sthdiscriminador);

if ($discriminador == 'Selecione o Discriminador') {
	$discriminador = $rowdiscriminador->discriminador;
}

$transacao = stripslashes(pg_escape_string($transacao));
$consultorio = stripslashes(pg_escape_string($consultorio));
$prioridade = stripslashes(pg_escape_string($prioridade));
$discriminador = stripslashes(pg_escape_string($discriminador));
$fluxograma = stripslashes(pg_escape_string($fluxograma));
$pa_sis = stripslashes(pg_escape_string($pa_sis));
$pa_dist = stripslashes(pg_escape_string($pa_dist));
$dor = stripslashes(pg_escape_string($dor));
$temperatura = stripslashes(pg_escape_string($temperatura));
$queixa = stripslashes(pg_escape_string($queixa));
$peso = stripslashes(pg_escape_string($peso));
$oxigenio = stripslashes(pg_escape_string($oxigenio));
$pulso = stripslashes(pg_escape_string($pulso));
$glicose = stripslashes(pg_escape_string($glicose));
$usuario = stripslashes(pg_escape_string($usuario));

include('conexao.php');
$stmty = "SELECT upper(nome) as nome FROM atendimentos a
		left join pessoas p on a.paciente_id = p.pessoa_id
		where transacao = $transacao";
$sth = pg_query($stmty) or die($stmty);
$row = pg_fetch_object($sth);
$nome = $row->nome;

include('conexao.php');
$stmty = "
	select count(*) as qtd 
		from classificacao 
	where cast(atendimento_id as integer) = $transacao";

$sth = pg_query($stmty) or die($stmty);
$row = pg_fetch_object($sth);
$qtd = $row->qtd;


include('conexao.php');
if ($qtd == 0) {
	$stmt1 = "insert into classificacao 
		(atendimento_id,discriminador,dor,encaminhamentos,fimclassificacao,fluxograma,glicose,nome,oxigenio,peso,pressaodiastolica,pressaosistolica,
		prioridade, observacao, temperatura,pulso,usuario,queixa) 
		values ('$transacao', '$discriminador','$dor','$consultorio','$fimclassificacao', '$fluxograma', '$glicose', '$nome','$oxigenio',
				'$peso','$pa_dist','$pa_sis','$prioridade', '$observacao', '$temperatura','$pulso', '$usuario', '$queixa')";
} else {
	$stmt1 = "UPDATE classificacao 
			SET discriminador = '$discriminador',dor = '$dor', encaminhamentos = '$consultorio', fimclassificacao = '$fimclassificacao',
			fluxograma = '$fluxograma',glicose = '$glicose',nome = '$nome',oxigenio = '$oxigenio',peso = '$peso',
			pressaodiastolica = '$pa_dist',pressaosistolica = '$pa_sis',prioridade = '$prioridade', observacao = '$observacao',temperatura = '$temperatura',
			queixa = '$queixa', pulso = '$pulso', usuario='$usuario' WHERE cast(atendimento_id as integer) = $transacao";
}


include('conexao.php');
if ($prioridade == 'BRANCO') {
	$stmt2 = "UPDATE atendimentos 
			SET especialidade = '$consultorio', prioridade = '$prioridade', observacao_triagem = '$observacao', status = 'Atendimento Finalizado', hora_triagem = '$horatriagem' 
		WHERE transacao = $transacao";
} else {
	$stmt2 = "UPDATE atendimentos 
			SET especialidade = '$consultorio', prioridade = '$prioridade', observacao_triagem = '$observacao', status = 'Aguardando Atendimento', hora_triagem = '$horatriagem' 
		WHERE transacao = $transacao";
}


$data = date('Y-m-d');
$hora = date('H:i');
$ip = $_SERVER['REMOTE_ADDR'];
include('verifica.php');
include('conexao.php');
$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora,ip) 
			values ('$usuario','REALIZOU TRIAGEM','$transacao','$data','$hora', '$ip')";
$sthLogs = pg_query($stmtLogs) or die($stmtLogs);


if (pg_query($stmt1) or die($stmt1)) {
	if (pg_query($stmt2) or die($stmt2)) {
		echo "Triagem realizado com sucesso! ";
	}
}
