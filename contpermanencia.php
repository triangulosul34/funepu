<?php

require("../vendor/autoload.php");

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = explode("-", $_POST['data']);
    $arquivo = 'Relatorio Tempo Permanencia.xls';
    $html = '';
    $html .= '<table style="font-size:12px" border="1">';
    $html .= '<tr>';
    $html .= '<td colspan="6" align=\'center\'>UPA ' . UNIDADE_CONFIG . ' - TEMPO DE PERMANENCIA -- ' . inverteData($start) . '</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<tr align=\'center\'>';
    $html .= '<td><b>' . utf8_decode("Prontuário") . '</b></td>';
    $html .= '<td><b>Paciente</b></td>';
    $html .= '<td><b>DT. Entrada</b></td>';
    $html .= '<td><b>' . utf8_decode("DT . Saída") . '</b></td>';
    $html .= '<td><b>Destino</b></td>';
    $html .= '<td><b>' . utf8_decode("Dias de Permanência") . '</b></td>';
    $html .= '</tr>';
    include('conexao.php');
    $stmt = "SELECT a.destino_id, b.paciente_id, c.nome, b.dat_cad AS data_entrada, CASE WHEN a.data IS NULL THEN b.dat_cad ELSE a.data END AS data_saida, CASE WHEN a.destino_id IS NULL THEN b.destino_paciente::integer ELSE a.destino_encaminhamento END AS destino FROM atendimentos b LEFT JOIN destino_paciente a ON a.atendimento_id = b.transacao INNER JOIN pessoas c ON b.paciente_id = c.pessoa_id WHERE ((EXTRACT(month FROM b.dat_cad) = '{$data[1]}' AND EXTRACT(year FROM b.dat_cad) = '{$data[0]}') OR (EXTRACT(month FROM a.data) = '{$data[1]}' AND EXTRACT(year FROM a.data) = '{$data[0]}')) AND b.destino_paciente = '03' ORDER BY b.dat_cad ASC";
    $sth = pg_query($stmt) or die($stmt);
    while ($row = pg_fetch_object($sth)) {

        $html .= '<tr>';
        $html .= '<td>' . $row->paciente_id . '</td>';
        $html .= '<td>' . $row->nome . '</td>';
        $html .= '<td>' . inverteData(substr($row->data_entrada, 0, 10)) . '</td>';
        $html .= '<td>' . inverteData(substr($row->data_saida, 0, 10)) . '</td>';
        if ($row->destino == '01') {
            $html .= '<td>ALTA</td>';
        } else if ($row->destino == '02') {
            $html .= '<td>ALTA / ENCAM. AMBUL.</td>';
        } else if ($row->destino == '07') {
            $html .= '<td>' . utf8_decode("EM OBSERVAÇÃO / MEDICAÇÃO") . '</td>';
        } else if ($row->destino == '10') {
            $html .= '<td>EXAMES / REAVALIACAO</td>';
        } else if ($row->destino == '03') {
            $html .= '<td>' . utf8_decode("PERMANÊCIA") . '</td>';
        } else if ($row->destino == '04') {
            $html .= '<td>TRANSF. OUTRA UPA</td>';
        } else if ($row->destino == '05') {
            $html .= '<td>TRANSF. INTERN. HOSPITALAR</td>';
        } else if ($row->destino == '06') {
            $html .= '<td>' . utf8_decode("ÓBITO") . '</td>';
        } else if ($row->destino == '09') {
            $html .= '<td>NAO RESPONDEU CHAMADO</td>';
        } else if ($row->destino == '11') {
            $html .= '<td>' . utf8_decode("ALTA EVASÃO") . '</td>';
        } else if ($row->destino == '12') {
            $html .= '<td>ALTA PEDIDO</td>';
        } else if ($row->destino == '14') {
            $html .= '<td>ALTA / POLICIA</td>';
        } else if ($row->destino == '15') {
            $html .= '<td>' . utf8_decode("ALTA / PENITENCIÁRIA") . '</td>';
        } else if ($row->destino == '16') {
            $html .= '<td>' . utf8_decode("ALTA / PÓS MEDICAMENTO") . '</td>';
        }
        if (strtotime($row->data_saida) == strtotime(substr($row->data_entrada, 0, 10))) {
            $html .= '<td>1</td>';
        } else {
            $html .= '<td>' . date('d', (strtotime($row->data_saida) - strtotime(substr($row->data_entrada, 0, 10)))) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';

    // Configurações header para forçar o download
    header("Expires: Mon, 26 Jul 2017 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
    header("Content-Description: PHP Generated Data");
    // Envia o conteúdo do arquivo
    echo $html;
    exit;
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
    <title>FUNEPU | Controle Permanencia</title>
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
                                                        » </p>Controle de Permanencia
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
                                                <li class="active">Controle Permanencia</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" id="form_permanencia" method="POST">
                                        <div class="row">
                                            <div class="col-3">
                                                <label for="atendimento">Atendimento</label>
                                                <input type="text" class="form-control" name="atendimento" id="atendimento">
                                            </div>
                                            <div class="col-3">
                                                <label for="destino">Destino</label>
                                                <input type="text" class="form-control" name="destino" id="destino" onblur="des()">
                                            </div>
                                            <div class="col-3">
                                                <label for="data">Data</label>
                                                <input type="month" name="data" id="data" class="form-control">
                                            </div>
                                            <div class="col-3">
                                                <input type="submit" name="gerar_relatorio" class="btn btn-raised btn-success" value="Gerar Relatorio">
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row">
                                        <div class="col-12" id="tabela"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        function des() {
            var atendimento = document.getElementById("atendimento").value;
            var destino = document.getElementById("destino").value;

            if (atendimento && destino) {
                $.get("baixar_destino.php", {
                    atendimento: atendimento.replace(/^0+/, ''),
                    destino: destino
                }, function(dataReturn) {
                    $('#tabela').html(dataReturn);
                })
            }
        }

        function cancelar_permanencia(a) {
            $.get("cancelar_controle_permanencia.php", {
                id: a
            }, function(dataReturn) {
                $('#tabela').html(dataReturn);
            })
        }

        $("form").submit(function() {
            if ($("#data").val() == null || $("#data").val() == "") {
                return false;
            }
        });
    </script>
</body>

</html>