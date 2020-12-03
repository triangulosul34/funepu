<?php

include 'Config.php';

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'PHPexcel/PHPExcel.php';
    require_once 'PHPexcel/PHPExcel/IOFactory.php';

    $erro = [];

    $file = $_FILES['excel']['tmp_name'];

    if (move_uploaded_file($file, "/var/www/html/".UPLOAD_EXCEL."/funepu/excelnotificacao/".basename($_FILES['excel']['name']))) {
        $enviado = true;
    } else {
        $enviado = false;
    }

    include('conexao.php');
    $sql = "select * from excel_notificacao where arquivo = '".basename($_FILES['excel']['name'])."'";
    $result = pg_query($sql) or die($sql);
    $row = pg_fetch_object($result);

    if ($row->arquivo) {
        echo "<script>alert('arquivo ".$row->arquivo." já enviado ao sistema. O mesmo será excluído para evitar duplicidades');</script>";
        include('conexao.php');
        $sql = "update excel_notificacao set controle = 0 where arquivo = '".basename($_FILES['excel']['name'])."'";
        $result = pg_query($sql) or die($sql);
    }

    if ($enviado) {
        $obj = PHPExcel_IOFactory::load("excelnotificacao/".basename($_FILES['excel']['name']));
        foreach ($obj->getWorksheetIterator() as $sheet) {
            $getHighestRow = $sheet->getHighestRow();
            for ($i = 3; $i <= $getHighestRow; $i++) {
                $nome = $sheet->getCellByColumnAndRow(0, $i)->getValue();
                $data_nascimento = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(1, $i)->getValue()));
                $nome_mae = $sheet->getCellByColumnAndRow(2, $i)->getValue();
                $cpf = $sheet->getCellByColumnAndRow(3, $i)->getValue();
                $tipo = $sheet->getCellByColumnAndRow(4, $i)->getValue();
                $data_notificacao = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(5, $i)->getValue()));
                $resultados = $sheet->getCellByColumnAndRow(6, $i)->getValue();
                $data_secretaria = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(7, $i)->getValue()));
                $assinatura_recebimento = $sheet->getCellByColumnAndRow(8, $i)->getValue();

                include('conexao.php');
                $sql = "select * from excel_notificacao where nome = '$nome' and data_nascimento = '$data_nascimento' and nome_mae = '$nome_mae' and data_notificacao =  '$data_notificacao' and controle = 1";
                $result = pg_query($sql) or die($sql);
                $row = pg_fetch_object($result);

                include('conexao.php');
                $sql2 = "select * from excel_notificacao where cpf = '$cpf' and data_notificacao =  '$data_notificacao' and controle = 1";
                $result2 = pg_query($sql2) or die($sql2);
                $row2 = pg_fetch_object($result2);

                if ($row->nome) {
                    array_push($erro, $row->nome);
                }

                if ($row2->nome && $row->nome != $row2->nome) {
                    array_push($erro, $row->nome2);
                }

                include('conexao.php');
                $sql = "insert into excel_notificacao(nome,data_nascimento,nome_mae,cpf,tipo,data_notificacao,resultados,data_secretaria,assinatura_recebimento,arquivo,controle) values('$nome','$data_nascimento','$nome_mae', '$cpf', '$tipo', '$data_notificacao', '$resultados', '$data_secretaria', '$assinatura_recebimento', '".basename($_FILES['excel']['name'])."', 1)";
                $result = pg_query($sql) or die($sql);
            }
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
    <title>FUNEPU | Notificacao</title>
    <link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link
        href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
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

<body class="  pace-done" cz-shortcut-listen="true">
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99"
            style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>

    <div class="wrapper">
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
                                                        <p
                                                            style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                            » </p>Registro de Controle
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
                                                    <li class="active">Notificacao</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- CORPO DA PAGINA -->
                                <div class="card-content">
                                    <div class="col-12">
                                        <form action="#" method="POST" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="file" name="excel" id="excel" class="form-control">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="submit" value="submit" class="btn btn-primary">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-mr-12">
                                                    <?php if ($enviado) {?>
                                                    <h3>Arquivo enviado com sucesso!!!</h3>
                                                    <?php } elseif (isset($enviado)) { ?>
                                                    <h3>Não foi possivel enviar o arquivo!!!</h3>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-mr-12">
                                                    <?php if ($erro) {
    echo "<h3>Possiveis duplicidades</h3>";
    foreach ($erro as $nome) {?>
                                                    <h4><?= $nome; ?>
                                                    </h4>
                                                    <?php }
} ?>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





</body>
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
<script defer src="/your-path-to-fontawesome/js/all.js"></script>
</body>

</html>