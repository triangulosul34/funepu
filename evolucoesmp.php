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
    $procedimentox = $_POST['procedimentox'];
    $destino     = $_POST['encaminhamento'];
    $nome        = $_POST['nome'];
    $xbox        = $_POST['xbox'];
    $CM          = $_POST['cb_cm'];
    $OR          = $_POST['cb_ort'];
    $start       = $_POST['start'];
    $end         = $_POST['end'];
    $transfere    = $_POST['cb_exame'];
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


    if ($start == '' and $end == '' and $nome == '') {
        $start          = date('d/m/Y', strtotime("-7 days"));
        $end          = date('d/m/Y');
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
    } else {
        if ($where != "") {
            $where = $where . " and (a.destino_paciente = '03' or a.destino_paciente = '05' or a.destino_paciente = '07' or a.destino_paciente = '10') ";
        } else {
            $where = $where . "  (a.destino_paciente = '03' or a.destino_paciente = '05' or a.destino_paciente = '07' or a.destino_paciente = '10') ";
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
    <title>FUNEPU | Assistente Social</title>
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
                                                        » </p>Assistência Social
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
                                                <li class="active">Evoluções</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form name="total" method="post">
                                        <div class="row">
                                            <div class="col-3">
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
                                            <div class="col col-lg-3">
                                                <label class="control-label" for="inputBasicFirstName">Data Ínicial</label>
                                                <input type="date" class="form-control text-center" name="start" id="start" OnKeyPress="formatar('##/##/####', this)" value="<?php echo $start; ?>" />
                                            </div>
                                            <div class="col col-lg-3 text-center">
                                                <label class="control-label" for="inputBasicFirstName">Data Final</label>
                                                <input type="date" class="form-control text-center" name="end" OnKeyPress="formatar('##/##/####', this)"/ value="<?php echo $end; ?>">
                                            </div>
                                        </div>
                                        <div class="row align-items-end">
                                            <br>
                                            <div class="offset-3 col-12">
                                                <button type="submit" name="pesquisa" value="semana" class="btn btn-raised btn-primary square btn-min-width mr-1 mb-1">Pesquisar</button>
                                                <button type="submit" name="hoje" value="hoje" class="btn btn-raised btn-success square btn-min-width mr-1 mb-1">Hoje</button>
                                                <button type="submit" name="ontem" value="ontem" class="btn btn-raised btn-info square btn-min-width mr-1 mb-1">Ontem</button>
                                                <button type="submit" name="semana" value="semana" class="btn btn-raised btn-warning square btn-min-width mr-1 mb-1">Semana</button>
                                            </div>
                                        </div>
                                        <div class="row mt-5">
                                            <div class="col-12">
                                                <table id="data_table" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th width='3%' style="font-size: 8.5pt;">
                                                                <div class="checkbox-custom checkbox-primary"><input type="checkbox" name="todos" id='todos' onclick='marcardesmarcar();' value="T"><label></label></div>
                                                            </th>
                                                            <th width='8%' style="font-size: 8.5pt;">Solicitação</th>
                                                            <th width='25%' style="font-size: 8.5pt;">Paciente</th>
                                                            <th width='18%' style="font-size: 8.5pt;">Origem</th>
                                                            <th width='5%' style="font-size: 8.5pt;">Atendim.</th>
                                                            <th width='15%' style="font-size: 8.5pt;">Encaminhamento</th>
                                                            <th width='26%' style="font-size: 8.5pt;">Ação</th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th style="font-size: 6.5pt;">#</th>
                                                            <th style="font-size: 6.5pt;">Solicitação</th>
                                                            <th style="font-size: 6.5pt;">Paciente</th>
                                                            <th style="font-size: 6.5pt;">Origem</th>
                                                            <th style="font-size: 6.5pt;">Atendimento</th>
                                                            <th style="font-size: 6.5pt;">Encaminhamento</th>
                                                            <th style="font-size: 6.5pt;">Ação</th>

                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
                                                        include('conexao.php');
                                                        $stmt = "select a.transacao,d.nome as nomemed, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,
							a.hora_atendimento, a.dat_cad, 	c.nome, k.origem, a.tipo,a.hora_destino,
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
								
								WHEN '03' THEN 'PERMANÊNCIA.' 
								WHEN '07' THEN 'EM OBSERVAÇÃO / MEDICAÇÃO' 
								WHEN '10' THEN 'EXAMES / REAVALIACAO' 
							END as destino,
							CASE p.destino_encaminhamento 
								WHEN '01' THEN 'ALTA' 
								WHEN '02' THEN 'ALTA / ENCAM. AMBUL.' 
								WHEN '11' THEN 'ALTA EVASÃO' 
								WHEN '12' THEN 'ALTA PEDIDO' 
								WHEN '15' THEN 'ALTA / PENITENCIÁRIA' 
								WHEN '14' THEN 'ALTA / PM' 
								WHEN '04' THEN 'TRANSF. OUTRA UPA' 
								WHEN '05' THEN 'TRANSFERENCIA HOSPITALAR' 
								WHEN '13' THEN 'TRANSFERENCIA INTERNA' 
								WHEN '06' THEN 'ÓBITO' 
							END as destinoalta, a.coronavirus
							from atendimentos a 
							left join pessoas c on a.paciente_id=c.pessoa_id
							left join pessoas d on a.med_atendimento=d.username
							left join destino_paciente p on p.atendimento_id = a.transacao
							left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) ";

                                                        if ($where != "") {
                                                            $stmt = $stmt . " where " . $where;
                                                        } else {
                                                            $stmt = $stmt . " where a.dat_cad='" . date('Y-m-d') . "'";
                                                        }

                                                        $stmt = $stmt . "  order by a.dat_cad desc,a.hora_cad desc ";
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
                                                            if ($row->coronavirus == 1) {
                                                                echo "<td align='center'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                echo "<td class='blink'>" . inverteData(substr($row->dat_cad, 0, 10)) . "</td>";
                                                                echo "<td class='blink'>" . $row->nome . "</td>";
                                                                echo "<td class='blink'>" . $row->origem . "</td>";
                                                                //echo "<td class='blink'>" . $row->hora_cad . "</td>";
                                                                echo "<td class='blink'>" . $row->hora_destino . "</td>";


                                                                echo "<td class=\"small\">" . $row->destino . "</td>";
                                                                //echo "<td class=\"small\">" . $row->destinoalta . "</td>";
                                                            } else {
                                                                echo "<td align='center'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                echo "<td>" . inverteData(substr($row->dat_cad, 0, 10)) . "</td>";
                                                                echo "<td>" . $row->nome . "</td>";
                                                                echo "<td>" . $row->origem . "</td>";
                                                                //echo "<td>" . $row->hora_cad . "</td>";
                                                                //echo "<td>" . $row->hora_triagem . "</td>";
                                                                echo "<td>" . $row->hora_destino . "</td>";


                                                                echo "<td class=\"small\">" . $row->destino . "</td>";
                                                            }



                                                            echo "<td>";

                                                            // if ($perfil == '03' or $perfil == '06') {
                                                            //     echo "<a href=\"atendimentoclinico.php?id=$row->transacao\" target=\"_blank\" class=\"btn \" data-toggle=\"tooltip\" title=\"Visualizar\"><i class=\"fas fa-eye\"></i></a>";
                                                            // }
                                                            echo "<a href=\"relFAA.php?id=$row->transacao\" target=\"_blank\" class=\"btn \" data-toggle=\"tooltip\" title=\"FAA\"><i class=\"fas fa-print\"></i></a>";

                                                            if ($perfil == '03' or $perfil == '06' or $perfil == '08' or $perfil == '13') {
                                                                echo "<a href=\"evolucao_atendimentomp.php?id=$row->transacao\" target=\"_blank\" class=\"btn\" data-toggle=\"tooltip\" title=\"Evolução\"><i class=\"fas fa-file-medical\"></i></a>";
                                                            }

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

        function carrega_discriminador(event) {
            var fluxo = document.getElementById("fluxograma").value;
            var url = 'carrega_discriminador.php?fluxo=' + fluxo;
            $.get(url, function(dataReturn) {
                $('#load_discriminador').html(dataReturn);
            });
        }
        $("#procedimentox").chosen({
            placeholder_text_single: "Selecione...",
            search_contains: true
        });



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

        });

        $("#confSit").click(function(event) {

            var transacaoModal = $("#transacaoMod").val();
            var situacaoMod = $("#situacaoMod").val();

            $.get('salvarsituacao.php?transacaoMod=' + transacaoModal + "&situacaoMod=" + situacaoMod, function(dataReturn) {
                alert(dataReturn);
                $('#modalConteudoSitu').modal('hide');
            });

        });
    </script>
</body>

</html>