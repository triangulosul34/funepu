<?php
include 'verifica.php';
$tipo_atendimento = $_GET['tipo_atendimento'];
$data = date('Y-m-d');

$consultorio = '';
if (rtrim($tipo_atendimento) == 'ADULTO') {
	$consultorio = 'Consultorio Adulto';
} else {
	$consultorio = 'Ortopedia';
}

if ($box == 14) {
	$where = $where . ' and coronavirus = 1';
} else {
	$where = $where . ' and coronavirus <> 1';
}

$qtdAtendiemento = '';
include 'conexao.php';
$stmtCount = 'SELECT count(*) as qtd from atendimentos ';
if (rtrim($tipo_atendimento) == 'ADULTO') {
	$stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Atendimento') AND especialidade = 'Consultorio Adulto' and cast(tipo as integer) != 9";
} elseif (rtrim($tipo_atendimento) == 'ORTOPEDIA') {
	$stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Atendimento') AND especialidade = 'Ortopedia' and cast(tipo as integer) != 9";
} elseif (rtrim($tipo_atendimento) == 'EXAME') {
	$stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d') . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Triagem') AND tipo = '6' and cast(tipo as integer) != 9";
} elseif (rtrim($tipo_atendimento) == 'PORTA') {
	$stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Atendimento') AND especialidade = 'Consultorio Adulto' and prioridade in ('AZUL','VERDE') and cast(tipo as integer) != 9";
} elseif (rtrim($tipo_atendimento) == 'ALA VERMELHA') {
	$stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Atendimento') AND especialidade = 'Consultorio Adulto' and prioridade in ('AZUL','VERDE') and cast(tipo as integer) != 9";
} elseif (rtrim($tipo_atendimento) == 'ODONTOLOGIA') {
	$stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' AND tipo = '9' and status <> 'Atendimento Finalizado'";
}

$stmtCount = $stmtCount;

$sthCount = pg_query($stmtCount) or die($stmtCount . $tipo_atendimento);
$rowCount = pg_fetch_object($sthCount);
$qtdAtendiemento = $rowCount->qtd;

include 'conexao.php';
$qtdatmed = 0;
$stmtqtdmd = "SELECT count(*) as qtd from atendimentos where med_atendimento = '$usuario' and dat_cad = '" . date('Y-m-d') . "' ";
$sthqtdmd = pg_query($stmtqtdmd) or die($stmtqtdmd);
$rowCountqtdmd = pg_fetch_object($sthqtdmd);
$qtdatmed = $rowCountqtdmd->qtd;
?>
<h3 style="text-align: center;">Aguardando Atendimento: <?php echo $qtdAtendiemento; ?>
	<?php if ($qtdAtendiemento > 1) {
	echo 'pacientes';
} else {
	echo 'paciente';
}
	?>
</h3>
<h4 style="text-align: center;">Você atendeu: <?php echo $qtdatmed; ?>


	<?php if ($qtdatmed > 1) {
		echo 'pacientes';
	} else {
		echo 'paciente';
	}
	?>
</h4>