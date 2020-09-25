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
$menu_grupo = '3';
$menu_sgrupo = '1';
$nome         = '';
$dtnasc     = '';
$telefone    = '';
$mae         = '';
include('verifica.php');
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
    $procedimentox    = $_POST['procedimentox'];
    $situacao         = $_POST['situacao'];
    $nome               = $_POST['nome'];
    $xbox              = $_POST['xbox'];


    $RX                  = $_POST['cb_rx'];
    $US                   = $_POST['cb_us'];
    $EC                   = $_POST['cb_ec'];
    $BI                   = $_POST['cb_bi'];
    $HE                   = $_POST['cb_he'];
    $SO                   = $_POST['cb_so'];
    $UR                   = $_POST['cb_ur'];
    $HO                   = $_POST['cb_ho'];
    $MC                   = $_POST['cb_mc'];
    $LB                   = $_POST['cb_lb'];


    $start               = $_POST['start'];
    $end               = $_POST['end'];
    $transfere           = $_POST['cb_exame'];
    $profissional     = $_POST['prof_transfere'];
    $cb_meus          = $_POST['cb_meus'];
    $cb_conf          = $_POST['cb_CONFERENCIA'];
    $ECG                = $_POST['cb_ecg'];
    $prontuario     = $_POST['prontuario'];

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

    if ($RX != "") {
        $modalidades = $modalidades . "'RX',";
    }
    if ($US != "") {
        $modalidades = $modalidades . "'US',";
    }
    if ($EC != "") {
        $modalidades = $modalidades . "'EC',";
    }
    if ($BI != "") {
        $modalidades = $modalidades . "'BI',";
        $aux1 = 1;
    }
    if ($HE != "") {
        $modalidades = $modalidades . "'HE',";
        $aux1 = 1;
    }
    if ($SO != "") {
        $modalidades = $modalidades . "'SO',";
    }
    if ($UR != "") {
        $modalidades = $modalidades . "'UR',";
    }
    if ($HO != "") {
        $modalidades = $modalidades . "'HO',";
    }
    if ($MC != "") {
        $modalidades = $modalidades . "'MC',";
        $aux1 = 1;
    }
    if ($LB != "") {
        $modalidades = $modalidades . "'LB'";
    }
    //
    $modalidades = substr($modalidades, 0, -1);


    $where2 = "f.sigla not in ('MC', 'HE', 'BI', 'HO', 'U1')";

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
            $where = $where . " and f.sigla in ($modalidades) ";
        } else {
            $where = $where . " f.sigla in ($modalidades) ";
        }
    }

    if ($start != "") {
        $data = inverteData($start);
        if ($where != "") {
            $where = $where . " and (b.dat_cad >= '$data')";
        } else {
            $where = $where . " (b.dat_cad >= '$data')";
        }
    }

    if ($end != "") {
        $data = inverteData($end);
        if ($where != "") {
            $where = $where . " and (b.dat_cad <= '$data')";
        } else {
            $where = $where . " (b.dat_cad <= '$data')";
        }
    }

    if ($situacao != "") {
        if ($situacao != "Pendentes") {
            if ($where != "") {
                $where = $where . " and (a.situacao = '$situacao')";
            } else {
                $where = $where . " (a.situacao = '$situacao')";
            }
        } else {
            if ($where != "" and $situacao == "Pendentes") {
                $where = $where . " and (a.situacao not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
            } else {
                $where = $where . " (a.situacao not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
            }
        }
    }

    if ($prontuario != '') {
        if ($where != "") {
            $where = $where . " and (b.paciente_id = '$prontuario')";
        } else {
            $where = $where . " (b.paciente_id = '$prontuario')";
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

    if (isset($_POST["excel"])) {
        $arquivo = 'Relatorio Atendimento.xls';
        $html = '';
        $html .= '<table border="1">';
        $html .= "<tr>";
        $html .= "<th>Solicitacao</th>";
        $html .= "<th>Nome</th>";
        $html .= "<th>Exame</th>";
        $html .= "</tr>";
        $date = date('Y-m-d');
        include('conexao.php');
        $stmt = "select a.transacao, a.exame_nro, b.paciente_id, a.exame_id, a.situacao, b.transacao, a.med_analise, b.dat_cad as cadastro, b.dt_solicitacao, b.dt_realizacao, b.convenio_id, a.pedido,
		c.nome, d.sigla as convenio, a.exame_id, e.descricao as desc_exames, f.sigla as modalidade, g.nome as medresp, h.nome as medconf from itenspedidos a left join pedidos b on 
		b.transacao=a.transacao left join pessoas c on b.paciente_id=c.pessoa_id  left join convenios d on b.convenio_id=d.convenio_id
		left join procedimentos e on a.exame_id=e.procedimento_id left join modalidades f on e.modalidade_id=f.modalidade_id 
		left join pessoas g on (a.med_analise=g.username and g.username is not null)left join pessoas h on (a.med_confere=h.username and h.username is not null)";
        if ($where != "") {
            $stmt = $stmt . " where  " . $where;
        } else {
            $stmt = $stmt . " where dat_cad = '$data' ";
        } //estou mexendo aqui...
        $stmt = $stmt . " order by dat_cad";
        $sth = pg_query($stmt) or die($stmt);
        //echo $stmt;
        $pessoa_con = '1,';
        $i = 0;
        while ($row = pg_fetch_object($sth)) {
            $html .= "<tr>";
            $html .= "<td>" . inverteData(substr($row->cadastro, 0, 10)) . "</td>";
            $html .= "<td>" . $row->nome . "</td>";
            $html .= "<td>" . $row->desc_exames . "</td>";
            $html .= "</tr>";
            include('conexao_laboratorio.php');
            // if($data_con == '' or $data_con == $row->cadastro){
            // 	$sql = "select * from pedidos a
            // 	inner join pessoas b on a.pessoa_id = b.pessoa_id 
            // 	inner join pedido_guia c on c.pedido_id = a.pedido_id
            // 	inner join pedido_item d on c.pedido_guia_id = d.pedido_guia_id
            // 	inner join procedimentos e on e.procedimentos_id = d.exame_id where a.data = '".substr($row->cadastro,0,10)."' and b.origem = 01 and pessoa_id_origem not in (".substr($pessoa_con, 0, -1).") and pessoa_id_origem = $row->paciente_id";
            // 	$result = pg_query($sql) or die($sql);
            // 	while ($row_con = pg_fetch_object($result)){
            // 		$html .= "<tr>";
            // 		$html .= "<td>".inverteData(substr($row_con->data,0,10))."</td>";
            // 		$html .= "<td>".$row_con->nome."</td>";
            // 		$html .= "<td>".$row_con->descricao."</td>";
            // 		$html .= "</tr>";
            // 	}
            // 	$data_con = $row->cadastro;
            // 	$pessoa_con = $pessoa_con."$row->paciente_id,";
            // }else{
            // 	$sql = "select * from pedidos a
            // 	inner join pessoas b on a.pessoa_id = b.pessoa_id 
            // 	inner join pedido_guia c on c.pedido_id = a.pedido_id
            // 	inner join pedido_item d on c.pedido_guia_id = d.pedido_guia_id
            // 	inner join procedimentos e on e.procedimentos_id = d.exame_id where a.data = '$data_con' and b.origem = 01 and pessoa_id_origem not in (".substr($pessoa_con, 0, -1).") and pessoa_id_origem = $row->paciente_id";
            // 	$result = pg_query($sql) or die($sql);
            // 	while ($row_con = pg_fetch_object($result)){
            // 		$html .= "<tr>";
            // 		$html .= "<td>".inverteData(substr($row_con->data,0,10))."</td>";
            // 		$html .= "<td>".$row_con->nome."</td>";
            // 		$html .= "<td>".$row_con->descricao."</td>";
            // 		$html .= "</tr>";
            // 	}
            // 	$pessoa_con = '1,';
            // 	$sql = "select * from pedidos a
            // 	inner join pessoas b on a.pessoa_id = b.pessoa_id 
            // 	inner join pedido_guia c on c.pedido_id = a.pedido_id
            // 	inner join pedido_item d on c.pedido_guia_id = d.pedido_guia_id
            // 	inner join procedimentos e on e.procedimentos_id = d.exame_id where a.data = '".substr($row->cadastro,0,10)."' and b.origem = 01 and pessoa_id_origem not in (".substr($pessoa_con, 0, -1).") and pessoa_id_origem = $row->paciente_id";
            // 	$result = pg_query($sql) or die($sql);
            // 	while ($row_con = pg_fetch_object($result)){
            // 		$html .= "<tr>";
            // 		$html .= "<td>".inverteData(substr($row_con->data,0,10))."</td>";
            // 		$html .= "<td>".$row_con->nome."</td>";
            // 		$html .= "<td>".$row_con->descricao."</td>";
            // 		$html .= "</tr>";
            // 	}
            // 	$data_con = $row->cadastro;
            // 	$pessoa_con = $pessoa_con."$row->paciente_id,";
            // 	$i++;
            // }
        }
        // include('conexao_laboratorio.php');
        // $sql = "select * from pedidos a
        // inner join pessoas b on a.pessoa_id = b.pessoa_id 
        // inner join pedido_guia c on c.pedido_id = a.pedido_id
        // 		inner join pedido_item d on c.pedido_guia_id = d.pedido_guia_id
        // 		inner join procedimentos e on e.procedimentos_id = d.exame_id where a.data = '$data_con' and b.origem = 01 and pessoa_id_origem not in (".substr($pessoa_con, 0, -1).")";
        // $result = pg_query($sql) or die($sql);
        // while ($row_con = pg_fetch_object($result)){
        // 	$html .= "<tr>";
        // 	$html .= "<td>".inverteData(substr($row_con->data,0,10))."</td>";
        // 	$html .= "<td>".$row_con->nome."</td>";
        // 	$html .= "<td>".$row_con->descricao."</td>";
        // 	$html .= "</tr>";
        // }
        // $data_con = $row->cadastro;
        // $pessoa_con = $pessoa_con."$row->paciente_id,";
        $html .= '</table>';
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
    <title>FUNEPU | Pagina Padrao</title>
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
<script>
    $('#dados').dataTable({
        "iDisplayLength": 100
    });

    function buscar_cidades() {
        var estado = $('#modalidade').val();
        if (estado) {
            var url = 'ajax_buscar_procedimentos.php?estado=' + estado;
            $.get(url, function(dataReturn) {
                $('#load_cidades').html(dataReturn);
            });
        }
    }

    function marcardesmarcar() {
        $('.marcar').each(function() {
            if (this.checked) $(this).attr("checked", false);
            else $(this).prop("checked", true);
        });
    }

    function doConfirm(id) {

        var ok = confirm("Confirma a exclusao?" + id)
        if (ok) {

            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    window.location = "create_dealer.php"; // self page
                }
            }

            xmlhttp.open("GET", "apagasgrupo.php?id=" + id);
            xmlhttp.send();
        }
    }

    function aConf(mes) {
        alertify.confirm(mes, function(e) {
            return e;
        });
    }

    function maiuscula(z) {
        v = z.value.toUpperCase();
        z.value = v;
    }

    function formatar(mascara, documento) {
        var i = documento.value.length;
        var saida = mascara.substring(0, 1);
        var texto = mascara.substring(i)

        if (texto.substring(0, 1) != saida) {
            documento.value += texto.substring(0, 1);
        }
    }

    function openInNewTab(url) {
        var win = window.open(url, '_blank');
        win.focus();
    }
    $("#procedimentox").chosen({
        placeholder_text_single: "Selecione...",
        search_contains: true
    });

    function somenteNumeros(num) {
        var er = /[^0-9.]/;
        er.lastIndex = 0;
        var campo = num;
        if (er.test(campo.value)) {
            campo.value = "";
        }
    }
</script>

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
                                                        » </p>Pedidos
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
                                                <li><a href="#">Atendimentos</a></li>
                                                <li class="active">Atendimentos Cadastrados</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form name="total" method="post">
                                        <div class="panel">
                                            <div class="panel-body">


                                                <div class="row">
                                                    <div class="col-3">
                                                        <label class="control-label">Procedimento</label>
                                                        <select class="form-control" name="procedimentox" id="procedimentox">
                                                            <option value="">Todos</option>
                                                            <?php
                                                            include('conexao.php');
                                                            $stmt = "Select procedimento_id, descricao from procedimentos order by descricao";
                                                            $sth = pg_query($stmt) or die($stmt);

                                                            while ($row = pg_fetch_object($sth)) {
                                                                echo "<option value=\"" . $row->procedimento_id . "\"";
                                                                if ($row->procedimento_id == $procedimentox) {
                                                                    echo "selected";
                                                                }
                                                                echo ">" . $row->descricao . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="control-label" for="inputBasicFirstName">Prontuário</label>
                                                        <input type="text" class="form-control" id="inputBasicFirstName" name="prontuario" placeholder="Número do Prontuário" autocomplete="off" value="<?php echo $prontuario; ?>" onkeyup="somenteNumeros(this);" />
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="control-label" for="inputBasicFirstName">Paciente</label>
                                                        <input type="text" class="form-control" id="inputBasicFirstName" name="nome" placeholder="Parte do Nome" autocomplete="off" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)" />
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="control-label">Situacao</label>
                                                        <select class="form-control" name="situacao" id="situacao">
                                                            <option value="">Todos</option>
                                                            <option value="Pendentes" <?php if ($situacao == 'Pendentes') {
                                                                                            echo "selected";
                                                                                        } ?>>Pendentes</option>
                                                            <option value="Cadastrado" <?php if ($situacao == 'Cadastrado') {
                                                                                            echo "selected";
                                                                                        } ?>>Cadastrado</option>
                                                            <option value="Realizado" <?php if ($situacao == 'Realizado') {
                                                                                            echo "selected";
                                                                                        } ?>>Realizado</option>
                                                            <option value="Alterado" <?php if ($situacao == 'Alterado') {
                                                                                            echo "selected";
                                                                                        } ?>>Alterado</option>
                                                            <option value="Editado" <?php if ($situacao == 'Editado') {
                                                                                        echo "selected";
                                                                                    } ?>>Editado</option>
                                                            <option value="Laudado" <?php if ($situacao == 'Laudado') {
                                                                                        echo "selected";
                                                                                    } ?>>Laudado</option>
                                                            <option value="Conferido" <?php if ($situacao == 'Conferido') {
                                                                                            echo "selected";
                                                                                        } ?>>Conferido</option>
                                                            <option value="Finalizado" <?php if ($situacao == 'Finalizado') {
                                                                                            echo "selected";
                                                                                        } ?>>Finalizado</option>
                                                            <option value="Env.Recepção" <?php if ($situacao == 'Env.Recepção') {
                                                                                                echo "selected";
                                                                                            } ?>>Enviado Recepção</option>
                                                            <option value="Rec.Recepção" <?php if ($situacao == 'Rec.Recepção') {
                                                                                                echo "selected";
                                                                                            } ?>>Recebido Recepção</option>
                                                            <option value="Entregue" <?php if ($situacao == 'Entregue') {
                                                                                            echo "selected";
                                                                                        } ?>>Entregue</option>
                                                            <option value="Cancelado" <?php if ($situacao == 'Cancelado') {
                                                                                            echo "selected";
                                                                                        } ?>>Cancelado</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Example Date Range -->

                                                <div class="row d-flex">
                                                    <div class="col col-3">
                                                        <label class="control-label" for="inputBasicFirstName">Data Ínicial</label>
                                                        <input type="date" class="form-control text-center" name="start" id="start" value="<?php echo $start; ?>" />
                                                    </div>

                                                    <div class="col col-3 text-center">
                                                        <label class="control-label" for="inputBasicFirstName">Data Final</label>
                                                        <input type="date" class="form-control text-center" name="end" value="<?php echo $end; ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="control-label">Modalidades</label>
                                                        <div class="row">
                                                            <div class="col-1">
                                                                <div class="checkbox-custom checkbox-primary">
                                                                    <input type="checkbox" name="cb_rx" value='RX' <?php if ($RX == 'RX') echo "checked"; ?>>
                                                                    <label>RX</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-1">
                                                                <div class="checkbox-custom checkbox-primary">
                                                                    <input type="checkbox" name="cb_us" value='US' <?php if ($US == 'US') echo "checked"; ?>>
                                                                    <label>US</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-1">
                                                                <div class="checkbox-custom checkbox-primary">
                                                                    <input type="checkbox" name="cb_ec" value='EC' <?php if ($EC == 'EC') echo "checked"; ?>>
                                                                    <label>ECG</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>


                                            <div class="col-12 mt-3" align="center">
                                                <button type="submit" name="pesquisa" value="semana" class="btn btn-primary">Pesquisar</button>
                                                <button type="submit" name="semana" value="semana" class="btn btn-custom">Semana</button>
                                                <button type="submit" name="ontem" value="ontem" class="btn btn-warning">Ontem</button>
                                                <button type="submit" name="hoje" value="hoje" class="btn btn-success">Hoje</button>
                                            </div>




                                        </div>
                                </div>
                                <!-- End Panel Inline Form -->
                                <!-- Panel Basic -->
                                <div class="panel">
                                    <header class="panel-heading">
                                        <div class="panel-actions"></div>
                                    </header>
                                    <div class="panel-body"><br><br>
                                        <table id="dados" class="table dataTable condensed width-full" data-plugin="dataTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="checkbox-custom checkbox-primary"><input type="checkbox" name="todos" id='todos' onclick='marcardesmarcar();' value="T"><label></label></div>
                                                    </th>
                                                    <th width="5%">Solicitação</th>
                                                    <th width="5%">Pedido</th>
                                                    <th width="25%">Paciente</th>
                                                    <th width="23%">Exame</th>
                                                    <th width="8%">Laudo</th>
                                                    <th width="8%">Conferencia</th>
                                                    <th width="6%">Hr.Realiz</th>
                                                    <th width="5%">Situação</th>
                                                    <th width="20%">Ação</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th width="5%">Solicitação</th>
                                                    <th width="5%">ID</th>
                                                    <th width="25%">Paciente</th>
                                                    <th width="23%">Exame</th>
                                                    <th width="8%">Laudo</th>
                                                    <th width="8%">Conferencia</th>
                                                    <th width="6%">Hr.Realiz</th>
                                                    <th width="5%">Situação</th>
                                                    <th width="20%">Ação</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                $date = date('Y-m-d');
                                                include('conexao.php');
                                                $stmt = "select a.transacao, a.exame_nro, b.paciente_id, a.exame_id, a.situacao, b.transacao, a.med_analise, b.dat_cad as cadastro, b.dt_solicitacao, b.dt_realizacao, b.convenio_id, a.pedido,
									c.nome, d.sigla as convenio, a.exame_id, e.descricao as desc_exames, f.sigla as modalidade, g.nome as medresp, h.nome as medconf from itenspedidos a left join pedidos b on 
									b.transacao=a.transacao left join pessoas c on b.paciente_id=c.pessoa_id  left join convenios d on b.convenio_id=d.convenio_id
									left join procedimentos e on a.exame_id=e.procedimento_id left join modalidades f on e.modalidade_id=f.modalidade_id 
									left join pessoas g on (a.med_analise=g.username and g.username is not null)left join pessoas h on (a.med_confere=h.username and h.username is not null)";
                                                if ($where != "") {
                                                    $stmt = $stmt . " where " . $where;
                                                } else {
                                                    $stmt = $stmt . " where dat_cad = '$data' ";
                                                } //estou mexendo aqui...
                                                $stmt = $stmt . "  AND f.modalidade_id in (1,2,4) ORDER BY b.dt_solicitacao desc";
                                                $sth = pg_query($stmt) or die($stmt);
                                                //echo $stmt;
                                                while ($row = pg_fetch_object($sth)) {
                                                    if (HABILITAR_PACS) {
                                                    include('conexao_pacs.php');
                                                    $stmt = "select a.pat_id, b.study_iuid, b.study_datetime from patient a, study b where b.patient_fk=a.pk and b.accession_no='$row->exame_nro' ";
                                                    $sthx = pg_query($stmt) or die($stmt);
                                                    }
                                                    //echo $stmt;
                                                    $rowst = pg_fetch_object($sthx);
                                                    $studyid = $rowst->study_iuid;
                                                    if ($studyid != "") {
                                                        $hora_rea = date('H:i:s', strtotime($rowst->study_datetime));
                                                    }

                                                    $classe = "class='bg-warning'";
                                                    if ($row->situacao == 'Aguardando') {
                                                        $classe = "bgcolor='#FFFFFF'";
                                                    }
                                                    if ($row->situacao == 'Editado') {
                                                        $classe = "class  ='bg-info'";
                                                    }
                                                    if ($row->situacao == 'Cadastrado') {
                                                        $classe = "class  ='bg-danger'";
                                                    }
                                                    if ($row->situacao == 'Realizado') {
                                                        $classe = "class  ='bg-success'";
                                                    }
                                                    if ($row->situacao == 'Laudado') {
                                                        $classe = "class  ='bg-primary'";
                                                    }
                                                    if ($row->situacao == 'Conferido') {
                                                        $classe = "bgcolor='#ffff00'";
                                                    }
                                                    if ($row->situacao == 'Finalizado') {
                                                        $classe = "bgcolor='#FFCC00'";
                                                    }
                                                    if ($row->situacao == 'Recepção') {
                                                        $classe = "bgcolor='#E6E6FA'";
                                                    }
                                                    if ($row->situacao == 'Entregue') {
                                                        $classe = "class  ='bg-warning'";
                                                    }
                                                    if ($row->situacao == 'Cancelado') {
                                                        $classe = "class  ='bg-active'";
                                                    }


                                                    $ip = getenv("REMOTE_ADDR");
                                                    echo "<tr " . $classe . ">";
                                                    echo "<td align='center'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                    echo "<td>" . inverteData(substr($row->cadastro, 0, 10)) . "</td>";
                                                    echo "<td>" . $row->paciente_id . "</td>";
                                                    echo "<td>" . $row->nome . "</td>";
                                                    echo "<td>" . $row->desc_exames . "-" . $row->exame_nro . "</td>";
                                                    //echo "<td>".utf8_encode($row->convenio)."</td>";							
                                                    echo "<td>" . utf8_encode(substr($row->medresp, 0, 8)) . "</td>";
                                                    echo "<td>" . utf8_encode(substr($row->medconf, 0, 8)) . "</td>";
                                                    echo "<td>" . $hora_rea . "</td>";
                                                    echo "<td>" . $row->situacao . "</td>";
                                                    echo "<td><button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Laudar\"><i class=\"fas fa-search\" aria-hidden=\"true\" onclick=\"openInNewTab('emitelaudos.php?id=$row->exame_nro')\"></i></button>";
                                                    if ($studyid != '') {
                                                        if (substr($ip, 0, 3) == "192") {
                                                            echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Imagens\"><i class=\"fas fa-x-ray\" aria-hidden=\"true\" onclick=\"window.open('http://" . URL_PACS . "/oviyam2/viewer.html?studyUID=" . $studyid . "&serverName=" . SERVER_PACS . "', 'Visualizador', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;\"></i></button>";
                                                        } else {
                                                            echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Imagens\"><i class=\"fas fa-x-ray\" aria-hidden=\"true\" onclick=\"window.open('http://" . URL_PACS . "/oviyam2/viewer.html?studyUID=" . $studyid . "&serverName=" . SERVER_PACS . "', 'Visualizador', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;\")\"></i></button>";
                                                        }
                                                    }
                                                    // echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Laudo de Imagens\"><i class=\"fas fa-print\" aria-hidden=\"true\" onclick=\"openInNewTab('emiteimagens.php?id=$row->exame_nro')\"></i></button>";
                                                    echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Protocolo\"><i class=\"fas fa-print\" aria-hidden=\"true\" onclick=\"openInNewTab('relpedido.php?transacao=$row->transacao')\"></i></button></td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>




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
</body>

</html>