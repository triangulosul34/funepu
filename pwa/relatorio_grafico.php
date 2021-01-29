<?php
$dia = date('d') - 1;
$mes = date('m');
$ano = date('Y');
$data = mktime(0, 0, 0, $mes, $dia, $ano);

include('conexao.php');
$stmt = "select count(*) as valor
from atendimentos 
where extract(day from dat_cad)='" . date('d') . "' and extract(month from dat_cad)='" . date('m') . "' and extract(year from dat_cad)='" . date('Y') . "'";
$sth = pg_query($stmt);
$row = pg_fetch_object($sth);
$qtde_atendimentos = $row->valor;

$stmt = "select case when hora_cad between '00:00' and '06:00' then '00:00 - 06:00' when hora_cad between '06:01' and '12:00' then '06:00 - 12:00' when hora_cad between '12:01' and '18:00' then '12:00 - 18:00' when hora_cad between '18:01' and '23:59' then '18:00 - 00:00' end as turno, count(*) as quantidade from atendimentos where extract(day from dat_cad)='" . date('d') . "' and extract(month from dat_cad)='" . date('m') . "' and extract(year from dat_cad)='" . date('Y') . "' group by 1";
$sth = pg_query($stmt);
$atendimento_hoje = pg_fetch_all($sth);

$stmt = "select case when hora_cad between '00:00' and '06:00' then '00:00 - 06:00' when hora_cad between '06:01' and '12:00' then '06:00 - 12:00' when hora_cad between '12:01' and '18:00' then '12:00 - 18:00' when hora_cad between '18:01' and '23:59' then '18:00 - 00:00' end as turno, count(*) as quantidade from atendimentos where dat_cad = '" . date('d/m/Y', $data) . "' group by 1";
$sth = pg_query($stmt);
$atendimento_ontem = pg_fetch_all($sth);

$stmt = "select case when b.sexo = 'M' then 'Masculino' when b.sexo = 'F' then 'Feminino' else 'Nao preenchido' end as sexo,count(*) as valor
from atendimentos a 
left join pessoas b on a.paciente_id=b.pessoa_id 
where extract(month from dat_cad)='" . date('m') . "' and extract(year from dat_cad)='" . date('Y') . "' group by 1";
$sth = pg_query($stmt);
$mensal_sexo = pg_fetch_all($sth);

$stmt = "select case 
when substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)<='10' then '0-10 ANOS'
when substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)>'10' AND substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)<'20' then '11-19 ANOS'
when substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)>'19' AND substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)<'40' then '20-39 ANOS'
when substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)>'39' AND substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)<'60' then '40-59 ANOS'
when substring(cast(age(b.dt_nasc) as varchar) from 0 for 3)>'59' then '60 ANOS +' 
END faixaetaria,count(*) as valor
from atendimentos a 
left join pessoas b on a.paciente_id=b.pessoa_id 
where extract(month from dat_cad)='" . date('m') . "' and extract(year from dat_cad)='" . date('Y') . "' group by 1";
$sth = pg_query($stmt);
$mensal_idade = pg_fetch_all($sth);

$stmt = "SELECT CASE
WHEN upper(a.prioridade) = 'VERMELHO' THEN 7
WHEN upper(a.prioridade) = 'LARANJA' THEN 6
WHEN upper(a.prioridade) = 'AMARELO' THEN 5
WHEN upper(a.prioridade) = 'VERDE' THEN 4
WHEN upper(a.prioridade) = 'AZUL' THEN 3
WHEN upper(a.prioridade) = 'BRANCO' THEN 2
WHEN upper(a.prioridade) = 'ORIENTACOESVACINAS' THEN 1
END AS prioridadem,
COALESCE(prioridade, NULL, 'EX / ORIENTAÇÕES') AS prioridades,
count(a.*) AS valor
FROM atendimentos a
LEFT JOIN pessoas b ON a.paciente_id=b.pessoa_id
WHERE extract(MONTH
   FROM a.dat_cad)='" . date('m') . "'
AND extract(YEAR
   FROM a.dat_cad)='" . date('Y') . "'

GROUP BY prioridade
ORDER BY prioridadem";
$sth = pg_query($stmt) or die($stmt);
$prioridade = pg_fetch_all($sth);

$stmt = "SELECT SUM(to_timestamp(hora_destino, 'HH24:MI')::time - to_timestamp(hora_cad, 'HH24:MI')::time)/count(*) as media_tempo
FROM atendimentos 
where extract(day from dat_cad)='" . date('d') . "' and extract(month from dat_cad)='" . date('m') . "' and extract(year from dat_cad)='" . date('Y') . "' 
and hora_destino is not null and (hora_destino > hora_cad and dat_cad = data_destino)";
$sth = pg_query($stmt);
$tempo_medio = pg_fetch_object($sth);
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
                                                        » </p>Gráficos
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
                                                <li class="active">Relatório Gráficos</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <div class="col-12">
                                                <div class="card bg-danger">
                                                    <div class="card-content">
                                                        <div class="px-3 py-3" onclick="window.location='triagemRecepcao.php';">
                                                            <div class=" media">
                                                                <div class="media-left align-self-center">
                                                                    <i class="far fa-clock white font-large-2 float-left"></i>
                                                                </div>
                                                                <div class="media-body white text-right">
                                                                    <p>Tempo médio de espera hoje</p>
                                                                    <p><?php echo date('d/m/Y'); ?></p>
                                                                    <h1><?php echo substr($tempo_medio->media_tempo, 0, 5); ?></h1>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="card bg-danger">
                                                    <div class="card-content">
                                                        <div class="px-3 py-3" onclick="window.location='triagemRecepcao.php';">
                                                            <div class=" media">
                                                                <div class="media-left align-self-center">
                                                                    <i class="far fa-clock white font-large-2 float-left"></i>
                                                                </div>
                                                                <div class="media-body white text-right">
                                                                    <p>Tempo médio de espera hoje</p>
                                                                    <p><?php echo date('d/m/Y'); ?></p>
                                                                    <h1><?php echo substr($tempo_medio->media_tempo, 0, 5); ?></h1>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <p>Atendimento Diario</p>
                                            <p>Acumulado <?php echo date('m/Y'); ?></p>
                                            <p><i class="fas fa-chart-bar"></i> <?php echo $qtde_atendimentos ?></p>
                                            <canvas id="atendimento_diario" width="400" height="400"></canvas>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-6 col-sm-12">
                                            <p>Atendimento Mensais(por Sexo)</p>
                                            <p>Acumulado <?php echo date('m/Y'); ?></p>
                                            <p><i class="fas fa-chart-bar"></i> <?php echo $qtde_atendimentos ?></p>
                                            <canvas id="mensal_sexo" width="400" height="400"></canvas>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <p>Atendimento Mensais(por Idade)</p>
                                            <p>Acumulado <?php echo date('m/Y'); ?></p>
                                            <p><i class="fas fa-chart-bar"></i> <?php echo $qtde_atendimentos ?></p>
                                            <canvas id="mensal_idade" width="400" height="400"></canvas>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12">
                                            <p>Prioridade Mensal</p>
                                            <p>Acumulado <?php echo date('m/Y'); ?></p>
                                            <p><i class="fas fa-chart-bar"></i> <?php echo $qtde_atendimentos ?></p>
                                            <canvas id="prioridade" width="400" height="400"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
    <script>
        new Chart(document.getElementById("atendimento_diario"), {
            type: "line",
            data: {
                labels: ["00:00 - 06:00", "06:00 - 12:00", "12:00 - 18:00", "18:00 - 00:00"],
                datasets: [{
                    label: "hoje",
                    data: [<?php echo $atendimento_hoje[0]['quantidade']; ?>, <?php echo $atendimento_hoje[1]['quantidade']; ?>, <?php echo $atendimento_hoje[2]['quantidade']; ?>, <?php echo $atendimento_hoje[3]['quantidade']; ?>],
                    fill: false,
                    borderColor: "rgb(0,0,255)",
                    lineTension: 0.5
                }, {
                    label: "ontem",
                    data: [<?php echo $atendimento_ontem[0]['quantidade']; ?>, <?php echo $atendimento_ontem[1]['quantidade']; ?>, <?php echo $atendimento_ontem[2]['quantidade']; ?>, <?php echo $atendimento_ontem[3]['quantidade']; ?>],
                    fill: false,
                    borderColor: "rgb(255,0,0, 0.3)",
                    lineTension: 0.5
                }]
            },
            options: {}
        });

        var ctx = document.getElementById('mensal_sexo').getContext('2d');
        var mensal_sexo = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['<?php echo $mensal_sexo[0]['sexo']; ?>', '<?php echo $mensal_sexo[1]['sexo']; ?>', '<?php echo $mensal_sexo[2]['sexo']; ?>'],
                datasets: [{
                    data: ['<?php echo $mensal_sexo[0]['valor']; ?>', '<?php echo $mensal_sexo[1]['valor']; ?>', '<?php echo $mensal_sexo[2]['valor']; ?>'],
                    backgroundColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(128,128,128, 1)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(128,128,128, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctx = document.getElementById('mensal_idade').getContext('2d');
        var mensal_idade = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['<?php echo $mensal_idade[0]['faixaetaria']; ?>', '<?php echo $mensal_idade[1]['faixaetaria']; ?>', '<?php echo $mensal_idade[2]['faixaetaria']; ?>', '<?php echo $mensal_idade[3]['faixaetaria']; ?>'],
                datasets: [{
                    data: ['<?php echo $mensal_idade[0]['valor']; ?>', '<?php echo $mensal_idade[1]['valor']; ?>', '<?php echo $mensal_idade[2]['valor']; ?>', '<?php echo $mensal_idade[3]['valor']; ?>'],
                    backgroundColor: [
                        'rgba(131,111,255, 1)',
                        'rgba(0,0,255, 1)',
                        'rgba(0,128,0, 1)',
                        'rgba(255,215,0, 1)'
                    ],
                    borderColor: [
                        'rgba(131,111,255, 1)',
                        'rgba(0,0,255, 1)',
                        'rgba(0,128,0, 1)',
                        'rgba(255,215,0, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var ctx = document.getElementById('prioridade').getContext('2d');
        var prioridade = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['<?php echo $prioridade[0]['prioridades']; ?>', '<?php echo $prioridade[1]['prioridades']; ?>', '<?php echo $prioridade[2]['prioridades']; ?>', '<?php echo $prioridade[3]['prioridades']; ?>', '<?php echo $prioridade[4]['prioridades']; ?>', '<?php echo $prioridade[5]['prioridades']; ?>', '<?php echo $prioridade[6]['prioridades']; ?>', '<?php echo $prioridade[7]['prioridades']; ?>'],
                datasets: [{
                    data: ['<?php echo $prioridade[0]['valor']; ?>', '<?php echo $prioridade[1]['valor']; ?>', '<?php echo $prioridade[2]['valor']; ?>', '<?php echo $prioridade[3]['valor']; ?>', '<?php echo $prioridade[4]['valor']; ?>', '<?php echo $prioridade[5]['valor']; ?>', '<?php echo $prioridade[6]['valor']; ?>', '<?php echo $prioridade[7]['valor']; ?>'],
                    backgroundColor: [
                        'rgba(211,211,211, 1)',
                        'rgba(220,220,220, 1)',
                        'rgba(0,0,255, 1)',
                        'rgba(0,128,0, 1)',
                        'rgba(255,215,0, 1)',
                        'rgba(255,165,0, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(128,128,128, 1)'
                    ],
                    borderColor: [
                        'rgba(211,211,211, 1)',
                        'rgba(220,220,220, 1)',
                        'rgba(0,0,255, 1)',
                        'rgba(0,128,0, 1)',
                        'rgba(255,215,0, 1)',
                        'rgba(255,165,0, 1)',
                        'rgba(255,0,0, 1)',
                        'rgba(128,128,128, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
</body>

</html>