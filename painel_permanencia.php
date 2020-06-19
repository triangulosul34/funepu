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
$start          = date('d/m/Y');
$end          = date('d/m/Y');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $procedimentox = $_POST['procedimentox'];
    $destino     = $_POST['encaminhamento'];
    $nome           = $_POST['nome'];
    $xbox          = $_POST['xbox'];
    $CM              = $_POST['cb_cm'];
    $OR           = $_POST['cb_ort'];
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

    if ($OR != "") {
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

    if ($destino != "") {

        if ($where != "") {
            $where = $where . " and a.destino_paciente = '$destino' ";
        } else {
            $where = $where . " a.destino_paciente = '$destino' ";
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
    <title>FUNEPU | Painel Permanencia</title>
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
                                                        » </p>Painel Permanencia
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
                                                <li class="active">Painel Permanencia</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" method="POST">
                                        <div class="row">
                                            <div class="col-4">
                                                <label>Paciente</label>
                                                <input type="text" class="form-control square" id="inputBasicFirstName" name="nome" placeholder="Parte do Nome" autocomplete="off" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)" />
                                            </div>
                                            <div class="col-3">
                                                <label>Encaminhamento</label>
                                                <select class="form-control square" name="encaminhamento" id="encaminhamento">
                                                    <option value="">Todos</option>
                                                    <option value="07" <?php if ($destino == '07') echo "selected"; ?>>EM OBSERVAÇÃO / MEDICAÇÃO</option>;
                                                    <option value="03" <?php if ($destino == '03') echo "selected"; ?>>PERMANÊCIA.</option>;
                                                    <option value="10" <?php if ($destino == '10') echo "selected"; ?>>EXAMES / REAVALIACAO</option>;
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Data Inicial</label>
                                                            <input type="date" class="form-control square" name="start" id="start" OnKeyPress="formatar('##/##/####', this)" value="<?php echo $start; ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Data Final</label>
                                                            <input type="date" class="form-control square" name="end" id="end" OnKeyPress="formatar('##/##/####', this)"/ value="<?php echo $end; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <label>Especialidades</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="cb_cm" name="cb_cm" value='CM' <?php if ($CM == 'CM') echo "checked"; ?>>
                                                    <label class="custom-control-label" for="cb_cm">Clinica Médica</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="cb_ort" name="cb_ort" value='OR' <?php if ($OR == 'OR') echo "checked"; ?>>
                                                    <label class="custom-control-label" for="cb_ort">Ortopedia</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <label class="control-label">Ação</label><br>
                                                <button type="submit" name="pesquisa" value="semana" class="btn btn-raised btn-primary square btn-min-width mr-1 mb-1">Pesquisar</button>
                                                <button type="submit" name="hoje" value="hoje" class="btn btn-raised btn-success square btn-min-width mr-1 mb-1">Hoje</button>
                                                <button type="submit" name="ontem" value="ontem" class="btn btn-raised btn-info square btn-min-width mr-1 mb-1">Ontem</button>
                                                <button type="submit" name="semana" value="semana" class="btn btn-raised btn-warning square btn-min-width mr-1 mb-1">Semana</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h2>Legenda</h2>
                                            <div class="row mt-1">
                                                <div class="col-sm-2 center">
                                                    <div class="bg-primary" style="width:20px; height: 20px; float: left; margin-right: 5px"></div>0 a 1 dia
                                                </div>
                                                <div class="col-sm-2 center">
                                                    <div class="bg-success" style="width:20px; height: 20px; float: left; margin-right: 5px"></div>2 dias
                                                </div>
                                                <div class="col-sm-2 center">
                                                    <div style="background-color:gold; width:20px; height: 20px; float: left; margin-right: 5px"></div>3 dias
                                                </div>
                                                <div class="col-sm-2 center">
                                                    <div class="bg-warning" style="width:20px; height: 20px; float: left; margin-right: 5px"></div>4 dias
                                                </div>
                                                <div class="col-sm-2 center">
                                                    <div class="bg-danger" style="width:20px; height: 20px; float: left; margin-right: 5px"></div>mais de 4 dias
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <table id="data_table" class="table-responsive table">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div class="checkbox-custom checkbox-primary"><input type="checkbox" name="todos" id='todos' onclick='marcardesmarcar();' value="T"><label></label></div>
                                                        </th>
                                                        <th>Solicitação</th>
                                                        <th>Paciente</th>
                                                        <th>Origem</th>
                                                        <th>Chegada</th>
                                                        <th>Triagem</th>
                                                        <th>Atendimento</th>
                                                        <th>Encaminhamento</th>
                                                        <th>Destino</th>
                                                        <th>Ação</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Solicitação</th>
                                                        <th>Paciente</th>
                                                        <th>Origem</th>
                                                        <th>Chegada</th>
                                                        <th>Triagem</th>
                                                        <th>Atendimento</th>
                                                        <th>Encaminhamento</th>
                                                        <th>Destino</th>
                                                        <th>Ação</th>

                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                    include('conexao.php');
                                                    $stmt = "select
									date_part('day', age(dat_cad )) as dia, date_part('month', age(dat_cad )) as mes,
									a.transacao,d.nome as nomemed, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.dat_cad, c.nome, k.origem, a.tipo,a.hora_destino,
									CASE prioridade 
									WHEN 'VERMELHO' THEN '0' 
									WHEN 'LARANJA' THEN '1' 
									WHEN 'AMARELO' THEN '2' 
									WHEN 'VERDE' THEN '3'  
									WHEN 'AZUL' THEN '4' 
									ELSE '5'
									END as ORDEM,
									CASE destino_paciente 
									WHEN '05' THEN 'TRANSF. INTERN. HOSPITALAR' 
									WHEN '03' THEN 'PERMANÊCIA.' 
									WHEN '07' THEN 'EM OBSERVAÇÃO / MEDICAÇÃO' 
									WHEN '10' THEN 'EXAMES / REAVALIACAO' 
									END as destino,
									CASE p.destino_encaminhamento 
									WHEN '01' THEN 'ALTA' 
									WHEN '02' THEN 'ALTA / ENCAM. AMBUL.' 
									WHEN '11' THEN 'ALTA EVASÃO' 
									WHEN '12' THEN 'ALTA PEDIDO' 
									WHEN '04' THEN 'TRANSF. OUTRA UPA' 
									WHEN '05' THEN 'TRANSFERENCIA HOSPITALAR' 
									WHEN '13' THEN 'TRANSFERENCIA INTERNA' 
									WHEN '06' THEN 'ÓBITO' 
									END as destinoalta
									from atendimentos a 
									left join pessoas c on a.paciente_id=c.pessoa_id
									left join pessoas d on a.med_atendimento=d.username
									left join destino_paciente p on p.atendimento_id = a.transacao
									left join tipo_origem k on k.tipo_id=cast(a.tipo as integer)  ";

                                                    if ($where != "") {
                                                        $stmt = $stmt . " where a.destino_paciente = '03' and " . $where;
                                                    } else {
                                                        $stmt = $stmt . " where a.destino_paciente = '03' and dat_cad between '$start' and '$end'";
                                                    }
                                                    $stmt = $stmt . " and p.destino_encaminhamento is null order by a.dat_cad desc,a.hora_cad desc";
                                                    $sth = pg_query($stmt) or die($stmt);
                                                    //echo $stmt; 
                                                    while ($row = pg_fetch_object($sth)) {



                                                        if ($row->mes < 1 && $row->dia   <= 1) {
                                                            $classe = "class='bg-primary'";
                                                        }
                                                        if ($row->mes < 1 && $row->dia   == 2) {
                                                            $classe = "class='bg-success'";
                                                        }
                                                        if ($row->mes < 1 && $row->dia   == 3) {
                                                            $classe = "style=\"background-color:gold\"";
                                                        }
                                                        if ($row->mes < 1 && $row->dia   == 4) {
                                                            $classe = "class='bg-warning'";
                                                        }
                                                        if ($row->mes < 1 && $row->dia   > 4) {
                                                            $classe = "class='bg-danger'";
                                                        }
                                                        if ($row->mes > 1) {
                                                            $classe = "class='bg-danger'";
                                                        }


                                                        $ip = getenv("REMOTE_ADDR");
                                                        echo "<tr " . $classe . ">";
                                                        echo "<td align='center'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                        echo "<td>" . inverteData(substr($row->dat_cad, 0, 10)) . "</td>";
                                                        echo "<td>" . $row->nome . "</td>";
                                                        echo "<td>" . $row->origem . "</td>";
                                                        echo "<td>" . $row->hora_cad . "</td>";
                                                        echo "<td>" . $row->hora_triagem . "</td>";
                                                        echo "<td>" . $row->hora_destino . "</td>";


                                                        echo "<td class=\"small\">" . $row->destino . "</td>";
                                                        echo "<td class=\"small\">" . $row->destinoalta . "</td>";



                                                        echo "<td>";

                                                        if ($perfil == '03' or $perfil == '06') {
                                                            echo "<a href=\"atendimentoclinico.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\"><i class=\"fas fa-search\"></i></a>";
                                                        }
                                                        echo "<a href=\"relFAA.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon  btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"FAA\"><i class=\"fas fa-print\"></i></a>";

                                                        if ($perfil == '03' or $perfil == '06' or $perfil == '08' or $perfil == '13') {
                                                            echo "<a href=\"evolucao_atendimento.php?id=$row->transacao\" target=\"_blank\" class=\"btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Evolução\"><i class=\"far fa-user\"></i></a>";
                                                        }
                                                        if ($perfil == '03' or $perfil == '06' or $perfil == '13') {
                                                            echo "<a href=\"prescricao_enfermagemx.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Prescrição\"><i class=\"far fa-file-pdf\"></i></a>";
                                                        }
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
</body>

</html>