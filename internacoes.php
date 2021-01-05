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
	$nome = ts_codifica($_POST['nome']);
	$start = $_POST['start'];
	$end = $_POST['end'];

	$where = '';

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
    <title>FUNEPU | Internacoes</title>
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

<script>
    function inter(a) {
        document.getElementById("obs_modal").innerHTML = a;
    }
</script>

<body class="pace-done" cz-shortcut-listen="true">
    <div class="modal fade" id="ExemploModalCentralizado" tabindex="-1" role="dialog"
        aria-labelledby="TituloModalCentralizado" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="TituloModalCentralizado">Título do modal</h5> -->
                    <div class="modal-body">

                        <label for="message-text" class="col-form-label">Observações:</label>
                        <!-- <textarea class="form-control" name="obs_modal" id="obs_modal" style="resize: none" rows="10" cols="60" form="usrform" static><?php echo $obs_modal; ?></textarea>
                        -->
                        <textarea name="obs_modal" id="obs_modal" cla3ss="form-control" rows="15" style="resize: none"
                            rows="10" cols="52" form="usrform" static
                            readonly><?php echo $obs_modal; ?></textarea>

                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                                        » </p>Internações
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
                                                <li class="active">Internações</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" method="POST">
                                        <div class="row">
                                            <div class="col-4"><label>Paciente</label>
                                                <input type="text" class="form-control square" id="inputBasicFirstName"
                                                    name="nome" placeholder="Parte do Nome" autocomplete="off"
                                                    value="<?php echo ts_decodifica($nome); ?>"
                                                    onkeyup="maiuscula(this)" />
                                            </div>
                                            <div class="col col-lg-3">
                                                <label class="control-label" for="inputBasicFirstName">Data
                                                    Ínicial</label>
                                                <input type="date" class="form-control text-center" name="start"
                                                    id="start" OnKeyPress="formatar('##/##/####', this)"
                                                    value="<?php echo $start; ?>" />
                                            </div>
                                            <div class="col col-lg-3 text-center">
                                                <label class="control-label" for="inputBasicFirstName">Data
                                                    Final</label>
                                                <input type="date" class="form-control text-center" name="end"
                                                    OnKeyPress="formatar('##/##/####', this)" /
                                                    value="<?php echo $end; ?>">
                                            </div>
                                            <div class="col-2"><label>Ação</label><button type="submit" name="pesquisa"
                                                    value="semana"
                                                    class="btn btn-raised btn-primary square btn-min-width mr-1 mb-1">Pesquisar</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row mt-5">
                                        <div class="col-12">
                                            <table id="data_table" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Paciente</th>
                                                        <th>Origem</th>
                                                        <th>Chegada</th>
                                                        <th>Triagem</th>
                                                        <th>Atendimento</th>
                                                        <th>Encaminhamento</th>
                                                        <th>Ação</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>Paciente</th>
                                                        <th>Origem</th>
                                                        <th>Chegada</th>
                                                        <th>Triagem</th>
                                                        <th>Atendimento</th>
                                                        <th>Encaminhamento</th>
                                                        <th>Ação</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
													include 'conexao.php';
													$stmt = "select a.transacao,d.nome as nomemed, a.paciente_id, a.obs_modal, a.status, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, 	c.nome, k.origem, a.tipo,a.hora_destino,
							CASE prioridade
								WHEN 'VERMELHO' THEN '0'
								WHEN 'LARANJA' THEN '1'
								WHEN 'AMARELO' THEN '2'
								WHEN 'VERDE' THEN '3'
								WHEN 'AZUL' THEN '4'
								ELSE '5'
							END as ORDEM,
							CASE destino_paciente
								WHEN '05' THEN 'TRANSF. INTERN. HOSPITALAR'
								WHEN '03' THEN 'PERMANÊNCIA.'
								WHEN '07' THEN 'EM OBSERVAÇÃO / MEDICAÇÃO'
								WHEN '10' THEN 'EXAMES / REAVALIACAO'
							END as destino,
							CASE p.destino_encaminhamento
								WHEN '01' THEN 'ALTA'
								WHEN '02' THEN 'ALTA / ENCAM. AMBUL.'
								WHEN '11' THEN 'ALTA EVASÃO'
								WHEN '12' THEN 'ALTA PEDIDO'
								WHEN '15' THEN 'ALTA / PENITENCIÁRIA'
								WHEN '14' THEN 'ALTA / PM'
								WHEN '04' THEN 'TRANSF. OUTRA UPA'
								WHEN '05' THEN 'TRANSFERENCIA HOSPITALAR'
								WHEN '13' THEN 'TRANSFERENCIA INTERNA'
								WHEN '06' THEN 'ÓBITO'
							END as destinoalta, a.coronavirus
							from atendimentos a
							left join pessoas c on a.paciente_id=c.pessoa_id
							left join pessoas d on a.med_atendimento=d.username
							left join destino_paciente p on p.atendimento_id = a.transacao
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) ";

													if ($where != '') {
														$stmt = $stmt . ' where ' . $where . ' and obs_modal is not null';
													} else {
														$stmt = $stmt . " where a.dat_cad='" . date('Y-m-d') . "' and obs_modal is not null";
													}

													$stmt = $stmt . " and (a.destino_paciente = '03' or a.destino_paciente = '05' or a.destino_paciente = '07' or a.destino_paciente = '10') order by a.dat_cad desc,a.hora_cad desc ";
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
														} ?>
                                                    <tr <?= $classe ?>>
                                                        <td><?= ts_decodifica($row->nome); ?>
                                                        </td>
                                                        <td><?= $row->origem; ?>
                                                        </td>
                                                        <td><?= $row->hora_cad; ?>
                                                        </td>
                                                        <td><?= $row->hora_triagem; ?>
                                                        </td>
                                                        <td><?= $row->hora_destino; ?>
                                                        </td>
                                                        <td><?= $row->destino; ?>
                                                        </td>
                                                        <td><button type="button" id="receituario" class="btn btn-light"
                                                                href="#"
                                                                data-id="<?= $_GET['id']; ?>"
                                                                data-toggle="modal"
                                                                data-target="#ExemploModalCentralizado"
                                                                value='Receituário'
                                                                onclick="inter(`<?= $row->obs_modal; ?>`)">
                                                                <i class="fas fa-procedures"></i>
                                                            </button></td>
                                                    </tr>
                                                    <?php
													} ?>
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