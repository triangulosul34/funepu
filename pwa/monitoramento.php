<?php
include 'conexao_laboratorio.php';

$valores = [];
$valores2 = [];
$aux = 0;

$sql = "SELECT count(DISTINCT(a.pessoa_id)) AS quantidade, CASE WHEN (c.urgencia = '1') AND (c.digitado is null) THEN c.urgencia::varchar ELSE c.situacao END AS situacao FROM pedidos a INNER JOIN pedido_guia b ON a.pedido_id = b.pedido_id INNER JOIN pedido_item c ON b.pedido_guia_id = c.pedido_guia_id INNER JOIN procedimentos d on d.procedimentos_id = c.exame_id WHERE CASE WHEN liberado is not null THEN a.data = CURRENT_DATE ELSE a.data >= '" . date('Y-m-d') . "' END AND d.setor not in (32 , 22, 23) AND situacao in ('Coletado', 'Em Analise', 'Digitado', 'Liberado') OR CASE WHEN liberado is not null THEN a.data = CURRENT_DATE ELSE a.data >= '" . date('Y-m-d') . "' END AND d.setor not in (32 , 22, 23) AND situacao is null AND b.origem <> '03' GROUP BY 2";
$result = pg_query($sql) or die($sql);
while ($row = pg_fetch_object($result)) {
	if ($row->situacao == '') {
		$row->situacao = 'Não Coletado';
	}
	if ($row->situacao == '1') {
		$row->situacao = 'Urgencia';
	}
	$valores[$aux] = $row->quantidade . ',' . $row->situacao;
	$aux++;
};

$aux = 0;

$sql = "SELECT count(DISTINCT(a.pessoa_id)) AS quantidade, CASE WHEN (c.urgencia = '1') AND (c.digitado is null) THEN c.urgencia::varchar ELSE c.situacao END AS situacao FROM pedidos a INNER JOIN pedido_guia b ON a.pedido_id = b.pedido_id INNER JOIN pedido_item c ON b.pedido_guia_id = c.pedido_guia_id INNER JOIN procedimentos d on d.procedimentos_id = c.exame_id WHERE CASE WHEN liberado is not null THEN a.data = CURRENT_DATE ELSE a.data >= '" . date('Y-m-d') . "' END and b.origem in ('01','02') and c.prioridade in ('AZUL','VERDE') AND d.setor not in (32 , 22, 23) GROUP BY 2";
$result = pg_query($sql) or die($sql);
while ($row = pg_fetch_object($result)) {
	if ($row->situacao == '') {
		$row->situacao = 'Não Coletado';
	}
	if ($row->situacao == '1') {
		$row->situacao = 'Urgencia';
	}
	$valores2[$aux] = $row->quantidade . ',' . $row->situacao;
	$aux++;
};

$sql = "SELECT avg((c.data_liberacao ||' '|| c.hora_liberacao)::timestamp - (c.data_abertura ||' '|| c.hora_abertura)::timestamp)  AS tempo FROM pedidos a INNER JOIN pedido_guia b ON a.pedido_id = b.pedido_id INNER JOIN pedido_item c ON b.pedido_guia_id = c.pedido_guia_id INNER JOIN procedimentos d on d.procedimentos_id = c.exame_id WHERE c.data_liberacao is not null and b.origem in ('01','02') and c.prioridade in ('AZUL','VERDE') AND d.setor not in (11,12,13,18,40,41,47,32,22,23) and a.data >= '" . date('Y-m-d') . "' and pendente <> 1 ";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);
$tempo_porta = $row->tempo;

$sql = "SELECT avg((c.data_liberacao ||' '|| c.hora_liberacao)::timestamp - (c.data_abertura ||' '|| c.hora_abertura)::timestamp)  AS tempo FROM pedidos a INNER JOIN pedido_guia b ON a.pedido_id = b.pedido_id INNER JOIN pedido_item c ON b.pedido_guia_id = c.pedido_guia_id INNER JOIN procedimentos d on d.procedimentos_id = c.exame_id WHERE c.data_liberacao is not null and a.data >= '" . date('Y-m-d') . "' AND d.setor not in (11,12,13,18,40,41,47,32,22,23) and procedimentos_id not in (821,822) and situacao in ('Coletado', 'Em Analise', 'Digitado', 'Liberado') AND b.origem <> '03'";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);
$tempo_total = $row->tempo;

$sql = "SELECT avg((c.data_abertura ||' '|| c.hora_abertura)::timestamp - (c.data_coleta ||' '|| c.hora_coleta)::timestamp)  AS tempo FROM pedidos a INNER JOIN pedido_guia b ON a.pedido_id = b.pedido_id INNER JOIN pedido_item c ON b.pedido_guia_id = c.pedido_guia_id INNER JOIN procedimentos d on d.procedimentos_id = c.exame_id WHERE c.data_abertura is not null and a.data >= '" . date('Y-m-d') . "' AND d.setor not in (11,12,13,18,40,41,47,32,22,23) and procedimentos_id not in (821,822) AND b.origem <> '03'";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);
$tempo_transporte = $row->tempo;

$sql = "SELECT count(DISTINCT(a.pessoa_id)) AS quantidade FROM pedidos a INNER JOIN pedido_guia b ON a.pedido_id = b.pedido_id INNER JOIN pedido_item c ON b.pedido_guia_id = c.pedido_guia_id  INNER JOIN procedimentos d on d.procedimentos_id = c.exame_id WHERE c.data_liberacao is null and a.data >= '" . date('Y-m-d') . "' AND CURRENT_TIMESTAMP  -  (c.data_coleta ||' '|| c.hora_coleta)::timestamp > '03:00:00' and b.origem in ('01','02') AND d.setor not in (11,12,13,18,40,41,47,32,22,23) and procedimentos_id not in (821,822) and situacao in ('Coletado', 'Em Analise', 'Digitado', 'Liberado')";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);
$quantidade_atraso = $row->quantidade;
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
   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
   <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/pickadate/pickadate.css">
   <script defer src="/your-path-to-fontawesome/js/all.js"></script>
   <!--load all styles -->



</head>
<script>
   window.onload = function() {
      var chart = new CanvasJS.Chart("geral", {
         theme: "light1", // "light1", "light2", "dark1", "dark2"
         exportEnabled: true,
         animationEnabled: true,
         data: [{
            type: "pie",
            startAngle: 25,
            toolTipContent: "<b>{label}</b>: {y}",
            showInLegend: "true",
            legendText: "{label}",
            indexLabelFontSize: 16,
            indexLabel: "{label} - {y}",
            dataPoints: [
               <?php foreach ($valores as $value) {
	$arr = explode(',', $value); ?>
               {
                  y: <?php echo $arr[0]; ?> ,
                  label: <?php echo '"' . $arr[1] . '"'; ?>
               },
               <?php
}
			   ?>
            ]
         }]
      });

      var chart1 = new CanvasJS.Chart("porta", {
         theme: "light1", // "light1", "light2", "dark1", "dark2"
         animationEnabled: true,
         data: [{
            type: "pie",
            startAngle: 25,
            indexLabel: "{label} {y}",
            dataPoints: [
               <?php foreach ($valores2 as $value) {
			   	$arr = explode(',', $value); ?>
               {
                  y: <?php echo $arr[0]; ?> ,
                  label: <?php echo '"' . $arr[1] . '"'; ?>
               },
               <?php
			   }
			   ?>
            ]
         }]
      });
      chart.render();
      chart1.render();
   }
</script>
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
   <!-- <?php include 'header.php'; ?> -->
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
                                          » </p>Monitoramento Laboratorio
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
                                    <li class="active">Monitoramento</li>
                                 </ol>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="card-content">
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-12 col-sm-12 col-12">
                                 <h3><i class="icon-pie-chart"></i><strong> MONITOR</strong> GERAL</h3>
                                 <div id="geral" style="height: 500px; width: 100%;"></div>
                              </div>
                           </div>
                           <div class="row mt-3">
                              <div class="col-md-6 col-sm-6 col-12">
                                 <h3><i class="icon-pie-chart"></i><strong> MONITOR</strong> PACIENTE PORTA</h3>
                                 <div id="porta" style="height: 300px; width: 100%;"></div>
                              </div>
                              <div class="col-md-6 col-sm-6 col-12">
                                 <div class="row">
                                    <div class="col-md-12 col-sm-12 col-12">
                                       <div class="card bg-primary text-white">
                                          <div class="card-header">
                                             <h3><i class="icon-pie-chart"></i><strong> TEMPO</strong> MEDIO TOTAL</h3>
                                          </div>
                                          <div class="card-body">
                                             <div style="height: 99px; width: 100%;">
                                                <h1><?php if ($tempo_total != '') {
			   	echo (new DateTime($tempo_total))->format('H:i:s');
			   } else {
			   } ?>
                                                </h1>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12">
                                       <div class="card bg-success text-white">
                                          <div class="card-header">
                                             <h3><i class="icon-pie-chart"></i><strong> TEMPO</strong> MEDIO PORTA</h3>
                                          </div>
                                          <div class="card-body">
                                             <div style="height: 99px; width: 100%;">
                                                <h1><?php if ($tempo_porta != '') {
			   	echo (new DateTime($tempo_porta))->format('H:i:s');
			   } else {
			   } ?>
                                                </h1>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12">
                                       <div class="card bg-info text-white">
                                          <div class="card-header">
                                             <h3><i class="icon-pie-chart"></i><strong> TEMPO</strong> MEDIO COLETA E
                                                TRANSPORTE
                                             </h3>
                                          </div>
                                          <div class="card-body">
                                             <div style="height: 99px; width: 100%;">
                                                <h1><?php if ($tempo_transporte != '') {
			   	echo (new DateTime($tempo_transporte))->format('H:i:s');
			   } else {
			   } ?>
                                                </h1>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row mt-3">
                              <div class="col-md-12 col-sm-12 col-12">
                                 <div class="card bg-danger text-white">
                                    <div class="card-header">
                                       <h3><i class="icon-pie-chart"></i><strong> EXAMES</strong> EM ATRASO
                                       </h3>
                                    </div>
                                    <div class="card-body">
                                       <h1><?php echo $quantidade_atraso; ?>
                                       </h1>
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
   <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
   <script src="app-assets/js/scripts.js" type="text/javascript"></script>
   <script src="app-assets/js/popover.js" type="text/javascript"></script>
   <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
   <script defer src="/your-path-to-fontawesome/js/all.js"></script>
   <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</body>

</html>