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
    $start  = $_GET['start'];
    $end    = $_GET['end'];
    if ($codigo != "") {
        $where = ' pessoa_id =' . $codigo;
    }
    if ($start == "") {
        $start  = date('d/m/Y');
        $end    = date('d/m/Y');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $procedimentox    = $_POST['procedimentox'];
    $situacao         = $_POST['situacao'];
    $nome               = $_POST['nome'];
    $xbox              = $_POST['xbox'];
    $CM                  = $_POST['cb_cm'];
    $OR               = $_POST['cb_or'];
    $start               = $_POST['start'];
    $end               = $_POST['end'];
    $transfere           = $_POST['cb_exame'];
    $profissional     = $_POST['prof_transfere'];
    $cb_meus          = $_POST['cb_meus'];
    $cb_conf          = $_POST['cb_CONFERENCIA'];
    $prontuario        = $_POST['prontuario'];
    $trs        = $_POST['trs'];
    $palavras = explode(" ", $nome);

    // if ((count($palavras) < 2) and $nome != '') {
    //     $pesquisa = 'qwwwqq';
    //     echo  "<script>alert('Consulta Invalida! Seja Especifico. Digite o nome e o sobrenome');</script>";
    // } else {


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
        $where = $where . " c.nome like '" . $nome . "%' ";
    }

    if ($procedimentox != "") {
        if ($where != "") {
            $where = $where . " and a.exame_id = $procedimentox";
        } else {
            $where = $where . " a.exame_id = $procedimentox";
        }
    }

    if ($trs != "") {
        if ($where != "") {
            $where = $where . " and a.transacao = $trs";
        } else {
            $where = $where . " a.transacao = $trs";
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


    if ($prontuario != '') {
        if ($where != "") {
            $where = $where . " and (a.paciente_id = '$prontuario')";
        } else {
            $where = $where . " (a.paciente_id = '$prontuario')";
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

if (isset($_POST["excel"])) {

    $arquivo = 'Relatorio Atendimento.xls';
    // Criamos uma tabela HTML com o formato da planilha
    $html = '';
    $html .= '<table style="font-size:8px" border="1">';
    $html .= '<tr>';
    $html .= '<td colspan="5" align=\'center\'>UPA PARQUE DO MIRANTE - RELACAO ATENDIMENTOS</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<tr align=\'center\'>';
    $html .= '<td><b>Solicitacao</b></td>';
    $html .= '<td><b>Paciente</b></td>';
    $html .= '<td><b>Origem</b></td>';
    $html .= '<td><b>Chegada</b></td>';
    $html .= '<td><b>Triagem</b></td>';
    $html .= '<td><b>Atendimento</b></td>';
    $html .= '<td><b>Situacao</b></td>';
    $html .= '</tr>';
    include('conexao.php');
    $stmt = "select a.transacao,d.nome as nomemed, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento,
						 a.dat_cad as cadastro, 	c.nome, k.origem, a.tipo,a.hora_destino,
						CASE prioridade 
									WHEN 'VERMELHO' THEN '0' 
									WHEN 'LARANJA' THEN '1' 
									WHEN 'AMARELO' THEN '2'
									WHEN 'VERDE' THEN '3'  
									WHEN 'AZUL' THEN '4' 
									ELSE '5'
									END as ORDEM from atendimentos a 
						left join pessoas c on a.paciente_id = c.pessoa_id
						left join pessoas d on a.med_atendimento = d.username
						left join tipo_origem k on cast(k.tipo_id as varchar) = a.tipo  ";

    if ($where != "") {
        $stmt = $stmt . " where " . $where;
    } else {
        $stmt = $stmt . " where dat_cad='" . date('Y-m-d') . "'";
    }

    $stmt = $stmt . " order by a.dat_cad desc,a.hora_cad desc ";
    $sth = pg_query($stmt) or die($stmt);
    //echo $stmt;
    $qtde = 0;
    while ($row = pg_fetch_object($sth)) {

        $html .= '<tr>';
        $html .= '<td>' . inverteData(substr($row->cadastro, 0, 10)) . '</td>';
        $html .= '<td>' . $row->nome . '</td>';
        $html .= '<td>' . $row->origem . '</td>';
        $html .= '<td>' . $row->hora_cad . '</td>';
        $html .= '<td>' . $row->hora_triagem . '</td>';
        $html .= '<td>' . $row->hora_destino . '</td>';
        $html .= '<td>' . $row->status . '</td>';
        $html .= '</tr>';

        $qtde = $qtde + 1;
    }
    $html .= '<tr>';
    $html .= '<td>Quantidade de Pacientes</td>';
    $html .= '<td></td>';
    $html .= '<td></td>';
    $html .= '<td></td>';
    $html .= '<td></td>';
    $html .= '<td></td>';
    $html .= '<td>' . $qtde . '</td>';
    $html .= '</tr>';
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
// }
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
    <title>FUNEPU | Pedidos</title>
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
    <script>

    </script>

</head>
<style>
    .table {
        width: 100%;
        margin-bottom: 1.5rem;
        color: #212529;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid white;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody+tbody {
        border-top: 2px solid #dee2e6;
    }



    footer {
        padding-left: 0px;
    }

    .blink {
        animation-duration: 0.85s;
        animation-name: blink;
        animation-iteration-count: infinite;
        animation-direction: alternate;
        animation-timing-function: ease-in-out;
    }

    @keyframes blink {
        from {
            color: white;
        }

        to {
            color: darkred;
        }
    }

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

    a {
        color: white;
    }

    a:hover {
        color: white;
        font-weight: 500;
        font-size: 15px;
    }
</style>

<body class="pace-done" cz-shortcut-listen="true">
    <div class="modal fade" id="modalConteudoSitu" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalTabs">Alterar Situação</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="conteudoModalSituacao"></div>
                <div class="modal-footer">
                    <div class="col-md-12 margin-top-10 padding-0">
                        <div class="row">
                            <div class="col-md-6">
                                <input type='button' name='confSit' id="confSit" onclick="situacao()" class="btn btn-success width-full" value='Confirmar Situação'>
                            </div>
                            <div class="col-md-6">
                                <input type='button' name='cancelarModal' data-dismiss="modal" id="cancelarModal" class="btn btn-danger width-full" value='Cancelar'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

    <!-- Modal -->
    <div class="modal" style='position: absolute;' id="modalConteudo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
        <div class="modal-dialog" role="document" style="max-width: 600px">
            <div class="modal-content">
                <div class="modal-header" style="padding: 0.5rem 1rem;">
                    <h6 class="modal-title" id="myModalLabel1">Triagem</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="conteudoModal"></div>
                <div class="modal-footer">
                    <div class="col-md-6">
                        <input type='button' name='confTriagem' id="confTriagem" class="btn btn-success width-full" value='Confirmar Triagem'>
                    </div>
                    <div class="col-md-6">
                        <input type='button' name='cancelarModal' data-dismiss="modal" id="cancelarModal" class="btn btn-danger width-full" value='Cancelar'>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- <div class="wrapper"> -->
    <!-- <?php include('menu.php'); ?> -->
    <?php include('header2.php'); ?>
    <div class="main-panel">
        <div class="">
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
                                                <li><a href="index.php" style="color: black;">Home</a></li>
                                                <li><a href="#" style="color: black;">Atendimentos</a></li>
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

                                                    <div class="col-md-2">
                                                        <label class="control-label" for="inputBasicFirstName">Atendimento</label>
                                                        <input type="text" class="form-control" id="trs" name="trs" value="<?php echo $trs; ?>" />
                                                    </div>
                                                    <div class="col col-lg-3">
                                                        <label class="control-label" for="inputBasicFirstName">Data Ínicial</label>
                                                        <input type="date" class="form-control text-center" name="start" id="start" value="<?php echo $start; ?>" />
                                                    </div>

                                                    <div class="col col-lg-3 text-center">
                                                        <label class="control-label" for="inputBasicFirstName">Data Final</label>
                                                        <input type="date" class="form-control text-center" name="end" value="<?php echo $end; ?>">
                                                    </div>





                                                    <div class="col-md-4">
                                                        <label class="control-label" for="inputBasicFirstName">Paciente</label>
                                                        <input type="text" class="form-control" id="inputBasicFirstName" name="nome" placeholder="Parte do Nome" autocomplete="off" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)" />
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="control-label">Situacao</label>
                                                        <select class="form-control" name="situacao" id="situacao">
                                                            <option value="">Todos</option>
                                                            <option value="Aguardando Triagem" <?php if ($situacao == 'Aguardando Triagem') {
                                                                                                    echo "selected";
                                                                                                } ?>>Aguardando Triagem</option>
                                                            <option value="Aguardando Atendimento" <?php if ($situacao == 'Aguardando Atendimento') {
                                                                                                        echo "selected";
                                                                                                    } ?>>Aguardando Atendimento</option>
                                                            <option value="Em Atendimento" <?php if ($situacao == 'Em Atendimento') {
                                                                                                echo "selected";
                                                                                            } ?>>Em Atendimento</option>
                                                            <option value="Atendimento Finalizado" <?php if ($situacao == 'Atendimento Finalizado') {
                                                                                                        echo "selected";
                                                                                                    } ?>>Atendimento Finalizado</option>
                                                            <option value="Não Resp. Chamado" <?php if ($situacao == 'Não Resp. Chamado') {
                                                                                                    echo "selected";
                                                                                                } ?>>Não Resp. Chamado</option>


                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">

                                                        <label class="control-label">Especialidades:</label>

                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="cb_cm" name="cb_cm" value='CM' <?php if ($CM == 'CM') echo "checked"; ?>>
                                                            <label class="custom-control-label" for="cb_cm">Clinica Médica</label>
                                                        </div>



                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="cb_or" name="cb_or" value='OR' <?php if ($OR == 'OR') echo "checked"; ?>>
                                                            <label class="custom-control-label" for="cb_or">Ortopedia</label>
                                                        </div>



                                                    </div>




                                                    <div class="col-8 text-center">
                                                        <label class="control-label">Ação</label><br>

                                                        <button type="submit" name="pesquisa" value="semana" class="btn btn-primary">Pesquisar</button>
                                                        <button type="submit" name="semana" value="semana" class="btn btn-custom">Semana</button>
                                                        <button type="submit" name="ontem" value="ontem" class="btn btn-warning">Ontem</button>
                                                        <button type="reset" name="limpar" value="limpar" class="btn btn-danger">Limpar</button>
                                                        <button type="button" class="btn btn-success" onclick="location.href='novoatendimento.php'"><i class="icon-stack2 position-left"></i> Adicionar novo Atendimento</button>
                                                        <?php if ($perfil == '04' or $perfil == '06') { ?>
                                                            <button type="button" class="btn btn-success" style="margin-top: 2px" onclick="location.href='novoatendimentoretroativo.php'"><i class="icon-stack2 position-left"></i> Atendimento Retroativo</button>
                                                        <?php } ?>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <table id="data_table" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th style="display:none;">
                                                                <div class="checkbox-custom checkbox-primary"><input type="checkbox" name="todos" id='todos' onclick='marcardesmarcar();' value="T"><label></label></div>
                                                            </th>
                                                            <th>Data/Hora</th>
                                                            <th>Paciente</th>
                                                            <th>Triagem</th>
                                                            <th>Atend.</th>
                                                            <th>Situação</th>
                                                            <th>Status Atend.</th>
                                                            <th>Ação</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th style="display:none;">#</th>
                                                            <th>Data/Hora</th>
                                                            <th>Paciente</th>
                                                            <th>Triagem</th>
                                                            <th>Atend.</th>
                                                            <th>Situação</th>
                                                            <th>Status Atend.</th>
                                                            <th>Ação</th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
                                                        include('conexao.php');
                                                        $stmt = "select a.transacao,d.nome as nomemed, case when z.destino_encaminhamento::varchar is null then a.destino_paciente else z.destino_encaminhamento::varchar end as destino_paciente, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.dat_cad as cadastro, 	c.nome, k.origem, a.tipo,a.hora_destino,
                                                        CASE prioridade WHEN 'VERMELHO' THEN '0' WHEN 'LARANJA' THEN '1' WHEN 'AMARELO' THEN '2' WHEN 'VERDE' THEN '3'  WHEN 'AZUL' THEN '4' ELSE '5'
                                                        END as ORDEM, a.coronavirus from atendimentos a 
                                                        left join pessoas c on a.paciente_id=c.pessoa_id
                                                        left join pessoas d on a.med_atendimento=d.username 
                                                        left join tipo_origem k on cast(k.tipo_id as varchar)=a.tipo 
                                                        left join destino_paciente z on a.transacao = z.atendimento_id";
                                                        if ($where != "") {
                                                            $stmt = $stmt . " where " . $where;
                                                        } else {
                                                            $stmt = $stmt . " where dat_cad='" . date('Y-m-d') . "'";
                                                        }
                                                        $stmt = $stmt . " order by a.dat_cad desc,a.hora_cad desc ";
                                                        $sth = pg_query($stmt) or die($stmt);
                                                        //echo $stmt; 
                                                        while ($row = pg_fetch_object($sth)) {

                                                            if ($row->prioridade   == 'AMARELO') {
                                                                $classe = "style=\"background-color:#FFEE58\"";
                                                                $color = "black";
                                                            }
                                                            if ($row->prioridade   == 'VERMELHO') {
                                                                $classe = "class='bg-danger' style='color: white'";
                                                                $color = "white";
                                                            }
                                                            if ($row->prioridade   == 'VERDE') {
                                                                $classe = "class='bg-success' style='color: white'";
                                                                $color = "white";
                                                            }
                                                            if ($row->prioridade   == 'AZUL') {
                                                                $classe = "class='bg-info' style='color: white'";
                                                                $color = "white";
                                                            }
                                                            if ($row->prioridade   == 'LARANJA') {
                                                                $classe = "class='bg-warning' style='color: white'";
                                                                $color = "white";
                                                            }
                                                            if ($row->prioridade   == '') {
                                                                $classe = "style=\"background-color:Gainsboro\"";
                                                                $color = "black";
                                                            } else {
                                                                $color = "black";
                                                            }

                                                            if ($row->destino_paciente == '03') {
                                                                $classe = "style=\"background-color:#4B0082\"";
                                                                $color = "white";
                                                            }

                                                            $ip = getenv("REMOTE_ADDR");
                                                            echo "<tr " . $classe . " >";
                                                            if ($row->coronavirus == 1) {
                                                                echo "<td style='display:none;'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                echo "<td class='blink' style=\"color:$color\">" . inverteData(substr($row->cadastro, 0, 10)) . '<br>' . $row->hora_cad . '<br>' . $row->paciente_id . "</td>";
                                                                echo "<td class='blink'><a data-toggle=\"popover\" data-content=\"Ir para o cadastro do paciente.\" data-trigger=\"hover\" data-original-title=\"Paciente\" href='novoatendimento.php?id=" . $row->transacao . "' target='_blank'>" . $row->nome . '<br><br> Origem:' . $row->origem . "</a></td>";
                                                                //echo "<td>".utf8_encode($row->convenio)."</td>";							
                                                                echo "<td class='blink' style=\"color:$color\">" . $row->hora_triagem . "</td>";
                                                                echo "<td class='blink' style=\"color:$color\">" . $row->hora_destino . "</td>";

                                                                if ($row->status == 'Atendimento Finalizado') {
                                                                    echo "<td class='blink' style=\"color:$color\">" . $row->status . "<br>";
                                                                    echo "<small>" . substr($row->nomemed, 0, 21) . "</small></td>";
                                                                } else {
                                                                    echo "<td class='blink' style=\"color:$color\">" . $row->status . " - " . substr($row->nomemed, 0, 21) . "</td>";
                                                                }
                                                            } else {
                                                                echo "<td style='display:none;'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                echo "<td style=\"color:$color\">" . inverteData(substr($row->cadastro, 0, 10)) . '<br>' . $row->hora_cad .  '<br>' . $row->paciente_id . "</td>";
                                                                echo "<td ><a data-toggle=\"popover\" data-content=\"Ir para o cadastro do paciente.\" data-trigger=\"hover\" data-original-title=\"Paciente\" href='novoatendimento.php?id=" . $row->transacao . "' target='_blank' style=\"color:$color\">" . $row->nome . '<br><br> Origem:' . $row->origem . "</a></td>";
                                                                //echo "<td>".utf8_encode($row->convenio)."</td>";							
                                                                echo "<td style=\"color:$color\">" . $row->hora_triagem . "</td>";
                                                                echo "<td style=\"color:$color\">" . $row->hora_destino . "</td>";

                                                                if ($row->status == 'Atendimento Finalizado') {
                                                                    echo "<td style=\"color:$color\">" . $row->status . "<br>";
                                                                    echo "<small>" . substr($row->nomemed, 0, 21) . "</small></td>";
                                                                } else {
                                                                    echo "<td style=\"color:$color\">" . $row->status . " - " . substr($row->nomemed, 0, 21) . "</td>";
                                                                }
                                                            }
                                                            echo "<td style=\"color:$color\">";
                                                            if ($row->destino_paciente == '01') {
                                                                echo 'ALTA';
                                                            } elseif ($row->destino_paciente == '02') {
                                                                echo 'ALTA / ENCAM. AMBUL.';
                                                            } elseif ($row->destino_paciente == '07') {
                                                                echo 'EM OBSERVAÇÃO / MEDICAÇÃO';
                                                            } elseif ($row->destino_paciente == '10') {
                                                                echo 'EXAMES / REAVALIACAO';
                                                            } elseif ($row->destino_paciente == '03') {
                                                                echo 'PERMANÊCIA.';
                                                            } elseif ($row->destino_paciente == '04') {
                                                                echo 'TRANSF. OUTRA UPA';
                                                            } elseif ($row->destino_paciente == '05') {
                                                                echo 'TRANSF. INTERN. HOSPITALAR';
                                                            } elseif ($row->destino_paciente == '06') {
                                                                echo 'ÓBITO';
                                                            } elseif ($row->destino_paciente == '09') {
                                                                echo 'NAO RESPONDEU CHAMADO';
                                                            } elseif ($row->destino_paciente == '11') {
                                                                echo 'ALTA EVASÃO';
                                                            } elseif ($row->destino_paciente == '12') {
                                                                echo 'ALTA PEDIDO';
                                                            } elseif ($row->destino_paciente == '14') {
                                                                echo 'ALTA / POLICIA';
                                                            } elseif ($row->destino_paciente == '15') {
                                                                echo 'ALTA / PENITENCIÁRIA';
                                                            } elseif ($row->destino_paciente == '16') {
                                                                echo 'ALTA / PÓS MEDICAMENTO';
                                                            } elseif ($row->destino_paciente == '20') {
                                                                echo 'ALTA VIA SISTEMA';
                                                            } elseif ($row->destino_paciente == '21') {
                                                                echo 'TRANSFERENCIA';
                                                            }
                                                            echo "</td>";

                                                            echo "<td>";
                                                            /*if($row->status != 'Aguardando Triagem'){*/
                                                            echo "<a href=\"atendimentoclinico.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\" style=\"color:$color\"><i class=\"fas fa-file-medical\"></i></a>";
                                                            /*}*/


                                                            if ($row->tipo == 9) {
                                                                echo "<a href=\"relOdonto.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"ODONTOLOGICO\" style=\"color:$color\"><i class=\"fas fa-print\"></i></a>";
                                                            } else {
                                                                echo "<a href=\"relFAA.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"FAA\" style=\"color:$color\"><i class=\"fas fa-print\"></i></a>";
                                                            }
                                                            // if ($perfil == '06' or $perfil == '04') { 
                                                        ?>
                                                            <?php if ($row->status != 'Atendimento Finalizado') { ?>
                                                                <a id="triagemmanual" data-id="<?php echo $row->transacao; ?>" class="btn btn-sm btn-icon btn-pure btn-default delete-row-btn" data-target="#modalConteudo" data-toggle="modal" data-original-title="Triagem" <?php if ($row->prioridade != '' and $row->prioridade != 'AMARELO') { ?>style="color:white" <?php } ?> onClick="valorTriagem(this);">
                                                                    <i class="fas fa-check-circle" aria-hidden="true" onclick=""></i>
                                                                </a>
                                                            <?php } ?>
                                                            <?php //}

                                                            //if ($perfil == '06' or $perfil == '04' or $perfil == '01') { 
                                                            ?>
                                                            <a id="mudasituacao" data-id="<?php echo $row->transacao; ?>" class="btn btn-sm btn-icon btn-pure btn-default delete-row-btn" data-target="#modalConteudoSitu" data-toggle="modal" data-original-title="Mudar Situação" <?php if ($row->prioridade != '' and $row->prioridade != 'AMARELO') { ?>style="color:white" <?php } ?> onClick="valorSituacao(this);">
                                                                <i class="fa fa-user" aria-hidden="true" onclick=""></i>
                                                            </a>
                                                            <?php //} 
                                                            ?>

                                                        <?php echo "</tr>";
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- <div class="col-md-12">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                    <h4 class="modal-title" id="exampleModalTabs">Triagem</h4>
                                                                    <div class="col-md-12" id="conteudoModal"></div>
                                                                    
                                                                </div>

                                                            </div> -->


                                            <!-- End Modal -->

                                            <!-- Modal -->

                                            <!-- End Modal -->

                                        </div>
                                </div>

                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>
    </div>
    </div>
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
        // $('#data_table_previous').text("Anterior")

        // $('#data_table').dataTable({
        //     "iDisplayLength": 100
        // });

        $("#procedimentox").chosen({
            placeholder_text_single: "Selecione...",
            search_contains: true
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

        function carrega_discriminador() {
            var fluxo = document.getElementById("fluxograma").value;
            var paciente = document.getElementById("paciente").value;
            var url = 'carrega_discriminador.php?fluxo=' + fluxo + '&paciente=' + paciente;
            $.get(url, function(dataReturn) {
                $('#load_discriminador').html(dataReturn);
            });
        }

        // Breakpoints();

        function carrega_notificacao(campo, paciente) {
            var id_discriminador = campo.value;
            $.post("get_notificacao_discriminacao.php", {
                id_discriminador: id_discriminador,
                paciente: paciente
            }, function(data) {
                if (data != '') {
                    swal("Atenção", "Fluxo com notficação obrigatória", "warning");
                    window.open('pdf_notificacao/' + data, '_blank');
                }
            });
        }



        function valorTriagem(valor) {
            transacao = $(valor).attr("data-id");

            $.get('triagemmanual.php?transacao=' + transacao, function(dataReturn) {
                $('#conteudoModal').html(dataReturn);
            });

            event.preventDefault();
        }


        function valorSituacao(valor) {
            transacao = $(valor).attr("data-id");

            $.get('mudasituacao.php?transacao=' + transacao, function(dataReturn) {
                $('#conteudoModalSituacao').html(dataReturn);
            });

            event.preventDefault();
        }

        $("#confTriagem").click(function(event) {
            var transacaoModal = $("#transacaoModal").val();
            var consultorioModal = $("#consultorioModal").val();
            var prioridadeModal = $("#prioridadeModal").val();
            var discriminador = $("#discriminador option:selected").text();
            var fluxograma = $("#fluxograma option:selected").text();
            var pa_sis = $("#pa_sis").val();
            var pa_dist = $("#pa_dis").val();
            var temperatura = $("#temp").val();
            var dor = $("#dor").val();
            var queixa = $("#queixa").val();
            var peso = $("#peso").val();
            var oxigenio = $("#oxigenio").val();
            var pulso = $("#pulso").val();
            var glicose = $("#glicose").val();


            if (prioridadeModal == '') {
                sweetAlert("Informe a prioridade para o atendimento!", "", "warning");
            } else {
                $.post("salvartriagemmanual.php", {
                        transacaoModal: transacaoModal,
                        consultorioModal: consultorioModal,
                        prioridadeModal: prioridadeModal,
                        discriminador: discriminador,
                        fluxograma: fluxograma,
                        pa_sis: pa_sis,
                        pa_dist: pa_dist,
                        dor: dor,
                        temperatura: temperatura,
                        queixa: queixa,
                        peso: peso,
                        oxigenio: oxigenio,
                        pulso: pulso,
                        glicose: glicose
                    },
                    function(dataReturn) {
                        alert(dataReturn);
                        $('#modalConteudo').modal('hide');
                    });
            }

        });



        function situacao() {
            var transacaoModal = $("#transacaoMod").val();
            var situacaoMod = $("#situacaoMod").val();

            $.get('salvarsituacao.php?transacaoMod=' + transacaoModal + "&situacaoMod=" + situacaoMod, function(dataReturn) {
                alert(dataReturn);
                $('#modalConteudoSitu').modal('hide');
            });

        };
    </script>
</body>

</html>