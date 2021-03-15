<?php

require 'tsul_ssl.php';

include 'verifica.php';

include 'conexao.php';
$stmt = "select descricao, tipo_atendimento from boxes where box_id='$box'";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$box_descricao = $row->descricao;
$tipo_atendimento = $row->tipo_atendimento;

$transacao = $_GET['atendimento'];
include 'conexao.php';
$bloqueiaAt = "select transacao,status, count(*) as total,nome
					from atendimentos a
					left join pessoas p on p.pessoa_id = a.paciente_id
						where med_atendimento = '" . ts_codifica($usuario) . "' and transacao != $transacao and status = 'Em Atendimento' and dat_cad = '" . date('Y-m-d') . "'
					group by 1,2,4";
$sthBlock = pg_query($bloqueiaAt) or die($bloqueiaAt);
$valBlock = pg_fetch_object($sthBlock);
if ($valBlock->total > 0) {
	echo "
		<script>
			alert('Finalize o atendimento de " . ts_decodifica($valBlock->nome) . " que esta em aberto. Você será redirecionado para o atendimento do paciente em questão.');
			var win = window.open('atendimentoclinico.php?med=1&id='+" . $valBlock->transacao . ", '_blank');
			win.focus();
		</script>";
} else {
	if (rtrim($tipo_atendimento) == 'ODONTOLOGIA') {
		echo "
			<script>
				var win = window.open('relOdonto.php?id='+" . $transacao . ", '_blank');
				win.focus();
			</script>";

		include 'conexao.php';
		$stmtnrc = "update atendimentos set dat_atendimento='" . date('Y-m-d') . "', hora_atendimento='" . date('H:i') . "', status = 'Atendimento Finalizado' where transacao = '$transacao' ";
		$sthnrc = pg_query($stmtnrc) or die($stmtnrc);
	} elseif ($perfil == '03') {
		echo "
			<script>
				var win = window.open('atendimentoclinico.php?med=1&id='+" . $transacao . ", '_blank');
				win.focus();
			</script>";
		include 'conexao.php';
		$stmtnrc = "update atendimentos set dat_atendimento='" . date('Y-m-d') . "', hora_atendimento='" . date('H:i') . "' where transacao = '$transacao' ";
		$sthnrc = pg_query($stmtnrc) or die($stmtnrc);
	} else {
		echo "
			<script>
				alert('Você não tem permissão para esta operação!');
			</script>";
	}
}
?>
<script>
window.location.href = window.location.href
</script>