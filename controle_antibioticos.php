<?php

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
    <title>FUNEPU | Controle Antibioticos</title>
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
                                                        » </p>Controle de Antibioticos
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
                                                <li class="active">Controle Antibioticos</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse1" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse1" aria-expanded="false" aria-controls="collapse1" class="card-title lead collapsed">CABEÇA E PESCOÇO</a>
                                                </div>
                                                <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb1_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(1)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 1 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse2" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse2" aria-expanded="false" aria-controls="collapse2" class="card-title lead collapsed">CUTÂNEA</a>
                                                </div>
                                                <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb2_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(2)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 2 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse3" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse3" aria-expanded="false" aria-controls="collapse3" class="card-title lead collapsed">GENITOURINÁRIA</a>
                                                </div>
                                                <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb3_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(3)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 3 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse4" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse4" aria-expanded="false" aria-controls="collapse4" class="card-title lead collapsed">PNEUMONIAS</a>
                                                </div>
                                                <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb4_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(4)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 4 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse5" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse5" aria-expanded="false" aria-controls="collapse5" class="card-title lead collapsed">OSTEOARTICULAR</a>
                                                </div>
                                                <div id="collapse5" role="tabpanel" aria-labelledby="headingCollapse5" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb5_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(5)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 5 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse6" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse6" aria-expanded="false" aria-controls="collapse6" class="card-title lead collapsed">GASTROINTESTINAL</a>
                                                </div>
                                                <div id="collapse6" role="tabpanel" aria-labelledby="headingCollapse6" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb6_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(6)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 6 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse7" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse7" aria-expanded="false" aria-controls="collapse7" class="card-title lead collapsed">SNC</a>
                                                </div>
                                                <div id="collapse7" role="tabpanel" aria-labelledby="headingCollapse7" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb7_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(7)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 7 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse8" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse8" aria-expanded="false" aria-controls="collapse8" class="card-title lead collapsed">FOURNIER</a>
                                                </div>
                                                <div id="collapse8" role="tabpanel" aria-labelledby="headingCollapse8" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb8_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(8)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 8 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="card collapse-icon accordion-icon-rotate">
                                                <div id="headingCollapse9" class="card-header pb-3">
                                                    <a data-toggle="collapse" href="#collapse9" aria-expanded="false" aria-controls="collapse9" class="card-title lead collapsed">DST'S</a>
                                                </div>
                                                <div id="collapse9" role="tabpanel" aria-labelledby="headingCollapse9" class="collapse" style="">
                                                    <div class="card-content">
                                                        <div class="card-body">
                                                            <table class="table table-responsive-sm" id="tb9_antibiotico">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Ordem</th>
                                                                        <th>Antibiotico</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th>Quantidade</th>
                                                                        <th>Ação <button onclick="adicionar(9)" class="btn btn-sm btn-primary ml-2">Novo</button></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 9 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                                                        <tr>
                                                                            <td><?= $row->ordem; ?></td>
                                                                            <td><?= $row->descricao; ?></td>
                                                                            <td><?= $row->via; ?></td>
                                                                            <td><?= $row->aprazamento; ?></td>
                                                                            <td><?= $row->quantidade; ?></td>
                                                                            <td style='display:none;'><?= $row->categoria; ?></td>
                                                                            <td style='display:none;'><?= $row->controle_id; ?></td>
                                                                            <td><button class="btn btn-sm btn-warning m-1" onclick="editar(this,<?= $row->controle_id; ?>)"><i class="far fa-edit"></i></button><button class="btn btn-sm btn-danger m-1" onclick="deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->controle; ?>)"><i class="far fa-trash-alt"></i></button></td>
                                                                        </tr>
                                                                    <?php } ?>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="retorno"></div>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        function adicionar(a) {
            $.get("adicionar_linha_antibiotico.php?categoria=" + a, function(dataReturn) {
                $('#retorno').html(dataReturn);
            });
        };

        function salvar(a) {
            var par = $(a).parent().parent();
            var ordem = par.children("td:nth-child(1)").children("input[type=text]").val();
            var antibiotico = par.children("td:nth-child(2)").children("select").val();
            var via = par.children("td:nth-child(3)").children("select").val();
            var aprazamento = par.children("td:nth-child(4)").children("select").val();
            var quantidade = par.children("td:nth-child(5)").children("input[type=text]").val();
            var categoria = par.children("td:nth-child(6)").html();

            $.get("salvar_antibiotico.php?ordem=" + ordem + "&antibiotico=" + antibiotico + "&via=" + via + "&aprazamento=" + aprazamento + "&quantidade=" + quantidade + "&categoria=" + categoria,
                function(dataReturn) {
                    $('#retorno').html(dataReturn);
                    Swal.fire('Cadastrado com sucesso');
                });
        }

        function editar_antibiotico(a, b) {
            var par = $(a).parent().parent();
            var ordem = par.children("td:nth-child(1)").children("input[type=text]").val();
            var antibiotico = par.children("td:nth-child(2)").children("select").val();
            var via = par.children("td:nth-child(3)").children("select").val();
            var aprazamento = par.children("td:nth-child(4)").children("select").val();
            var quantidade = par.children("td:nth-child(5)").children("input[type=text]").val();
            var categoria = par.children("td:nth-child(6)").html();
            var id = par.children("td:nth-child(7)").html();

            $.get("editar_antibiotico.php?ordem=" + ordem + "&antibiotico=" + antibiotico + "&via=" + via + "&aprazamento=" + aprazamento + "&categoria=" + categoria + "&quantidade=" + quantidade + "&id=" + id, function(dataReturn) {
                $('#retorno').html(dataReturn);
                Swal.fire('Cadastrado com sucesso');
            });
        }

        function editar(a) {
            var par = $(a).parent().parent();
            var ordem = par.children("td:nth-child(1)");
            var antibiotico = par.children("td:nth-child(2)");
            var via = par.children("td:nth-child(3)");
            var aprazamento = par.children("td:nth-child(4)");
            var quantidade = par.children("td:nth-child(5)");
            var categoria = par.children("td:nth-child(6)");
            var controle = par.children("td:nth-child(7)");
            var salvar = par.children("td:nth-child(8)");

            ordem.html("<input type='text' class='form-control' onkeypress='return event.charCode >= 48 && event.charCode <= 57' value='" + ordem.html() + "'/>");
            antibiotico.html("<select class='form-control selectnew multat' data-size='4' multiple='multiple' data-live-search='true' name='antibiotico' id='antibiotico' ><option value=''></option><?php
                                                                                                                                                                                                        include('conexao.php');
                                                                                                                                                                                                        $stmt = 'Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where b.antibiotico = 1';
                                                                                                                                                                                                        $sth = pg_query($stmt) or die($stmt);
                                                                                                                                                                                                        while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                                                            echo '<option value=\'' . $row->id . '\'';
                                                                                                                                                                                                            echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                        } ?></select>");
            via.html("<select class='form-control selectnew' data-size='4' data-live-search='true' name='via' id='via'><option value=" +
                "></option>" +
                <?php include('conexao.php');
                $stmt = "Select descricao from vias_administracao";
                $sth = pg_query($stmt) or die($stmt);
                while ($row = pg_fetch_object($sth)) { ?> "<option value='<?= $row->descricao; ?>'><?= $row->descricao; ?></option>" + <?php } ?> "</select>");
            aprazamento.html("<select class='form-control selectnew' data-size='4' data-live-search='true' name='aprazamento' id='aprazamento'><option value=''></option>" +
                <?php include('conexao.php');
                $stmt = "Select * from aprazamento";
                $sth = pg_query($stmt) or die($stmt);
                while ($row = pg_fetch_object($sth)) { ?> "<option value='<?= $row->descricao; ?>'><?= $row->descricao; ?></option>" +
                <?php }
                ?> "</select>");
            categoria.html(categoria.html());
            quantidade.html("<input type='text' class='form-control' onkeypress='return event.charCode >= 48 && event.charCode <= 57' value='" + quantidade.html() + "'/>");
            controle.html(categoria.html());
            salvar.html("<button class='btn btn-success' onclick='editar_antibiotico(this)'>Salvar</button>");
            $('.multat').select2();
        }

        function deletar_antibiotico(a, b) {
            $.get("deletar_antibiotico.php?controle=" + a + "&categoria=" + b, function(dataReturn) {
                $('#retorno').html(dataReturn);
            });
        };
    </script>
</body>

</html>