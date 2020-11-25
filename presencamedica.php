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
$start          = '';
$end          = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['gerarrelatorio'])) {
        $start = $_POST['start'];
        $end   = $_POST['end'];
        $medico = $_POST['medico'];
        $where = '';

        if ($medico) {
            $where = " and pessoa_id = $medico";
        }

        include('conexao.php');
        $stmt1 = "select nome, p.pessoa_id, count(*) as qtd,a.med_atendimento, 
					CASE
					WHEN a.hora_destino >= '00:00' and a.hora_destino <'07:00'   THEN '00h a 06:59'
					WHEN a.hora_destino >= '07:00' and a.hora_destino <'13:00'   THEN '07h a 12:59'
					WHEN a.hora_destino >= '13:00' and a.hora_destino <'19:00'   THEN '13h a 18:59'
					WHEN a.hora_destino >= '19:00' and a.hora_destino <'00:00' 	 THEN '19h a 23:59'
					WHEN a.hora_destino >= '19:00' and a.hora_destino >= '00:00' THEN '19h e passou das 23:59'
					END as HORAS FROM atendimentos a 
					left join pessoas p on p.username = a.med_atendimento
					WHERE nome is not null AND status = 'Atendimento Finalizado' $where and dat_cad between '$start' and '$end'								
					group by 1,2,4,5
					order by nome";

        $sth1 = pg_query($stmt1);

        include('conexao.php');
        $stmt2 = "select nome, p.pessoa_id, count (*) as qtd, e.usuario,
					CASE
					WHEN e.hora >= '00:00' and e.hora < '07:00' THEN '00h a 06:59'
					WHEN e.hora >= '07:00' and e.hora < '13:00' THEN '07h a 12:59'
					WHEN e.hora >= '13:00' and e.hora < '19:00' THEN '13h a 18:59'
					WHEN e.hora >= '19:00' and e.hora < '00:00' THEN '19h a 23:59'
					END as horas FROM evolucoes e
					left join pessoas p on p.username = e.usuario
					where nome is not null and data between '$start' and '$end' $where and p.pessoa_id not in ($leo)					
					group by 1,2,4,5
					order by nome";

        $sth2 = pg_query($stmt2);

        include('conexao.php');
        $stmt3 = "select p.nome,l.usuario,p.perfil, min(l.hora) as login from logs l
					left join pessoas p on p.username = l.usuario
					left join atendimentos a on a.med_atendimento = p.username  
					where data between '$start' and '$end' $where and p.perfil = '03'
					group by 1,2,3";

        $sth3 = pg_query($stmt3);
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
    <title>FUNEPU | Presença Medica</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.css" />
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
                                                    <p
                                                        style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                        » </p>Relatório de Presença Medica
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
                                                <li class="active">Presença Medica</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <h1 align='center'>UPA Parque do Mirante</h1>
                                                <h2 style="text-align: center">Atendimento Externo</h2>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col col-md-6">
                                                        <label class="control-label" for="inputBasicFirstName">Data
                                                            Ínicial</label>
                                                        <input type="date" class="form-control text-center" name="start"
                                                            id="start"
                                                            value="<?php echo $_POST['start']; ?>" />
                                                    </div>
                                                    <div class="col col-md-6 text-center">
                                                        <label class="control-label" for="inputBasicFirstName">Data
                                                            Final</label>
                                                        <input type="date" class="form-control text-center" name="end"
                                                            id="end"
                                                            value="<?php echo $_POST['end']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">Medico</label>
                                                <select name="medico" id="medico" class="form-control">
                                                    <option value=""></option>
                                                    <?php
                                                include('conexao.php');
                                                $sql = "select * from pessoas where tipo_pessoa = 'Medico Laudador'";
                                                $result = pg_query($sql);
                                                while ($row = pg_fetch_object($result)) {
                                                    ?>
                                                    <option
                                                        value="<?= $row->pessoa_id; ?>"
                                                        <?php if ($_POST['medico'] == $row->pessoa_id) {
                                                        echo "selected";
                                                    } ?>>
                                                        <?= $row->nome; ?>
                                                    </option>
                                                    <?php
                                                } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <button type="submit" name="gerarrelatorio" class="btn btn-primary"
                                                    style="width:100%">Gerar Dados</button>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="counter text-left">
                                                    <p class="font-size-20 blue-grey-700">Atendimento por Médico - <span
                                                            class="font-size-18">
                                                            <?php echo inverteData($_POST['end']); ?></span>
                                                    </p>
                                                    <?php

                                                    $horaAtual = date('H');
                                                    $datainicio = $_POST['start'];
                                                    $datafinal = $_POST['end'];

                                                    include('conexao.php');
                                                    $stmt = "	SELECT count(*) as qtd  
															FROM atendimentos a
                                                            inner join pessoas b on a.med_atendimento = b.username 
															WHERE dat_cad between '$datainicio' and '$datafinal' $where AND status = 'Atendimento Finalizado' 
															and med_atendimento is not null";

                                                    $sth = pg_query($stmt);
                                                    $rows = pg_fetch_object($sth);
                                                    ?>

                                                    <div class="counter-number-group">
                                                        <span class="counter-number red-600"><?php echo $rows->qtd; ?></span>
                                                        <span class="counter-number-related red-600">Atendimentos</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="counter text-left">
                                                    <div class="counter-label">Média</div>
                                                    <div class="counter-number-group">
                                                        <span class="counter-number"><?php echo number_format($rows->qtd / $horaAtual, 2, ',', '.'); ?></span>
                                                        <span class="counter-number-related">Atendimento/Hora</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <table class="table">
                                                    <?php

                                                    $i = 0;
                                                    $meuarray = array();

                                                    include('conexao.php');
                                                    while ($row = pg_fetch_object($sth1)) { ?>

                                                    <?php $meuarray[$i++] = $row->pessoa_id; ?>
                                                    <tr>
                                                        <td><?php echo $row->nome ?>
                                                        </td>
                                                        <td><?php echo $row->horas ?>
                                                        </td>
                                                        <td><?php echo str_pad($row->qtd, 4, '0', STR_PAD_LEFT) ?>
                                                        </td>
                                                    </tr>


                                                    <?php }

                                                    foreach ($meuarray as $value) {
                                                        if ($leo == '') {
                                                            $leo .= $value;
                                                        } else {
                                                            $leo .= ',' . $value;
                                                        }
                                                    }
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row m-2">
                                            <div class="col-md-12">
                                                <h1 style="text-align: center">Atendimento Interno</h1>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="counter text-left">
                                                    <p class="font-size-20 blue-grey-700">Atendimento por Médico - <span
                                                            class="font-size-18">
                                                            <?php echo inverteData($_POST['end']); ?></span>
                                                    </p>
                                                    <?php
                                                    $horaAtual = date('H');
                                                    $datainicio = $_POST['start'];
                                                    $datafinal = $_POST['end'];

                                                    include('conexao.php');
                                                    $stmt = "	SELECT count(*) as qtd  
														FROM evolucoes a
                                                        inner join pessoas b on b.username = a.usuario 
														WHERE data between '$datainicio' and '$datafinal' $where
														and usuario is not null";

                                                    $sth = pg_query($stmt);
                                                    $rows = pg_fetch_object($sth);
                                                    ?>

                                                    <div class="counter-number-group">
                                                        <span class="counter-number red-600"><?php echo $rows->qtd; ?></span>
                                                        <span class="counter-number-related red-600">Atendimentos</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="counter text-left">
                                                    <div class="counter-label">Média</div>
                                                    <div class="counter-number-group">
                                                        <span class="counter-number"><?php echo number_format($rows->qtd / $horaAtual, 2, ',', '.'); ?></span>
                                                        <span class="counter-number-related">Atendimento/Hora</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table mt-3">
                                                    <?php
                                                    include('conexao.php');
                                                    $stmt2 = "select nome, p.pessoa_id, count (*) as qtd, e.usuario,
													CASE
													WHEN e.hora >= '00:00' and e.hora < '07:00' THEN '00h a 06:59'
													WHEN e.hora >= '07:00' and e.hora < '13:00' THEN '07h a 12:59'
													WHEN e.hora >= '13:00' and e.hora < '19:00' THEN '13h a 18:59'
													WHEN e.hora >= '19:00' and e.hora < '00:00' THEN '19h a 23:59'
													END as horas FROM evolucoes e
													left join pessoas p on p.username = e.usuario
													where nome is not null and data between '$start' and '$end' $where and p.pessoa_id not in ($leo)				
													group by 1,2,4,5
													order by nome";

                                                    $sth2 = pg_query($stmt2);

                                                    while ($row = pg_fetch_object($sth2)) { ?>
                                                    <tr>
                                                        <td><?php echo $row->nome ?>
                                                        </td>
                                                        <td><?php echo $row->horas ?>
                                                        </td>
                                                        <td><?php echo str_pad($row->qtd, 4, '0', STR_PAD_LEFT) ?>
                                                        </td>
                                                    </tr>
                                                    <?php }
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 m-2">
                                                <h1 style="text-align: center">Login dos Médicos no Sistema</h1>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <table class="table">
                                                    <?php
                                                    include('conexao.php');
                                                    while ($row = pg_fetch_object($sth3)) { ?>
                                                    <tr>
                                                        <td><?php echo $row->nome ?>
                                                        </td>
                                                        <td><?php echo $row->login ?>
                                                        </td>
                                                    </tr>
                                                    <?php }
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" align="center"><button id="imprimirelatorio"
                                                    class="btn btn-success">Imprimir</button></div>
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
    <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.js" type="text/javascript"></script>
    <script>
        $("#imprimirelatorio").click(function(event) {
            var profissional = $("#profissional").val();
            var start = $("#start").val();
            var end = $("#end").val();
            var medico = $("#medico").val();

            var url = 'relpresencamedica.php?start=' + start + '&end=' + end+'&medico='+medico;
            window.open(url);
        });

        $("select").chosen({
            width: "100%"
        });
    </script>
</body>

</html>