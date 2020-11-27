<!DOCTYPE html>
<html lang="pt-br" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="tsul" content="tsul">
    <meta name="keywords" content="tsul">
    <meta name="author" content="TSUL">
    <title>FUNEPU | Ações usuario</title>
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
                                                        » </p>Ações Médicas
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
                                                <li class="active">Ações do Usuário</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" method="POST">
                                        <div class="row">
                                            <div class="col col-lg-3">
                                                <label class="control-label" for="inputBasicFirstName">Data Ínicial</label>
                                                <input type="date" class="form-control text-center" name="start" id="start" value="<?php echo $_POST['start']; ?>" />
                                            </div>
                                            <div class="col col-lg-3 text-center">
                                                <label class="control-label" for="inputBasicFirstName">Data Final</label>
                                                <input type="date" class="form-control text-center" name="end" id="end" value="<?php echo $_POST['end']; ?>">
                                            </div>
                                            <div class="col col-lg-3 text-center">
                                                <label class="control-label" for="inputBasicFirstName">Profissional</label>
                                                <select name="medico" id="medico" class="form-control">
                                                    <option value=""></option>
                                                    <?php
                                                        include('conexao.php');
                                                        $sql = "select * from pessoas where tipo_pessoa = 'Medico Laudador' order by nome";
                                                        $result = pg_query($sql);
                                                        while ($row = pg_fetch_object($result)) {
                                                            ?>
                                                            <option
                                                                value="<?= $row->username; ?>"
                                                                <?php if ($_POST['medico'] == $row->username) {
                                                                echo "selected";
                                                            } ?>>
                                                                <?= $row->nome; ?>
                                                            </option>
                                                            <?php
                                                        } ?>
                                                </select>
                                            </div>
                                            <div class="col-3" align="center"><label class="control-label" for="inputBasicFirstName">Ação</label><br>
                                                <button type="submit" name="pesquisa" value="semana" class="btn btn-primary">Pesquisar</button></div>
                                        </div>
                                        <div class="col-12">
                                            <table id="data_table" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Usuário</th>
                                                        <th>Ação</th>
                                                        <th>Atendimento</th>
                                                        <th>Data</th>
                                                        <th>Hora</th>
                                                        <th>IP</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Usuário</th>
                                                        <th>Ação</th>
                                                        <th>Atendimento</th>
                                                        <th>Data</th>
                                                        <th>Hora</th>
                                                        <th>IP</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    $hoje = date('d/m/Y');

                                                    $end = $_POST['end'];
                                                    $start = $_POST['start'];
                                                    $medico = $_POST['medico'];
                                                    



                                                    $where = "";
                                                    if ($end != '' or $start != '') {
                                                        $where = " where l.data between '$start' and '$end' ";
                                                    }
                                                    if ($medico != '' ) {
                                                        $where = $where." and usuario= '$medico' ";
                                                    }


                                                    include('conexao.php');
                                                    $stmt = "SELECT l.*, p.nome FROM logs l
						left join pessoas p on p.username = l.usuario";
                                                    if ($where == '') {
                                                        $stmt = $stmt . " where l.data = '$hoje' ";
                                                    } else {
                                                        $stmt = $stmt . ' ' . $where;
                                                    }
                                                    $stmt = $stmt . "order by log_id, l.data desc,l.hora desc";
                                                    $sth = pg_query($stmt) or die($stmt);
                                                    //echo $stmt;
                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                        <tr>
                                                            <td><?php echo $row->log_id; ?></td>
                                                            <td><?php echo $row->nome; ?></td>
                                                            <td><?php echo $row->tipo_acao; ?></td>
                                                            <td><?php echo $row->atendimento_id; ?></td>
                                                            <td><?php echo date('d/m/Y', strtotime($row->data)); ?></td>
                                                            <td><?php echo date('H:i', strtotime($row->hora)); ?></td>
                                                            <td><?php echo $row->ip; ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table> 
                                        </div>
                                        <div class="col-md-12" align="center"><button id="imprimirelatorio"
                                                    class="btn btn-success">Imprimir</button></div>
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
    <script>
        $("#imprimirelatorio").click(function(event) {
            var profissional = $("#medico").val();
            var start = $("#start").val();
            var end   = $("#end").val();

            var url = 'relacoesmed.php?start=' + start + '&end=' + end + '&medico=' + profissional;
            window.open(url);
        });
    </script>
</body>

</html>