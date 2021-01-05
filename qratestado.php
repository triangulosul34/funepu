<?php

include 'conexao.php';
require 'tsul_ssl.php';

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

function iniciais($str)
{
	$pos = 0;
	$saida = '';
	while (($pos = strpos($str, ' ', $pos)) !== false) {
		if (isset($str[$pos + 1]) && $str[$pos + 1] != ' ') {
			$saida .= substr($str, $pos + 1, 1);
		}
		$pos++;
	}

	return $str[0] . $saida;
}

$atestado_id = $_GET['a'];
$codigo = $_GET['c'];

$sql = "SELECT * FROM atestados a INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id WHERE atestado_id = $atestado_id and atendimento_id = $codigo";
$result = pg_query($sql);
$rowa = pg_fetch_object($result);

$nome = ts_decodifica($rowa->nome);
$dia = $rowa->partir_dia;
$qtd = $rowa->qtd_dias;
?>
<!DOCTYPE html>
<html lang="en" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="Apex admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, Apex admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>FUNEPU | Atestados</title>
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
</head>

<style>
    body {
        background-image: url('app-assets/img/gallery/login.jpeg');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }

    .sh {
        -moz-box-shadow: 0 0 10px;
        -webkit-box-shadow: 0 0 10px;
        box-shadow: 0 0 10px;
    }
</style>

<body data-col="1-column" class=" 1-column  blank-page">
    <div class="wrapper">
        <div class="main-panel">
            <div class="main-content">
                <div class="content-wrapper">
                    <section>
                        <div class="container-fluid">
                            <div class="row full-height-vh m-0">
                                <div class="col-12 d-flex align-items-center justify-content-center">
                                    <div class="card sh">
                                        <div class="card-content">
                                            <div class="card-body login-img">
                                                <div class="row m-0">
                                                    <div
                                                        class="col-lg-6 d-lg-block d-none py-2 text-center align-middle">
                                                        <img src="app-assets/img/gallery/upa24.png" class="mt-3" alt=""
                                                            width="280" height="180">
                                                    </div>
                                                    <div class="col-lg-6 col-md-12 bg-white px-4 pt-3">
                                                        <form name='formulario' method='POST' action='checklogin.php'>
                                                            <h4 class="mb-3 card-title"><strong>Verificação de
                                                                    atestado</strong></h4>
                                                            <p><b>Paciente:</b> <?php echo $nome == '' ? '<span style="color:red">Não encontrado</span>' : iniciais($nome); ?>
                                                            </p>
                                                            <p><b>Data:</b> <?php echo $dia == '' ? '<span style="color:red">Não encontrado</span>' : inverteData($dia); ?>
                                                            </p>
                                                            <p><b>Dias de Atestado:</b> <?php echo $dia == '' ? '<span style="color:red">Não encontrado</span>' : $qtd; ?>
                                                            </p>
                                                            <div class="row">
                                                                <div class='col-md-6' align='center'>
                                                                    <?php if ($nome == '') { ?>
                                                                    <i class="fas fa-times-circle font-large-1"
                                                                        style="color: red;"></i>
                                                                    <?php } else { ?>
                                                                    <i class="fas fa-check-circle font-large-1"
                                                                        style="color: #15AABF;"></i>
                                                                    <?php } ?>
                                                                </div>
                                                                <div class='col-md-6' align='right'><img
                                                                        src="app-assets/img/gallery/logotc.png" alt=""
                                                                        width="40" height="40"></div>
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
                    </section>
                </div>
            </div>
        </div>
    </div>
    <script src="app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/core/popper.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/prism.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/jquery.matchHeight-min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/screenfull.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/pace/pace.min.js" type="text/javascript"></script>
    <script src="app-assets/js/app-sidebar.js" type="text/javascript"></script>
    <script src="app-assets/js/notification-sidebar.js" type="text/javascript"></script>
    <script src="app-assets/js/customizer.js" type="text/javascript"></script>
</body>

</html>