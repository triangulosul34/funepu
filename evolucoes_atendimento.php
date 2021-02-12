<?php
require 'tsul_ssl.php';
require 'verifica.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$transacao = $_GET['id'];

	include 'conexao.php';
	$data = date('Y-m-d');
	$hora = date('H:i');
	$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
  values ('$usuario','ACESSOU A VISUALIZAÇÃO DE EVOLUCOES ADMINISTRATIVA','$transacao','$data','$hora')";
	$sthLogs = pg_query($stmtLogs) or die($stmtLogs);
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
  <title>FUNEPU | Evolucoes</title>
  <link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
  <link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
  <link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
  <link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
  <link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
    rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
  <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
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

<body class="  pace-done" cz-shortcut-listen="true">
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
                <!-- <div class="card-header" style="display: flex;align-items: center;justify-content: space-between; background: #00777a"> -->

                <div class="card-header">
                  <div class="row">
                    <div class="col-6">
                      <div class="row">
                        <div class="col-12">
                          <h4 class="card-title">
                            <p style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                              » </p>Evolucoes
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
                          <li class="active">Evolucoes</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- CORPO DA PAGINA -->
                <div class="card-content">
                  <div class="col-12">
                    <div class="row mt-4">
                      <?php
										$stmtEvolucao = "SELECT count(*) as qtd from evolucoes where atendimento_id	= $transacao";
										$sthEv = pg_query($stmtEvolucao) or die($stmtEvolucao);
										$rowEv = pg_fetch_object($sthEv);

										if ($rowEv->qtd > 0) {
											?>
                      <div class="col-12"><br>
                        <h3 align="center">Evoluções</h3>
                        <hr style="width: 100%;" />
                        <div class="col-sm-12" style="height: 255px; overflow-y: auto; overflow-x: hidden;"
                          id="conteudoPrescricao"><br>

                          <table class="table">
                            <thead>
                              <tr>
                                <th width="10%">Nº da Evolução</th>
                                <th width="12%">Nº do Atendimento</th>
                                <th width="10%">Data</th>
                                <th width="10%">Hora</th>
                                <th>Profissional</th>
                                <th width="9%">Ação</th>
                              </tr>
                            </thead>

                            <body>
                              <?php
															$stmt = 'SELECT a.evolucao_id,a.atendimento_id,a.tipo,a.data,a.hora,b.nome,a.evolucao 
                                                            FROM evolucoes a
                                                                left join pessoas b ON b.username = a.usuario
                                                            WHERE a.atendimento_id =' . $transacao . ' order by 1 desc';
											$sth = pg_query($stmt) or die($stmt);

											while ($row = pg_fetch_object($sth)) {
												echo '<tr>';
												echo '<td>' . str_pad($row->evolucao_id, 7, '0', STR_PAD_LEFT) . '</td>';
												echo '<td>' . str_pad($row->atendimento_id, 7, '0', STR_PAD_LEFT) . '</td>';
												echo '<td>' . date('d/m/Y', strtotime($row->data)) . '</td>';
												echo '<td>' . $row->hora . '</td>'; ?>

                              <td><?php

																	if ($row->tipo == 6) {
																		echo 'Super Usuário - ';
																	}
												if ($row->tipo == 3) {
													echo 'Medico - ';
												}
												if ($row->tipo == 8) {
													echo 'Enfermagem - ';
												}

												echo ts_decodifica($row->nome) ?>
                              </td>

                              <?php

												echo "<td><a href=\"relevolucao.php?id=$row->evolucao_id\" target=\"_blank\" class=\"btn btn-sm btn-info\" data-toggle=\"tooltip\" data-original-title=\"Ficha de Evolução\"><i class=\"fas fa-print\"></i></a></td>";

												echo '<tr>';
											} ?>

                            </body>

                          </table>

                        </div>


                      </div>
                      <?php
										} ?>
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





</body>
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
<script defer src="/your-path-to-fontawesome/js/all.js"></script>
</body>

</html>