<?php
error_reporting(0);
include 'verifica.php';
include 'funcoes.php';
include 'conexao.php';
require 'tsul_ssl.php';

date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$id = $_GET['id'];
	include 'conexao.php';
	$stmt = 'select b.transacao, b.idade, b.dat_cad,  b.status, b.cad_user, a.exame_id, a.observacoes, a.exame_nro, a.digitador_hora,a.resultado, a.digitador_data, a.digitador, a.situacao,
			 a.med_analise, a.data_analise, a.hora_analise, c.nome, c.dt_nasc, c.imagem, c.sexo, d.descricao, e.nome as solicitante, e.celular as sol_celular, e.email as sol_email, laudo_padrao,
			 a.med_confere, a.data_confere, a.hora_confere, a.hora_impressao, a.data_impressao, a.usuario_impressao from itenspedidos a left join
			 pedidos   b  on a.transacao   =b.transacao left join pessoas c  on b.paciente_id =c.pessoa_id left join procedimentos d
			 on a.exame_id=d.procedimento_id left join solicitantes e on b.solicitante_id=e.solicitante_id where a.exame_nro=' . $id;
	$sth = pg_query($stmt) or die($stmt);
	$row = pg_fetch_object($sth);
	$transacao = $row->transacao;
	$dat_cad = inverteData(substr($row->dat_cad, 0, 10));
	$status = $row->status;
	$cad_user = $row->cad_user;
	$exame_id = $row->exame_id;
	$exame_nro = $row->exame_nro;
	$exame_desc = $row->descricao;
	$nome = ts_decodifica($row->nome);
	$imagem = $row->imagem;
	$dt_nasc = $row->dt_nasc;
	$sexo = $row->sexo;
	$descricao = $row->descricao;
	$solicitante = $row->solicitante;
	$sol_email = $row->sol_email;
	$sol_celular = $row->sol_celular;
	$resultado = $row->resultado;
	if ($resultado == '') {
		$laudo = $row->laudo_padrao;
	} else {
		$laudo = $resultado;
	}
	$observacoes = $row->observacoes;
	$digitador = $row->digitador;
	$digitador_data = $row->digitador_data;
	$digitador_hora = $row->digitador_hora;
	$med_analise = $row->med_analise;
	$data_analise = $row->data_analise;
	$hora_analise = $row->hora_analise;
	$med_confere = $row->med_confere;
	$data_confere = $row->data_confere;
	$hora_confere = $row->hora_confere;
	$usuario_impressao = $row->usuario_impressao;
	$data_impressao = $row->data_impressao;
	$hora_impressao = $row->hora_impressao;
	$situacao = $row->situacao;
	if ($med_analise != '') {
		include 'conexao.php';
		$stmt = "select nome from pessoas where username = '$med_analise'";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);
		$medico_analise = ts_decodifica($row->nome);
	}
	if ($med_confere != '') {
		include 'conexao.php';
		$stmt = "select nome from pessoas where username = '$med_confere'";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);
		$medico_confere = ts_decodifica($row->nome);
	}
	include 'conexaopacs.php';
	$sql = "select study_iuid, study_datetime from study where cast(accession_no as integer)=$id";
	$sth = pg_query($sql) or die($sql);
	$row = pg_fetch_object($sth);
	$study = $row->study_iuid;
	$studydata = $row->study_datetime;
	$ano = substr($row->study_datetime, 0, 4);
	$mes = substr($row->study_datetime, 5, 2);
	$dia = substr($row->study_datetime, 8, 2);
	$hora = substr($row->study_datetime, 11, 2);
	$diretorio = $ano . '/' . intval($mes) . '/' . intval($dia) . '/' . intval($hora);
	$enviado = 'N';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$exame_nro = $_POST['exame_nro'];
	$imagens = $_POST['imagem'];
	$study = $_POST['study'];
	$tipo = $_POST['tipo_laudo'];
	$data = date('Y-m-d');
	$hora = date('H:i');
	$arquivos = '';
	foreach ($imagens as $numeros) {
		if ($arquivos == '') {
			$arquivos = $numeros;
		} else {
			$arquivos = $arquivos . '-' . $numeros;
		}
	}
	$enviado = 'S';
	if ($tipo == '01') {
		header("location: relimagens3X2.php?id=$exame_nro&study=$study&imagens=$arquivos");
	}
	if ($tipo == '02') {
		header("location: relimagens2X2.php?id=$exame_nro&study=$study&imagens=$arquivos");
	}
	if ($tipo == '03') {
		header("location: relimagens3X3X4.php?id=$exame_nro&study=$study&imagens=$arquivos");
	}
	if ($tipo == '04') {
		header("location: relimagens4X3.php?id=$exame_nro&study=$study&imagens=$arquivos");
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
    <title>FUNEPU | Pagina Padrao</title>
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

    .format-pesquisa-input,
    .format-pesquisa-input label,
    .format-pesquisa-input input {
        width: 100% !important;
    }

    .frase-add-a {
        font-size: 2em;
        text-decoration: none !important;
        display: block;
    }

    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
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
                                                        » </p>Página Padrão
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
                                                <li><a href="#">Atendimentos</a></li>
                                                <li class="active">Atendimentos Cadastrados</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-xs-12 masonry-item">
                                            <div class="widget">
                                                <div class="widget-header white bg-cyan-600 padding-30 clearfix">
                                                    <a class="avatar avatar-100 pull-left margin-right-20"
                                                        href="javascript:void(0)"> <img src="<?php if ($imagem != '') {
	echo 'imagens/clientes/' . $imagem;
} else {
	echo 'imagens/user_man.png';
} ?>" alt="">
                                                    </a>
                                                    <div class="pull-left">
                                                        <div class="font-size-20 margin-bottom-15"><?php echo $nome; ?>
                                                        </div>
                                                        <p class="margin-bottom-5 text-nowrap">
                                                            <i class="icon wb-list margin-right-10"
                                                                aria-hidden="true"></i>
                                                            <span class="text-break"><?php echo $exame_desc; ?></span>

                                                        </p>
                                                        <p class="margin-bottom-5 text-nowrap">
                                                            <i class="icon wb-user margin-right-10"
                                                                aria-hidden="true"></i>
                                                            <span class="text-break"><?php echo $solicitante . '-' . $sol_celular . '-' . $sol_email; ?></span>
                                                        </p>
                                                        <p class="margin-bottom-5 text-nowrap">
                                                            <i class="icon wb-calendar margin-right-10"
                                                                aria-hidden="true"></i>
                                                            <span class="text-break"><?php echo $dat_cad; ?></span>
                                                        </p>
                                                        <p class="margin-bottom-5 text-nowrap">
                                                            <i class="icon wb-check margin-right-10"
                                                                aria-hidden="true"></i>
                                                            <span class="text-break"><?php echo $situacao; ?></span>
                                                        </p>
                                                        <p class="margin-bottom-5 text-nowrap">
                                                            <i class="icon wb-check margin-right-10"
                                                                aria-hidden="true"></i>
                                                            <span class="text-break"><?php echo $study . ' ' . $diretorio ?></span>
                                                        </p>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div id="laudo-div" class="col-xlg-7 col-md-12">
                                            <!-- Example Panel With Heading -->

                                            <div class="panel panel-bordered">
                                                <form name="laudos" method="post" class="form-inline">
                                                    <div class="panel-heading">
                                                        <h3 class="panel-title">Laudo</h3>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="checkbox-custom checkbox-primary col-md-2">
                                                                <input type="checkbox" name="imagemtodos" id="idAll"
                                                                    value="todos"><label>Marcar Todos</label>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <label>Formato</label>
                                                                <select name="tipo_laudo" class="form-control"
                                                                    id="tipo_laudo">
                                                                    <option value="01">3X2</option>
                                                                    <option value="02">2X2</option>
                                                                    <option value="03">3X5</option>
                                                                    <option value="04">4X3</option>
                                                                </select>
                                                            </div>

                                                            <div>
                                                                <input type="hidden" name="exame_nro" id="exame_nro"
                                                                    value="<?php echo $exame_nro; ?>">
                                                                <input type="hidden" name="study" id="study"
                                                                    value="<?php echo $study; ?>">
                                                                <?php
																if ($enviado != 'S' && $study != '') {
																	$output = shell_exec("grep -R -l '$study' /pacs/dcm4chee-2.18.3-psql/server/default/archive/$diretorio");
																	$oparray = preg_split("#[\r\n]+#", $output);

																	$output = shell_exec(" mkdir /var/www/html/dcenter/html/dicom_temp/$study");

																	$x = 0;
																	foreach ($oparray as $dicom) {
																		$x = $x + 1;
																		shell_exec(" sh /pacs/dcm4che-3.3.8/bin/dcm2jpg $dicom /var/www/html/dcenter/html/dicom_temp/$study/dicom$x.jpg");
																	}
																	echo '<table>';
																	echo '<tr>';
																	$x = 1;
																	$y = 1;
																	foreach (glob("/var/www/html/dcenter/html/dicom_temp/$study/*.jpg") as $filename) {
																		echo "<td><div class=\"checkbox-custom checkbox-primary\"><input type='checkbox' name='imagem[]' value='" . $y . "' checked><label></label></div><img src='" . substr($filename, 27, 80) . "' width='300' height='300'></td>";
																		$y = $y + 1;
																		$x = $x + 1;
																		if ($x > 3) {
																			echo '</tr><tr>';
																			$x = 1;
																		}
																	}
																	echo '</tr>';
																	echo '</table>';
																}
																?>

                                                            </div>
                                                            <div class="panel-footer">
                                                                <div align="center">
                                                                    <button type="submit" name='gravar' value='Imprimir'
                                                                        class="btn btn-success">Imprimir</button>
                                                                    <button type="submit" name='xcancela'
                                                                        value='xcancelar'
                                                                        class="btn btn-danger">Cancelar</button>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </form>
                                            </div>

                                        </div>



                                    </div>
                                    <!-- End Example Contextual -->

                                    <div class="row">
                                        <div id="laudo-bott" class="col-xlg-7 col-md-12">
                                            <div class="col-md-12 col-xs-12 masonry-item">
                                                <div class="widget">
                                                    <div class="widget-header white bg-blue-600 padding-30 clearfix">

                                                        <?php if ($digitador != '') {
																	echo '<p class="margin-bottom-5 text-nowrap">';
																	echo '<i class="icon wb-check margin-right-10" aria-hidden="true"></i>';
																	echo '<span class="text-break">Digitado por ' . $digitador . ' - ' . inverteData($digitador_data) . ' as ' . $digitador_hora . '</span>';
																	echo '</p>';
																}
														?>
                                                        <?php if ($medico_analise != '') {
															echo '<p class="margin-bottom-5 text-nowrap">';
															echo '<i class="icon wb-check margin-right-10" aria-hidden="true"></i>';
															echo "<span class=\"text-break\"> Laudado  por $medico_analise  - " . inverteData($data_analise) . " as $hora_analise></span></p>";
														}
														?>
                                                        <?php if ($medico_confere != '') {
															echo '<p class="margin-bottom-5 text-nowrap">';
															echo '<i class="icon wb-check margin-right-10" aria-hidden="true"></i>';
															echo "<span class=\"text-break\"> Conferido  por $medico_confere  - " . inverteData($data_confere) . " as $hora_confere></span></p>";
														}
														?>
                                                        <?php if ($usuario_impressao != '') {
															echo '<p class="margin-bottom-5 text-nowrap">';
															echo '<i class="icon wb-print margin-right-10" aria-hidden="true"></i>';
															echo "<span class=\"text-break\"> Impresso  por $usuario_impressao  - " . inverteData($data_impressao) . " as $hora_impressao></span></p>";
														}
														?>
                                                        <?php if ($usuario_entrega != '') {
															echo '<p class="margin-bottom-5 text-nowrap">';
															echo '<i class="icon wb-envelope margin-right-10" aria-hidden="true"></i>';
															echo "<span class=\"text-break\"> Entregue  por $usuario_entrega  - " . inverteData($data_entrega) . " as $hora_entrega></span></p>";
														}
														?>

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
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript">
        </script>
        <script src="app-assets/js/scripts.js" type="text/javascript"></script>
        <script src="app-assets/js/popover.js" type="text/javascript"></script>
        <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
        <script defer src="/your-path-to-fontawesome/js/all.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</body>

</html>