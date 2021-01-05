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
error_reporting(0);
$menu_grupo = '1';
$menu_sgrupo = '2';
$nome = '';
$dtnasc = '';
$telefone = '';
$mae = '';
$where = "tipo_pessoa='Medico Laudador'";
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$codigo = $_GET['id'];
	if ($codigo != '') {
		$where = "tipo_pessoa='Medico Laudador' and pessoa_id =" . $codigo;
	}
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$nome = ts_codifica($_POST['nome']);
	$dtnasc = $_POST['dtnasc'];
	$telefone = $_POST['telefone'];
	$mae = ts_codifica($_POST['mae']);
	$where = '';
	if ($nome != '') {
		$where = " tipo_pessoa='Medico Laudador' and nome like '" . $nome . "%' ";
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
    <title>FUNEPU | Medicos</title>
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
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99"
            style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>

    <div class="wrapper">
        <?php include 'menu.php'; ?>
        <?php include 'header.php'; ?>
        <div class="main-panel">
            <div class="main-content">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4 class="card-title">
                                                        <p
                                                            style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                            » </p>MEDICOS
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
                                                    <li class="active">Medicos</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- CORPO DA PAGINA -->
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="#" method="post">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="inputBasicFirstName">Nome</label>
                                                        <input type="text" class="form-control square"
                                                            id="inputBasicFirstName" name="nome"
                                                            placeholder="Nome do Medico" autocomplete="off"
                                                            onkeyup="maiuscula(this)"
                                                            value="<?php echo ts_decodifica($nome); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for="inputBasicLastName">Nascimento</label>
                                                    <input type="text" class="form-control square"
                                                        id="inputBasicLastName" name="dtnasc"
                                                        value="<?php echo $dtnasc; ?>"
                                                        placeholder="dd/mm/aaaa" autocomplete="off" />
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-12" align="center">
                                                    <button type="submit" name="pesquisa"
                                                        class="btn btn-raised btn-primary square btn-min-width mr-1 mb-1">Pesquisar</button>
                                                    <button type="reset" name="limpar"
                                                        class="btn btn-raised btn-danger square btn-min-width mr-1 mb-1">Limpar</button>
                                                    <button type="button"
                                                        class="btn btn-raised btn-success square btn-min-width mr-1 mb-1"
                                                        onclick="location.href='cadastro.php?tipo=M'">Adicionar novo
                                                        Medico</button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="row mt-4 mb-5" align="center">
                                            <div class="col-12">
                                                <hr
                                                    style="color: #12A1A6; background-color: #12A1A6; height: 1px; width: 900px;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="data_table" class="table" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Nome</th>
                                                            <th>Data Nasc.</th>
                                                            <th>Telefone</th>
                                                            <th>Filiação</th>
                                                            <th>Situação</th>
                                                            <th>Ação</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Nome</th>
                                                            <th>Data Nasc.</th>
                                                            <th>Telefone</th>
                                                            <th>Filiação</th>
                                                            <th>Situação</th>
                                                            <th>Ação</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
														include 'conexao.php';
														$stmt = 'SELECT * FROM pessoas';
														if ($where != '') {
															$stmt = $stmt . ' where ' . $where;
														}
														$stmt = $stmt . ' order by nome';
														$sth = pg_query($stmt) or die($stmt);
														while ($row = pg_fetch_object($sth)) {
															if ($row->situacao == '1') {
																$situacao = 'Inativo';
															} else {
																$situacao = 'Ativo';
															}
															echo '<tr>';
															echo '<td>' . str_pad($row->pessoa_id, 7, '0', STR_PAD_LEFT) . '</td>';
															echo '<td>' . ts_decodifica($row->nome) . '</td>';
															echo '<td>' . inverteData($row->dt_nasc) . '</td>';
															echo '<td>' . $row->telefone . '</td>';
															echo '<td>' . ts_decodifica($row->nome_mae) . '</td>';
															echo '<td>' . $situacao . '</td>';
															echo '<td><a href="alteracadastro.php?id=' . $row->pessoa_id . '"><i class="fas fa-edit"></i></a> <a href=""  onclick="doConfirm(' . $row->pessoa_id . ');" data-popup="tooltip" title="" data-original-title="Inativar"><i class="fas fa-trash-alt"></i></a></td>';

															echo '</tr>';
														}
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
    </div>
    <?php include 'footer.php'; ?>
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
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script>
        function doConfirm(id) {

            var ok = confirm("Confirma a exclusao?" + id)
            if (ok) {

                if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else { // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.location = "create_dealer.php"; // self page
                    }
                }

                xmlhttp.open("GET", "apagacadastro.php?id=" + id);
                xmlhttp.send();
            }
        }

        function aConf(mes) {
            alertify.confirm(mes, function(e) {
                return e;
            });
        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        function reset() {
            $("#toggleCSS").attr("href", "../themes/alertify.default.css");
            alertify.set({
                labels: {
                    ok: "OK",
                    cancel: "Cancel"
                },
                delay: 5000,
                buttonReverse: false,
                buttonFocus: "ok"
            });
        }

        // ==============================
        // Standard Dialogs
        $("#alert").on('click', function() {
            reset();
            alertify.alert("This is an alert dialog");
            return false;
        });

        $("#confirm").on('click', function() {
            reset();
            alertify.confirm("This is a confirm dialog", function(e) {
                if (e) {
                    alertify.success("You've clicked OK");
                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
            return false;
        });

        $("#prompt").on('click', function() {
            reset();
            alertify.prompt("This is a prompt dialog", function(e, str) {
                if (e) {
                    alertify.success("You've clicked OK and typed: " + str);
                } else {
                    alertify.error("You've clicked Cancel");
                }
            }, "Default Value");
            return false;
        });

        // ==============================
        // Ajax
        $("#ajax").on("click", function() {
            reset();
            alertify.confirm("Confirm?", function(e) {
                if (e) {
                    alertify.alert("Successful AJAX after OK");
                } else {
                    alertify.alert("Successful AJAX after Cancel");
                }
            });
        });

        // ==============================
        // Standard Dialogs
        $("#notification").on('click', function() {
            reset();
            alertify.log("Standard log message");
            return false;
        });

        $("#success").on('click', function() {
            reset();
            alertify.success("Success log message");
            return false;
        });

        $("#error").on('click', function() {
            reset();
            alertify.error("Error log message");
            return false;
        });

        // ==============================
        // Custom Properties
        $("#delay").on('click', function() {
            reset();
            alertify.set({
                delay: 10000
            });
            alertify.log("Hiding in 10 seconds");
            return false;
        });

        $("#forever").on('click', function() {
            reset();
            alertify.log("Will stay until clicked", "", 0);
            return false;
        });

        $("#labels").on('click', function() {
            reset();
            alertify.set({
                labels: {
                    ok: "Accept",
                    cancel: "Deny"
                }
            });
            alertify.confirm("Confirm dialog with custom button labels", function(e) {
                if (e) {
                    alertify.success("You've clicked OK");
                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
            return false;
        });

        $("#focus").on('click', function() {
            reset();
            alertify.set({
                buttonFocus: "cancel"
            });
            alertify.confirm("Confirm dialog with cancel button focused", function(e) {
                if (e) {
                    alertify.success("You've clicked OK");
                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
            return false;
        });

        $("#order").on('click', function() {
            reset();
            alertify.set({
                buttonReverse: true
            });
            alertify.confirm("Confirm dialog with reversed button order", function(e) {
                if (e) {
                    alertify.success("You've clicked OK");
                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
            return false;
        });

        // ==============================
        // Custom Log
        $("#custom").on('click', function() {
            reset();
            alertify.custom = alertify.extend("custom");
            alertify.custom("I'm a custom log message");
            return false;
        });

        // ==============================
        // Custom Themes
        $("#bootstrap").on('click', function() {
            reset();
            $("#toggleCSS").attr("href", "../themes/alertify.bootstrap.css");
            alertify.prompt("Prompt dialog with bootstrap theme", function(e) {
                if (e) {
                    alertify.success("You've clicked OK");
                } else {
                    alertify.error("You've clicked Cancel");
                }
            }, "Default Value");
            return false;
        });
    </script>
</body>

</html>