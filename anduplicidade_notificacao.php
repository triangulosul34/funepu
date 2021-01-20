<?php

include 'Config.php';

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

$where = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$nome = strtoupper($_POST['nome']);
	$data_notificacao_inicial = $_POST['data_notificacao_inicial'];
	$data_notificacao_final = $_POST['data_notificacao_final'];
	$data_secretaria_inicial = $_POST['data_secretaria_inicial'];
	$data_secretaria_final = $_POST['data_secretaria_final'];

	(empty($nome)) ?: $where = $where . " AND nome = '$nome'";
	if ($data_notificacao_inicial) {
		$where = $where . " AND data_notificacao >= '" . inverteData($data_notificacao_inicial) . "'";

		if ($data_notificacao_final) {
			$where = $where . " AND data_notificacao <= '" . inverteData($data_notificacao_final) . "'";
		} else {
			$where = $where . " AND data_notificacao <= '" . inverteData($data_notificacao_inicial) . "'";
		}
	}

	if ($data_secretaria_inicial) {
		$where = $where . " AND data_secretaria >= '" . inverteData($data_secretaria_inicial) . "'";

		if ($data_secretaria_final) {
			$where = $where . " AND data_secretaria <= '" . inverteData($data_secretaria_final) . "'";
		} else {
			$where = $where . " AND data_secretaria <= '" . inverteData($data_secretaria_inicial) . "'";
		}
	}
}
(!empty($where)) ?: $where = $where . " AND data_secretaria = '" . date('d/m/Y') . "'";
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
   <title>FUNEPU | Analise de Duplicidades</title>
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
   <!-- <?php include 'menu.php'; ?> -->
   <?php include 'header2.php'; ?>
   <div class="main-panel">
      <div class="">
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
                                          » </p>Analise de Duplicidades
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
                                    <li class="active">Analise Duplicidade</li>
                                 </ol>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="card-content">
                        <div class="card-body">
                           <form method="post" action="#">
                              <div class="row">
                                 <div class="col-md-4">
                                    <div class="form-group"><label for="">Nome</label><input type="text"
                                          class="form-control" name="nome" id="nome"
                                          value="<?= $nome; ?>">
                                    </div>
                                 </div>
                                 <div class="col-md-5">
                                    <div class="form-group"><label for="">Data Notificacao - Periodo</label>
                                       <div class="row">
                                          <div class="col-md-6">
                                             <input type="date" class="form-control" name="data_notificacao_inicial"
                                                id="data_notificacao_inicial"
                                                value="<?= $data_notificacao_inicial; ?>">
                                          </div>
                                          <div class="col-md-6">
                                             <input type="date" class="form-control" name="data_notificacao_final"
                                                id="data_notificacao_final"
                                                value="<?= $data_notificacao_final; ?>">
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-5">
                                    <div class="form-group"><label for="">Data Secretaria - Periodo</label>
                                       <div class="row">
                                          <div class="col-md-6">
                                             <input type="date" class="form-control" name="data_secretaria_inicial"
                                                id="data_secretaria_inicial"
                                                value="<?= $data_secretaria_inicial; ?>">
                                          </div>
                                          <div class="col-md-6">
                                             <input type="date" class="form-control" name="data_secretaria_final"
                                                id="data_secretaria_final"
                                                value="<?= $data_secretaria_final; ?>">
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-2 mt-3">
                                    <button type="submit " class="btn btn-info">Analisar</button>
                                 </div>
                              </div>
                           </form>
                           <div class="row mt-3">
                              <div class="col-md-12">
                                 <table class="table" id="dttable">
                                    <thead>
                                       <tr>
                                          <th scope="col">Nome</th>
                                          <th scope="col">Data de Nascimento</th>
                                          <th scope="col">Nome da Mãe</th>
                                          <th scope="col">CPF</th>
                                          <th scope="col">Data de Notificacao</th>
                                          <th scope="col">Data Env. Secretaria</th>
                                          <th scope="col">Tipo</th>
                                          <th scope="col">Resultado</th>
                                          <th scope="col">Arquivo</th>
                                          <th scope="col">Acao</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <?php
							  $cpf = [];
							  $pesquisa = 0;
									   if (UNIDADEABV_CONFIG == 'mr') {
									   	include 'conexao.php';
									   } else {
									   	include 'conexao2.php';
									   }
									   if (UNIDADEABV_CONFIG == 'mr') {
									   	$un = CON2;
									   } else {
									   	$un = CON1;
									   }
									   $sql = "SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados 
                                                   FROM excel_notificacao 
                                                   WHERE controle = 1 $where
                                                   UNION 
                                                   SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados
                                                   FROM dblink('host=localhost
                                                               user=postgres
                                                               password=tsul2020## 
                                                               dbname=$un', 'SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados
                                                                                 FROM excel_notificacao 
                                                                                 WHERE controle = 1 " . str_replace("'", "''", $where) . "') 
                                                                                 AS a(nome character varying,data_nascimento character varying,nome_mae character varying,cpf character varying,data_notificacao character varying,data_secretaria character varying,tipo character varying,resultados character varying)";
									   $result = pg_query($sql) or die($sql);
									   while ($row = pg_fetch_object($result)) {
									   	if ($row->cpf != '') {
									   		$wherecpf = $where;
									   		$wherecpf = $wherecpf . " AND cpf = '$row->cpf' AND tipo = '$row->tipo'";
									   		$sqlcpf = "SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, count(cpf),array_to_string(array_agg(excel_notificacao),',') excel_notificacao, array_to_string(array_agg(arquivo),',') arquivo 
                                    FROM (
                                       SELECT excel_notificacao,nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, arquivo 
                                       FROM excel_notificacao 
                                       WHERE controle = 1 $wherecpf 
                                       UNION 
                                       SELECT excel_notificacao,nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, arquivo 
                                       FROM dblink('host=localhost
                                                    user=postgres 
                                                    password=tsul2020## 
                                                    dbname=upa-002', 'SELECT excel_notificacao,nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, arquivo 
                                                    FROM excel_notificacao WHERE controle = 1 " . str_replace("'", "''", $wherecpf) . "') AS a(excel_notificacao integer,nome character varying,data_nascimento character varying,nome_mae character varying,cpf character varying,data_notificacao character varying,data_secretaria character varying,tipo character varying,resultados character varying, arquivo character varying)) t1 group by 1,2,3,4,5,6,7,8 having count(cpf) > 1";
									   		$resultcpf = pg_query($sqlcpf) or die($sqlcpf);
									   		while ($rowcpf = pg_fetch_object($resultcpf)) {
									   			$excel_notificacao = explode(',', $rowcpf->excel_notificacao);
									   			$arquivo = explode(',', $rowcpf->arquivo);
									   			echo '<tr id=' . $excel_notificacao[0] . '>';
									   			echo '<td>' . utf8_decode($rowcpf->nome) . '</td>';
									   			echo '<td>' . $rowcpf->data_nascimento . '</td>';
									   			echo '<td>' . utf8_decode($rowcpf->nome_mae) . '</td>';
									   			echo '<td>' . $rowcpf->cpf . '</td>';
									   			echo '<td>' . $rowcpf->data_notificacao . '</td>';
									   			echo '<td>' . $rowcpf->data_secretaria . '</td>';
									   			echo '<td>' . utf8_decode($rowcpf->tipo) . '</td>';
									   			echo '<td>' . $rowcpf->resultados . '</td>';
									   			echo '<td>' . $arquivo[0] . '</td>';
									   			echo '<td><button type="button" class="btn btn-danger btn-sm" onclick="del(\'' . $excel_notificacao[0] . '\',\'' . $arquivo[0] . '\')"><i class="far fa-calendar-times"></i></button></td>';
									   			echo '</tr id=' . $excel_notificacao[1] . '>';
									   			echo '<tr>';
									   			echo '<td>' . utf8_decode($rowcpf->nome) . '</td>';
									   			echo '<td>' . $rowcpf->data_nascimento . '</td>';
									   			echo '<td>' . utf8_decode($rowcpf->nome_mae) . '</td>';
									   			echo '<td>' . $rowcpf->cpf . '</td>';
									   			echo '<td>' . $rowcpf->data_notificacao . '</td>';
									   			echo '<td>' . $rowcpf->data_secretaria . '</td>';
									   			echo '<td>' . utf8_decode($rowcpf->tipo) . '</td>';
									   			echo '<td>' . $rowcpf->resultados . '</td>';
									   			echo '<td>' . $arquivo[1] . '</td>';
									   			echo '<td><button type="button" class="btn btn-danger btn-sm" onclick="del(\'' . $excel_notificacao[1] . '\',\'' . $arquivo[1] . '\')"><i class="far fa-calendar-times"></i></button></td>';
									   			echo '</tr>';
									   			$pesquisa = 1;
									   		}
									   	}
									   	if ($pesquisa == 0) {
									   		$wherend = $where;
									   		$wherend = $wherend . " AND nome = '$row->nome' AND nome_mae = '$row->nome_mae' AND tipo = '$row->tipo'";
									   		$sqlnd = "SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, count(cpf),array_to_string(array_agg(excel_notificacao),',') excel_notificacao, array_to_string(array_agg(arquivo),',') arquivo 
                                    FROM (
                                       SELECT excel_notificacao,nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, arquivo 
                                       FROM excel_notificacao 
                                       WHERE controle = 1 $wherend 
                                       UNION 
                                       SELECT excel_notificacao,nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, arquivo 
                                       FROM dblink('host=localhost
                                                    user=postgres 
                                                    password=tsul2020## 
                                                    dbname=upa-002', 'SELECT excel_notificacao,nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados, arquivo 
                                                    FROM excel_notificacao WHERE controle = 1 " . str_replace("'", "''", $wherend) . "') AS a(excel_notificacao integer,nome character varying,data_nascimento character varying,nome_mae character varying,cpf character varying,data_notificacao character varying,data_secretaria character varying,tipo character varying,resultados character varying, arquivo character varying)) t1 group by 1,2,3,4,5,6,7,8 having count(cpf) > 1";
									   		$resultnd = pg_query($sqlnd) or die($sqlnd);
									   		while ($rownd = pg_fetch_object($resultnd)) {
									   			$excel_notificacao = explode(',', $rownd->excel_notificacao);
									   			$arquivo = explode(',', $rownd->arquivo);
									   			echo '<tr id=' . $excel_notificacao[0] . '>';
									   			echo '<td>' . utf8_decode($rownd->nome) . '</td>';
									   			echo '<td>' . $rownd->data_nascimento . '</td>';
									   			echo '<td>' . utf8_decode($rownd->nome_mae) . '</td>';
									   			echo '<td>' . $rownd->cpf . '</td>';
									   			echo '<td>' . $rownd->data_notificacao . '</td>';
									   			echo '<td>' . $rownd->data_secretaria . '</td>';
									   			echo '<td>' . utf8_decode($rownd->tipo) . '</td>';
									   			echo '<td>' . $rownd->resultados . '</td>';
									   			echo '<td>' . $arquivo[0] . '</td>';
									   			echo '<td><button type="button" class="btn btn-danger btn-sm" onclick="del(\'' . $excel_notificacao[0] . '\',\'' . $arquivo[0] . '\')"><i class="far fa-calendar-times"></i></button></td>';
									   			echo '</tr>';
									   			echo '<tr id=' . $excel_notificacao[1] . '>';
									   			echo '<td>' . utf8_decode($rownd->nome) . '</td>';
									   			echo '<td>' . $rownd->data_nascimento . '</td>';
									   			echo '<td>' . utf8_decode($rownd->nome_mae) . '</td>';
									   			echo '<td>' . $rownd->cpf . '</td>';
									   			echo '<td>' . $rownd->data_notificacao . '</td>';
									   			echo '<td>' . $rownd->data_secretaria . '</td>';
									   			echo '<td>' . utf8_decode($rownd->tipo) . '</td>';
									   			echo '<td>' . $rownd->resultados . '</td>';
									   			echo '<td>' . $arquivo[1] . '</td>';
									   			echo '<td><button type="button" class="btn btn-danger btn-sm" onclick="del(\'' . $excel_notificacao[1] . '\',\'' . $arquivo[1] . '\')"><i class="far fa-calendar-times"></i></button></td>';
									   			echo '</tr>';
									   		}
									   	}
									   	$pesquisa = 0;
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
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script>
         $('#dttable').DataTable({
            "order": []
         });

         function del(a, b) {
            $.get('deletarnotificacao.php?a=' + a + "&b=" + b, function(
               dataReturn) {

            });

            document.getElementById(a).remove();
         }
      </script>
</body>

</html>