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
error_reporting(0);
$menu_grupo = '3';
$menu_sgrupo = '1';
$nome         = '';
$dtnasc     = '';
$telefone    = '';
$mae         = '';
include('verifica.php');
if ($perfil == '03') {
    header("location:loginbox.php");
}
$RX             = '';
$US              = '';
$CT             = '';
$MM             = '';
$RM             = '';
$DS             = '';
$ECO         = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $codigo = $_GET['id'];
    if ($codigo != "") {
        $where = ' pessoa_id =' . $codigo;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $procedimentox = $_POST['procedimentox'];
    $situacao     = $_POST['situacao'];
    $nome           = $_POST['nome'];
    $xbox          = $_POST['xbox'];
    $CM              = $_POST['cb_cm'];
    $PED           = $_POST['cb_ped'];
    $start           = $_POST['start'];
    $end           = $_POST['end'];
    $transfere       = $_POST['cb_exame'];
    $profissional = $_POST['prof_transfere'];
    $cb_meus      = $_POST['cb_meus'];
    $cb_conf      = $_POST['cb_CONFERENCIA'];

    $where = "";


    if (isset($_POST['semana'])) {
        $start          = date('d/m/Y', strtotime("-7 days"));
        $end          = date('d/m/Y');
    }
    if (isset($_POST['hoje'])) {
        $start          = date('d/m/Y');
        $end          = date('d/m/Y');
    }
    if (isset($_POST['ontem'])) {
        $start          = date('d/m/Y', strtotime("-1 days"));
        $end          = date('d/m/Y', strtotime("-1 days"));
    }
    $modalidades = "";

    if ($CM != "") {
        $modalidades = $modalidades . "'Consultorio Adulto',";
    }

    if ($PED != "") {
        $modalidades = $modalidades . "'Ortopedia',";
    }

    $modalidades = substr($modalidades, 0, -1);

    if ($nome != "") {
        $where = $where . " c.nome like '%" . $nome . "%' ";
    }

    if ($procedimentox != "") {
        if ($where != "") {
            $where = $where . " and a.exame_id = $procedimentox";
        } else {
            $where = $where . " a.exame_id = $procedimentox";
        }
    }

    if ($xbox != "") {
        if ($where != "") {
            $where = $where . " and box = $xbox";
        } else {
            $where = $where . " box = $xbox";
        }
    }

    if ($modalidades != "") {
        if ($where != "") {
            $where = $where . " and a.especialidade in ($modalidades) ";
        } else {
            $where = $where . " a.especialidade in ($modalidades) ";
        }
    }

    if ($start != "") {
        $data = inverteData($start);
        if ($where != "") {
            $where = $where . " and (a.dat_cad >= '$data')";
        } else {
            $where = $where . " (a.dat_cad >= '$data')";
        }
    }

    if ($end != "") {
        $data = inverteData($end);
        if ($where != "") {
            $where = $where . " and (a.dat_cad <= '$data')";
        } else {
            $where = $where . " (a.dat_cad <= '$data')";
        }
    }

    if ($situacao != "") {
        if ($situacao != "Pendentes") {
            if ($where != "") {
                $where = $where . " and (a.status = '$situacao')";
            } else {
                $where = $where . " (a.status = '$situacao')";
            }
        } else {
            if ($where != "" and $status == "Pendentes") {
                $where = $where . " and (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
            } else {
                $where = $where . " (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
            }
        }
    }

    if ($cb_meus != "") {
        if ($where != "") {
            $where = $where . " and (a.med_analise = '$cb_meus' or a.med_confere = '$cb_meus' )";
        } else {
            $where = $where . " (a.med_analise = '$cb_meus' or a.med_confere = '$cb_meus')";
        }
    }

    if ($cb_conf != "") {
        if ($where != "") {
            $where = $where . " and (a.med_confere = '$cb_conf')";
        } else {
            $where = $where . " (a.med_confere = '$cb_conf')";
        }
    }
    $stmtx = "nao entrei";

    if ($transfere != "") {
        if (isset($_POST["transferir"])) {
            include('conexao.php');
            $stmty = "Select username from pessoas where pessoa_id = $profissional";

            $sth = pg_query($stmty) or die($stmty);
            $row = pg_fetch_object($sth);
            $username = $row->username;
            if ($username != "") {
                include('conexao.php');
                $stmtx = "Update itenspedidos set med_analise = '" . $username . "' where exame_nro in (" . implode(',', $transfere) . ") and (situacao='Editado' or situacao='Cadastrado')";
                $sth = pg_query($stmtx) or die($stmtx);

                foreach ($transfere as $item) {
                    include('conexao.php');
                    $data  = date('Y-m-d H:i:s');
                    $stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Distribuicao', '$usuario', '$data' )";
                    $sth   = pg_query($stmtx) or die($stmtx);
                }
            }
        }
        if (isset($_POST["transfconf"])) {
            include('conexao.php');
            $stmty = "Select username from pessoas where pessoa_id = $profissional";

            $sth = pg_query($stmty) or die($stmty);
            $row = pg_fetch_object($sth);
            $username = $row->username;
            if ($username != "") {
                include('conexao.php');
                $stmtx = "Update itenspedidos set med_confere = '" . $username . "' where exame_nro in (" . implode(',', $transfere) . ") and (situacao='Laudado' or situacao='Editado')";
                $sth = pg_query($stmtx) or die($stmtx);

                foreach ($transfere as $item) {
                    include('conexao.php');
                    $data  = date('Y-m-d H:i:s');
                    $stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Distribuicao', '$usuario', '$data' )";
                    $sth   = pg_query($stmtx) or die($stmtx);
                }
            }
        }
        if (isset($_POST["imprimir"])) {
            echo "<script>alert('Imprimir')</script>";
        }
        if (isset($_POST["enviar"])) {
            include('conexao.php');
            $stmtx = "Update itenspedidos set situacao = 'Env.Recepção', envio_recepcao=now(), usu_envio_recepcao='$usuario'
                where exame_nro in (" . implode(',', $transfere) . ") and (situacao='Finalizado' or situacao='Impresso')";
            $sth = pg_query($stmtx) or die($stmtx);

            foreach ($transfere as $item) {
                include('conexao.php');
                $data  = date('Y-m-d H:i:s');
                $stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Env Recepcao', '$usuario', '$data' )";
                $sth   = pg_query($stmtx) or die($stmtx);
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
    <title>FUNEPU | Aguardando Atendimento</title>
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
                                                    <p
                                                        style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                        » </p>Página Padrão
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
                                                <li class="active">Aguardando Atendimento</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body" id="atualizaAT">
                                    <div class="row">
                                        <div class="col-6">
                                            <h2 style="text-align: center; margin-bottom: 20px;">Atendimento Ortopedia
                                            </h2>
                                            <table id="dados" class="table">
                                                <thead>
                                                    <tr>

                                                        <th width="10%">Solicitação</th>
                                                        <th width="70%">Paciente</th>
                                                        <th width="70%">Triagem</th>
                                                        <th width="20%">Situação</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>

                                                        <th width="10%">Solicitação</th>
                                                        <th width="70%">Paciente</th>
                                                        <th width="70%">Triagem</th>
                                                        <th width="20%">Situação</th>


                                                    </tr>
                                                </tfoot>
                                                <tbody id="atualizaAtPediatrico">
                                                    <?php
                                                    include('conexao.php');
                                                    $stmt = "select a.transacao, a.paciente_id, extract(year from age(c.dt_nasc)) as idade, a.status, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, c.nome, k.origem, a.tipo, 
							CASE prioridade 
								WHEN 'VERMELHO' THEN '0' 
								WHEN 'LARANJA' THEN '1' 
								WHEN 'AMARELO' THEN '2' 
								WHEN 'VERDE' THEN '3' 
								WHEN 'AZUL' THEN '4' 
							ELSE '5' 
							END as ORDEM 
							from atendimentos a 
							left join pessoas c on a.paciente_id=c.pessoa_id 
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer)
							WHERE status = 'Aguardando Atendimento' and  dat_cad between '" . date('d/m/Y', strtotime("-1 days")) . "' and '" . date('d/m/Y') . "' and a.especialidade = 'Ortopedia' and tipo != '6' and tipo != '9' 
							ORDER by ORDEM ASC, pidade, dat_cad, hora_cad asc";
                                                    $sth = pg_query($stmt) or die($stmt);
                                                    //echo $stmt;
                                                    while ($row = pg_fetch_object($sth)) {
                                                        if ($row->prioridade   == 'AMARELO') {
                                                            $classe = "style=\"background-color:gold\"";
                                                        }
                                                        if ($row->prioridade   == 'VERMELHO') {
                                                            $classe = "class='bg-danger'";
                                                        }
                                                        if ($row->prioridade   == 'VERDE') {
                                                            $classe = "class='bg-success'";
                                                        }
                                                        if ($row->prioridade   == 'AZUL') {
                                                            $classe = "class='bg-primary'";
                                                        }
                                                        if ($row->prioridade   == 'LARANJA') {
                                                            $classe = "class='bg-warning'";
                                                        }
                                                        if ($row->prioridade   == '') {
                                                            $classe = "style=\"background-color:Gainsboro\"";
                                                        }


                                                        $ip = getenv("REMOTE_ADDR");
                                                        echo "<tr " . $classe . ">";
                                                        echo "<td>" . date('d/m/Y', strtotime($row->dat_cad)) . "<br>" . $row->hora_cad . "</td>";
                                                        echo "<td>" . $row->nome . "</td>";

                                                        echo "<td>" . $row->hora_triagem . "</td>";
                                                        echo "<td>" . $row->status . "</td>";
                                                        echo "<td>";



                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-6">
                                            <h2 class="page-title" style="text-align: center; margin-bottom: 20px;">
                                                Atendimento Adulto</h2>
                                            <table id="dados" class="table">
                                                <thead>
                                                    <tr>

                                                        <th width="10%">Solicitação</th>
                                                        <th width="70%">Paciente</th>
                                                        <th width="70%">Triagem</th>
                                                        <th width="20%">Situação</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>

                                                        <th width="10%">Solicitação</th>
                                                        <th width="70%">Paciente</th>
                                                        <th width="70%">Triagem</th>
                                                        <th width="20%">Situação</th>


                                                    </tr>
                                                </tfoot>
                                                <tbody id="atualizaAtAdulto">
                                                    <?php
                                                    include('conexao.php');
                                                    $stmt = "select a.transacao, a.paciente_id, extract(year from age(c.dt_nasc)) as idade, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.status, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, c.nome, k.origem, a.tipo, 
							CASE
                            WHEN a.prioridade = 'VERMELHO' and a.destino_paciente is null THEN '0' 
                            WHEN a.prioridade = 'LARANJA' and a.destino_paciente is null THEN '1' 
                            WHEN a.destino_paciente = '10' and a.prioridade = 'AMARELO'  THEN '2'
                            WHEN a.prioridade = 'AMARELO' and a.destino_paciente is null THEN '3' 
                            WHEN a.destino_paciente = '10' and a.prioridade = 'VERDE'  THEN '4'
                            WHEN a.prioridade = 'VERDE' and a.destino_paciente is null THEN '5' 
                            WHEN a.prioridade = 'AZUL' and a.destino_paciente is null THEN '6' 
                        ELSE '7'  
							END as ORDEM 
							from atendimentos a 
							left join pessoas c on a.paciente_id=c.pessoa_id 
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
							WHERE status = 'Aguardando Atendimento' and  dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and a.especialidade = 'Consultorio Adulto' and tipo != '6' and tipo != '9' and dat_cad > '2019-08-11' 
							ORDER by ORDEM ASC, pidade, dat_cad, hora_cad asc";
                                                    $sth = pg_query($stmt) or die($stmt);
                                                    while ($row = pg_fetch_object($sth)) {
                                                        if ($row->prioridade   == 'AMARELO') {
                                                            $classe = "style=\"background-color:gold\"";
                                                        }
                                                        if ($row->prioridade   == 'VERMELHO') {
                                                            $classe = "class='bg-danger'";
                                                        }
                                                        if ($row->prioridade   == 'VERDE') {
                                                            $classe = "class='bg-success'";
                                                        }
                                                        if ($row->prioridade   == 'AZUL') {
                                                            $classe = "class='bg-primary'";
                                                        }
                                                        if ($row->prioridade   == 'LARANJA') {
                                                            $classe = "class='bg-warning'";
                                                        }
                                                        if ($row->prioridade   == '') {
                                                            $classe = "style=\"background-color:Gainsboro\"";
                                                        }


                                                        $ip = getenv("REMOTE_ADDR");
                                                        echo "<tr " . $classe . ">";
                                                        echo "<td>" . date('d/m/Y', strtotime($row->dat_cad)) . "<br>" . $row->hora_cad . "</td>";
                                                        echo "<td>" . $row->nome . "</td>";

                                                        echo "<td>" . $row->hora_triagem . "</td>";
                                                        echo "<td>" . $row->status . "</td>";
                                                        echo "<td>";



                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-6">
                                            <h2 class="page-title" style="text-align: center; margin-bottom: 20px;">
                                                Atendimento Odontologico</h2>
                                            <table id="dados" class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="10%">Solicitação</th>
                                                        <th width="70%">Paciente</th>
                                                        <th width="70%">Triagem</th>
                                                        <th width="20%">Situação</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th width="10%">Solicitação</th>
                                                        <th width="70%">Paciente</th>
                                                        <th width="70%">Triagem</th>
                                                        <th width="20%">Situação</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody id="atualizaAtAdulto">
                                                    <?php
                                                    include('conexao.php');
                                                    $stmt = "select a.transacao, a.paciente_id, extract(year from age(c.dt_nasc)) as idade, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.status, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, c.nome, k.origem, a.tipo, 
							CASE prioridade 
								WHEN 'VERMELHO' THEN '0' 
								WHEN 'LARANJA' THEN '1' 
								WHEN 'AMARELO' THEN '2' 
								WHEN 'VERDE' THEN '3' 
								WHEN 'AZUL' THEN '4' 
							ELSE '5' 
							END as ORDEM 
							from atendimentos a 
							left join pessoas c on a.paciente_id=c.pessoa_id 
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
							WHERE status = 'Aguardando Atendimento' and  dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and tipo = '9'  
							ORDER by ORDEM ASC, pidade, dat_cad, hora_cad asc";
                                                    $sth = pg_query($stmt) or die($stmt);
                                                    while ($row = pg_fetch_object($sth)) {
                                                        if ($row->prioridade   == 'AMARELO') {
                                                            $classe = "style=\"background-color:gold\"";
                                                        }
                                                        if ($row->prioridade   == 'VERMELHO') {
                                                            $classe = "class='bg-danger'";
                                                        }
                                                        if ($row->prioridade   == 'VERDE') {
                                                            $classe = "class='bg-success'";
                                                        }
                                                        if ($row->prioridade   == 'AZUL') {
                                                            $classe = "class='bg-primary'";
                                                        }
                                                        if ($row->prioridade   == 'LARANJA') {
                                                            $classe = "class='bg-warning'";
                                                        }
                                                        if ($row->prioridade   == '') {
                                                            $classe = "style=\"background-color:Gainsboro\"";
                                                        }


                                                        $ip = getenv("REMOTE_ADDR");
                                                        echo "<tr " . $classe . ">";
                                                        echo "<td>" . date('d/m/Y', strtotime($row->dat_cad)) . "<br>" . $row->hora_cad . "</td>";
                                                        echo "<td>" . $row->nome . "</td>";

                                                        echo "<td>" . $row->hora_triagem . "</td>";
                                                        echo "<td>" . $row->status . "</td>";
                                                        echo "<td>";



                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-6">
                                            <h2 class="page-title" style="text-align: center; margin-bottom: 20px;">
                                                Aguardando Exames</h2>
                                            <table id="dados" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Data Solicitação</th>
                                                        <th>Nome</th>
                                                        <th>Medico Solicitante</th>
                                                        <th>Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    include('conexao.php');
                                                    $sql = "select a.transacao, c.transacao as atendimento_id from pedidos a inner join pessoas b on a.paciente_id = b.pessoa_id inner join atendimentos c on a.atendimento_id = c.transacao where c.destino_paciente = '10' and dt_solicitacao >= CURRENT_DATE -1 and c.status = 'Aguardando Atendimento'";
                                                    $result = pg_query($sql) or die($sql);
                                                    while ($row = pg_fetch_object($result)) {
                                                        include('conexao_laboratorio.php');
                                                        $sql2 = "select distinct a.data, b.nome, a.medico_solicitante, array_to_string(array_agg(DISTINCT d.situacao), ',') as situacao from pedidos a
								inner join pessoas b on a.pessoa_id = b.pessoa_id
								inner join pedido_guia c on a.pedido_id = c.pedido_id 
								inner join pedido_item d on c.pedido_guia_id = d.pedido_guia_id where cod_pedidos::varchar like '%" . $row->transacao . "' and c.origem = '" . ORIGEM_CONFIG . "' and a.data >= CURRENT_DATE -1 group by 1,2,3";
                                                        $result2 = pg_query($sql2) or die($sql2);
                                                        $row2 = pg_fetch_object($result2);
                                                        if ($row2) {
                                                            ?>
                                                    <tr <?php if ($row2->situacao == '') { ?>bgcolor="#FF0000"
                                                        style="color: #fff;" <?php } elseif ($row2->situacao == 'Liberado') { ?>bgcolor="#0B610B"
                                                        style="color: #fff;" <?php } else { ?>bgcolor="#F7FE2E"
                                                        style="color: #000000;" <?php } ?>>
                                                        <td><?php echo inverteData($row2->data); ?>
                                                        </td>
                                                        <td><?php echo $row2->nome; ?>
                                                        </td>
                                                        <td><?php echo $row2->medico_solicitante; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($row2->situacao == 'Liberado') {
                                                                echo "<a href='atendimentoclinico.php?id=$row->atendimento_id' target='_blank' class=\"btn btn-pure btn-danger\"><i class=\"far fa-eye\"></i></a>";
                                                            } ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Data Solicitação</th>
                                                        <th>Nome</th>
                                                        <th>Medico Solicitante</th>
                                                        <th>Ação</th>
                                                    </tr>
                                                </tfoot>
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
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript">
        </script>
        <script src="app-assets/js/scripts.js" type="text/javascript"></script>
        <script src="app-assets/js/popover.js" type="text/javascript"></script>
        <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
        <script defer src="/your-path-to-fontawesome/js/all.js"></script>
        <script>
            var tempo = setInterval(carrega, 5000);

            function carrega(event) {
                $.get('atualizaAt.php', function(dataReturn) {
                    $('#atualizaAT').html(dataReturn);
                });
            }
        </script>
</body>

</html>