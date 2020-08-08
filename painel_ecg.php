<?php
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}
error_reporting(0);
$menu_grupo = '30';
$menu_sgrupo = '31';
$nome = '';
$dtnasc = '';
$telefone = '';
$mae = '';
$data = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $codigo = $_GET['id'];
    if ($codigo != "") {
        $where = ' where pessoa_id =' . $codigo;
    }

    $data = $_GET['data'];
    if ($data != "") {
        $where = " where dat_cad ='$data'";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data          = $_POST['data'];
    $conveniox  = $_POST['conveniox'];
    $modalidade = $_POST['modalidade'];
    $situacao = $_POST['situacao'];

    $where = "";

    if ($data != "") {
        if ($where != "") {
            $where = $where . " and   b.dat_cad = '$data'";
        } else {
            $where = $where . " where b.dat_cad = '$data'";
        }
    }

    if ($modalidade != "") {
        if ($where != "") {
            $where = $where . " and f.modalidade_id = 4";
        } else {
            $where = $where . " where f.modalidade_id = 4";
        }
    }

    if ($situacao != "") {
        if ($where != "") {
            $where = $where . " and a.situacao = 'Cadastrando'";
        } else {
            $where = $where . " where a.situacao = 'Cadastrando'";
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
    <title>FUNEPU | Painel Ecocardiograma</title>
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
        width: 340px;
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
                                                        » </p>Painel Ecocardiograma
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
                                                <li class="active">Painel Ecocardiograma</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label>Data</label> <input type="text" class="form-control square" id="data" name="data" value="<?php echo date('d/m/Y'); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label>Modalidade</label>
                                                <?php
                                                include('conexao.php');
                                                $stmt = "Select * from modalidades WHERE modalidade_id = 4";
                                                $sth = pg_query($stmt) or die($stmt);
                                                $row = pg_fetch_object($sth);
                                                ?>
                                                <input type="text" class="form-control square" id="modalidade" name="modalidade" value="<?php echo $row->descricao; ?>" readonly>
                                                <input type="hidden" class="form-control" id="modalidade_id" name="modalidade_id" value="<?php echo $row->modalidade_id; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12" id="dados">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width='8%'>Data</th>
                                                        <th width='5%'>AN</th>
                                                        <th width='5%'>ID Pac</th>
                                                        <th width='33%'>Nome</th>
                                                        <th>Data de Nascimento</th>
                                                        <th width='25%'>Solicitante</th>
                                                        <th width='32%'>Descricao</th>
                                                        <th width='5%'>Situação</th>
                                                        <th width='2%'>Ação</th>

                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th width='8%'>Data</th>
                                                        <th width='5%'>AN</th>
                                                        <th width='5%'>ID Pac</th>
                                                        <th width='33%'>Nome</th>
                                                        <th>Data de Nascimento</th>
                                                        <th width='25%'>Solicitante</th>
                                                        <th width='32%'>Descricao</th>
                                                        <th width='5%'>Situação</th>
                                                        <th width='2%'>Ação</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    include('conexao.php');
                                                    $stmt = "select a.transacao, a.exame_nro, a.exame_id, a.situacao, a.contraste, b.transacao, a.med_analise, b.dat_cad as cadastro, b.dt_solicitacao, b.dt_realizacao, b.convenio_id, a.pedido,
						c.nome, d.sigla as convenio, a.exame_id, e.descricao as desc_exames, f.sigla as modalidade from itenspedidos a left join pedidos b on 
						b.transacao=a.transacao left join pessoas c on b.paciente_id=c.pessoa_id  left join convenios d on b.convenio_id=d.convenio_id
						left join procedimentos e on a.exame_id=e.procedimento_id left join modalidades f on e.modalidade_id=f.modalidade_id 
						WHERE f.modalidade_id = 4 AND b.dat_cad = '$data' AND a.situacao = 'Cadastrando'
						order by a.transacao DESC";

                                                    $sth = pg_query($stmt) or die($stmt);
                                                    while ($row = pg_fetch_object($sth)) {
                                                        if ($row->situacao   == 'Realizado') {
                $classe = "class='bg-info'";
            } else {
                $classe = "class='bg-danger'";
            }
                                                        echo "<tr " . $classe . ">";
                                                        echo "<td><a href='emitelaudos.php?id=" . $row->exame_nro . "' target='_blank'>";
                                                        echo str_pad($row->transacao, 5, "0", STR_PAD_LEFT);
                                                        echo "</a></td>";
                                                        echo "<td>" . inverteData(substr($row->cadastro, 0, 10)) . "</td>";
                                                        echo "<td>" . $row->convenio . "</td>";
                                                        echo "<td>" . $row->nome . "</td>";
                                                        echo "<td>" . $row->modalidade . "</td>";
                                                        echo "<td>" . $row->situacao . "</td>";
                                                        echo "<td><a href=\"alterastatuspedido.php?id=" . $row->exame_nro . "\"><i class=\"fas fa-radiation-alt\" style='color:white' title=\"Marcar como realizado\"></i>";
                                                        echo "</tr>";
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
        <script>
            function atualizar() {
                var data = $('#data').val();
                var modalidade = $('#modalidade_id').val();
                var url = 'ajax_buscar_painel_especialidade.php?data=' + data + '&modalidade=' + modalidade;

                $.get(url, function(dataReturn) {
                    $('#dados').html(dataReturn);
                });
            }

            setInterval("atualizar()", 5000);
        </script>
</body>

</html>