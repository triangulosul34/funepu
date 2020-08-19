<?php


error_reporting(0);



include('verifica.php');
include('conexao.php');
$stmt   = "select nome, imagem, perfil from pessoas where username='$usuario'";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$nome_usuario = $row->nome;
$imagem = $row->imagem;

if ($box == '') {
    header("location:loginbox.php");
}

include('conexao.php');
$stmt   = "select descricao from boxes where box_id='$box'";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$box_descricao = $row->descricao;
include('funcoes.php');
$atalho  = "";
$modalidade = "";
$exame         = "";
$marca        = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $codigo         = $_POST['codigo'];
    $transacao         = $_POST['transacao'];
    $pesquisa_paciente = $_POST['pesquisa_paciente'];
    $item            = $_POST['item'];
    $prontuario        = $_POST['prontuario'];
    $descricao      = $_POST['descricao'];;
    $classificacao    = $_POST['classificacao'];;

    if (isset($_POST['proximo'])) {
        include('conexao.php');
        $stmt = "select a.transacao, a.paciente_id, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.dat_cad as cadastro,c.nome, 
			k.origem, f.descricao as clinica,c.nome_social
			from atendimentos a 
			left join pessoas c on a.paciente_id=c.pessoa_id  
			left join especialidade f on a.especialidade = f.descricao 
			left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
			WHERE status = 'Aguardando Triagem' and dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and 
			cast(tipo as integer) != '6' and cast(tipo as integer) != '11'
			order by 3, 1 asc limit 1
			";
        $sth         = pg_query($stmt) or die($stmt);
        $row         = pg_fetch_object($sth);
        $nome         = $row->nome;
        $transacao     =  $row->transacao;

        if ($row->nome_social != '') {
            $nome         = $row->nome_social . '(' . $row->nome . ')';
        } else {
            $nome         = $row->nome;
        }
        if ($transacao != "") {
            include('conexao.php');
            $stmt   = "Update Atendimentos set status='Em Triagem' where transacao = $transacao ";
            $sth         = pg_query($stmt) or die($stmt);
        } else {
            $erro = "Nenhum paciente aguardando triagem";
        }
    }


    if (isset($_POST['nrchamada'])) {
        include('conexao.php');
        $stmt   = "Update Atendimentos set status='Não Respondeu Chamado', destino_paciente='09', data_destino='" . date('Y-m-d') . "', hora_destino='" . date('H:i') . "' where transacao = $transacao ";
        $sth         = pg_query($stmt) or die($stmt);
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

<body class="pace-done" cz-shortcut-listen="true">
    <div class="modal fade text-left" id="modalConteudo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title" id="myModalLabel8">Triagem</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="conteudoModal">
                </div>
                <div class="modal-footer">
                    <input type='button' name='confTriagem' id="confTriagem" class="btn btn-raised btn-primary square btn-min-width mr-1 mb-1" value='Confirmar Triagem' onclick="salvar_triagem()">
                    <input type='button' name='cancelarModal' data-dismiss="modal" id="cancelarModal" class="btn btn-raised btn-danger square btn-min-width mr-1 mb-1" value='Cancelar'>
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
                                                        » </p>Página Padrão
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
                                                <li><a href="#">Atendimentos</a></li>
                                                <li class="active">Atendimentos Cadastrados</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
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
                                    <div class="row mt-3 mb-3">
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <form name='pedido' method='post'>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control square" readonly name="paciente" value='<?php echo $nome; ?>'>
                                                    <input type="hidden" class="form-control" readonly name="transacao" value='<?php echo $transacao; ?>'>
                                                    <button type="button" id="triagemmanual" data-id="<?php echo $transacao; ?>" class="btn btn-success" data-target="#modalConteudo" data-toggle="modal" data-original-title="Triagem Manual" onClick="valorTriagem(this);"><i class="fas fa-band-aid"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm-12" align="center">
                                                <?php
                                                if ($nome != '') {
                                                    echo '<button type="submit" name="nrchamada" id="nrchamada" value="nrchamada" class="btn btn-primary">Não Respondeu Chamada</button>';
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
								where c.nome like upper('%$pesquisa_paciente%')";
                                                    echo "<table class='table table-striped table-bordered'>";
                                                    echo "<tr>";
                                                    echo "<td width='15%'>Data</td>";
                                                    echo "<td width='55%'>Nome</td>";
                                                    echo "<td width='15%'>Situacao</td>";
                                                    echo "<td width='15%'>Ação</td>";
                                                    echo "</tr>";

                                                    $sth = pg_query($stmt) or die($stmt);
                                                    while ($row = pg_fetch_object($sth)) {
                                                        $transacao = $row->transacao;
                                                        echo "<tr>";
                                                        echo "<td>" . substr($row->cadastro, 0, 10) . "</td>";
                                                        echo "<td>" . $row->nome . "</td>";
                                                        echo "<td>" . $row->status . "</td>";
                                                        echo '<td><a href="relFAA.php?id=' . $transacao . '" target="_blank" id="triagemmanual"><i class="fas fa-print"></i></a></td>';
                                                        echo "</tr>";
                                                    }
                                                    echo "</table>";
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
        setTimeout(function() {
            $('#erro').fadeOut('fast');
        }, 5000);

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


        function carrega_discriminador() {
            var fluxo = document.getElementById("fluxograma").value;
            var paciente = document.getElementById("paciente").value;
            var url = 'carrega_discriminador.php?fluxo=' + fluxo + '&paciente=' + paciente;
            $.get(url, function(dataReturn) {
                $('#load_discriminador').html(dataReturn);
            });
        }

        function valorTriagem(valor) {
            transacao = $(valor).attr("data-id");

            $.get('triagemmanual.php?transacao=' + transacao, function(dataReturn) {
                $('#conteudoModal').html(dataReturn);
            });

            event.preventDefault();
        }

        function reTriagem(valor) {
            transacao = $(valor).attr("data-id");

            $.get('triagemmanual.php?transacao=' + transacao, function(dataReturn) {
                $('#conteudoModal').html(dataReturn);
            });

            event.preventDefault();
        }

        function salvar_triagem() {
            document.getElementById("confTriagem").disabled = true;
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
                    $('#botaoChamar').html('<button type="submit" name="proximo"   value="proximo"   class="btn btn-success">Chamar Próximo Paciente</button>');
                    $("#nrchamada").css("display", "none");
                });
        }




        (function(document, window, $) {
            'use strict';

            var Site = window.Site;
            $(document).ready(function() {
                Site.run();
            });
        })(document, window, jQuery);
    </script>
</body>

</html>