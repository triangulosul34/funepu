<?php

include 'verifica.php';
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
$menu_grupo = '3';
$menu_sgrupo = '3';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$nome = ts_codifica($_POST['nome']);
	$pessoa_id = $_POST['prontuario'];
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
    <title>FUNEPU | Solicitação de APAC</title>
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
                                                        » </p>Solicitação de APAC
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
                                                <li class="active">Solicitacao APAC</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" method="POST">
                                        <div class="row">
                                            <div class="col-12" align="center">
                                                <label>Paciente</label>
                                                <input type="text" class="form-control square" id="inputBasicFirstName"
                                                    name="nome" placeholder="Parte do Nome" autocomplete="off"
                                                    value="<?php echo ts_decodifica($nome); ?>"
                                                    onkeyup="maiuscula(this)" />
                                            </div>
                                            <div class="col-12 mt-2" align="center">
                                                <button type="submit" name="pesquisa" value="semana"
                                                    class="btn btn-raised btn-primary square btn-min-width mr-1 mb-1">Pesquisar</button>
                                                <button type="button"
                                                    class="btn btn-raised btn-success square btn-min-width mr-1 mb-1"
                                                    onclick="location.href='formapac.php'"><i
                                                        class="icon-stack2 position-left"></i> Novo APAC</button>
                                            </div>
                                        </div>
                                    </form>
                                    <table id="data_table" class="table">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" name="" id=""></th>
                                                <th>Id</th>
                                                <th>Data</th>
                                                <th>Paciente</th>
                                                <th>Exame</th>
                                                <th>Solicitante</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Id</th>
                                                <th>Data</th>
                                                <th>Paciente</th>
                                                <th>Exame</th>
                                                <th>Solicitante</th>
                                                <th>Ação</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
											include 'conexao.php';
											$stmt = 'SELECT a.pessoa_id, a.med_solicitante, a.data_solicitacao, a.apac_id, b.nome,c.descricao FROM apacs_solicitadas a
						left join pessoas b on a.pessoa_id=b.pessoa_id
						left join procedimentos c on a.procedimento_id=c.procedimento_id ';

											if ($nome <> '') {
												$stmt = $stmt . " where b.nome like '%$nome%' ";
											}

											$stmt = $stmt . ' order by 3 desc';
											$sth = pg_query($stmt) or die($stmt);
											while ($row = pg_fetch_object($sth)) {
												echo '<tr>';
												echo '<td><input type="checkbox" name="" id=""></td>';
												echo "<td width='7%' >" . str_pad($row->apac_id, 4, '0', STR_PAD_LEFT) . '</td>';
												echo "<td width='10%' >" . inverteData($row->data_solicitacao) . '</td>';
												echo '<td >' . ts_decodifica($row->nome) . '</td>';
												echo "<td width='30%'>" . $row->descricao . '</td>';
												echo '<td>' . $row->med_solicitante . '</td>';

												echo "<td><a target='_blank' href=apac.php?prontuario=$row->pessoa_id&apac_id=$row->apac_id><i class=\"fas fa-search\"></i></a></td>";
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
</body>

</html>