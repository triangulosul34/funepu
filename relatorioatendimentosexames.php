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

include 'verifica.php';
include 'Config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['gerarexcel'])) {
		$start = $_POST['start'];
		$end = $_POST['end'];
		$modalidade = $_POST['modalidade'];

		$where = '';
		if ($start == '') {
			$start = date('d/m/Y');
		}
		if ($end == '') {
			$end = date('d/m/Y');
		}

		$arquivo = 'Relatorio de Exames.xls';
		$html = '';
		$html .= '<table style="font-size:12px" border="1">';
		$html .= '<tr>';
		$html .= '<td colspan="3" align=\'center\'>UPA ' . UNIDADE_CONFIG . ' - RELATORIO DE EXAMES -- ' . $start . ' a ' . $end . '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<tr align=\'center\'>';
		$html .= '<td><b>Data</b></td>';
		$html .= '<td><b>Nome</b></td>';
		$html .= '<td><b>Descricao</b></td>';
		$html .= '</tr>';
		if ($modalidade == 5) {
			if (isset($_POST['coronavirus'])) {
				$where = ' and c.exame_id in (962, 1145,960)';
			}
			include 'conexao_laboratorio.php';
			$sql = "select TO_CHAR(a.data, 'DD/MM/YYYY') as data, e.nome, d.descricao from pedidos a inner join pedido_guia b on a.pedido_id = b.pedido_id inner join pedido_item c on b.pedido_guia_id = c.pedido_guia_id inner join procedimentos d on c.exame_id = d.procedimentos_id inner join pessoas e on a.pessoa_id = e.pessoa_id
			where a.data between '" . inverteData($start) . "' and '" . inverteData($end) . "' $where and b.origem = '" . ORIGEM_CONFIG . "' and c.situacao = 'Liberado' order by 3,2,1";
			$sthRel = pg_query($sql) or die($sql);
			while ($row = pg_fetch_object($sthRel)) {
				$html .= '<tr>';
				$html .= '<td>' . $row->data . '</td>';
				$html .= '<td>' . $row->nome . '</td>';
				$html .= '<td>' . $row->descricao . '</td>';
				$html .= '</tr>';
			}
		} else {
			if ($modalidade != '') {
				if ($modalidade == '3' or $modalidade == '4') {
					$where = ' and modalidade_id = ' . $modalidade;
				} else {
					$where = " and (a.situacao = 'Realizado' or a.situacao = 'Finalizado') and modalidade_id = " . $modalidade;
				}
			} else {
				$where = " and case when modalidade_id = 1 or modalidade_id = 2 then (a.situacao = 'Realizado' or a.situacao = 'Finalizado') else a.situacao is not null end";
			}

			include 'conexao.php';
			$stmtRel = "select TO_DATE(b.dat_cad::text, 'YYYY-MM-DD HH24:MI:SS') as data, d.nome, c.descricao  from itenspedidos a left join pedidos b on a.transacao=b.transacao
            left join pessoas d on b.paciente_id = d.pessoa_id	left join procedimentos c on a.exame_id=c.procedimento_id where b.dat_cad between '" . inverteData($start) . "' and '" . inverteData($end) . "' and c.exames_laboratoriais is null and d.nome <> 'LUCILTON VIEIRA FARIA' $where order by 3,2,1";
			$sthRel = pg_query($stmtRel) or die($stmtRel);
			while ($row = pg_fetch_object($sthRel)) {
				$html .= '<tr>';
				$html .= '<td>' . $row->data . '</td>';
				$html .= '<td>' . ts_decodifica($row->nome) . '</td>';
				$html .= '<td>' . $row->descricao . '</td>';
				$html .= '</tr>';
			}
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
    <title>FUNEPU | Relatorio de Exames</title>
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
                                                        » </p>Relatorio Exames Paciente
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
                                                <li><a href="active">Relatorio Exames</a></li>
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
                                                <label>Modalidade</label>
                                                <select name="modalidade" id="modalidade" class="form-control square">
                                                    <option></option>
                                                    <option value="" <?php if ($_POST['modalidade'] == '') {
	echo ' selected ';
} ?>>Todos
                                                    </option>
                                                    <?php
													include 'conexao.php';
													$stmt = 'select p.modalidade_id,m.descricao as modalidade
														from procedimentos p
                                                        left join modalidades m on m.modalidade_id = p.modalidade_id
                                                        where p.modalidade_id in (1,2,3,4,5)
														group by 1,2
														order by 1';
													$sth = pg_query($stmt) or die($stmt);
													while ($row = pg_fetch_object($sth)) {
														echo '<option value="' . $row->modalidade_id . '"';
														if ($row->modalidade_id == $_POST['modalidade']) {
															echo ' selected ';
														}
														echo '>' . $row->modalidade . '</option>';
													} ?>
                                                </select>
                                            </div>
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
                                            <div class="col-3">
                                                <label class="control-label">Ação</label><br>
                                                <button type="submit" name="gerarexcel" class="btn btn-success"
                                                    style="width:100%">Gerar Excel</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="custom-control custom-checkbox ">
                                                    <input type="checkbox" class="custom-control-input"
                                                        name="coronavirus" id="coronavirus" value=''>
                                                    <label class="custom-control-label" style="font-size: 10pt"
                                                        for="coronavirus">Exames covid</label>
                                                </div>
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