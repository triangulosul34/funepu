<?php

error_reporting(0);

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

include('verifica.php');
$qtde_atendimentos = '';
$dias = '';
$start          = '';
$end          = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['gerarrelatorio'])) {
        $start          = $_POST['start'];
        $end          = $_POST['end'];

        include('conexao.php');
        $stmtRel = "SELECT count(*) as qtde, 
				CASE
				WHEN destino_paciente= '01' or destino_paciente= '02' 
				or destino_paciente= '11' or destino_paciente= '12' 
				or destino_paciente= '15' 
				or destino_paciente= '14' THEN 'ALTA'		
				WHEN destino_paciente= '06' THEN 'ÓBITO'
				WHEN destino_paciente= '03' THEN 'PERMANÊNCIA'
				WHEN destino_paciente= '07' THEN 'EM OBSERVAÇÃO / MEDICAÇÃO'
				END AS destino,
				CASE
				WHEN destino_paciente= '01' or destino_paciente= '02' or destino_paciente= '11' or destino_paciente= '12' or destino_paciente= '14' or destino_paciente= '15' THEN '01,02,11,12,14,15'
				END AS vlr_destino
				from atendimentos a
				left join destino_paciente d on d.atendimento_id = a.transacao
				where destino_paciente in ('01','11', '02', '12','14','15','06', '03', '07') and dat_cad between '$start' and '$end'
				group by 2,3 
				order by 2 ASC, 1 DESC";
        $sthRel = pg_query($stmtRel);

        include('conexao.php');
        $stmtRelCont = "SELECT count(*) as qtde
			from atendimentos 
			where dat_cad between '$start' and '$end' order by 1 desc";
        $sthRelCont = pg_query($stmtRelCont);
        $rowCount = pg_fetch_object($sthRelCont);
        $qtde_atendimentos = $rowCount->qtde;
        $dias = date('d');
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
    <title>FUNEPU | Relatorio de Atendimento</title>
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
    <!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

    <!-- <div class="wrapper"> -->
    <?php include('menu.php'); ?>
    <?php include('header.php'); ?>
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
                                                        » </p>relatorio atendimento
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
                                                <li class="active">Relatorio Atendimento</li>
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
                                                <div class="form-group">
                                                    <label>Data Inicial</label>
                                                    <input type="date" class="form-control square" id="start" name="start" value="<?php echo $_POST['start']; ?>" onkeydown="mascaraData(this)">
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Data Final</label>
                                                    <input type="date" class="form-control square" id="end" name="end" value="<?php echo $_POST['end']; ?>" onkeydown="mascaraData(this)">
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <label class="control-label">Ação</label><br>
                                                <button type="submit" name="gerarrelatorio" class="btn btn-primary" style="width:100%">Gerar Relatório</button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php if (isset($_POST['gerarrelatorio']) and $qtde_atendimentos > 0) { ?>
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <h4>Atendimentos de</h4>
                                                <p class="font-size-20 blue-grey-700"><?php echo inverteData($start); ?> até <?php echo inverteData($end); ?></p>
                                            </div>
                                            <div class="col-6">
                                                <h4>Atendimentos</h4>
                                                <p><?php echo $qtde_atendimentos; ?> Atendimentos</p>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <table class="table">
                                                <thead align="left">
                                                    <tr>
                                                        <th>Destino</th>
                                                        <th>Quantidade</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php
                                                    while ($rowRel = pg_fetch_object($sthRel)) {

                                                        $valoresDestino = explode(",", $rowRel->vlr_destino);
                                                        $valoresDestino = "'$valoresDestino[0]', '$valoresDestino[1]', '$valoresDestino[2]', '$valoresDestino[3]','$valoresDestino[4]','$valoresDestino[5]'";

                                                        echo '<tr>';
                                                        echo '<td style="font-weight: bold">' . $rowRel->destino . '</td>';
                                                        echo '<td style="font-weight: bold">' . $rowRel->qtde . '</td>';
                                                        echo '</tr>';
                                                        include('conexao.php');
                                                        if ($rowRel->destino == 'ALTA') {
                                                            $stmtRel2 = "SELECTx count(*) as qtde, 
												CASE
													WHEN destino_paciente= '01' THEN 'ALTA'
													WHEN destino_paciente= '02' THEN 'ALTA / ENCAM. AMBUL.'
													WHEN destino_paciente= '11' THEN 'ALTA EVASÃO' 
													WHEN destino_paciente= '12' THEN 'ALTA PEDIDO' 
													WHEN destino_paciente= '06' THEN 'ÓBITO'
													WHEN destino_paciente= '14' THEN 'ALTA / PM' 
													WHEN destino_paciente= '15' THEN 'ALTA / PENITENCIÁRIA' 
													END AS destino
												from atendimentos a
												left join destino_paciente d on d.atendimento_id = a.transacao
												where destino_paciente in (" . $valoresDestino . ") and destino_paciente != '' and dat_cad between '$start' and '$end'
												group by 2 
												order by 2 ASC, 1 DESC";
                                                            $sthRel2 = pg_query($stmtRel2);
                                                            while ($rowRel2 = pg_fetch_object($sthRel2)) {
                                                                echo '<tr>';
                                                                $destino = '';
                                                                if ($rowRel2->destino == 'ALTA') {
                                                                    $destino = 'ALTA DOMICÍLIO';
                                                                } else {
                                                                    $destino = $rowRel2->destino;
                                                                }
                                                                echo '<td>' . $destino . '</td>';
                                                                echo '<td>' . $rowRel2->qtde . '</td>';
                                                                echo '</tr>';
                                                            }
                                                        } elseif ($rowRel->destino == 'EM OBSERVAÇÃO / MEDICAÇÃO') {
                                                            $stmtRel2 = "SELECT count(*) as qtde, 
													CASE
														WHEN destino_encaminhamento = '01' THEN 'ALTA'
														WHEN destino_encaminhamento = '02' THEN 'ALTA / ENCAM. AMBUL.'
														WHEN destino_encaminhamento = '11' THEN 'ALTA EVASÃO' 
														WHEN destino_encaminhamento = '12' THEN 'ALTA PEDIDO' 
														WHEN destino_encaminhamento = '15' THEN 'ALTA / PENITENCIÁRIA'
														WHEN destino_encaminhamento = '14' THEN 'ALTA / PM' 
														WHEN destino_encaminhamento = '04' THEN 'TRANSF. OUTRA UPA'
														WHEN destino_encaminhamento = '05' THEN 'TRANSFERENCIA HOSPITALAR'
														WHEN destino_encaminhamento = '13' THEN 'TRANSFERENCIA INTERNA'
														WHEN destino_encaminhamento = '03' THEN 'PERMANÊCIA.' 
														WHEN destino_encaminhamento = '06' THEN 'ÓBITO' 
														ELSE 'NÃO SE APLICA'
														END AS destino
													from atendimentos a
													left join destino_paciente d on d.atendimento_id = a.transacao
													where destino_paciente in ('07') and destino_paciente != '' and dat_cad between '$start' and '$end'
													group by 2 
													order by 2 ASC, 1 DESC";
                                                            $sthRel2 = pg_query($stmtRel2);
                                                            while ($rowRel2 = pg_fetch_object($sthRel2)) {
                                                                echo '<tr>';
                                                                $destino = $rowRel2->destino;
                                                                echo '<td>' . $destino . '</td>';
                                                                echo '<td>' . $rowRel2->qtde . '</td>';
                                                                echo '</tr>';
                                                            }
                                                        } elseif ($rowRel->destino == 'PERMANÊNCIA') {
                                                            $stmtRel2 = "SELECT count(*) as qtde, 
														CASE
															WHEN destino_encaminhamento = '01' THEN 'ALTA'
															WHEN destino_encaminhamento = '02' THEN 'ALTA / ENCAM. AMBUL.'
															WHEN destino_encaminhamento = '11' THEN 'ALTA EVASÃO' 
															WHEN destino_encaminhamento = '12' THEN 'ALTA PEDIDO' 
															WHEN destino_encaminhamento = '15' THEN 'ALTA / PENITENCIÁRIA'
															WHEN destino_encaminhamento = '14' THEN 'ALTA / PM' 
															WHEN destino_encaminhamento = '04' THEN 'TRANSF. OUTRA UPA'
															WHEN destino_encaminhamento = '05' THEN 'TRANSFERENCIA HOSPITALAR'
															WHEN destino_encaminhamento = '13' THEN 'TRANSFERENCIA INTERNA'
															WHEN destino_encaminhamento = '03' THEN 'PERMANÊCIA' 
															WHEN destino_encaminhamento = '06' THEN 'ÓBITO' 
															ELSE 'NÃO SE APLICA'
															END AS destino
														from atendimentos a
														left join destino_paciente d on d.atendimento_id = a.transacao
														where destino_paciente in ('03') and destino_paciente != '' and dat_cad between '$start' and '$end'
														group by 2 
														order by 2 ASC, 1 DESC";
                                                            $sthRel2 = pg_query($stmtRel2);
                                                            while ($rowRel2 = pg_fetch_object($sthRel2)) {
                                                                echo '<tr>';
                                                                $destino = $rowRel2->destino;
                                                                echo '<td>' . $destino . '</td>';
                                                                echo '<td>' . $rowRel2->qtde . '</td>';
                                                                echo '</tr>';
                                                            }
                                                        }
                                                    } ?>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="col-12" align="center"><button id="imprimirelatorio" class="btn btn-raised btn-success square">Imprimir</button></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
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
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script>
        $("#imprimirelatorio").click(function(event) {
            var especialidade = $("#especialidade").val();
            var start = $("#start").val();
            var end = $("#end").val();

            var url = 'relAtendimento.php?start=' + start + '&end=' + end;
            window.open(url);
        });
    </script>
</body>

</html>