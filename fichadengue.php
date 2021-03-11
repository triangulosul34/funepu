<?php
include 'tsul_ssl.php';
 function inverteData($data)
 {
 	if (count(explode('/', $data)) > 1) {
 		return implode('-', array_reverse(explode('/', $data)));
 	} elseif (count(explode('-', $data)) > 1) {
 		return implode('/', array_reverse(explode('-', $data)));
 	}
 }
if ($_POST['idexcluir']) {
	include 'conexao.php';
	$sql = 'delete from ficha_dengue where ficha_dengue_id = ' . $_POST['idexcluir'];
	$result = pg_query($sql) or die($sql);
} ?>
<!DOCTYPE html>
<html lang="pt-br" class="loading">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="tsul" content="tsul">
  <meta name="keywords" content="tsul">
  <meta name="author" content="TSUL">
  <title>FUNEPU | Ficha Dengue</title>
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
                            » </p>Fichas Dengue e Chikungunya
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
                        <li class="active">Dengue e Chikungunya</li>
                      </ol>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-content">
                <div class="card-body">
                  <form action="#" method="post" id="dengue_chikungunya">
                    <div class="row">
                      <div class="col-md-9">
                        <div class="form-group">
                          <label for="">Nome</label><input type="text" name="nome" id="nome" class="form-control"
                            value="<?= $_POST['nome']; ?>">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <label for="">Data de Nascimento</label><input type="date" name="data_nascimento"
                          id="data_nascimento" class="form-control"
                          value="<?= $_POST['data_nascimento']; ?>">
                      </div>
                      <div class="col col-lg-3">
                        <label class="control-label" for="inputBasicFirstName">Data de Notificação</label>
                        <input type="date" class="form-control text-center" name="start" id="start"
                          value="<?= $_POST['start']; ?>" />
                      </div>

                      <div class="col col-lg-3 text-center">
                        <label class="control-label" for="inputBasicFirstName">.</label>
                        <input type="date" class="form-control text-center" name="end"
                          value="<?= $_POST['end']; ?>">
                      </div>
                      <div class="col-md-1 mt-3"><button type="submit" class="btn btn-primary">Pesquisar</button></div>
                    </div>
                    <div class="row mt-5">
                      <div class="col-md-12" align="center">
                        <a href="form_ficha_dengue.php" type="button" class="btn btn-success">Nova Ficha</a>
                      </div>
                    </div>
                    <div class="row mt-3">
                      <div class="col-md-12">
                        <table class="table" id="data_table">
                          <thead>
                            <tr>
                              <th>Nome</th>
                              <th>Data de Nascimento</th>
                              <th>Ação</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
														include 'conexao.php';
														$sql = 'SELECT * FROM ficha_dengue a inner join pessoas b on a.pessoa_id = b.pessoa_id WHERE ';
														if ($_POST['nome']) {
															($where) ? $where = $where . " AND nome like '" . ts_codifica($_POST['nome']) . "'" : $where = "nome like '" . ts_codifica($_POST['nome']) . "'";
														}
														if ($_POST['data_nascimento']) {
															($where) ? $where = $where . " AND data_nascimento = '{$_POST['data_nascimento']}'" : $where = "data_nascimento = '{$_POST['data_nascimento']}'";
														}
														if ($_POST['start']) {
															($where) ? $where = $where . " AND data_notificacao BETWEEN '" . inverteData($_POST['start']) . "'" : $where = "data_notificacao BETWEEN '" . inverteData($_POST['start']) . "'";
															if ($_POST['end']) {
																$where = $where . " AND '" . inverteData($_POST['end']) . "'";
															} else {
																$where = $where . " AND '" . date('d-m-Y') . "'";
															}
														}
														if (!$where) {
															$where = "data_form ='" . date('Y-m-d') . "'";
														}
														$sql = $sql . " $where";
														$result = pg_query($sql) or die($sql);
														while ($row = pg_fetch_object($result)) {
															?>
                            <tr>
                              <td><?= ts_decodifica($row->nome); ?>
                              </td>
                              <td><?= inverteData($row->data_nascimento); ?>
                              </td>
                              <td>
                                <a href="pdf_form_ficha_dengue.php?id=<?= $row->ficha_dengue_id; ?>"
                                  class="m-1"><i class="fas fa-print"></i></a>
                                <a href="form_ficha_dengue.php?id=<?= $row->ficha_dengue_id; ?>"
                                  class="m-1 danger"><i class="fas fa-pencil-alt"></i></a>
                                <?php if ($perfil == '06') { ?>
                                <button type="button" name="excluir_formulario"
                                  onclick="excluir(<?= $row->ficha_dengue_id; ?>)"
                                  class="btn btn-sm btn-warning"><i class="fas fa-trash"></i></button>
                                <?php } ?>
                              </td>
                            </tr>
                            <?php
														} ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <input type="text" name="idexcluir" id="idexcluir" value="">
                  </form>
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
    function excluir(a) {
      document.getElementById('idexcluir').value = a;
      document.getElementById("dengue_chikungunya").submit();
    }
  </script>
</body>

</html>