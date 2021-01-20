<?php
include 'verifica.php';
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
	$tipo = $_POST['tipo'];
	$resultados = $_POST['resultado'];

	(empty($nome)) ?: $where = $where . " AND nome = '$nome'";
	(empty($tipo)) ?: $where = $where . " AND tipo = '$tipo'";
	(empty($resultados)) ?: $where = $where . " AND resultados = '$resultados'";

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
   <title>FUNEPU | Relatorio notificacao</title>
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
                                          » </p>Relatorio de Notificacoes
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
                                    <li class="active">Relatorio Notificacao</li>
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
                                 <div class="col-md-3">
                                    <div class="form-group"><label for="">Tipo</label><select name="tipo" id="tipo"
                                          class="form-control">
                                          <option value=""></option>
                                          <?php
								  include 'conexao.php';
						  $sql = "select distinct tipo from excel_notificacao where tipo <> ''";
					$result = pg_query($sql) or die($sql);
					while ($row = pg_fetch_object($result)) {
						?>
                                          <option
                                             value="<?= $row->tipo; ?>"
                                             <?php if ($tipo == $row->tipo) {
							echo 'selected';
						} ?>>
                                             <?= $row->tipo; ?>
                                          </option>
                                          <?php
					} ?>
                                       </select></div>
                                 </div>
                                 <div class="col-md-3">
                                    <div class="form-group"><label for="">Resultado</label><select name="resultado"
                                          id="resultado" class="form-control">
                                          <option value=""></option>
                                          <?php
								  include 'conexao.php';
						  $sql = "select distinct resultados from excel_notificacao where resultados <> ''";
					$result = pg_query($sql) or die($sql);
					while ($row = pg_fetch_object($result)) {
						?>
                                          <option
                                             value="<?= $row->resultados; ?>"
                                             <?php if ($resultados == $row->resultados) {
							echo 'selected';
						} ?>>
                                             <?= $row->resultados; ?>
                                          </option>
                                          <?php
					} ?>
                                       </select></div>
                                 </div>
                                 <div class="col-md-4">
                                    <label for="">Unidade</label>
                                    <div class="col-md-12">
                                       <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                             name="option1" value="mr" <?php if (UNIDADEABV_CONFIG == 'mr' && !isset($_POST['option1'])) {
						echo 'checked';
					} elseif (isset($_POST['option1'])) {
						echo 'checked';
					} ?>>
                                          <label class="form-check-label" for="inlineCheckbox1">Mirante</label>
                                       </div>
                                       <div class="form-check form-check-inline">
                                          <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                             name="option2" value="sb" <?php if (UNIDADEABV_CONFIG == 'sb' && !isset($_POST['option2'])) {
						echo 'checked';
					} elseif (isset($_POST['option2'])) {
						echo 'checked';
					} ?>>
                                          <label class="form-check-label" for="inlineCheckbox2">Sao Benedito</label>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-2 mt-3">
                                    <button type="submit " class="btn btn-info">Gerar Relatorio</button>
                                 </div>
                              </div>
                           </form>
                           <div class="row m-2">
                              <div class="col-md-12">
                                 <button type="button" class="btn btn-success" onclick="gerar_excel()">Imprimir</button>
                              </div>
                           </div>
                           <div class="row mt-3">
                              <div class="col-md-12">
                                 <table class="table" id="dttable">
                                    <thead>
                                       <tr>
                                          <th scope="col">Nome</th>
                                          <th scope="col">Data de Notificacao</th>
                                          <th scope="col">Data Env. Secretaria</th>
                                          <th scope="col">Tipo</th>
                                          <th scope="col">Resultado</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <?php
										  if (isset($_POST['option1']) && isset($_POST['option2'])) {
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
										  } elseif (isset($_POST['option1'])) {
										  	if (UNIDADEABV_CONFIG == 'mr') {
										  		include 'conexao.php';
										  	} else {
										  		include 'conexao2.php';
										  	}
										  	$sql = "SELECT * FROM excel_notificacao WHERE controle = 1 $where";
										  	$result = pg_query($sql) or die($sql);
										  } elseif (isset($_POST['option2'])) {
										  	if (UNIDADEABV_CONFIG == 'mr') {
										  		include 'conexao2.php';
										  	} else {
										  		include 'conexao.php';
										  	}
										  	$sql = "SELECT * FROM excel_notificacao WHERE controle = 1 $where";
										  	$result = pg_query($sql) or die($sql);
										  }
								while ($row = pg_fetch_object($result)) {
									?>
                                       <tr>
                                          <td><?= $row->nome; ?>
                                          </td>
                                          <td><?= $row->data_notificacao; ?>
                                          </td>
                                          <td><?= $row->data_secretaria; ?>
                                          </td>
                                          <td><?= $row->tipo; ?>
                                          </td>
                                          <td><?= $row->resultados; ?>
                                          </td>
                                       </tr>
                                       <?php
								}
									   ?>
                                    </tbody>
                                    </thead>
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

      function gerar_excel() {
         nome = document.getElementById('nome').value;
         data_notificacao_inicial = document.getElementById('data_notificacao_inicial').value;
         data_notificacao_final = document.getElementById('data_notificacao_final').value;
         data_secretaria_inicial = document.getElementById('data_secretaria_inicial').value;
         data_secretaria_final = document.getElementById('data_secretaria_final').value;
         tipo = document.getElementById('tipo').value;
         resultado = document.getElementById('resultado').value;

         if (document.getElementById("inlineCheckbox1").checked && document.getElementById("inlineCheckbox2").checked) {
            unidade = 1;
         } else if (document.getElementById("inlineCheckbox1").checked) {
            unidade = 2;
         } else if (document.getElementById("inlineCheckbox2").checked) {
            unidade = 3;
         }

         window.location.href = "gerar_excel_notificacao.php?nome=" + nome + "&data_notificacao_inicial=" +
            data_notificacao_inicial + "&data_notificacao_final=" + data_notificacao_final +
            "&data_secretaria_inicial=" + data_secretaria_inicial + "&data_secretaria_final=" + data_secretaria_final +
            "&tipo=" + tipo + "&resultado=" + resultado + "&unidade=" + unidade;
      }
   </script>
</body>

</html>