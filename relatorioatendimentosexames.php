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
include('Config.php');
$qtde_atendimentos = '';
$dias = '';
$start          = '';
$end          = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['gerarrelatorio'])) {
        $start          = $_POST['start'];
        $end          = $_POST['end'];
        $modalidade     = $_POST['modalidade'];

        $where = '';

        if ($modalidade == 5) {
            include('conexao_laboratorio.php');

            $sql = "select d.descricao, count(*) as qtde from pedidos a
			inner join pedido_guia b on a.pedido_id = b.pedido_id
			inner join pedido_item c on b.pedido_guia_id = c.pedido_guia_id
			inner join procedimentos d on c.exame_id = d.procedimentos_id
			where a.data between '" . inverteData($start) . "' and '" . inverteData($end) . "' and b.origem = '" . ORIGEM_CONFIG . "' and c.situacao = 'Liberado' group by 1";
            $sthRel = pg_query($sql) or die($sql);

            $sql2 = "select count(*) as qtde from pedidos a
			inner join pedido_guia b on a.pedido_id = b.pedido_id
			inner join pedido_item c on b.pedido_guia_id = c.pedido_guia_id
			where a.data between '" . inverteData($start) . "' and '" . inverteData($end) . "' and b.origem = '" . ORIGEM_CONFIG . "' and c.situacao = 'Liberado'";
            $result = pg_query($sql2) or die($sql2);
            $rowCount = pg_fetch_object($result);
            $qtde_atendimentos = $rowCount->qtde;
            $dias = date('d');
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

            include('conexao.php');
            $stmtRel = "select descricao, count(*) as qtde, modalidade_id
																	from itenspedidos a
																	left join atendimentos b on a.atendimento_id = b.transacao
																	left join procedimentos p on p.procedimento_id = a.exame_id
																	where b.dat_cad between '" . inverteData($start) . "' and '" . inverteData($end) . "'
									$where
																	group by 1,3 order by 1
																	";

            $sthRel = pg_query($stmtRel) or die($stmtRel);

            include('conexao.php');
            $stmtRelCont = "select count(*) as qtde
																	from itenspedidos a
																	left join atendimentos b on a.atendimento_id = b.transacao
																	left join procedimentos p on p.procedimento_id = a.exame_id
																	where b.dat_cad between '" . inverteData($start) . "' and '" . inverteData($end) . "'
									$where";
            $sthRelCont = pg_query($stmtRelCont);
            $rowCount = pg_fetch_object($sthRelCont);
            $qtde_atendimentos = $rowCount->qtde;
            $dias = date('d');
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
    <title>FUNEPU | Relatorio de Exames</title>
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
                                                        » </p>relatorio exames
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
                                                <li class="active">Relatorio Exames</li>
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
                                                                        } ?>>Todos</option>
                                                    <?php
                                                    include('conexao.php');
                                                    $stmt = "select p.modalidade_id,m.descricao as modalidade
														from procedimentos p
                                                        left join modalidades m on m.modalidade_id = p.modalidade_id
                                                        where p.modalidade_id in (1,2,3,4,5)
														group by 1,2
														order by 1";
                                                    $sth = pg_query($stmt) or die($stmt);
                                                    while ($row = pg_fetch_object($sth)) {
                                                        echo "<option value=\"" . $row->modalidade_id . "\"";
                                                        if ($row->modalidade_id == $_POST['modalidade']) {
                                                            echo ' selected ';
                                                        }
                                                        echo ">" . $row->modalidade . "</option>";
                                                    } ?>
                                                </select>
                                            </div>
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
                                                        <th>Prioridade</th>
                                                        <th>Quantidade</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    include('conexao.php');
                                                    while ($rowRel = pg_fetch_object($sthRel)) { ?>
                                                        <tr>
                                                            <td><?php echo $rowRel->descricao; ?></td>
                                                            <td><?php echo str_pad($rowRel->qtde, 5, '0', STR_PAD_LEFT); ?></td>
                                                        </tr>
                                                    <?php } ?>
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
            var modalidade = $('#modalidade').val();

            var url = 'relAtendimento.php?start=' + start + '&end=' + end + '&tipo_relatorio=exames&modalidade=' + modalidade;
            window.open(url);
        });
    </script>
</body>

</html>