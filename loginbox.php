<!DOCTYPE html>
<html lang="en" class="loading">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="Apex admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
  <meta name="keywords" content="admin template, Apex admin template, dashboard template, flat admin template, responsive admin template, web app">
  <meta name="author" content="PIXINVENT">
  <title>FUNEPU | Login</title>
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
  <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
</head>

<style>
  body {
    background-image: url('app-assets/img/gallery/login.jpeg');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
  }

  .sh {
    -moz-box-shadow: 0 0 10px;
    -webkit-box-shadow: 0 0 10px;
    box-shadow: 0 0 10px;
  }
</style>

<body data-col="1-column" class=" 1-column  blank-page">
  <div class="wrapper">
    <div class="main-panel">
      <div class="main-content">
        <div class="content-wrapper">
          <section>
            <div class="container-fluid">
              <div class="row full-height-vh m-0">
                <div class="col-12 d-flex align-items-center justify-content-center">
                  <div class="card sh">
                    <div class="card-content">
                      <div class="card-body login-img">
                        <div class="row m-0">
                          <div class="col-lg-6 d-lg-block d-none py-2 text-center align-middle">
                            <img src="app-assets/img/gallery/upa24.png" class="mt-3" alt="" width="280" height="180">
                          </div>
                          <div class="col-lg-6 col-md-12 bg-white px-4 pt-3">
                            <form name='formulario' method='POST' action='checkloginbox.php'>
                              <h4 class="mb-2 card-title">CONECTE-SE</h4>
                              <p class="card-text mb-3">
                                Bem-vindo - Entre com suas credenciais.
                              </p>
                              <input type="text" class="form-control mb-2" id="myusername" name="myusername" placeholder="Usuário">
                              <input type="password" class="form-control mb-2" id="mypassword" name="mypassword" placeholder="Senha">
                              <select name='box' class="form-control mb-3" id="box" style="display: none;">
                                <option value="">Selecione o consultório</option>
                                <?php
								include 'conexao.php';
								$stmt = "Select * from boxes where situacao='0' order by descricao";
								$sth = pg_query($stmt) or die($stmt);
								while ($row = pg_fetch_object($sth)) {
									echo '<option value="' . $row->box_id . '"';
									if ($row->box_id == $box) {
										echo 'selected';
									}
									echo '>' . $row->descricao . '</option>';
								}
								?>
                              </select>
                              <input type="hidden" name="conf_consultorio" id="conf_consultorio">
                              <div class="row">
                                <div class='col-md-6' align='left'>
                                  <button class="btn btn-primary" type='submit' onClick="return valida()">Acessar</button>
                                </div>
                                <div class='col-md-6' align='right'><img src="app-assets/img/gallery/logotc.png" alt="" width="40" height="40"></div>
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
          </section>
        </div>
      </div>
    </div>
  </div>
  <script src="app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script>
  <script src="app-assets/vendors/js/core/popper.min.js" type="text/javascript"></script>
  <script src="app-assets/vendors/js/core/bootstrap.min.js" type="text/javascript"></script>
  <script src="app-assets/vendors/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
  <script src="app-assets/vendors/js/prism.min.js" type="text/javascript"></script>
  <script src="app-assets/vendors/js/jquery.matchHeight-min.js" type="text/javascript"></script>
  <script src="app-assets/vendors/js/screenfull.min.js" type="text/javascript"></script>
  <script src="app-assets/vendors/js/pace/pace.min.js" type="text/javascript"></script>
  <script src="app-assets/js/app-sidebar.js" type="text/javascript"></script>
  <script src="app-assets/js/notification-sidebar.js" type="text/javascript"></script>
  <script src="app-assets/js/customizer.js" type="text/javascript"></script>
  <script>
    function valida() {
      if (document.form1.myusername.value == '') {
        sweetAlert("Informe o usuário", "", "warning");
        return false;
      }
      if (document.form1.mypassword.value == '') {
        sweetAlert("Informe a senha", "", "warning");
        return false;
      }
      if (document.form1.box.value == '') {
        sweetAlert("Informe o consultório onde será feito o atendimento!", "", "warning");
        return false;
      }
    }

    document.getElementById("box").value = localStorage.getItem("consultorio");
    document.getElementById("conf_consultorio").value = localStorage.getItem("consultorio");
  </script>
</body>

</html>