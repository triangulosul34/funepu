<?php

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
	$where = '';
	if (isset($_POST['pesquisar'])) {
		if ($_POST['start'] != '') {
			$data = $_POST['start'];
			if ($where != '') {
				$where = $where . " and (a.dat_cad >= '$data')";
			} else {
				$where = $where . " (a.dat_cad >= '$data')";
			}
		} else {
			if ($where != '') {
				$where = $where . " and (a.dat_cad >= '" . date('Y-m-d') . "')";
			} else {
				$where = $where . " (a.dat_cad >= '" . date('Y-m-d') . "')";
			}
		}

		if ($_POST['end'] != '') {
			$data = $_POST['end'];
			if ($where != '') {
				$where = $where . " and (a.dat_cad <= '$data')";
			} else {
				$where = $where . " (a.dat_cad <= '$data')";
			}
		} else {
			if ($where != '') {
				$where = $where . " and (a.dat_cad <= '" . date('Y-m-d') . "')";
			} else {
				$where = $where . " (a.dat_cad <= '" . date('Y-m-d') . "')";
			}
		}
	}

	if (isset($_POST['excel'])) {
		if ($_POST['start'] != '') {
			$data = $_POST['start'];
			if ($where != '') {
				$where = $where . " and (a.dat_cad >= '$data')";
			} else {
				$where = $where . " (a.dat_cad >= '$data')";
			}
		} else {
			if ($where != '') {
				$where = $where . " and (a.dat_cad >= '" . date('Y-m-d') . "')";
			} else {
				$where = $where . " (a.dat_cad >= '" . date('Y-m-d') . "')";
			}
		}

		if ($_POST['end'] != '') {
			$data = $_POST['end'];
			if ($where != '') {
				$where = $where . " and (a.dat_cad <= '$data')";
			} else {
				$where = $where . " (a.dat_cad <= '$data')";
			}
		} else {
			if ($where != '') {
				$where = $where . " and (a.dat_cad <= '" . date('Y-m-d') . "')";
			} else {
				$where = $where . " (a.dat_cad <= '" . date('Y-m-d') . "')";
			}
		}
		$arquivo = 'Relatorio Tempo Permanencia.xls';
		$html = '';
		$html .= '<table style="font-size:12px" border="1">';
		$html .= '<tr>';
		$html .= '<td colspan="7" align=\'center\'>UPA ' . UNIDADE_CONFIG . ' - Atendimento de Pacientes Sintomaticos -- ' . inverteData($_POST['start']) . ' - ' . inverteData($_POST['end']) . '</td>';
		$html .= '</tr>';
		$html .= '<tr align=\'center\'>';
		$html .= '<td><b>Data/Hora</b></td>';
		$html .= '<td><b>Transacao</b></td>';
		$html .= '<td><b>Nome</b></td>';
		$html .= '<td><b>Triagem</b></td>';
		$html .= '<td><b>Atendimento</b></td>';
		$html .= '<td><b>Data Destino</b></td>';
		$html .= '<td><b>Destino</b></td>';
		$html .= '</tr>';
		$count = 0;
		include 'conexao.php';
		$stmt = "select a.transacao,d.nome as nomemed, case when z.destino_encaminhamento::varchar is null then a.destino_paciente else z.destino_encaminhamento::varchar end as destino_paciente, case when z.destino_encaminhamento::varchar is null then a.dat_cad else z.data end as data_paciente, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.dat_cad as cadastro, 	c.nome, k.origem, a.tipo,a.hora_destino,
        CASE prioridade WHEN 'VERMELHO' THEN '0' WHEN 'LARANJA' THEN '1' WHEN 'AMARELO' THEN '2' WHEN 'VERDE' THEN '3'  WHEN 'AZUL' THEN '4' ELSE '5'
        END as ORDEM, a.coronavirus from atendimentos a 
        left join pessoas c on a.paciente_id=c.pessoa_id
        left join pessoas d on a.med_atendimento=d.username 
        left join tipo_origem k on cast(k.tipo_id as varchar)=a.tipo 
        left join destino_paciente z on a.transacao = z.atendimento_id";
		if ($where != '') {
			$stmt = $stmt . ' where ' . $where;
		} else {
			$stmt = $stmt . " where dat_cad='" . date('Y-m-d') . "'";
		}
		$stmt = $stmt . ' and coronavirus <> 0';
		$stmt = $stmt . ' order by a.dat_cad desc,a.hora_cad desc ';
		$sth = pg_query($stmt) or die($stmt);
		while ($row = pg_fetch_object($sth)) {
			$count++;
			$html .= '<tr>';
			$html .= '<td>' . inverteData(substr($row->cadastro, 0, 10)) . '/' . $row->hora_cad . '</td>';
			$html .= '<td>' . $row->transacao . '</td>';
			$html .= '<td>' . ts_decodifica($row->nome) . '</td>';
			$html .= '<td>' . $row->hora_triagem . '</td>';
			$html .= '<td>' . $row->hora_destino . '</td>';
			if ($row->destino_paciente == '01') {
				$html .= '<td>ALTA</td>';
			} elseif ($row->destino_paciente == '02') {
				$html .= '<td>ALTA / ENCAM. AMBUL.</td>';
			} elseif ($row->destino_paciente == '07') {
				$html .= '<td>' . utf8_decode('EM OBSERVAÇÃO / MEDICAÇÃO') . '</td>';
			} elseif ($row->destino_paciente == '10') {
				$html .= '<td>EXAMES / REAVALIACAO</td>';
			} elseif ($row->destino_paciente == '03') {
				$html .= '<td>PERMANÊCIA.</td>';
			} elseif ($row->destino_paciente == '04') {
				$html .= '<td>TRANSF. OUTRA UPA</td>';
			} elseif ($row->destino_paciente == '05') {
				$html .= '<td>TRANSF. INTERN. HOSPITALAR</td>';
			} elseif ($row->destino_paciente == '06') {
				$html .= '<td>ÓBITO</td>';
			} elseif ($row->destino_paciente == '09') {
				$html .= '<td>NAO RESPONDEU CHAMADO</td>';
			} elseif ($row->destino_paciente == '11') {
				$html .= '<td>ALTA EVASÃO</td>';
			} elseif ($row->destino_paciente == '12') {
				$html .= '<td>ALTA PEDIDO</td>';
			} elseif ($row->destino_paciente == '14') {
				$html .= '<td>ALTA / POLICIA</td>';
			} elseif ($row->destino_paciente == '15') {
				$html .= '<td>ALTA / PENITENCIÁRIA</td>';
			} elseif ($row->destino_paciente == '16') {
				$html .= '<td>ALTA / PÓS MEDICAMENTO</td>';
			} elseif ($row->destino_paciente == '20') {
				$html .= '<td>ALTA VIA SISTEMA</td>';
			} elseif ($row->destino_paciente == '21') {
				$html .= '<td>TRANSFERENCIA</td>';
			}
			if ($row->destino_paciente and ($row->destino_paciente != '07' and $row->destino_paciente != '10' and $row->destino_paciente != '03')) {
				$html .= '<td>' . inverteData(substr($row->data_paciente, 0, 10)) . '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '<tr>';
		$html .= '<td colspan="5" align=\'left\'>Quantidade de Pacientes</td>';
		$html .= '<td colspan="2" align=\'right\'>' . $count . '</td>';
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
	<title>FUNEPU | Atendimentos Sintomaticos</title>
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
														» </p>ATENDIMENTOS SINTOMATICOS
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
												<li class="active">Atendimentos Sintomaticos</li>
											</ol>
										</div>
									</div>
								</div>
							</div>
							<div class="card-content">
								<div class="card-body">
									<form action="#" method="post">
										<div class="row">
											<div class="col-md-3 text-center">
												<label class="control-label" for="inputBasicFirstName">Data
													Ínicial</label>
												<input type="date" class="form-control text-center" name="start"
													id="start"
													value="<?php echo $_POST['start']; ?>" />
											</div>
											<div class="col-md-3 text-center">
												<label class="control-label" for="inputBasicFirstName">Data
													Final</label>
												<input type="date" class="form-control text-center" name="end"
													value="<?php echo $_POST['end']; ?>">
											</div>
											<div class="col-md-3">
												<button class="btn btn-primary" type="submit"
													name="pesquisar">Pesquisar</button>
											</div>
										</div>
										<div class="row mt-4" align="center">
											<div class="col-md-12">
												<button class="btn btn-success" type="submit" name="excel">Gerar
													Excel</button>
											</div>
										</div>
									</form>
									<div class="row mt-4">
										<div class="col-12">
											<table id="data_table" class="table table-striped">
												<thead>
													<tr>
														<th>Data/Hora</th>
														<th>Atendimento</th>
														<th>Nome</th>
														<th>Triagem</th>
														<th>Atendimento</th>
														<th>Data Destino</th>
														<th>Destino</th>
													</tr>
												</thead>
												<tbody>
													<?php
		include 'conexao.php';
		$stmt = "select a.transacao,d.nome as nomemed, case when z.destino_encaminhamento::varchar is null then a.destino_paciente else z.destino_encaminhamento::varchar end as destino_paciente, case when z.destino_encaminhamento::varchar is null then a.dat_cad else z.data end as data_paciente, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.dat_cad as cadastro, 	c.nome, k.origem, a.tipo,a.hora_destino,
        CASE prioridade WHEN 'VERMELHO' THEN '0' WHEN 'LARANJA' THEN '1' WHEN 'AMARELO' THEN '2' WHEN 'VERDE' THEN '3'  WHEN 'AZUL' THEN '4' ELSE '5'
        END as ORDEM, a.coronavirus from atendimentos a 
        left join pessoas c on a.paciente_id=c.pessoa_id
        left join pessoas d on a.med_atendimento=d.username 
        left join tipo_origem k on cast(k.tipo_id as varchar)=a.tipo 
        left join destino_paciente z on a.transacao = z.atendimento_id";
		if ($where != '') {
			$stmt = $stmt . ' where ' . $where;
		} else {
			$stmt = $stmt . " where dat_cad='" . date('Y-m-d') . "'";
		}
		$stmt = $stmt . ' and coronavirus <> 0';
		$stmt = $stmt . ' order by a.dat_cad desc,a.hora_cad desc ';
		$sth = pg_query($stmt) or die($stmt);
		while ($row = pg_fetch_object($sth)) { ?>
													<tr>
														<td><?= inverteData(substr($row->cadastro, 0, 10)) . '<br>' . $row->hora_cad; ?>
														</td>
														<td><?= $row->transacao; ?>
														</td>
														<td><?= ts_decodifica($row->nome); ?>
														</td>
														<td><?= $row->hora_triagem; ?>
														</td>
														<td><?= $row->hora_destino; ?>
														</td>
														<td>
															<?php
														if ($row->destino_paciente == '01') {
															echo 'ALTA';
														} elseif ($row->destino_paciente == '02') {
															echo 'ALTA / ENCAM. AMBUL.';
														} elseif ($row->destino_paciente == '07') {
															echo 'EM OBSERVAÇÃO / MEDICAÇÃO';
														} elseif ($row->destino_paciente == '10') {
															echo 'EXAMES / REAVALIACAO';
														} elseif ($row->destino_paciente == '03') {
															echo 'PERMANÊCIA.';
														} elseif ($row->destino_paciente == '04') {
															echo 'TRANSF. OUTRA UPA';
														} elseif ($row->destino_paciente == '05') {
															echo 'TRANSF. INTERN. HOSPITALAR';
														} elseif ($row->destino_paciente == '06') {
															echo 'ÓBITO';
														} elseif ($row->destino_paciente == '09') {
															echo 'NAO RESPONDEU CHAMADO';
														} elseif ($row->destino_paciente == '11') {
															echo 'ALTA EVASÃO';
														} elseif ($row->destino_paciente == '12') {
															echo 'ALTA PEDIDO';
														} elseif ($row->destino_paciente == '14') {
															echo 'ALTA / POLICIA';
														} elseif ($row->destino_paciente == '15') {
															echo 'ALTA / PENITENCIÁRIA';
														} elseif ($row->destino_paciente == '16') {
															echo 'ALTA / PÓS MEDICAMENTO';
														} elseif ($row->destino_paciente == '20') {
															echo 'ALTA VIA SISTEMA';
														} elseif ($row->destino_paciente == '21') {
															echo 'TRANSFERENCIA';
														}
														?>
														</td>
														<td><?php
													if ($row->destino_paciente and ($row->destino_paciente != '07' and $row->destino_paciente != '10' and $row->destino_paciente != '03')) {
														echo inverteData(substr($row->data_paciente, 0, 10));
													}
													?>
														</td>
													</tr>
													<?php }
												?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
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
</body>

</html>