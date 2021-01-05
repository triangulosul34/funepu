<?php
require '../vendor/autoload.php';
require 'tsul_ssl.php';
function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}
?>
<br><br>
<div class="row">
	<div class="col-6">
		<h2 style="text-align: center; margin-bottom: 20px;">Atendimento Ortopedia</h2>
		<table id="dados" class="table">
			<thead>
				<tr>

					<th width="10%">Solicitação</th>
					<th width="70%">Paciente</th>
					<th width="70%">Triagem</th>
					<th width="20%">Situação</th>
				</tr>
			</thead>
			<tfoot>
				<tr>

					<th width="10%">Solicitação</th>
					<th width="70%">Paciente</th>
					<th width="70%">Triagem</th>
					<th width="20%">Situação</th>


				</tr>
			</tfoot>
			<tbody id="atualizaAtPediatrico">
				<?php
				include 'conexao.php';
				$stmt = "select a.transacao, a.paciente_id, extract(year from age(c.dt_nasc)) as idade, a.status, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, c.nome, k.origem, a.tipo, 
							CASE prioridade 
								WHEN 'VERMELHO' THEN '0' 
								WHEN 'LARANJA' THEN '1' 
								WHEN 'AMARELO' THEN '2' 
								WHEN 'VERDE' THEN '3' 
								WHEN 'AZUL' THEN '4' 
							ELSE '5' 
							END as ORDEM 
							from atendimentos a 
							left join pessoas c on a.paciente_id=c.pessoa_id 
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer)
							WHERE status = 'Aguardando Atendimento' and  dat_cad between '" . date('d/m/Y', strtotime('-1 days')) . "' and '" . date('d/m/Y') . "' and a.especialidade = 'Ortopedia' and tipo != '6' and tipo != '9' 
							ORDER by ORDEM ASC, pidade, dat_cad, hora_cad asc";
				$sth = pg_query($stmt) or die($stmt);
				//echo $stmt;
				while ($row = pg_fetch_object($sth)) {
					if ($row->prioridade == 'AMARELO') {
						$classe = 'style="background-color:gold"';
					}
					if ($row->prioridade == 'VERMELHO') {
						$classe = "class='bg-danger'";
					}
					if ($row->prioridade == 'VERDE') {
						$classe = "class='bg-success'";
					}
					if ($row->prioridade == 'AZUL') {
						$classe = "class='bg-primary'";
					}
					if ($row->prioridade == 'LARANJA') {
						$classe = "class='bg-warning'";
					}
					if ($row->prioridade == '') {
						$classe = 'style="background-color:Gainsboro"';
					}

					$ip = getenv('REMOTE_ADDR');
					echo '<tr ' . $classe . '>';
					echo '<td>' . date('d/m/Y', strtotime($row->dat_cad)) . '<br>' . $row->hora_cad . '</td>';
					echo '<td>' . ts_decodifica($row->nome) . '</td>';

					echo '<td>' . $row->hora_triagem . '</td>';
					echo '<td>' . $row->status . '</td>';
					echo '<td>';

					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-6">
		<h2 class="page-title" style="text-align: center; margin-bottom: 20px;">Atendimento Adulto</h2>
		<table id="dados" class="table">
			<thead>
				<tr>

					<th width="10%">Solicitação</th>
					<th width="70%">Paciente</th>
					<th width="70%">Triagem</th>
					<th width="20%">Situação</th>
				</tr>
			</thead>
			<tfoot>
				<tr>

					<th width="10%">Solicitação</th>
					<th width="70%">Paciente</th>
					<th width="70%">Triagem</th>
					<th width="20%">Situação</th>


				</tr>
			</tfoot>
			<tbody id="atualizaAtAdulto">
				<?php
				include 'conexao.php';
				$stmt = "select a.transacao, a.paciente_id, extract(year from age(c.dt_nasc)) as idade, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.status, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, c.nome, k.origem, a.tipo, 
							CASE
                            WHEN a.prioridade = 'VERMELHO' and a.destino_paciente is null THEN '0' 
                            WHEN a.prioridade = 'LARANJA' and a.destino_paciente is null THEN '1' 
                            WHEN a.destino_paciente = '10' and a.prioridade = 'AMARELO'  THEN '2'
                            WHEN a.prioridade = 'AMARELO' and a.destino_paciente is null THEN '3' 
                            WHEN a.destino_paciente = '10' and a.prioridade = 'VERDE'  THEN '4'
                            WHEN a.prioridade = 'VERDE' and a.destino_paciente is null THEN '5' 
                            WHEN a.prioridade = 'AZUL' and a.destino_paciente is null THEN '6' 
                        ELSE '7'  
							END as ORDEM 
							from atendimentos a 
							left join pessoas c on a.paciente_id=c.pessoa_id 
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
							WHERE status = 'Aguardando Atendimento' and  dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' and a.especialidade = 'Consultorio Adulto' and tipo != '6' and tipo != '9' and dat_cad > '2019-08-11' 
							ORDER by ORDEM ASC, pidade, dat_cad, hora_cad asc";
				$sth = pg_query($stmt) or die($stmt);
				while ($row = pg_fetch_object($sth)) {
					if ($row->prioridade == 'AMARELO') {
						$classe = 'style="background-color:gold"';
					}
					if ($row->prioridade == 'VERMELHO') {
						$classe = "class='bg-danger'";
					}
					if ($row->prioridade == 'VERDE') {
						$classe = "class='bg-success'";
					}
					if ($row->prioridade == 'AZUL') {
						$classe = "class='bg-primary'";
					}
					if ($row->prioridade == 'LARANJA') {
						$classe = "class='bg-warning'";
					}
					if ($row->prioridade == '') {
						$classe = 'style="background-color:Gainsboro"';
					}

					$ip = getenv('REMOTE_ADDR');
					echo '<tr ' . $classe . '>';
					echo '<td>' . date('d/m/Y', strtotime($row->dat_cad)) . '<br>' . $row->hora_cad . '</td>';
					echo '<td>' . ts_decodifica($row->nome) . '</td>';

					echo '<td>' . $row->hora_triagem . '</td>';
					echo '<td>' . $row->status . '</td>';
					echo '<td>';

					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
</div>
<div class="row mt-5">
	<div class="col-6">
		<h2 class="page-title" style="text-align: center; margin-bottom: 20px;">Atendimento Odontologico</h2>
		<table id="dados" class="table">
			<thead>
				<tr>
					<th width="10%">Solicitação</th>
					<th width="70%">Paciente</th>
					<th width="70%">Triagem</th>
					<th width="20%">Situação</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th width="10%">Solicitação</th>
					<th width="70%">Paciente</th>
					<th width="70%">Triagem</th>
					<th width="20%">Situação</th>
				</tr>
			</tfoot>
			<tbody id="atualizaAtAdulto">
				<?php
				include 'conexao.php';
				$stmt = "select a.transacao, a.paciente_id, extract(year from age(c.dt_nasc)) as idade, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.status, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, c.nome, k.origem, a.tipo, 
							CASE prioridade 
								WHEN 'VERMELHO' THEN '0' 
								WHEN 'LARANJA' THEN '1' 
								WHEN 'AMARELO' THEN '2' 
								WHEN 'VERDE' THEN '3' 
								WHEN 'AZUL' THEN '4' 
							ELSE '5' 
							END as ORDEM 
							from atendimentos a 
							left join pessoas c on a.paciente_id=c.pessoa_id 
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
							WHERE status = 'Aguardando Atendimento' and  dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' and tipo = '9'  
							ORDER by ORDEM ASC, pidade, dat_cad, hora_cad asc";
				$sth = pg_query($stmt) or die($stmt);
				while ($row = pg_fetch_object($sth)) {
					if ($row->prioridade == 'AMARELO') {
						$classe = 'style="background-color:gold"';
					}
					if ($row->prioridade == 'VERMELHO') {
						$classe = "class='bg-danger'";
					}
					if ($row->prioridade == 'VERDE') {
						$classe = "class='bg-success'";
					}
					if ($row->prioridade == 'AZUL') {
						$classe = "class='bg-primary'";
					}
					if ($row->prioridade == 'LARANJA') {
						$classe = "class='bg-warning'";
					}
					if ($row->prioridade == '') {
						$classe = 'style="background-color:Gainsboro"';
					}

					$ip = getenv('REMOTE_ADDR');
					echo '<tr ' . $classe . '>';
					echo '<td>' . date('d/m/Y', strtotime($row->dat_cad)) . '<br>' . $row->hora_cad . '</td>';
					echo '<td>' . ts_decodifica($row->nome) . '</td>';

					echo '<td>' . $row->hora_triagem . '</td>';
					echo '<td>' . $row->status . '</td>';
					echo '<td>';

					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-6">
		<h2 class="page-title" style="text-align: center; margin-bottom: 20px;">Aguardando Exames</h2>
		<table id="dados" class="table">
			<thead>
				<tr>
					<th>Data Solicitação</th>
					<th>Nome</th>
					<th>Medico Solicitante</th>
					<th>Ação</th>
				</tr>
			</thead>
			<tbody>
				<?php
				include 'conexao.php';
				$sql = "select a.transacao, c.transacao as atendimento_id from pedidos a inner join pessoas b on a.paciente_id = b.pessoa_id inner join atendimentos c on a.atendimento_id = c.transacao where c.destino_paciente = '10' and dt_solicitacao >= CURRENT_DATE -1 and c.status = 'Aguardando Atendimento'";
				$result = pg_query($sql) or die($sql);
				while ($row = pg_fetch_object($result)) {
					include 'conexao_laboratorio.php';
					$sql2 = "select distinct a.data, b.nome, a.medico_solicitante, array_to_string(array_agg(DISTINCT d.situacao), ',') as situacao from pedidos a
								inner join pessoas b on a.pessoa_id = b.pessoa_id
								inner join pedido_guia c on a.pedido_id = c.pedido_id 
								inner join pedido_item d on c.pedido_guia_id = d.pedido_guia_id where cod_pedidos::varchar like '%" . $row->transacao . "' and c.origem = '" . ORIGEM_CONFIG . "' and a.data >= CURRENT_DATE -1 group by 1,2,3";
					$result2 = pg_query($sql2) or die($sql2);
					$row2 = pg_fetch_object($result2);
					if ($row2) {
						?>
				<tr <?php if ($row2->situacao == '') { ?>bgcolor="#FF0000"
					style="color: #fff;" <?php } elseif ($row2->situacao == 'Liberado') { ?>bgcolor="#0B610B"
					style="color: #fff;" <?php } else { ?>bgcolor="#F7FE2E" style="color:
					#000000;" <?php } ?>>
					<td><?php echo inverteData($row2->data); ?>
					</td>
					<td><?php echo ts_decodifica($row2->nome); ?>
					</td>
					<td><?php echo $row2->medico_solicitante; ?>
					</td>
					<td>
						<?php if ($row2->situacao == 'Liberado') {
							echo "<a href='atendimentoclinico.php?id=$row->atendimento_id' target='_blank' class=\"btn btn-pure btn-danger\"><i class=\"far fa-eye\"></i></a>";
						} ?>
					</td>
				</tr>
				<?php
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th>Data Solicitação</th>
					<th>Nome</th>
					<th>Medico Solicitante</th>
					<th>Ação</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>