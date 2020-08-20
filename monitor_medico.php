<?php

include('verifica.php');
error_reporting(0);




include('conexao.php');
$stmt   = "select nome, imagem, perfil,sexo from pessoas where username='$usuario'";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$nome_usuario = $row->nome;
$imagem = $row->imagem;
$sexo = $row->sexo;

if ($box == '') {
    header("location:loginbox.php");
}


include('conexao.php');
$stmt   = "select descricao, tipo_atendimento from boxes where box_id='$box'";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$box_descricao    = $row->descricao;
$tipo_atendimento = $row->tipo_atendimento;
include('funcoes.php');
$atalho  = "";
$modalidade = "";
$exame         = "";
$marca        = "";
$data = date('Y-m-d');

$consultorio = '';
if (rtrim($tipo_atendimento) == 'ADULTO') {
    $consultorio = 'Consultorio Adulto';
} else {
    $consultorio = 'Ortopedia';
}

include('conexao.php');
$qtdatmed = 0;
$stmtqtdmd = "SELECT count(*) as qtd from atendimentos where med_atendimento = '$usuario' and dat_cad = '" . date('Y-m-d') . "' ";
$sthqtdmd = pg_query($stmtqtdmd) or die($stmtqtdmd);
$rowCountqtdmd = pg_fetch_object($sthqtdmd);
$qtdatmed = $rowCountqtdmd->qtd;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $codigo         = $_POST['codigo'];
    $transacao         = $_POST['transacao'];
    $pesquisa_paciente = $_POST['pesquisa_paciente'];
    $item            = $_POST['item'];
    $prontuario        = $_POST['prontuario'];
    $descricao      = $_POST['descricao'];;
    $classificacao    = $_POST['classificacao'];;
    include('conexao.php');
    $stmt   = "select descricao, tipo_atendimento from boxes where box_id='$box'";
    $sth = pg_query($stmt) or die($stmt);
    $row = pg_fetch_object($sth);
    $box_descricao    = $row->descricao;
    $tipo_atendimento = $row->tipo_atendimento;

    if (isset($_POST['proximo']) and $pesquisa_paciente == "") {
        include('conexao.php');
        $stmt   = "select a.destino_paciente, a.transacao, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.nec_especiais, a.dat_cad as cadastro,
		c.nome,c.nome_social, k.origem, f.descricao as clinica, CASE
            WHEN a.prioridade = 'VERMELHO' and a.destino_paciente is null THEN '0' 
            WHEN a.prioridade = 'LARANJA' and a.destino_paciente is null THEN '1' 
            WHEN a.prioridade = 'AMARELO' and a.destino_paciente is null THEN '2' 
            WHEN a.destino_paciente = '10'  THEN '3'
            WHEN a.prioridade = 'VERDE' and a.destino_paciente is null THEN '4' 
            WHEN a.prioridade = 'AZUL' and a.destino_paciente is null THEN '5' 
        ELSE '6' END as prioridade_cor
		from atendimentos a left join pessoas c on a.paciente_id=c.pessoa_id  left join especialidade f on a.especialidade = f.descricao left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
		 ";

        if (rtrim($tipo_atendimento) == 'ADULTO') {
            $stmt = $stmt . " where dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and (a.status = 'Aguardando Atendimento') AND a.especialidade = 'Consultorio Adulto' ";
        } elseif (rtrim($tipo_atendimento) == 'PEDIATRIA') {
            $stmt = $stmt . " where dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and (a.status = 'Aguardando Atendimento') AND a.especialidade = 'Ortopedia' ";
        } elseif (rtrim($tipo_atendimento) == 'EXAME') {
            $stmt = $stmt . " where dat_cad between '" . date('Y-m-d') . "' and '" . date('Y-m-d') . "' and (a.status = 'Aguardando Triagem') AND a.tipo = '6' ";
        } elseif (rtrim($tipo_atendimento) == 'PORTA') {
            $stmt = $stmt . " where dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and (a.status = 'Aguardando Atendimento') AND a.especialidade = 'Consultorio Adulto' and prioridade in ('AZUL','VERDE') ";
        }

        $stmt = $stmt . " and cast(a.tipo as integer) != 9 and dat_cad > '2019-08-11' order by prioridade_cor,pidade, a.hora_cad limit 1";
        $sth         = pg_query($stmt) or die($stmt);
        $row         = pg_fetch_object($sth);

        if ($row->nome_social != '') {
            $nome         = $row->nome_social . '(' . $row->nome . ')';
        } else {
            $nome         = $row->nome;
        }
        if ($row->nec_especiais != 'Nenhuma') {
            $deficiência = $row->nec_especiais;
        }
        $destino_paciente = $row->destino_paciente;
        $transacao     =  $row->transacao;
    }

    if (isset($_POST['nrchamada'])) {

        include('conexao.php');
        $stmtnrc = "update atendimentos set status='Não Respondeu Chamado',destino_paciente= (case when destino_paciente is null then '09' else destino_paciente end),data_destino='" . date('Y-m-d') . "', hora_destino='" . date('H:i') . "' where transacao = '$transacao' ";
        $sthnrc     = pg_query($stmtnrc) or die($stmtnrc);

        $data = date('Y-m-d');
        $hora = date('H:i');
        include('conexao.php');
        $stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
				values ('$usuario','FINALIZOU SEM ATENDER - NÃO RESPONDEU CHAMADO','$transacao','$data','$hora')";
        $sthLogs = pg_query($stmtLogs) or die($stmtLogs);
    }
    if (isset($_POST['nrchamadalab'])) {

        include('conexao.php');
        $stmtnrc = "update atendimentos set status='Não respondeu retorno para resultado de exames',data_destino='" . date('Y-m-d') . "', hora_destino='" . date('H:i') . "' where transacao = '$transacao' ";
        $sthnrc     = pg_query($stmtnrc) or die($stmtnrc);

        $data = date('Y-m-d');
        $hora = date('H:i');
        include('conexao.php');
        $stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
				values ('$usuario','FINALIZOU SEM ATENDER - NÃO RESPONDEU RETORNO PARA RESULTADO DE EXAMES','$transacao','$data','$hora')";
        $sthLogs = pg_query($stmtLogs) or die($stmtLogs);
    }
}
$qtdAtendiemento = "";
include('conexao.php');
$stmtCount = "SELECT count(*) as qtd from atendimentos   ";
if (rtrim($tipo_atendimento) == 'ADULTO') {
    $stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Atendimento') AND especialidade = 'Consultorio Adulto' and cast(tipo as integer) != 9";
} elseif (rtrim($tipo_atendimento) == 'ORTOPEDIA') {
    $stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Atendimento') AND especialidade = 'Consultorio Pediátrico' ";
} elseif (rtrim($tipo_atendimento) == 'EXAME') {
    $stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d') . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Triagem') AND tipo = '6' ";
} elseif (rtrim($tipo_atendimento) == 'PORTA') {
    $stmtCount = $stmtCount . " where dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and (status = 'Aguardando Atendimento') AND especialidade = 'Consultorio Adulto' and prioridade in ('AZUL','VERDE') ";
}


//$stmtCount=$stmtCount."and cast(tipo as integer) != 9";

$sthCount = pg_query($stmtCount) or die($stmtCount);
$rowCount = pg_fetch_object($sthCount);
$qtdAtendiemento = $rowCount->qtd;
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
    <title>FUNEPU | Monitor Medico</title>
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
    .custom-hr {
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

<body class="pace-done" cz-shortcut-listen="true" <?php if ($deficiência) { ?> onload="necessidades_especiais()" <?php } ?>>
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
                                                        » </p>Monitor Medico
                                                </h4>
                                            </div>
                                            <div class="col-12">
                                                <hr class="custom-hr">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <ol class="breadcrumb">
                                                <li><a href="../index.html">Home</a></li>
                                                <li class="active">Monitor Medico</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <input type="hidden" id="tipoAt" value="<?php echo $tipo_atendimento; ?>">
                                    <div id="retorno_atd"></div>
                                    <div class="row">
                                        <div class="col-12 mb-5" id="qtdPaciente">
                                            <h3 style="text-align: center;">Aguardando Atendimento: <?php echo $qtdAtendiemento; ?>


                                                <?php if ($qtdAtendiemento > 1) {
                                                    echo 'pacientes';
                                                } else {
                                                    echo 'paciente';
                                                }
                                                ?>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="fas fa-id-card-alt" style="font-size: 90pt;color:#12A1A6"></i>
                                        </div>
                                        <div class="col-10">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h1><?php echo $nome_usuario; ?></h1>
                                                </div>
                                            </div>
                                            <div class=" row">
                                                <div class="col-12">
                                                    <h2><?php echo $box_descricao; ?></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <form name='pedido' method='post'>
                                        <div class="row mt-3 mb-3">
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control square" readonly name="paciente" value='<?php echo $nome; ?>'>
                                                    <input type="hidden" class="form-control" readonly name="transacao" value='<?php echo $transacao; ?>'>
                                                    <button type="button" class="btn btn-success" onclick='atende(<?php echo $transacao; ?>)'><i class="fas fa-briefcase-medical"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12" align="center">
                                                <?php
                                                if ($nome != '' and ($destino_paciente != '09' and $destino_paciente != '10' and $destino_paciente != '03')) {
                                                    echo '<button type="button" name="nrchamada" onclick="chamada()" id="nrchamada" value="nrchamada" class="btn btn-primary">Não Respondeu Chamada</button>';
                                                } else if ($destino_paciente) {
                                                    echo '<button type="button" name="nrchamadalab" onclick="chamadalab()" id="nrchamadalab" value="nrchamadalab" class="btn btn-primary">Não Respondeu Retorno Resultado de Exames</button>';
                                                } else {
                                                    echo '<button type="submit" name="proximo"   value="proximo"   class="btn btn-success">Chamar Próximo Paciente</button>';
                                                }
                                                ?>
                                                <div id="botaoChamar">

                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($erro != '') { ?>
                                            <h1 id="erro"><?php echo $erro; ?></h1>
                                        <?php } ?>
                                        <div class="row mt-3 mb-3">
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <h3>Pesquisa por Paciente</h3>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="pesquisa_paciente">
                                                    <button type="submit" name='busca' class="btn btn-primary"><i class="fas fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <?php
                                                if ($pesquisa_paciente != "") {
                                                    include('conexao.php');
                                                    $stmt = "select a.transacao, a.cid_principal, a.destino_paciente, a.data_destino, a.queixa, a.exame_fisico, a.diagnostico_principal,a.prioridade,
                                    a.paciente_id, a.status, a.tipo, a.dat_cad as cadastro, c.nome
                                    from atendimentos a 
                                    left join pessoas c on a.paciente_id=c.pessoa_id 
                                    where c.nome like upper('%$pesquisa_paciente%') and (a.especialidade = '$consultorio') and a.dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "'";
                                                    echo "<table class='table table-striped table-bordered'>";
                                                    echo "<tr>";
                                                    echo "<td width='15%'>Data</td>";
                                                    echo "<td width='55%'>Nome</td>";
                                                    echo "<td width='20%'>Situacao</td>";
                                                    echo "<td width='10%'>Ação</td>";
                                                    echo "</tr>";

                                                    $sth = pg_query($stmt) or die($stmt);
                                                    while ($row = pg_fetch_object($sth)) {

                                                        echo "<tr>";
                                                        echo "<td>" . date('d/m/Y', strtotime($row->cadastro)) . "</td>";
                                                        echo "<td>" . $row->nome . "</td>";
                                                        echo "<td>" . $row->status . "</td>";
                                                        echo "
                                            <td><a href=\"atendimentoclinico.php?id=$row->transacao\" target=\"_blank\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\"><i class=\"fas fa-briefcase-medical\"></i></a></td>";
                                                        echo "</tr>";
                                                    }
                                                    echo "</table>";
                                                    $pesquisa_paciente = "";
                                                }
                                                ?>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
        <script>
            function atende(id) {
                var url = 'retorno_validacao_atendimento.php?atendimento=' + id;
                $.get(url, function(dataReturn) {
                    $('#retorno_atd').html(dataReturn);
                });
                document.location.reload(true);
            }

            function atualizar() {
                var tipo_atendimento = $('#tipoAt').val();
                var url = 'qtdpacientex.php?tipo_atendimento=' + tipo_atendimento;
                $.get(url, function(dataReturn) {
                    $('#qtdPaciente').html(dataReturn);
                });
            }

            setInterval("atualizar()", 5000);

            function chamada() {
                Swal.fire({
                    title: 'CUIDADO!!!',
                    text: "Tem certeza de que deseja finalizar esse paciente como não respondeu chamado?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.value) {
                        Swal.fire(
                            'FINALIZADO',
                            'Paciente não respondeu chamado!',
                            'success'
                        );
                        $('<input />').attr('type', 'hidden')
                            .attr('name', 'nrchamada')
                            .attr('value', 'nrchamada')
                            .appendTo('form');
                        $("form").submit().setTimeout(function() {}, 1000);
                    }
                })
            }

            function necessidades_especiais() {
                Swal.fire({
                    title: 'NECESSIDADES ESPECIAIS',
                    text: "Paciente com deficiência <?= $deficiência; ?>",
                    icon: 'question',
                    iconHtml: '<i class="fas fa-wheelchair"></i>',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                })
            }

            function chamadalab() {
                Swal.fire({
                    title: 'CUIDADO!!!',
                    text: "Tem certeza de que deseja finalizar esse paciente como não respondeu retorno resultado de exames?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.value) {
                        Swal.fire(
                            'FINALIZADO',
                            'Paciente não respondeu retorno!',
                            'success'
                        );
                        $('<input />').attr('type', 'hidden')
                            .attr('name', 'nrchamadalab')
                            .attr('value', 'nrchamadalab')
                            .appendTo('form');
                        $("form").submit().setTimeout(function() {}, 1000);
                    }
                })
            }
        </script>
</body>

</html>