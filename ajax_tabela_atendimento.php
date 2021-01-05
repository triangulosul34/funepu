<?php
require 'tsul_ssl.php';
$transacao = $_GET['transacao'];
$nome = $_GET['nome'];
$cns = $_GET['cns'];
$idade = $_GET['idade'];
$prontuario = $_GET['prontuario'];
?>
<?php include 'conexao.php';
$stmt = "select a.data, a.hora, c.nome, a.prescricao_id, d.nome as medico
					from prescricoes a
					left join atendimentos b on a.atendimento_id = b.transacao
					left join pessoas c on b.paciente_id = c.pessoa_id
					left join pessoas d on d.pessoa_id = a.profissional_id
					where a.atendimento_id = $transacao order by a.hora desc";
$sth = pg_query($stmt) or die($stmt);

while ($row = pg_fetch_object($sth)) {
	$seq = $row->sequencia + 1;
	echo '<tr>';
	echo "<td class='small'>" . $seq . '</td>';
	echo "<td class='small'>" . date('d/m/Y', strtotime($row->data)) . '</td>';
	echo "<td class='small'>" . $row->hora . '</td>';
	echo "<td class='small'>" . ts_decodifica($row->nome) . '</td>';
	echo "<td class='small'>" . $row->medico . '</td>';
	echo "<td class='small'>
				<a href=\"prescricaoenfermagemy.php?id=$row->prescricao_id&p=$transacao\" target=\"_blank\" 
				class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" 
				data-original-title=\"Prescrição\">
                <i class=\"fas fa-print\"></i>
				</a>
				<input type='button' onClick=\"window.open('popprescricao.php?pr=$row->prescricao_id&id=$transacao&nome=$nome&cns=$cns&idade=$idade&prontuario=$prontuario', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=980, height=550'); return false;\" target='_blank'
				class='btn btn-secondary btn-sm' data-toggle=\"tooltip\" data-original-title=\"Duplicar Prescrição\" value='+'>
				
			</td>";

	echo '<tr>';
}
