<?php

include 'Config.php';
require 'tsul_ssl.php';

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$start = $_POST['start'];
	$end = $_POST['end'];
	$hora_inicial = $_POST['hora_cad_inicial'];
	$hora_final = $_POST['hora_cad_final'];

	if ($start != '') {
		$data = inverteData($start);
		if ($where != '') {
			$where = $where . " and (a.dat_cad >= '$data')";
		} else {
			$where = $where . " (a.dat_cad >= '$data')";
		}
	}

	if ($end != '') {
		$data = inverteData($end);
		if ($where != '') {
			$where = $where . " and (a.dat_cad <= '$data')";
		} else {
			$where = $where . " (a.dat_cad <= '$data')";
		}
	}

	if ($hora_inicial != '') {
		if ($where != '') {
			$where = $where . " and (a.hora_cad >= '$hora_inicial')";
		} else {
			$where = $where . " (a.hora_cad >= '$hora_inicial')";
		}
	}

	if ($hora_final != '') {
		if ($where != '') {
			$where = $where . " and (a.hora_cad <= '$hora_final')";
		} else {
			$where = $where . " (a.hora_cad <= '$hora_final')";
		}
	}

	if (isset($_POST['permanencia'])) {
		$arquivo = 'Relatorio Tempo Permanencia.xls';
		$html = '';
		$html .= '<table style="font-size:12px" border="1">';
		$html .= '<tr>';
		$html .= '<td colspan="9" align=\'center\'>UPA ' . UNIDADE_CONFIG . ' - TEMPO DE PERMANENCIA -- ' . inverteData($start) . '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<tr align=\'center\'>';
		$html .= '<td><b>Atendimento</b></td>';
		$html .= '<td><b>Paciente</b></td>';
		$html .= '<td><b>Contato</b></td>';
		$html .= '<td><b>Origem</b></td>';
		$html .= '<td><b>Prioridade</b></td>';
		$html .= '<td><b>Hora Entrada</b></td>';
		$html .= '<td><b>Hora Triagem</b></td>';
		$html .= '<td><b>Hora Atendimento</b></td>';
		$html .= '<td><b>Hora Alta</b></td>';
		$html .= '<td><b>Medico</b></td>';
		$html .= '<td><b>Tempo Permanencia</b></td>';
		$html .= '</tr>';
		include 'conexao.php';
		$stmt = "select a.transacao, c.nome, case when c.celular is null or c.celular = '' then case when c.celular2 is null or c.celular2 = '' then case when c.telefone is null or c.telefone ='' then c.telefone2 else c.telefone end else c.celular2 end else c.celular end as contato, k.origem, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.hora_destino, d.nome as nomemed, (a.data_destino::date || ' ' || a.hora_destino::time)::timestamp - (a.dat_cad::date || ' ' || a.hora_cad::time)::timestamp as permanencia from atendimentos a 
						left join pessoas c on a.paciente_id = c.pessoa_id
						left join pessoas d on a.med_atendimento = d.username
                        left join tipo_origem k on cast(k.tipo_id as varchar) = a.tipo
                        left join destino_paciente p on p.atendimento_id = a.transacao  ";

		if ($where != '') {
			$stmt = $stmt . ' where ' . $where;
		} else {
			$stmt = $stmt . " where dat_cad='" . date('Y-m-d') . "'";
		}

		$stmt = $stmt . ' and (p.destino_encaminhamento <> 6 or p.destino_encaminhamento is null)  order by c.nome';
		$sth = pg_query($stmt) or die($stmt);
		while ($row = pg_fetch_object($sth)) {
			$html .= '<tr>';
			$html .= '<td>' . $row->transacao . '</td>';
			$html .= '<td>' . ts_decodifica($row->nome) . '</td>';
			$html .= '<td>' . $row->contato . '</td>';
			$html .= '<td>' . $row->origem . '</td>';
			$html .= '<td>' . $row->prioridade . '</td>';
			$html .= '<td>' . $row->hora_cad . '</td>';
			$html .= '<td>' . $row->hora_triagem . '</td>';
			$html .= '<td>' . $row->hora_atendimento . '</td>';
			$html .= '<td>' . $row->hora_destino . '</td>';
			$html .= '<td>' . ts_decodifica($row->nomemed) . '</td>';
			$html .= '<td>' . $row->permanencia . '</td>';
			$html .= '</tr>';
		}
		$html .= '</table>';

		// Configurações header para forçar o download
		header('Expires: Mon, 26 Jul 2017 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D,d M YH:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/x-msexcel');
		header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
		header('Content-Description: PHP Generated Data');
		// Envia o conteúdo do arquivo
		echo $html;
		exit;
	}
	if (isset($_POST['excel'])) {
		$arquivo = 'Relatorio Atendimento.xls';
		// Criamos uma tabela HTML com o formato da planilha
		$html = '';
		$html .= '<table style="font-size:12px" border="1">';
		$html .= '<tr>';
		$html .= '<td colspan="8" align=\'center\'>UPA ' . UNIDADE_CONFIG . ' - RELACAO ATENDIMENTOS</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<tr align=\'center\'>';
		$html .= '<td><b>Solicitacao</b></td>';
		$html .= '<td><b>Paciente</b></td>';
		$html .= '<td><b>Origem</b></td>';
		$html .= '<td><b>Chegada</b></td>';
		$html .= '<td><b>Triagem</b></td>';
		$html .= '<td><b>Atendimento</b></td>';
		$html .= '<td><b>Situacao</b></td>';
		$html .= '<td><b>Data de Envio</b></td>';
		$html .= '</tr>';
		include 'conexao.php';
		$stmt = 'select a.status, a.dat_cad, a.hora_cad,a.hora_triagem, c.nome, a.hora_destino, k.origem, data_envio from atendimentos a 
                        left join pessoas c on a.paciente_id = c.pessoa_id
                        left join tipo_origem k on cast(k.tipo_id as varchar) = a.tipo';

		if ($where != '') {
			$stmt = $stmt . ' where ' . $where;
		} else {
			$stmt = $stmt . " where dat_cad='" . date('Y-m-d') . "'";
		}

		$stmt = $stmt . ' order by c.nome collate ordenacao_nome';
		$sth = pg_query($stmt) or die($stmt);
		//echo $stmt;
		$qtde = 0;
		while ($row = pg_fetch_object($sth)) {
			$html .= '<tr>';
			$html .= '<td>' . inverteData(substr($row->dat_cad, 0, 10)) . '</td>';
			$html .= '<td>' . ts_decodifica($row->nome) . '</td>';
			$html .= '<td>' . $row->origem . '</td>';
			$html .= '<td>' . $row->hora_cad . '</td>';
			$html .= '<td>' . $row->hora_triagem . '</td>';
			$html .= '<td>' . $row->hora_destino . '</td>';
			$html .= '<td>' . $row->status . '</td>';
			$html .= '<td>' . $row->data_envio . '</td>';
			$html .= '</tr>';

			$qtde = $qtde + 1;
		}
		$html .= '<tr>';
		$html .= '<td>Quantidade de Pacientes</td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td>' . $qtde . '</td>';
		$html .= '</tr>';
		$html .= '</table>';

		// Configurações header para forçar o download
		header('Expires: Mon, 26 Jul 2017 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D,d M YH:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/x-msexcel');
		header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
		header('Content-Description: PHP Generated Data');
		// Envia o conteúdo do arquivo
		echo $html;
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="pt-br" class="loading">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="tsul" content="tsul">
	<meta name="keywords" content="tsul">
	<meta name="author" content="TSUL">
	<title>FUNEPU | Relatorio Diário</title>
	<link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
	<link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
	<link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
	<link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
	<link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<link
		href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
		rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
	<link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
	<link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
	<link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
	<link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
	<link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
	<link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="app-assets/vendors/css/pickadate/pickadate.css">
	<script defer src="/your-path-to-fontawesome/js/all.js"></script>
	<!--load all styles -->



</head>
<style>
	hr {
		color: #12A1A6;
		background-color: #12A1A6;
		margin-top: 0px;
		margin-bottom: 0px;
		height: 4px;
		width: 300px;
		margin-left: 0px;
		border-top-width: 0px;
	}
</style>

<body class="pace-done" cz-shortcut-listen="true">
	<!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

	<!-- <div class="wrapper"> -->
	<?php include 'menu.php'; ?>
	<?php include 'header.php'; ?>
	<div class="main-panel">
		<div class="main-content">
			<div class="content-wrapper">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<!-- <div class="card-header" style="display: flex;align-items: center;justify-content: space-between; background: #00777a"> -->

							<div class="card-header">
								<div class="row">
									<div class="col-6">
										<div class="row">
											<div class="col-12">
												<h4 class="card-title">
													<p
														style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
														» </p>Relatorio de Atendimentos Diario
												</h4>
											</div>
											<div class="col-12">
												<hr>
											</div>
										</div>

									</div>
									<div class="col-6">
										<div class="float-right">
											<ol class="breadcrumb">
												<li><a href="../index.html">Home</a></li>
												<li class="active">Rel Diario</li>
											</ol>
										</div>
									</div>
								</div>
							</div>
							<div class="card-content">
								<div class="card-body">
									<form action="#" method="POST">
										<div class="row">
											<div class="col-3">
												<div class="form-group">
													<label>Data Inicial</label>
													<input type="date" class="form-control square" id="start"
														name="start"
														value="<?php echo $_POST['start']; ?>"
														onkeydown="mascaraData(this)">
												</div>
											</div>
											<div class="col-3">
												<div class="form-group">
													<label>Data Final</label>
													<input type="date" class="form-control square" id="end" name="end"
														value="<?php echo $_POST['end']; ?>"
														onkeydown="mascaraData(this)">
												</div>
											</div>
											<div class="col-2"><label for="">Hora inicial</label><input type="time"
													class="form-control" name="hora_cad_inicial" id="hora_cad_inicial"
													value="<?php echo $_POST['hora_cad_inicial']; ?>">
											</div>
											<div class="col-2"><label for="">Hora final</label><input type="time"
													class="form-control" name="hora_cad_final" id="hora_cad_final"
													value="<?php echo $_POST['hora_cad_final']; ?>">
											</div>
											<div class="col-2">
												<label class="control-label">Ação</label><br>
												<button type="submit" name="gerarrelatorio" class="btn btn-primary"
													style="width:100%">Pesquisar</button>
											</div>
										</div>
										<div class="row mb-2">
											<div class="col-sm-4" id="idbaixaprontuario">
												<div class="form-group">
													<label>Baixa de Protuario</label>
													<div class="input-group">
														<div
															class="custom-control custom-checkbox custom-control-inline">
															<input type="checkbox" id="baixa_prontuario"
																name="baixa_prontuario" class="custom-control-input"
																onclick="baixaprontuario()">
															<label class="custom-control-label"
																for="baixa_prontuario">Sim</label>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-4" style="display: none;" id="prontuario">
												<label class="control-label">Baixa de Protuario</label> <input
													type="text" name="baixar_prontuario" id="baixar_prontuario"
													class="form-control" value="" style="font-weight: bold;"
													onkeyup="maiuscula(this)">
											</div>
										</div>
										<div class="row">
											<div class="col-12">
												<button type="submit" name="excel" class="btn btn-success"
													value="excel">Gerar Excel </button>
												<button type="submit" name="permanencia" class="btn btn-info">Tempo de
													Permanência em Excel</button>
											</div>
										</div>
										<div class="row mt-3">
											<div class="col-12">
												<table id="data_table" class="table">
													<thead>
														<tr>
															<th style="display:none;">
																<div class="checkbox-custom checkbox-primary"><input
																		type="checkbox" name="todos" id='todos'
																		onclick='marcardesmarcar();'
																		value="T"><label></label></div>
															</th>
															<th width="10%">Data/Hora</th>
															<th width="25%">Paciente</th>
															<th width="8%">Triagem</th>
															<th width="8%">Atend.</th>
															<th width="14%">Situação</th>
															<th width="12%">Ação</th>
														</tr>
													</thead>
													<tfoot>
														<tr>
															<th style="display:none;">#</th>
															<th width="5%">Data/Hora</th>
															<th width="25%">Paciente</th>
															<th width="8%">Triagem</th>
															<th width="8%">Atend.</th>
															<th width="12%">Situação</th>
															<th width="12%">Ação</th>
														</tr>
													</tfoot>
													<tbody>
														<?php
														include 'conexao.php';
														$stmt = "select a.transacao,a.med_atendimento as nomemed, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.dat_cad as cadastro, 	c.nome, k.origem, a.tipo,a.hora_destino,
                                                        CASE prioridade WHEN 'VERMELHO' THEN '0' WHEN 'LARANJA' THEN '1' WHEN 'AMARELO' THEN '2' WHEN 'VERDE' THEN '3'  WHEN 'AZUL' THEN '4' ELSE '5'
                                                        END as ORDEM, a.coronavirus from atendimentos a 
                                                        left join pessoas c on a.paciente_id=c.pessoa_id
                                                        left join tipo_origem k on cast(k.tipo_id as varchar)=a.tipo ";
														if ($where != '') {
															$stmt = $stmt . ' where ' . $where;
														} else {
															$stmt = $stmt . " where dat_cad='" . date('Y-m-d') . "'";
														}
														$stmt = $stmt . ' order by a.dat_cad desc,a.hora_cad desc ';
														$sth = pg_query($stmt) or die($stmt);
														//echo $stmt;
														while ($row = pg_fetch_object($sth)) {
															if ($row->prioridade == 'AMARELO') {
																$classe = 'style="background-color:#FFEE58"';
																$color = 'black';
															}
															if ($row->prioridade == 'VERMELHO') {
																$classe = "class='bg-danger' style='color: white'";
																$color = 'white';
															}
															if ($row->prioridade == 'VERDE') {
																$classe = "class='bg-success' style='color: white'";
																$color = 'white';
															}
															if ($row->prioridade == 'AZUL') {
																$classe = "class='bg-info' style='color: white'";
																$color = 'white';
															}
															if ($row->prioridade == 'LARANJA') {
																$classe = "class='bg-warning' style='color: white'";
																$color = 'white';
															}
															if ($row->prioridade == '') {
																$classe = 'style="background-color:Gainsboro"';
																$color = 'black';
															} else {
																$color = 'black';
															}

															$ip = getenv('REMOTE_ADDR');
															echo '<tr ' . $classe . ' >';
															if ($row->coronavirus == 1) {
																echo "<td style='display:none;'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . '"><label></label></div></td>';
																echo "<td class='blink'>" . inverteData(substr($row->cadastro, 0, 10)) . '<br>' . $row->hora_cad . '<br>' . $row->paciente_id . '</td>';
																echo "<td class='blink'><a data-toggle=\"popover\" data-content=\"Ir para o cadastro do paciente.\" data-trigger=\"hover\" data-original-title=\"Paciente\" href='novoatendimento.php?id=" . $row->transacao . "' target='_blank'>" . ts_decodifica($row->nome) . '<br><br> Origem:' . $row->origem . '</a></td>';
																//echo "<td>".utf8_encode($row->convenio)."</td>";
																echo "<td class='blink'>" . $row->hora_triagem . '</td>';
																echo "<td class='blink'>" . $row->hora_destino . '</td>';

																if ($row->status == 'Atendimento Finalizado') {
																	echo "<td class='blink'>" . $row->status . '<br>';
																	echo '<small>' . substr($row->nomemed, 0, 21) . '</small></td>';
																} else {
																	echo "<td class='blink'>" . $row->status . '</td>';
																}
															} else {
																echo "<td style='display:none;'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . '"><label></label></div></td>';
																echo '<td>' . inverteData(substr($row->cadastro, 0, 10)) . '<br>' . $row->hora_cad . '<br>' . $row->paciente_id . '</td>';
																echo "<td ><a data-toggle=\"popover\" data-content=\"Ir para o cadastro do paciente.\" data-trigger=\"hover\" data-original-title=\"Paciente\" href='novoatendimento.php?id=" . $row->transacao . "' target='_blank' style=\"color:$color\">" . ts_decodifica($row->nome) . '<br><br> Origem:' . $row->origem . '</a></td>';
																//echo "<td>".utf8_encode($row->convenio)."</td>";
																echo '<td>' . $row->hora_triagem . '</td>';
																echo '<td>' . $row->hora_destino . '</td>';

																if ($row->status == 'Atendimento Finalizado') {
																	echo '<td>' . $row->status . '<br>';
																	echo '<small>' . substr($row->nomemed, 0, 21) . '</small></td>';
																} else {
																	echo '<td>' . $row->status . '</td>';
																}
															}

															echo '<td>';
															/*if($row->status != 'Aguardando Triagem'){*/
															echo "<a href=\"atendimentoclinico.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\" style=\"color:$color\"><i class=\"fas fa-file-medical\"></i></a>";
															/*}*/

															if ($row->tipo == 9) {
																echo "<a href=\"relOdonto.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"ODONTOLOGICO\" style=\"color:$color\"><i class=\"fas fa-print\"></i></a>";
															} else {
																echo "<a href=\"relFAA.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"FAA\" style=\"color:$color\"><i class=\"fas fa-print\"></i></a>";
															}
															// if ($perfil == '06' or $perfil == '04') {
														?>
														<?php if ($row->status != 'Atendimento Finalizado') { ?>
														<a id="triagemmanual"
															data-id="<?php echo $row->transacao; ?>"
															class="btn btn-sm btn-icon btn-pure btn-default delete-row-btn"
															data-target="#modalConteudo" data-toggle="modal"
															data-original-title="Triagem" <?php if ($row->prioridade != '' and $row->prioridade != 'AMARELO') { ?>style="color:white"
															<?php } ?>
															onClick="valorTriagem(this);">
															<i class="fas fa-check-circle" aria-hidden="true"
																onclick=""></i>
														</a>
														<?php } ?>
														<?php //}

															if ($perfil == '06' or $perfil == '04' or $perfil == '01') { ?>
														<a id="mudasituacao"
															data-id="<?php echo $row->transacao; ?>"
															class="btn btn-sm btn-icon btn-pure btn-default delete-row-btn"
															data-target="#modalConteudoSitu" data-toggle="modal"
															data-original-title="Mudar Situação" <?php if ($row->prioridade != '' and $row->prioridade != 'AMARELO') { ?>style="color:white"
															<?php } ?>
															onClick="valorSituacao(this);">
															<i class="fa fa-user" aria-hidden="true" onclick=""></i>
														</a>
														<?php } ?>

														<?php echo '</tr>';
														}
														?>
													</tbody>
												</table>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="response"></div>
	<?php include 'footer.php'; ?>
	<!-- </div> -->

	<script src="app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/core/popper.min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/core/bootstrap.min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/prism.min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/jquery.matchHeight-min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/screenfull.min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/pace/pace.min.js" type="text/javascript"></script>
	<script src="app-assets/vendors/js/chartist.min.js" type="text/javascript"></script>
	<script src="app-assets/js/app-sidebar.js" type="text/javascript"></script>
	<script src="app-assets/js/notification-sidebar.js" type="text/javascript"></script>
	<script src="app-assets/js/customizer.js" type="text/javascript"></script>
	<script src="app-assets/js/dashboard1.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
	<script src="app-assets/js/scripts.js" type="text/javascript"></script>
	<script src="app-assets/js/popover.js" type="text/javascript"></script>
	<script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
	<script defer src="/your-path-to-fontawesome/js/all.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		function baixaprontuario() {
			document.getElementById("idbaixaprontuario").style.display = 'none';
			document.getElementById("prontuario").style.display = 'block';
		}

		$(document).ready(function() {
			$(window).keydown(function(event) {
				if (event.keyCode == 13) {
					if (document.getElementById("baixar_prontuario").value != '') {
						$.get('baixar_prontuario.php?a=' + document.getElementById("baixar_prontuario").value,
							function(dataReturn) {
								$("#response").html(dataReturn);
							});
					}
					document.getElementById("baixar_prontuario").value = '';
					event.preventDefault();
					return false;
				}
			});
		});
	</script>
</body>

</html>