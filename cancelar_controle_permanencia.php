<?php
$id = $_GET['id'];

require 'tsul_ssl.php';

include 'conexao.php';
$sql = "DELETE FROM destino_paciente where destino_id = $id";
$result = pg_query($sql) or die($sql);
?>
<table id="data_table" class="table">
	<thead>
		<tr>
			<th>Prontuário</th>
			<th>Paciente</th>
			<th>DT. Entrada</th>
			<th>DT. Saída</th>
			<th>Hora</th>
			<th>Destino</th>
			<th>Dias de Permanência</th>
			<th>Ação</th>
		</tr>
	</thead>
	<tbody>
		<?php
		include 'conexao.php';
		$sql = "SELECT a.destino_id, b.paciente_id, c.nome, b.dat_cad as data_entrada, a.data as data_saida, a.hora, a.destino_encaminhamento as destino  FROM destino_paciente a INNER JOIN atendimentos b ON a.atendimento_id = b.transacao INNER JOIN pessoas c ON b.paciente_id = c.pessoa_id INNER JOIN controle_permanencia d ON d.atendimento_id = a.atendimento_id WHERE motivo = 'Finalizado pelo controle de Permanencia' AND data_controle = '" . date('Y-m-d') . "' ORDER BY d.controle_permanecia_id desc";
		$result = pg_query($sql) or die($sql);
		while ($row = pg_fetch_object($result)) {
			?>
		<tr>
			<td><?= $row->paciente_id; ?>
			</td>
			<td><?= ts_decodifica($row->nome); ?>
			</td>
			<td><?= inverteData(substr($row->data_entrada, 0, 10)); ?>
			</td>
			<td><input type="text" id="data_saida"
					value="<?= inverteData($row->data_saida); ?>"
					OnKeyPress="formatar('##/##/####', this)"
					onblur="altera_data(this.value,<?= $row->destino_id; ?>)">
			</td>
			<td><input type="text" id="data_saida"
					value="<?= $row->hora; ?>"
					OnKeyPress="formatar('##:##', this)"
					onblur="altera_hora(this.value,<?= $row->destino_id; ?>)">
			</td>
			<?php
				if ($row->destino == '01') {
					echo '<td>ALTA</td>';
				} elseif ($row->destino == '02') {
					echo '<td>ALTA / ENCAM. AMBUL.</td>';
				} elseif ($row->destino == '07') {
					echo '<td>EM OBSERVAÇÃO / MEDICAÇÃO</td>';
				} elseif ($row->destino == '10') {
					echo '<td>EXAMES / REAVALIACAO</td>';
				} elseif ($row->destino == '03') {
					echo '<td>PERMANÊCIA</td>';
				} elseif ($row->destino == '04') {
					echo '<td>TRANSF. OUTRA UPA</td>';
				} elseif ($row->destino == '05') {
					echo '<td>TRANSF. INTERN. HOSPITALAR</td>';
				} elseif ($row->destino == '06') {
					echo '<td>ÓBITO</td>';
				} elseif ($row->destino == '09') {
					echo '<td>NAO RESPONDEU CHAMADO</td>';
				} elseif ($row->destino == '11') {
					echo '<td>ALTA EVASÃO</td>';
				} elseif ($row->destino == '12') {
					echo '<td>ALTA PEDIDO</td>';
				} elseif ($row->destino == '14') {
					echo '<td>ALTA / POLICIA</td>';
				} elseif ($row->destino == '15') {
					echo '<td>ALTA / PENITENCIÁRIA</td>';
				} elseif ($row->destino == '16') {
					echo '<td>ALTA / PÓS MEDICAMENTO</td>';
				} elseif ($row->destino == '20') {
					echo '<td>ALTA VIA SISTEMA</td>';
				} elseif ($row->destino == '21') {
					echo '<td>TRANSFERENCIA</td>';
				}
			$d1 = strtotime($row->data_saida);
			$d2 = strtotime(substr($row->data_entrada, 0, 10));
			$dataFinal = ($d2 - $d1) / 86400;
			if ($dataFinal < 0) {
				$dataFinal *= -1;
			} ?>
			<td><?= $dataFinal; ?>
			</td>
			<td><button class="btn btn-raised btn-danger btn-min-width mr-1 mb-1"
					onclick="cancelar_permanencia(<?= $row->destino_id; ?>)">Cancelar</button>
			</td>
		</tr>
		<?php
		} ?>
	</tbody>
</table>