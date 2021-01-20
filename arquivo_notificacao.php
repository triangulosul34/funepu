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
	$arquivo = $_POST['arquivo'];

	(empty($arquivo)) ?: $where = $where . " arquivo = '$arquivo'";
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
   <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.min.css">
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
                                 <div class="col-md-3">
                                    <div class="form-group"><label for="">Arquivos</label><select name="arquivo"
                                          id="arquivo" class="form-control">
                                          <option value=""></option>
                                          <?php
								  include 'conexao.php';
						  $sql = 'select distinct arquivo from excel_notificacao where controle = 1';
					$result = pg_query($sql) or die($sql);
					while ($row = pg_fetch_object($result)) {
						?>
                                          <option
                                             value="<?= $row->arquivo; ?>"
                                             <?php if ($arquivo == $row->arquivo) {
							echo 'selected';
						} ?>>
                                             <?= $row->arquivo; ?>
                                          </option>
                                          <?php
					} ?>
                                       </select></div>
                                 </div>
                                 <div class="col-md-5 mt-3">
                                    <button type="submit " class="btn btn-info">Abrir Arquivo</button>
                                    <button type="button " class="btn btn-danger" onclick="remove_arquivo()">Excluir
                                       Arquivo</button>
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
									   if (!empty($where)) {
									   	$sql = "SELECT * FROM excel_notificacao WHERE controle = 1 AND $where";
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
   <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.jquery.min.js"></script>
   <script>
      $('#dttable').DataTable({
         "order": []
      });

      function gerar_excel() {
         arquivo = document.getElementById('arquivo').value;
         <?php
if (UNIDADEABV_CONFIG == 'mr') { ?>
         unidade = 2;
         <?php } else { ?>
         unidade = 3;
         <?php } ?>

         if (arquivo) {
            window.location.href = "gerar_excel_notificacao.php?arquivo=" + arquivo + "&unidade=" + unidade;
         }
      }

      function remove_arquivo() {
         arquivo = document.getElementById('arquivo').value;

         $.get('remover_arquivo.php?a=' + arquivo, function(
            dataReturn) {
            window.location.reload();
         });
      }

      $(function() {
         $("#arquivo").chosen({
            no_results_text: "Oops, nothing found!"
         });
      });
   </script>
</body>

</html>