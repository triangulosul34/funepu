<?php
error_reporting(0);
include('verifica.php');
include('funcoes.php');
include('conexao.php');
$edita  = false;
$editor = false;
date_default_timezone_set('America/Sao_Paulo');

function busca_comments($exame_nro)
{

	return "";
}
function busca_tecnico($exame_nro)
{

	return "";
}


$query = 'SELECT atalho, frase FROM frases';
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

$arrWords = array();
$arrWordsSub = array();

while ($row = pg_fetch_row($result)) {
	array_push($arrWords, $row[0]);
	array_push($arrWordsSub, $row[1]);
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

	$id = $_GET['id'];
	include('conexao.php');
	$stmt = "select b.transacao, b.idade, b.dat_cad,  b.hora_cad, b.status, b.cad_user, a.pessoa_id, a.exame_id, a.observacoes, a.exame_nro, a.digitador_hora,a.resultado, a.digitador_data, a.digitador, a.situacao,
			 a.med_analise, a.data_analise, a.hora_analise, c.nome, c.dt_nasc, c.imagem, c.sexo, d.modalidade_id, d.descricao, e.nome as solicitante, e.celular as sol_celular, e.email as sol_email, laudo_padrao, a.laudo_sel,
			 a.contraste, a.contraste_ml, a.med_confere, a.data_confere, a.hora_confere, a.hora_impressao, a.data_impressao, a.usuario_impressao from itenspedidos a left join
			 pedidos   b  on a.transacao   =b.transacao left join pessoas c  on b.paciente_id =c.pessoa_id left join procedimentos d
			 on a.exame_id=d.procedimento_id left join solicitantes e on b.solicitante_id=e.solicitante_id where a.exame_nro=" . $id;
	$sth = pg_query($stmt) or die($stmt);
	$row = pg_fetch_object($sth);
	$transacao = $row->transacao;
	$dat_cad = inverteData(substr($row->dat_cad, 0, 10));
	$status = $row->status;
	$cad_user = $row->cad_user;
	$hora_cad = $row->hora_cad;
	$exame_id = $row->exame_id;
	$exame_nro = $row->exame_nro;
	$exame_desc = $row->descricao;
	$nome = $row->nome;
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
	$med_analise =  $row->med_analise;
	$data_analise = $row->data_analise;
	$hora_analise = $row->hora_analise;
	$med_confere =  $row->med_confere;
	$data_confere = $row->data_confere;
	$hora_confere = $row->hora_confere;
	$usuario_impressao =  $row->usuario_impressao;
	$data_impressao = $row->data_impressao;
	$hora_impressao = $row->hora_impressao;
	$situacao = $row->situacao;
	$pessoa_id = $row->pessoa_id;
	$contraste = $row->contraste;
	$contraste_ml = $row->contraste_ml;
	$laudo_sel    = $row->laudo_sel;
	$modalidade   = $row->modalidade_id;
	$idade        = $row->idade;
	if ($sexo == 'F') {
		$sexod = 'Feminino';
	} else {
		$sexod = 'Masculino';
	}
	if ($observacoes == "" and ($modalidade == 3 or $modalidade == 9)) {
		$observacoes = 'Tecnico:' . busca_tecnico($exame_nro) . PHP_EOL . busca_comments($exame_nro);
	}
	if ($med_analise != '') {
		include('conexao.php');
		$stmt = "select nome from pessoas where username = '$med_analise'";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);
		$medico_analise = $row->nome;
		if ($usuario != $med_analise) {
			$editor = false;
		} else {
			$editor = true;
		}
	}
	if ($med_confere != '') {
		include('conexao.php');
		$stmt = "select nome from pessoas where username = '$med_confere'";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);
		$medico_confere = $row->nome;
		if ($usuario != $med_confere) {
			$editor = false;
		} else {
			$editor = true;
		}
		if ($usuario == $med_analise) {
			$editor = true;
		}
	} else {
		if ($tipopessoa == 'Medico Laudador') {
			$editor = true;
		}
	}

	include('conexao.php');
	$stmtLab = "select perfil from pessoas where username = '$usuario'";
	$sthLab = pg_query($stmtLab) or die($stmtLab);
	$rowLab = pg_fetch_object($sthLab);
	$tipo_perfil = $rowLab->perfil;
	if ($tipo_perfil == '11') {
		$editor = true;
	}
	if ($situacao == 'Cadastrado') {
		$editor = true;
	}
	if ($situacao == 'Editado') {
		$editor = true;
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$exame_nro  = $_POST['exame_nro'];
	$digitador  = $_POST['digitador'];
	$med_analise = $_POST['laudador'];
	$situacao   = $_POST['situacao'];
	$med_confere = $_POST['conferente'];
	$contraste  = $_POST['contraste'];
	$contraste_ml = $_POST['contraste_ml'];
	$laudo      = $_POST['content'];
	$data       = date('Y-m-d');
	$hora       = date('H:i');
	$observacoes = $_POST['coments'];
	if ($contraste == "") {
		$contraste = 'N';
	}
	if ($contraste_ml == "") {
		$contraste_ml = '0';
	}
	if (isset($_POST["gravar"])) {

		include('conexao.php');
		$stmt = "update itenspedidos set observacoes='$observacoes', resultado = '" . pg_escape_string($laudo) . "' where exame_nro=" . $exame_nro;
		$sth = pg_query($stmt) or die($stmt);


		if ($tipopessoa == 'Administrativo') {
			include('conexao.php');
			if ($situacao == 'Cadastrado' or $situacao == 'Editado') {
				$stmt = "update itenspedidos set resultado = '$laudo', digitador='$usuario', digitador_data='$data', digitador_hora='$hora', situacao='Editado', contraste='$contraste', contraste_ml='$contraste_ml', observacoes='$observacoes' where exame_nro=" . $exame_nro;
				$sth = pg_query($stmt) or die($stmt);
				include('conexao.php');
				$datalog  = date('Y-m-d H:i:s');
				$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Editou', '$usuario', '$datalog' )";
				$sth   = pg_query($stmtx) or die($stmtx);
			}
		}

		if ($tipopessoa == 'Medico Laudador') {
			if ($med_analise == "" or $med_analise == $usuario) {

				include('conexao.php');
				$stmt = "update itenspedidos set resultado = '" . pg_escape_string($laudo) . "', med_analise='$usuario', data_analise='$data', hora_analise='$hora',  observacoes='$observacoes' where exame_nro=" . $exame_nro;
				$sth = pg_query($stmt) or die($stmt);
				include('conexao.php');
				$datalog  = date('Y-m-d H:i:s');
				$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Laudou', '$usuario', '$datalog' )";
				$sth   = pg_query($stmtx) or die($stmtx);
				$med_analise = $usuario;
			}

			if ($modalidade == '6' or $modalidade == '8') {
				if (($med_confere == "" or $med_confere == $usuario) and ($med_analise != $usuario)) {
					include('conexao.php');
					$stmt = "update itenspedidos set resultado = '" . pg_escape_string($laudo) . "', med_confere='$usuario', data_confere='$data', hora_confere='$hora', observacoes='$observacoes' where exame_nro=" . $exame_nro;
					$sth = pg_query($stmt) or die($stmt);
					include('conexao.php');
					$datalog  = date('Y-m-d H:i:s');
					$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Conferiu', '$usuario', '$datalog' )";
					$sth   = pg_query($stmtx) or die($stmtx);
				}
			}
		}
	}
	if (isset($_POST["gravarlaudo"])) {

		include('conexao.php');
		$stmt = "update itenspedidos set observacoes='$observacoes', resultado = '" . pg_escape_string($laudo) . "' where exame_nro=" . $exame_nro;
		$sth = pg_query($stmt) or die($stmt);


		if ($tipopessoa == 'Administrativo') {
			include('conexao.php');
			if ($situacao == 'Cadastrado' or $situacao == 'Editado') {
				$stmt = "update itenspedidos set resultado = '$laudo', digitador='$usuario', digitador_data='$data', digitador_hora='$hora', situacao='Editado', contraste='$contraste', contraste_ml='$contraste_ml', observacoes='$observacoes' where exame_nro=" . $exame_nro;
				$sth = pg_query($stmt) or die($stmt);
				include('conexao.php');
				$datalog  = date('Y-m-d H:i:s');
				$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Editou', '$usuario', '$datalog' )";
				$sth   = pg_query($stmtx) or die($stmtx);
			}
		}

		if ($tipopessoa == 'Medico Laudador') {
			if ($med_analise == "" or $med_analise == $usuario) {

				include('conexao.php');
				$stmt = "update itenspedidos set resultado = '" . pg_escape_string($laudo) . "', med_analise='$usuario', data_analise='$data', hora_analise='$hora', situacao='Laudado',  observacoes='$observacoes' where exame_nro=" . $exame_nro;
				$sth = pg_query($stmt) or die($stmt);
				include('conexao.php');
				$datalog  = date('Y-m-d H:i:s');
				$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Laudou', '$usuario', '$datalog' )";
				$sth   = pg_query($stmtx) or die($stmtx);
				$med_analise = $usuario;
			}

			if ($modalidade == '6' or $modalidade == '8') {
				if (($med_confere == "" or $med_confere == $usuario) and ($med_analise != $usuario)) {
					include('conexao.php');
					$stmt = "update itenspedidos set resultado = '" . pg_escape_string($laudo) . "', med_confere='$usuario', data_confere='$data', hora_confere='$hora', situacao='Conferido',  observacoes='$observacoes' where exame_nro=" . $exame_nro;
					$sth = pg_query($stmt) or die($stmt);
					include('conexao.php');
					$datalog  = date('Y-m-d H:i:s');
					$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Conferiu', '$usuario', '$datalog' )";
					$sth   = pg_query($stmtx) or die($stmtx);
				}
			}
		}
	}
	if (isset($_POST["finalizar"])) {


		if ($tipopessoa == 'Medico Laudador') {

			include('conexao.php');
			$stmt = "select med_analise from itenspedidos where exame_nro=" . $exame_nro;
			$sth = pg_query($stmt) or die($stmt);
			$row = pg_fetch_object($sth);
			$med_laudador = $row->med_analise;

			if ($med_laudador == $usuario or $med_analise == '') {
				include('conexao.php');
				$stmt = "update itenspedidos set resultado = '" . pg_escape_string($laudo) . "', med_analise='$usuario', data_analise='$data', hora_analise='$hora', situacao='Finalizado',  observacoes='$observacoes' where exame_nro=" . $exame_nro;
				$sth = pg_query($stmt) or die($stmt);
			}
			if ($med_laudador != $usuario) {
				include('conexao.php');
				$stmt = "update itenspedidos set resultado = '" . pg_escape_string($laudo) . "', med_confere='$usuario', data_confere='$data', hora_confere='$hora', situacao='Finalizado',  observacoes='$observacoes' where exame_nro=" . $exame_nro;
				$sth = pg_query($stmt) or die($stmt);
			}

			include('conexao.php');
			$datalog  = date('Y-m-d H:i:s');
			$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Finalizou', '$usuario', '$datalog' )";
			$sth   = pg_query($stmtx) or die($stmtx);

			$editor = false;
		}
	}
	if (isset($_POST["imprimir"])) {
		if ($situacao != 'Env.Recepção' and $situacao != 'Entregue') {
			include('conexao.php');
			$stmt = "update itenspedidos set  data_impressao='$data', hora_impressao='$hora', usuario_impressao='$usuario', situacao='Impresso' where exame_nro=" . $exame_nro;
			$sth = pg_query($stmt) or die($stmt);
		}

		include('conexao.php');
		$datalog  = date('Y-m-d H:i:s');
		$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($exame_nro, 'Impressao', '$usuario', '$datalog' )";
		$sth   = pg_query($stmtx) or die($stmtx);

		echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('rellaudo.php?id=" . $exame_nro . "','_blank');</script>";
	}
	include('conexao.php');
	$stmt = "select b.transacao, b.idade, b.dat_cad,  b.hora_cad, b.status, b.cad_user, a.exame_id, a.observacoes, a.exame_nro, a.digitador_hora,a.resultado, a.digitador_data, a.digitador, a.situacao,
			 a.med_analise, a.data_analise, a.pessoa_id, a.hora_analise, c.nome, c.dt_nasc, c.imagem, c.sexo, d.descricao, e.nome as solicitante, e.celular as sol_celular, e.email as sol_email, laudo_padrao,
			 a.med_confere, a.data_confere, a.hora_confere, a.hora_impressao, a.data_impressao, a.usuario_impressao from itenspedidos a left join
			 pedidos   b  on a.transacao   =b.transacao left join pessoas c  on b.paciente_id =c.pessoa_id left join procedimentos d
			 on a.exame_id=d.procedimento_id left join solicitantes e on b.solicitante_id=e.solicitante_id where a.exame_nro=" . $exame_nro;
	$sth = pg_query($stmt) or die($stmt);
	$row = pg_fetch_object($sth);
	if (isset($_POST["carregar"])) {
		$laudo = $row->laudo_padrao;
	}
	$transacao = $row->transacao;
	$dat_cad = inverteData(substr($row->dat_cad, 0, 10));
	$status = $row->status;
	$cad_user = $row->cad_user;
	$dat_cad  = $row->dat_cad;
	$dtcadastro = $row->dat_cad . ' ' . $row->hora_cad;
	$hora_cad = $row->hora_cad;
	$exame_id = $row->exame_id;
	$exame_nro = $row->exame_nro;
	$exame_desc = $row->descricao;
	$nome = $row->nome;
	$imagem = $row->imagem;
	$dt_nasc = $row->dt_nasc;
	$sexo = $row->sexo;
	$descricao = $row->descricao;
	$solicitante = $row->solicitante;
	$sol_email = $row->sol_email;
	$sol_celular = $row->sol_celular;
	$observacoes = $row->observacoes;
	$data = date('Y-m-d');
	$hora = date('H:i');
	$digitador = $row->digitador;
	$digitador_data = $row->digitador_data;
	$digitador_hora = $row->digitador_hora;
	$med_analise =  $row->med_analise;
	$data_analise = $row->data_analise;
	$hora_analise = $row->hora_analise;
	$usuario_impressao =  $row->usuario_impressao;
	$data_impressao = $row->data_impressao;
	$hora_impressao = $row->hora_impressao;
	$med_confere =  $row->med_confere;
	$data_confere = $row->data_confere;
	$hora_confere = $row->hora_confere;
	$situacao = $row->situacao;
	$pessoa_id = $row->pessoa_id;
	if ($med_analise != "") {
		include('conexao.php');
		$stmt = "select nome from pessoas where username = '$med_analise'";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);
		$medico_analise = $row->nome;
		if ($usuario != $med_analise) {
			$editor = false;
		} else {
			$editor = true;
		}
	}
	if ($med_confere != "") {
		include('conexao.php');
		$stmt = "select nome from pessoas where username = '$med_confere'";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);
		$medico_confere = $row->nome;
		if ($usuario != $med_confere) {
			$editor = false;
		} else {
			$editor = true;
		}
	}
	if ($sexo == 'F') {
		$sexod = 'Feminino';
	} else {
		$sexod = 'Masculino';
	}
	if ($situacao == 'Cadastrado' or $situacao == 'Cadastrando') {
		$editor = true;
	}
	if ($situacao == 'Editado') {
		$editor = true;
	}

	include('conexao.php');
	$stmtLab = "select perfil from pessoas where username = '$usuario'";
	$sthLab = pg_query($stmtLab) or die($stmtLab);
	$rowLab = pg_fetch_object($sthLab);
	$tipo_perfil = $rowLab->perfil;
	if ($tipo_perfil == '11') {
		$editor = true;
	}
}
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="bootstrap admin template">
	<meta name="author" content="">

	<title>Funepu | Emiss�o de Laudos</title>

	<link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="assets/css/bootstrap.css">
	<link rel="stylesheet" href="assets/css/bootstrap-extend.min.css">
	<link rel="stylesheet" href="assets/css/site.min.css">

	<!-- Plugins -->
	<link rel="stylesheet" href="assets/vendor/animsition/animsition.css">
	<link rel="stylesheet" href="assets/vendor/asscrollable/asScrollable.css">
	<link rel="stylesheet" href="assets/vendor/switchery/switchery.css">
	<link rel="stylesheet" href="assets/vendor/intro-js/introjs.css">
	<link rel="stylesheet" href="assets/vendor/slidepanel/slidePanel.css">
	<link rel="stylesheet" href="assets/vendor/flag-icon-css/flag-icon.css">
	<link rel="stylesheet" href="assets/examples/css/widgets/social.css">
	<!-- Plugins For This Page -->
	<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/vendor/asscrollable/asScrollable.css">
	<link rel="stylesheet" href="../assets/vendor/webui-popover/webui-popover.css">
	<link rel="stylesheet" href="assets/examples/css/uikit/tooltip-popover.css">
	<link rel="stylesheet" href="assets/vendor/datatables-bootstrap/dataTables.bootstrap.css">
	<link rel="stylesheet" href="assets/vendor/datatables-fixedheader/dataTables.fixedHeader.css">
	<link rel="stylesheet" href="assets/vendor/datatables-responsive/dataTables.responsive.css">
	<link rel="stylesheet" href="assets/vendor/sweetalert/dist/sweetalert.css">

	<!-- Fonts -->
	<link rel="stylesheet" href="assets/fonts/web-icons/web-icons.min.css">
	<link rel="stylesheet" href="assets/fonts/brand-icons/brand-icons.min.css">
	<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
	<link rel="stylesheet" href="assets/examples/css/tables/datatable.css">
	<script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>
	<script type="text/javascript">

	</script>
	<style type="text/css">
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

	<!--[if lt IE 9]>
    <script src="assets/vendor/html5shiv/html5shiv.min.js"></script>
    <![endif]-->

	<!--[if lt IE 10]>
    <script src="assets/vendor/media-match/media.match.min.js"></script>
    <script src="assets/vendor/respond/respond.min.js"></script>
    <![endif]-->

	<!-- Scripts -->
	<script src="assets/vendor/modernizr/modernizr.js"></script>
	<script src="assets/vendor/breakpoints/breakpoints.js"></script>
	<script src="assets/vendor/tinymce/js/tinymce/tinymce.min.js"></script>
	<script>
		Breakpoints();
	</script>

	<script type="text/javascript">
		function tab(field, event) {
			if (event.which == 13 /* IE9/Firefox/Chrome/Opera/Safari */ || event.keyCode == 13 /* IE8 and earlier */ ) {
				for (i = 0; i < field.form.elements.length; i++) {
					if (field.form.elements[i].tabIndex == field.tabIndex + 1) {
						field.form.elements[i].focus();
						if (field.form.elements[i].type == "text") {
							field.form.elements[i].select();
							break;
						}
					}
				}
				return false;
			}
			return true;
		}

		function confirmacao(id) {
			swal({
					title: "Confirma a troca do laudo?",
					text: "Os dados serão perdidos!",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Sim, trocar laudo!",
					cancelButtonText: "Não, cancelar!",
					closeOnConfirm: false,
					closeOnCancel: false
				},
				function(isConfirm) {
					if (isConfirm) {
						window.location.href = "trocarlaudo.php?id=" + id;
					} else {
						swal("Cancelado", "Não ocorreu a troca do laudo", "error");
						return false;
					}
				});
		}

		function openInNewTab(url) {
			var win = window.open(url, '_blank');
			win.focus();
		}

		function carregalaudo(exame) {
			var oFrasesDiv = $("#frases-div");
			var oLaudosDiv = $("#laudos-div");
			var oVarDiv = $("#variaveis-div");
			var oLaudoDiv = $("#laudo-div");
			var oAnteriorDiv = $("#anteriores-div");

			oFrasesDiv.hide("1000");
			oVarDiv.hide("1000");
			oLaudosDiv.hide("1000");

			if (oAnteriorDiv.is(":visible")) {
				oAnteriorDiv.hide("1000");
				oLaudoDiv.removeClass("col-xlg-6").addClass("col-xlg-12");
				oLaudoDiv.removeClass("col-md-6").addClass("col-md-12");
			} else {
				oAnteriorDiv.show("1000");
				oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-6");
				oLaudoDiv.removeClass("col-md-12").addClass("col-md-6");

			}

			if (exame) {
				var url = 'buscar_laudo.php?exame=' + exame;
				$.get(url, function(dataReturn) {
					$('#summernote1').summernote('reset');
					$('#summernote1').summernote('code', dataReturn);
					$('#summernote1').summernote('disable');
				});
			}

		}
	</script>
</head>

<body>
	<!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

	<?php
	include('header2.php');
	?>

	<?php
	//include ('menulaudos.php');
	?>



	<!-- Page -->
	<div class="page animsition">
		<br> <br> <br>
		<div class="page-header">
			<ol class="breadcrumb">
				<li><a href="../index.html">Home</a></li>
				<li class="active">Basic UI</li>
			</ol>
			<h1 class="page-title">Emissor de laudo </h1>
			<div class="page-header-actions">

			</div>
		</div>
		<!-- Page Content -->
		<form name="laudos" method="post" action="#">

			<div class="page-content container-fluid">
				<div class="row">
					<div class="col-md-12" style="height: 270px;">
						<div class="widget col-md-12">
							<label class="control-label">Identificação</label>
							<div class="widget-header white bg-cyan-600 padding-30 clearfix">
								<div class="pull-left">
									<div class="font-size-15 margin-bottom-15"><?php echo $nome; ?><br> Idade:<?php echo $idade . ' -  ' . 'Sexo:' . $sexod; ?></div>
									<p class="margin-bottom-5 text-nowrap">
										<i class="icon wb-list margin-right-10" aria-hidden="true"></i>
										<span class="text-break"><?php echo $exame_desc; ?></span>

									</p>
									<p class="margin-bottom-5 text-nowrap">
										<i class="icon wb-user margin-right-10" aria-hidden="true"></i>
										<span class="text-break"><?php echo substr($solicitante, 0, 20) . '...'; ?></span>
									</p>
									<p class="margin-bottom-5 text-nowrap">
										<i class="icon wb-calendar margin-right-10" aria-hidden="true"></i>
										<span class="text-break"><?php echo $dat_cad; ?></span>
										<i class="icon wb-check margin-right-10" aria-hidden="true"></i>
										<span class="text-break"><?php echo $situacao; ?></span>

									</p>


								</div>
							</div>
						</div>


					</div>

				</div>
				<div class="row">
					<div id="laudo-div" class="col-xlg-12 col-md-12">
						<!-- Example Panel With Heading -->

						<form name="laudoexame" method="post">
							<div class="panel panel-bordered">
								<div class="panel-heading">

									<h4 class="panel-title">
										<div class="col-md-12" align="right">
											<?php

											$ip = getenv("REMOTE_ADDR");

											$studyid = '';
											if ($studyid != '') {
												if (substr($ip, 0, 3) == "192") {
													echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Imagens\"><i class=\"icon wb-table\" aria-hidden=\"true\" onclick=\"window.open('http://192.168.0.244:8080/oviyam2/viewer.html?studyUID=" . $studyid . "&serverName=DCENTER', 'Visualizador', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;\"></i></button>";
												} else {
													echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Imagens\"><i class=\"icon wb-table\" aria-hidden=\"true\" onclick=\"window.open('http://dcenter.ddns.net:9595/oviyam2/viewer.html?studyUID=" . $studyid . "&serverName=DCENTER', 'Visualizador', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;\")\"></i></button>";
												}
											}

											?> <button type="button" id='f4' name='f4' class='btn btn-default'>F4 - Variáveis</button>
											<input type='button' name='carregar' class="btn btn-default" value='Carregar Documentos' onclick="window.open('webcam/indexlaudodocs.php?transacao=<?php echo $transacao; ?>', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;">

										</div>
									</h4>
									<div align="center">

										<button type="submit" name='gravar' value='gravar' class="btn btn-primary">Gravar</button>

										<?php
										if ($tipopessoa != 'Administrativo') {
											echo "<button type=\"submit\" name='gravarlaudo'    value='gravarlaudo'        class=\"btn btn-primary\">Gravar como Laudado</button>";
											echo "<button type=\"submit\" name='finalizar' value='finalizar'     class=\"btn btn-info\"   >Gravar e Finalizar     </button>";
										}

										if ($situacao == 'Finalizado' or  $situacao == 'Impresso' or  $situacao == 'Env.Recepção' or $situacao == 'Entregue') {

											echo "<button type=\"submit\" name='imprimir' value='imprimir'      class=\"btn btn-success\">Imprimir</button>";
										}
										?>

										<button type="submit" name='xcancela' value='xcancelar' class="btn btn-danger" onclick='javascript:window.opener.location.reload();window.close();'>Fechar</button>
									</div>

								</div>
								<div class="panel-body">
									<input type="hidden" name="exame_nro" id="exame_nro" value="<?php echo $exame_nro; ?>">
									<input type="hidden" name="usuariox" id="usuariox" value="<?php echo $usuario; ?>">
									<input type="hidden" name="laudotemp" id="laudotemp" value="">
									<textarea id="tinymce" class="input-block-level" name="content" rows="18"><?php echo $laudo; ?></textarea>
									<?php
									include('conexao.php');
									$stmt = "SELECT count(*) as qtde FROM arquivos_documentos where transacao=$transacao";
									$sth = pg_query($stmt) or die($stmt);
									$row = pg_fetch_object($sth);
									if ($row->qtde > 0) {
										echo '<div class="col-sm-12">';
										echo '<h3 class="page-title" align="center">Anexos</h3>';
										echo '<hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />';
										echo '<div class="col-sm-12" id="anexos"';
										echo '<div class="col-md-12">';
										echo '<div class="form-group">';
										echo '<table class="table table-hover table-striped width-full">';
										echo '<thead><tr>';
										echo "<th width='15%'>Data</th><th width='30%'>Tipo</th><th width='20%'>Descricao</th><th width='25%'>Usuario</th><th width='10%'>Açao<th>";
										echo '</tr></thead><tbody>';
										$x = 0;
										include('conexao.php');
										$stmt = "SELECT a.tipo_doc_id, a.descricao, a.data_arquivo, a.usuario, a.arquivo, b.descricao as tipo 
											FROM arquivos_documentos a, tipo_documentos b where a.tipo_doc_id=b.tipo_doc_id and transacao=$transacao and arquivo is not null 
											order by data_arquivo ";
										$sth = pg_query($stmt) or die($stmt);
										while ($row = pg_fetch_object($sth)) {
											$x = $x + 1;
											echo "<tr>";
											echo "<td>" . inverteData($row->data_arquivo) . "</td>";
											echo "<td>" . $row->tipo . "</td>";
											echo "<td>" . $row->descricao . "</td>";
											echo "<td>" . $row->usuario . "</td>";
											echo "<td><a href='imagens/documentos/" . $row->arquivo . "' target='_blank' class=\"btn-pure icon wb-search\"></a></td>";
											echo "</tr>";
											$total_recebido = $total_recebido + $row->valor;
										}
										echo "</tbody></table>";
										echo "</div>";
										echo "</div>";
										echo "<br>";
										echo "</div>";
										echo "</div>";
									}
									?>
									<input type="hidden" name="modalidade" value="<?php echo $modalidade; ?>">
								</div>
								<div class="panel-footer">

								</div>
							</div>
							<input type="hidden" name="situacao" value="<?php echo $situacao; ?>">
							<input type="hidden" name="digitador" value="<?php echo $digitador; ?>">
							<input type="hidden" name="laudador" value="<?php echo $med_analise; ?>">
							<input type="hidden" name="conferente" value="<?php echo $med_confere; ?>">
						</form>

					</div>



					<!-- End Example Panel With Heading -->


					<div id="frases-div" class="col-xlg-5 col-md-5">
						<!-- Example Panel With Heading -->
						<div class="panel panel-bordered">
							<div class="panel-heading">
								<h3 class="panel-title">Frases</h3>
							</div>
							<div class="panel-body">
								<div class="col-sm-12" id="div-frases" name="div-frases" style="height: 480px;
								overflow: scroll;">
									<table id="frases-table" class="table">
										<thead>
											<tr>
												<th>Atalho</th>
												<th>Frase</th>
												<th>Ação</th>
											</tr>
										</thead>
										<tbody>
											<?php
											if ($editor != false) {

												include('conexao.php');
												$stmt = "SELECT a.frase_id, a.atalho, a.frase, b.sigla FROM frases a, modalidades b where a.modalidade_id=b.modalidade_id and a.procedimento_detalhes like '%" . $exame_id . "%' order by frase";
												$sth = pg_query($stmt) or die($stmt);
												while ($row = pg_fetch_object($sth)) {

													echo "<tr>";
													echo "<td>" . $row->atalho . "</td>";
													echo "<td><span class='frase-span'>" . $row->frase . "</span></td>";
													echo "<td><a class='frase-add-a' href=\"#\">+</a></td>";
													echo "</tr>";
												}

												include('conexao.php');
												$stmt = "SELECT a.frase_id, a.frase, a.atalho, b.sigla FROM frases a, modalidades b where a.modalidade_id=b.modalidade_id and a.procedimento_detalhes not like '%" . $exame_id . "%' order by frase";
												$sth = pg_query($stmt) or die($stmt);
												while ($row = pg_fetch_object($sth)) {

													echo "<tr>";
													echo "<td>" . $row->atalho . "</td>";
													echo "<td><span class='frase-span'>" . $row->frase . "</span></td>";
													echo "<td><a class='frase-add-a' href=\"#\">+</a></td>";
													echo "</tr>";
												}
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<!-- End Example Panel With Heading -->
					</div>



					<div id="variaveis-div" class="col-xlg-5 col-md-5">
						<!-- Example Panel With All -->
						<div class="panel panel-bordered">
							<div class="panel-heading">
								<h3 class="panel-title">Variaveis</h3>
							</div>
							<div class="panel-body" style="height: 990px;
								overflow: scroll;">
								<div class="col-md-12">

									<?php
									include('conexao.php');
									if ($laudo_sel == "") {
										$stmt = "SELECT * from procedimentos_laudos_detalhes where procedimento_id=$exame_id order by identificador";
									} else {
										$stmt = "SELECT * from procedimentos_laudos_detalhes where procedimento_id=$laudo_sel order by identificador";
									}
									$sth = pg_query($stmt) or die($stmt);
									$xy = 1;
									while ($row = pg_fetch_object($sth)) {

										if ($row->tipo == '1') {
											echo "<div class=\"form-group\">";
											echo "<label class=\"control-label\">" . $row->campo . "</label>";
											echo "<input type=\"text\" class=\"form-control\" id=\"$row->campo\" tabIndex=\"" . $xy . "\" name=\"$row->campo\" 	value=\"\"";
											if ($editor == false) {
												echo "readonly";
											}
											echo " onkeydown=\"return tab(this, event)\" >";
											echo "</div>";
										}
										if ($row->tipo == '2') {
											echo "<div class=\"form-group\">";
											echo "<label class=\"control-label\">" . $row->campo . "</label>";
											echo "<input type=\"text\" class=\"form-control\" id=\"$row->campo\" tabIndex=\"" . $xy . "\" name=\"$row->campo\" 	value=\"\"";
											if ($editor == false) {
												echo "readonly";
											}
											echo " onkeydown=\"return tab(this, event)\">";
											echo "</div>";
										}
										if ($row->tipo == '3') {
											echo "<div class=\"form-group\">";
											echo "<label class=\"control-label\">" . $row->campo . "</label>";

											if ($editor != false) {
												echo "<select class=\"form-control\"   id=\"$row->campo\" tabIndex=\"" . $xy . "\" name=\"$row->campo\">";
												echo "<option value=''></option>";
												$str = explode(';', $row->complemento);
												$qtd = count($str);
												for ($i = 0; $i < $qtd; $i++) {
													echo "<option value=\"" . $str[$i] . "\">" . $str[$i] . "</option>";
												}
												echo "</select>";
											}
											echo "</div>";
										}
									}
									?>

								</div>
							</div>
						</div>
						<!-- End Example Panel With All -->
					</div>
					<div id="laudos-div" class="col-xlg-5 col-md-5">
						<!-- Example Panel With Heading -->
						<div class="panel panel-bordered">
							<div class="panel-heading">
								<h3 class="panel-title">Laudos</h3>
							</div>
							<div class="panel-body">
								<div class="col-sm-12" id="div-tlaudos" name="div-tlaudos" style="height: 480px;
								overflow: scroll;">
									<table id="laudos-table" class="table table-hover dataTable table-striped width-full" data-plugin="dataTable">
										<thead>
											<tr>
												<th>Mod.</th>
												<th>Laudo</th>

											</tr>
										</thead>
										<tbody>
											<?php
											if ($editor != false) {
												include('conexao.php');
												$stmt = "SELECT a.procedimento_id, a.descricao, b.sigla FROM procedimentos a, modalidades b where a.modalidade_id=b.modalidade_id order by 3,2";
												$sth = pg_query($stmt) or die($stmt);
												while ($row = pg_fetch_object($sth)) {

													echo "<tr>";
													echo "<td>" . $row->sigla . "</td>";
													echo "<td><a href=\"javascript:func()\" onclick=\"confirmacao('" . $row->procedimento_id . "," . $exame_nro . "')\"'>" . $row->descricao . "</a></td>";
													echo "</tr>";
												}
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<!-- End Example Panel With Heading -->
					</div>
					<div id="anteriores-div" class="col-xlg-6 col-md-6">
						<!-- Example Panel With Heading -->
						<div class="panel panel-bordered">
							<div class="panel-heading">
								<h3 class="panel-title">Exames Anteriores</h3>
							</div>
							<div class="panel-body">
								<textarea class="input-block-level" id="summernote1" name="content1" rows="48" readonly><?php echo $laudo; ?></textarea>
							</div>
						</div>
						<!-- End Example Panel With Heading -->
					</div>

				</div>
				<!-- End Example Contextual -->
				<div class="clearfix visible-md-block visible-lg-block hidden-xlg"></div>
			</div>
			<div class="row">
				<div id="laudo-bott" class="col-xlg-7 col-md-12">
					<div class="col-md-6 col-xs-6 masonry-item">
						<div class="widget">
							<div class="widget-header white bg-blue-600 padding-30 clearfix">

								<?php if ($digitador != "") {
									echo "<p class=\"margin-bottom-5 text-nowrap\">";
									echo "<i class=\"icon wb-check margin-right-10\" aria-hidden=\"true\"></i>";
									echo "<span class=\"text-break\">Digitado por " . $digitador . " - " . inverteData($digitador_data) . ' as ' . $digitador_hora . "</span>";
									echo "</p>";
								}
								?>
								<?php if ($medico_analise != "") {
									echo "<p class=\"margin-bottom-5 text-nowrap\">";
									echo "<i class=\"icon wb-check margin-right-10\" aria-hidden=\"true\"></i>";
									echo "<span class=\"text-break\"> Laudado  por $medico_analise  - " . inverteData($data_analise) . " as $hora_analise></span></p>";
								}
								?>
								<?php if ($medico_confere != "") {
									echo "<p class=\"margin-bottom-5 text-nowrap\">";
									echo "<i class=\"icon wb-check margin-right-10\" aria-hidden=\"true\"></i>";
									echo "<span class=\"text-break\"> Conferido  por $medico_confere  - " . inverteData($data_confere) . " as $hora_confere></span></p>";
								}
								?>
								<?php if ($usuario_impressao != "") {
									echo "<p class=\"margin-bottom-5 text-nowrap\">";
									echo "<i class=\"icon wb-print margin-right-10\" aria-hidden=\"true\"></i>";
									echo "<span class=\"text-break\"> Impresso  por $usuario_impressao  - " . inverteData($data_impressao) . " as $hora_impressao></span></p>";
								}
								?>
								<?php if ($usuario_entrega != "") {
									echo "<p class=\"margin-bottom-5 text-nowrap\">";
									echo "<i class=\"icon wb-envelope margin-right-10\" aria-hidden=\"true\"></i>";
									echo "<span class=\"text-break\"> Entregue  por $usuario_entrega  - " . inverteData($data_entrega) . " as $hora_entrega></span></p>";
								}
								?>

							</div>
						</div>
					</div>
					<div class="col-md-6 col-xs-6 masonry-item">
						<div class="widget">
							<div class="widget-header white bg-blue-600 padding-30 clearfix">

								<?php
								echo "<p class=\"margin-bottom-5 text-nowrap\">";
								echo "<i class=\"icon wb-check margin-right-10\" aria-hidden=\"true\"></i>";
								echo "<span class=\"text-break\"> $dat_cad - $hora_cad = $cad_user - Cadastrou Pedido></span></p>";
								include('conexao.php');
								$sql = "select * from log_exames where exame_nro=" . $exame_nro . " order by data_hora";
								$sth = pg_query($sql) or die($sql);
								while ($row = pg_fetch_object($sth)) {
									echo "<p class=\"margin-bottom-5 text-nowrap\">";
									echo "<i class=\"icon wb-check margin-right-10\" aria-hidden=\"true\"></i>";
									echo "<span class=\"text-break\"> $row->data_hora - $row->usuario - $row->acao - $row->observacoes></span></p>";
								}
								?>




							</div>
						</div>
					</div>
				</div>

			</div>

		</form>
		<!-- End Page Content -->
	</div>
	<!-- End Page -->


	<!-- Footer -->
	<?php include('footer.php'); ?>

	<!-- Core  -->
	<script src="assets/vendor/jquery/jquery.js"></script>
	<script src="assets/vendor/bootstrap/bootstrap.js"></script>
	<script src="assets/vendor/animsition/jquery.animsition.js"></script>
	<script src="assets/vendor/asscroll/jquery-asScroll.js"></script>
	<script src="assets/vendor/mousewheel/jquery.mousewheel.js"></script>
	<script src="assets/vendor/asscrollable/jquery.asScrollable.all.js"></script>
	<script src="assets/vendor/ashoverscroll/jquery-asHoverScroll.js"></script>

	<!-- Plugins -->
	<script src="assets/vendor/switchery/switchery.min.js"></script>
	<script src="assets/vendor/intro-js/intro.js"></script>
	<script src="assets/vendor/screenfull/screenfull.js"></script>
	<script src="assets/vendor/slidepanel/jquery-slidePanel.js"></script>



	<!-- Plugins For This Page -->

	<script src="assets/vendor/asprogress/jquery-asProgress.js"></script>
	<script src="assets/vendor/draggabilly/draggabilly.pkgd.js"></script>
	<script src="assets/vendor/datatables/jquery.dataTables.js"></script>
	<script src="assets/vendor/datatables-fixedheader/dataTables.fixedHeader.js"></script>
	<script src="assets/vendor/datatables-bootstrap/dataTables.bootstrap.js"></script>
	<script src="assets/vendor/datatables-responsive/dataTables.responsive.js"></script>

	<!-- Scripts -->
	<script src="assets/js/core.js"></script>
	<script src="assets/js/site.js"></script>

	<script src="assets/js/sections/menu.js"></script>
	<script src="assets/js/sections/menubar.js"></script>
	<script src="assets/js/sections/gridmenu.js"></script>
	<script src="assets/js/sections/sidebar.js"></script>

	<script src="assets/js/configs/config-colors.js"></script>
	<script src="assets/js/configs/config-tour.js"></script>

	<script src="assets/js/components/asscrollable.js"></script>
	<script src="assets/js/components/animsition.js"></script>
	<script src="assets/js/components/slidepanel.js"></script>
	<script src="assets/js/components/switchery.js"></script>

	<!-- Scripts For This Page -->
	<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
	<script src="assets/js/plugins/responsive-tabs.js"></script>
	<script src="assets/js/components/tabs.js"></script>
	<script src="assets/js/components/asprogress.js"></script>
	<script src="assets/js/components/panel.js"></script>
	<script src="assets/js/components/asscrollable.js"></script>
	<script src="assets/js/components/webui-popover.js"></script>
	<script src="assets/examples/js/uikit/tooltip-popover.js"></script>
	<script src="assets/examples/js/uikit/panel-structure.js"></script>
	<script src="assets/vendor/bootstrap-table/bootstrap-table.min.js"></script>
	<script src="assets/vendor/bootstrap-table/extensions/mobile/bootstrap-table-mobile.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {

			$(function() {
				$('[data-toggle="popover"]').popover()
			})

			document.onkeydown = fkey;

			var wasPressed = false;
			var oFrasesDiv = $("#frases-div");
			var oLaudosDiv = $("#laudos-div");
			var oVarDiv = $("#variaveis-div");
			var oLaudoDiv = $("#laudo-div");
			var oAnteriorDiv = $("#anteriores-div");
			var oVar = {};

			// Aplica��o do datatable na tabela de frases
			$("#frases-table").dataTable({
				"bLengthChange": false,
				"bPaginate": false,
				"ordering": false,
				language: {
					search: "_INPUT_",
					searchPlaceholder: "Pesquisar"
				},
				"dom": ' <"search"f><"top"l>rt<"bottom"ip><"clear">'
			});
			$("#laudos-table").dataTable({
				"bLengthChange": false,
				language: {
					search: "_INPUT_",
					searchPlaceholder: "Pesquisar"
				},
				"dom": ' <"search"f><"top"l>rt<"bottom"ip><"clear">'
			});

			$('.dataTables_filter').addClass('format-pesquisa-input');

			$("#frases-div, #variaveis-div, #laudos-div, #anteriores-div").hide();

			oLaudoDiv.removeClass("col-xlg-7").addClass("col-xlg-12");



			// Controle das teclas de atalho das frases e vari�veis
			function fkey(e) {

				if ((e.keyCode == 113) || (e.keyCode == 115) || (e.keyCode == 117) || (e.keyCode == 118)) {

					if (e.keyCode == 113) {

						oVarDiv.hide("1000");
						oLaudosDiv.hide("1000");
						oAnteriorDiv.hide("1000");

						if (oFrasesDiv.is(":visible")) {
							oFrasesDiv.hide("1000");
							oLaudoDiv.removeClass("col-xlg-7").addClass("col-xlg-12");
							oLaudoDiv.removeClass("col-md-7").addClass("col-md-12");
						} else {
							oFrasesDiv.show("1000");
							oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-7");
							oLaudoDiv.removeClass("col-md-12").addClass("col-md-7");
						}

					}
					if (e.keyCode == 115) {

						oFrasesDiv.hide("1000");
						oLaudosDiv.hide("1000");
						oAnteriorDiv.hide("1000");

						if (oVarDiv.is(":visible")) {
							oVarDiv.hide("1000");
							oLaudoDiv.removeClass("col-xlg-7").addClass("col-xlg-12");
							oLaudoDiv.removeClass("col-md-7").addClass("col-md-12");
						} else {
							oVarDiv.show("1000");
							oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-7");
							oLaudoDiv.removeClass("col-md-12").addClass("col-md-7");
						}
					}
					if (e.keyCode == 117) {

						oFrasesDiv.hide("1000");
						oVarDiv.hide("1000");
						oAnteriorDiv.hide("1000");

						if (oLaudosDiv.is(":visible")) {
							oLaudosDiv.hide("1000");
							oLaudoDiv.removeClass("col-xlg-7").addClass("col-xlg-12");
							oLaudoDiv.removeClass("col-md-7").addClass("col-md-12");
						} else {
							oLaudosDiv.show("1000");
							oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-7");
							oLaudoDiv.removeClass("col-md-12").addClass("col-md-7");

						}
					}
					if (e.keyCode == 118) {

						oFrasesDiv.hide("1000");
						oVarDiv.hide("1000");
						oLaudosDiv.hide("1000");

						if (oAnteriorDiv.is(":visible")) {
							oAnteriorDiv.hide("1000");
							oLaudoDiv.removeClass("col-xlg-6").addClass("col-xlg-12");
							oLaudoDiv.removeClass("col-md-6").addClass("col-md-12");
						} else {
							oAnteriorDiv.show("1000");
							oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-6");
							oLaudoDiv.removeClass("col-md-12").addClass("col-md-6");
						}
					}
				}
			}

			// Controle da adicao das frases no laudo
			$(".frase-add-a").click(function(e) {

				e.preventDefault();

				var sFrase = " " + $(this).closest("tr").find(".frase-span").text() + " ";

				tinymce.activeEditor.execCommand('mceInsertContent', false, sFrase);
			});

			$("#f2").click(function(e) {
				oVarDiv.hide("1000");
				oLaudosDiv.hide("1000");
				oAnteriorDiv.hide("1000");

				if (oFrasesDiv.is(":visible")) {
					oFrasesDiv.hide("1000");
					oLaudoDiv.removeClass("col-xlg-7").addClass("col-xlg-12");
					oLaudoDiv.removeClass("col-md-7").addClass("col-md-12");
				} else {
					oFrasesDiv.show("1000");
					oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-7");
					oLaudoDiv.removeClass("col-md-12").addClass("col-md-7");
				}

			});
			$("#f4").click(function(e) {
				oFrasesDiv.hide("1000");
				oLaudosDiv.hide("1000");
				oAnteriorDiv.hide("1000");

				if (oVarDiv.is(":visible")) {
					oVarDiv.hide("1000");
					oLaudoDiv.removeClass("col-xlg-7").addClass("col-xlg-12");
					oLaudoDiv.removeClass("col-md-7").addClass("col-md-12");
				} else {
					oVarDiv.show("1000");
					oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-7");
					oLaudoDiv.removeClass("col-md-12").addClass("col-md-7");
				}
			});
			$("#f6").click(function(e) {
				oFrasesDiv.hide("1000");
				oVarDiv.hide("1000");
				oAnteriorDiv.hide("1000");

				if (oLaudosDiv.is(":visible")) {
					oLaudosDiv.hide("1000");
					oLaudoDiv.removeClass("col-xlg-7").addClass("col-xlg-12");
					oLaudoDiv.removeClass("col-md-7").addClass("col-md-12");
				} else {
					oLaudosDiv.show("1000");
					oLaudoDiv.removeClass("col-xlg-12").addClass("col-xlg-7");
					oLaudoDiv.removeClass("col-md-12").addClass("col-md-7");

				}
			});
			$("#envia_msn").click(function(e) {
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth() + 1; //January is 0!
				var yyyy = today.getFullYear();
				if (dd < 10) {
					dd = '0' + dd
				}

				if (mm < 10) {
					mm = '0' + mm
				}

				today = dd + '/' + mm + '/' + yyyy;

				$('#coments').append(document.getElementById("usuariox").value + ' em ' + today + ':\n');
				$('#coments').append(document.getElementById("mensagem").value + '\n');
			});

			<?php
			$js_arrWords = json_encode($arrWords);
			echo "var arrayWords = " . $js_arrWords . ";\n";

			$js_arrWordsSub = json_encode($arrWordsSub);
			echo "var arrayWordsSub = " . $js_arrWordsSub . ";\n";
			?>

			//TALVEZ ESTA CLASS '.note-editable' ABAIXO possa ser outra
			//depende do seu summernote, clica na caixa de texto dele com o botao direito, DEPOIS EM INSPECIONAR ELEMENTO e ve o nome de uma das class=

			/*
			function focusOn(el) {
				el.focus();
				if (typeof window.getSelection != "undefined"
						&& typeof document.createRange != "undefined") {
					var range = document.createRange();
					range.selectNodeContents(el);
					range.collapse(false);
					var sel = window.getSelection();
					sel.removeAllRanges();
					sel.addRange(range);
				} else if (typeof document.body.createTextRange != "undefined") {
					var textRange = document.body.createTextRange();
					textRange.moveToElementText(el);
					textRange.collapse(false);
					textRange.select();
				}
			}
			
			var summerNoteField = $('.note-editable');

			summerNoteField.keyup(function(){
				
				arrayWords.forEach(function(entry, index) {
					if(summerNoteField.text().indexOf(entry) >= 0){


						var oldText = summerNoteField.html();
						summerNoteField.html('');
						summerNoteField.html(oldText.replace(entry,arrayWordsSub[index]));
						
						focusOn(summerNoteField.get(0));

					}
				});
			});
			*/

			// Controle o valor das vari�veis no summernote
			$("#variaveis-div .panel-body").find(":text, select").change(function() {

				oVar = $(this);

				$("#laudo-div .note-editable").find("#" + oVar.attr("id")).text(" " + oVar.val() + " ");
			});

			var oCamposVar = $("#variaveis-div .panel-body").find(":text, select");

			// Controla o valor das variveis no editor de texto
			oCamposVar.change(function() {

				oVar = $(this);

				$("#tinymce_ifr").contents().find("#" + oVar.attr("name")).text(" " + oVar.val() + " ");
			});

			oCamposVar.keypress(function(e) {

				if (e.which == 13 || e.keyCode == 13) {
					e.preventDefault();
				}
			});
		});

		tinymce.init({
			selector: "#tinymce",
			language: "pt_BR",
			browser_spellcheck: true,
			height: 1000,
			plugins: [
				'advlist autolink lists link image charmap print preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars code fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
			],
			toolbar1: 'undo redo | insert | styleselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | sizeselect | bold italic | fontselect | fontsizeselect | forecolor backcolor ',
			image_advtab: true,
			fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt"
			<?php
			if ($editor == false) {
				echo ",readonly : 1";
			}
			?>

		});
		$('#summernote1').summernote({
			height: "800px",
			toolbar: [
				['style', ['style']],
				['text', ['bold', 'italic', 'underline', 'color', 'clear']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				['fontsize', ['fontsize']],
				['view', ['codeview']],
				['font', ['fontname']],
			]
		});
		$("#atualiza_msn").on('click', function(event) {
			var exame = $("#exame_nro").val();
			$.get('buscacomentario.php?exame=' + exame, function(dataReturn) {
				$('#coments').val(dataReturn);
			});

		});
	</script>

</body>

</html>