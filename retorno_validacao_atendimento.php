<?php
include('verifica.php');
$transacao =  $_GET['atendimento'];
include('conexao.php');
$bloqueiaAt = "select transacao,status, count(*) as total,nome
					from atendimentos a
					left join pessoas p on p.pessoa_id = a.paciente_id
						where med_atendimento = '$usuario' and transacao != $transacao and status = 'Em Atendimento' and dat_cad = '" . date('Y-m-d') . "'
					group by 1,2,4";
$sthBlock = pg_query($bloqueiaAt);
$valBlock = pg_fetch_object($sthBlock);
if ($valBlock->total > 0) {
    echo "
		<script>
			alert('Finalize o atendimento de " . $valBlock->nome . " que esta em aberto. Você será redirecionado para o atendimento do paciente em questão.');
			var win = window.open('atendimentoclinico.php?med=1&id='+" . $valBlock->transacao . ", '_blank');
			win.focus();
		</script>";
} else {
    if ($perfil == '03') {
        echo "
			<script>
				var win = window.open('atendimentoclinico.php?med=1&id='+" . $transacao . ", '_blank');
				win.focus();
			</script>";
        include('conexao.php');
        $stmtnrc = "update atendimentos set dat_atendimento='" . date('Y-m-d') . "', hora_atendimento='" . date('H:i') . "' where transacao = '$transacao' ";
        $sthnrc = pg_query($stmtnrc) or die($stmtnrc);
    } else {
        echo "
			<script>
				alert('Você não tem permissão para esta operação!');
			</script>";
    }
}
