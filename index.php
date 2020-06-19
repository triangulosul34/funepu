<?php include('verifica.php'); ?>
<!DOCTYPE html>
<html lang="en" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Apex admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Apex admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>FUNEPU | Pagina Inicial</title>
    <link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
</head>

<body class="bg-light">
    <div class="wrapper">
        <?php include('menu.php'); ?>
        <?php include('header.php'); ?>
        <div class="main-panel">
            <div class="main-content">
                <div class="content-wrapper">
                    <section>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12 mt-3 mb-1">
                                    <div class="content-header">Acolhimento e Pronto Atendimento</div>
                                    <p class="content-sub-header">Selecione uma opção abaixo</p>
                                </div>
                            </div>

                            <div class="row" matchheight="card">
                                <div class="col-xl-3 col-lg-6 col-12">
                                    <div class="card bg-warning">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='novoatendimento.php';">
                                                <div class="media">
                                                    <div class="media-body white text-left">
                                                    <?php
													
														include ('conexao.php');
														$stmt = "Select count(*) as qtde from atendimentos where dat_cad='".date('Y-m-d')."' and hora_triagem is null ";
														$sth = pg_query ( $stmt ) or die ( $stmt );
														$row = pg_fetch_object ( $sth );
													
                                                        echo '<h3>'.str_pad($row->qtde, 3, '0', STR_PAD_LEFT).'</h3>';
													?>               
                                                        <span>RECEPÇÃO</span>
                                                    </div>
                                                    <div class="media-right align-self-center">
                                                        <i class="icon-layers white font-large-2 float-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-12">
                                    <div class="card bg-success">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='triagemRecepcao.php';">
                                                <div class="media">
                                                    <div class="media-body white text-left">
                                                    <?php
													
														include ('conexao.php');
														$stmt = "Select count(*) as qtde from atendimentos where dat_cad='".date('Y-m-d')."' and hora_triagem is not null ";
														$sth = pg_query ( $stmt ) or die ( $stmt );
														$row = pg_fetch_object ( $sth );
													
                                                        echo '<h3>'.str_pad($row->qtde, 3, '0', STR_PAD_LEFT).'</h3>';
													?>
                                                        <span>TRIAGEM</span>
                                                    </div>
                                                    <div class="media-right align-self-center">
                                                        <a><i class="fas fa-hand-holding-medical white font-large-2 float-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-12">
                                    <div class="card bg-danger">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='monitor_medico.php';">
                                                <div class="media">
                                                    <div class="media-body white text-left">
                                                    <?php
													
														include ('conexao.php');
														$stmt = "Select count(*) as qtde from atendimentos where dat_cad='".date('Y-m-d')."' and hora_atendimento is not null ";
														$sth = pg_query ( $stmt ) or die ( $stmt );
														$row = pg_fetch_object ( $sth );
													
                                                        echo '<h3>'.str_pad($row->qtde, 3, '0', STR_PAD_LEFT).'</h3>';
													?>
                                                        <span>ATENDIMENTO MEDICO</span>
                                                    </div>
                                                    <div class="media-right align-self-center">
                                                        <i class="fas fa-user-md white font-large-2 float-right"></i>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-12">
                                    <div class="card bg-primary">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='evolucoes.php';">
                                                <div class="media">
                                                    <div class="media-body white text-left">
                                                        <h3>000</h3>
                                                        <span>EVOLUÇÂO DIÁRIA</span>
                                                    </div>
                                                    <div class="media-right align-self-center">
                                                        <i class="fas fa-file-medical-alt white font-large-2 float-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-3 mb-1">
                                    <div class="content-header">Painéis e Monitoramento</div>
                                    <p class="content-sub-header">Selecione uma opção abaixo</p>
                                </div>
                            </div>
                            <div class="row" matchheight="card">
                                <div class="col-4">
                                    <div class="card bg-danger">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='triagemRecepcao.php';">
                                                <div class=" media">
                                                    <div class="media-left align-self-center">
                                                        <i class="fas fa-user-nurse white font-large-2 float-left"></i>

                                                    </div>
                                                    <div class="media-body white text-right">
													
                                                        <h3>000</h3>
                                                        <span>AGUARDANDO TRIAGEM</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-success">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='atendimentoRecepcao.php';">
                                                <div class="media">
                                                    <div class="media-left align-self-center">
                                                        <i class="fas fa-user-friends white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body white text-right">
                                                        <h3>000</h3>
                                                        <span>AGUARDANDO ATENDIMENTO</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-warning">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='monitorAtendMedicos.php';">
                                                <div class="media">
                                                    <div class="media-left align-self-center">
                                                        <i class="fas fa-user-md white font-large-2 float-left"></i>
                                                    </div>
                                                    <div class="media-body white text-right">
                                                        <h3>000</h3>
                                                        <span>MONITOR EM ATENDIMENTO</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" matchheight="card">
                                <div class="col-4">
                                    <div class="card bg-warning">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='painel_us.php';">
                                                <div class="media">
                                                    <div class="media-body white text-left">
                                                        <h3>000</h3>
                                                        <span>PAINEL US</span>
                                                    </div>
                                                    <div class="media-right align-self-center">
                                                        <i class="icon-layers white font-large-2 float-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-success">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='painel_rx.php';">
                                                <div class="media">
                                                    <div class="media-body white text-left">
                                                        <h3>000</h3>
                                                        <span>PAINEL RX</span>
                                                    </div>
                                                    <div class="media-right align-self-center">
                                                        <a><i class="fas fa-radiation white font-large-2 float-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="card bg-danger">
                                        <div class="card-content">
                                            <div class="px-3 py-3" onclick="window.location='painel_ecg.php';">
                                                <div class="media">
                                                    <div class="media-body white text-left">
                                                        <h3>000</h3>
                                                        <span>PAINEL ECG</span>
                                                    </div>
                                                    <div class="media-right align-self-center">
                                                        <i class="fas fa-heartbeat white font-large-2 float-right"></i>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>
                </div>
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