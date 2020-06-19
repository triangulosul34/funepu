<?php
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

include('verifica.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $atendimento = $_GET['id'];
    $nome        = $_GET['nome'];
    $cns         = $_GET['cns'];
    $idade       = $_GET['idade'];
    $prontuario  = $_GET['prontuario'];
    $pr           = $_GET['pr'];
    $prioridade  = $_GET['prioridade'];
    if ($prescricao == '') {

        include('conexao.php');
        $stmt = "select nextval('prescricoes_prescricao_id_seq')";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $prescricao = $row->nextval;
    }
    if ($pr != "") {
        include('conexao.php');
        $su = "select * from prescricao_item where prescricao_id = $pr";
        $stu = pg_query($su) or die($su);
        while ($rowu = pg_fetch_object($stu)) {
            $si = "insert into prescricao_item (obs_med, prescricao_id, descricao, dosagem, aprazamento, codigo_medicamento, via, complemento, prescricao_item_id, tipo, diluente, bomba)
						values('$rowu->obs_med', $prescricao, '$rowu->descricao', ";
            if ($rowu->dosagem == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "'$rowu->dosagem',";
            }
            if ($rowu->aprazamento == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "'$rowu->aprazamento',";
            }
            if ($rowu->codigo_medicamento == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "'$rowu->codigo_medicamento',";
            }
            if ($rowu->via == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "'$rowu->via',";
            }
            if ($rowu->complemento == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "'$rowu->complemento',";
            }
            if ($rowu->prescricao_item_id == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "$rowu->prescricao_item_id,";
            }
            if ($rowu->tipo == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "$rowu->tipo,";
            }
            if ($rowu->diluente == '') {
                $si = $si . "null,";
            } else {
                $si = $si . "'$rowu->diluente',";
            }
            if ($rowu->bomba == '') {
                $si = $si . "null)";
            } else {
                $si = $si . "'$rowu->bomba')";
            }
            $st = pg_query($si) or die($si);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prescricao  = $_POST['precricao'];
    $atendimento = $_POST['atendimento'];

    include('conexao.php');
    $stmt = "insert into prescricoes (prescricao_id, atendimento_id, data, hora, profissional_id)
					values ($prescricao, $atendimento, '" . date('Y-m-d') . "', '" . date("H:i:s") . "', $id)";
    $sth = pg_query($stmt) or die($stmt);

    header('Location: prescricaoenfermagemy.php?id=' . $prescricao . '&p=' . $atendimento);
}

if (isset($_POST["cancelar"])) {
    $prescricao  = $_POST['precricao'];
    include('conexao.php');
    $stmt = "delete from prescricao_item where prescricao_id = $prescricao";
    $sth = pg_query($stmt) or die($stmt);

    header('Location: evolucoes.php');
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
    <title>FUNEPU | Nova Prescricao</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
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
    <!-- <?php include('menu.php'); ?> -->
    <?php include('header.php'); ?>
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
                                                        » </p>Nova Prescricao
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
                                                <li class="active">Nova Prescricao</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data" action="#" id="formp" onsubmit="sal.disabled = true; return true;">
                                        <input type="hidden" name="precricao" id="prescricao" class="form-control" value="<?php echo $prescricao; ?>" readonly>
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Atendimento</label>
                                                    <input type="text" name="atendimento" id="atendimento" class="form-control square" value="<?php echo $atendimento; ?>" readonly>
                                                    <input type="hidden" name="medico" id="medico" class="form-control" value="<?php echo $usuario; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <div class="form-group">
                                                    <label>Nome do Paciente</label>
                                                    <input type="text" class="form-control square" id="nome" name="nome" value="<?php echo $nome; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>CNS</label>
                                                    <input type="text" class="form-control square" id="cns" name="cns" value="<?php echo $cns; ?>">
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Idade</label>
                                                    <input type="text" class="form-control square" id="idade" name="idade" value="<?php echo $idade; ?>" readonly>
                                                    <input type="hidden" class="form-control square" id="prioridade" name="prioridade" value="<?php echo $prioridade; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Prontuario</label>
                                                    <input type="text" class="form-control square" id="prontuario" name="prontuario" value="<?php echo $prontuario; ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label>Data</label>
                                                    <input type="text" class="form-control square" id="data" name="data" value="<?php echo date("d/m/Y") ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <hr style="width: 100%;">
                                        <div id="solucoes">
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>
                                                        <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                                                                                                                                                            echo "checked";
                                                                                                                                                        } ?>>Dieta
                                                    </label>
                                                    <label>
                                                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                                                                                                                                                        echo "checked";
                                                                                                                                                                    } ?>>Medicamento
                                                    </label>
                                                    <label>
                                                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == 'solucoes') {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                } ?>>Soluções
                                                    </label>
                                                    <label>
                                                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == 'precricao_cuidados') {
                                                                                                                                                                                echo "checked";
                                                                                                                                                                            } ?>>Cuidado
                                                    </label>
                                                    <label>
                                                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == 'paciente') {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                } ?>>medicamento particular do paciente
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" align="center" id="teste" name="teste" style="display:none;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div id="tabela" class="col-12">
                                                <?php if ($pr) { ?>
                                                    <table id="tabela" class="table">
                                                        <thead>
                                                            <tr>
                                                                <th width="30">Procedimento</th>
                                                                <th width="20">Via</th>
                                                                <th width="10%">Aprazamento</th>
                                                                <th width="10%">Quantidade</th>
                                                                <th width="10%">Diluente</th>
                                                                <th width="10%">Complemento</th>
                                                                <th width="10%">Ação</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            include('conexao.php');
                                                            $stmt = "select *
																	from prescricao_item
																	where prescricao_id = $pr";
                                                            $sth = pg_query($stmt) or die($stmt);
                                                            while ($row = pg_fetch_object($sth)) {
                                                                echo "<tr>";
                                                                echo "<td>" . $row->descricao . "</td>";
                                                                echo "<td>" . $row->via . "</td>";
                                                                echo "<td>" . $row->aprazamento . "</td>";
                                                                echo "<td>" . $row->dosagem . "</td>";
                                                                echo "<td>" . $row->diluente . "</td>";
                                                                echo "<td>" . $row->complemento . "</td>";
                                                                echo "<td><button type=\"button\" name=\"editalinha\" onclick=\"editaconta(" . $row->prescricao_item_id . "," . $row->tipo . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-edit\"></i></button>";
                                                                echo "<button type=\"button\" name=\"apagalinha\" onclick=\"deletaconta(" . $row->prescricao_item_id . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-trash-alt\"></i></button></td>";
                                                                echo "</tr>";
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12" align="center">
                                                <input type="button" name='sal' id='sal' onclick="javascript:salvar()" class="btn btn-success" value="Finalizar">
                                                <input type="submit" name='cancelar' id='cancelar' class="btn btn-danger" value="Cancelar Prescricao">
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>
        <script>
            function obs_med(elem) {
                // 	var indiceDoSelectEscolhido = elem.selectedIndex;
                // 	var valorDoElementoEscolhido = elem.options[indiceDoSelectEscolhido];
                // 	alert("oi")
                // 	document.getElementById("teste").style.display="block";
                var obs = document.getElementById("obs_text").value;
                //alert(obs)
            }



            //$("#escolha").chosen({width: "100%"})
            //$("#via").chosen({width: "100%"})
            //$("#aprazamento").chosen({width: "100%"})
            $("select").chosen({
                width: "100%"
            })

            $("#escolha").change(function() {
                alert("Oi")
            });

            function adc() {
                var rads = $("input[name='optradio']:checked").val();
                if (rads == 'solucoes') {
                    var bomba = document.getElementById("bomba");
                    if (bomba.checked == true) {
                        var b = "1";
                    } else {
                        var b = "0";
                    }

                    var s = new Array;
                    escolha = document.getElementsByClassName('teste');
                    for (i = 0; i < escolha.length; i++) {
                        s[i] = escolha[i].value;
                        //aux = i;
                    }
                    var c = new Array;
                    var dosagem = document.getElementsByClassName('dosagem');
                    for (i = 0; i < dosagem.length; i++) {
                        c[i] = dosagem[i].value;
                        c[i] = c[i].replace(",", ".");
                    }

                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var diluente = document.getElementById("diluente").value;
                    var v = document.getElementById("via");
                    var via = v.options[v.selectedIndex].text;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var obs = document.getElementById("obs_text").value;
                    var obs_final = obs.trim();
                    var alphaExp = /(.*[a-z]){3}/i;
                    //[aux + 1] = diluente;
                    //alert(obs);

                    if ($("#teste").is(":visible")) {
                        if (obs == '') {
                            swal({
                                title: "Informe o Diagnóstico",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }

                    if ($("#teste").is(":visible")) {
                        if (alphaExp.test(obs)) {} else {
                            swal({
                                title: "Descreva corretamente a hipótese diagnóstica.",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }

                    if (via == '') {
                        swal({
                            title: "Informe uma via de administração",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('via').focus;
                        return false;
                    } else if (aprazamento == '') {
                        swal({
                            title: "Informe um aprazamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;

                    } else if (escolha === null || escolha.length == 0) {
                        swal({
                            title: "Informe um medicamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (diluente == '') {
                        swal({
                            title: "Informe um diluente",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('diluente').focus;
                        return false;
                    } else if (c == '') {
                        swal({
                            title: "Informe a quantidade",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('diluente').focus;
                        return false;
                    } else {
                        //alert('Passei 4');
                        //alert(obs);
                        var url = 'ajax_prescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolhavetor=' + s + '&diluente=' + diluente + '&dosagemvetor=' + c + '&radio=' + rads + '&diluente=' + diluente + '&complemento=' + complemento + '&via=' + via + '&aprazamento=' + aprazamento + '&bomba=' + b + '&obs=' + obs;
                        $.get(url, function(dataReturn) {
                            $('#tabela').html(dataReturn);
                        });
                    }

                } else if (rads == 'paciente') {
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var paciente = document.getElementById("paciente").value;

                    var url = 'ajax_prescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&paciente=' + paciente + '&radio=' + rads;
                    $.get(url, function(dataReturn) {
                        $('#tabela').html(dataReturn);
                    });
                    document.getElementById("paciente").value = '';
                } else if (rads == 'dietas') {
                    var bomba = document.getElementById("bomba");
                    if (bomba.checked == true) {
                        var b = "1";
                    } else {
                        var b = "0";
                    }
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var dosagem = document.getElementById("dosagem").value;
                    var e = document.getElementById("escolha");
                    var escolha = e.options[e.selectedIndex].text;
                    var value = document.getElementById("escolha").value;
                    var v = document.getElementById("via");
                    var via = v.options[v.selectedIndex].text;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var qtd = document.getElementById("aprazamento").value;
                    var obs = document.getElementById("obs_text").value;
                    //alert(obs);
                    var url = 'ajax_prescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolha=' + escolha + '&via=' + via + '&aprazamento=' + aprazamento + '&complemento=' + complemento + '&dosagem=' + dosagem + '&radio=' + rads + '&value=' + value + '&qtd=' + qtd + '&bomba=' + b + '&obs=' + obs;
                    $.get(url, function(dataReturn) {
                        $('#tabela').html(dataReturn);
                    });

                } else if (rads == 'medicamentos') {

                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var dosagem = document.getElementById("dosagem").value;
                    var e = document.getElementById("escolha");
                    var escolha = e.options[e.selectedIndex].text;
                    var value = document.getElementById("escolha").value;
                    var v = document.getElementById("via");
                    var via = v.options[v.selectedIndex].text;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var qtd = document.getElementById("aprazamento").value;
                    var obs = document.getElementById("obs_text").value;
                    var obs_final = obs.trim();
                    var alphaExp = /(.*[a-z]){3}/i;
                    //alert(obs);

                    if ($("#teste").is(":visible")) {
                        if (obs == '') {
                            swal({
                                title: "Informe o Diagnóstico",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }

                    if ($("#teste").is(":visible")) {
                        if (alphaExp.test(obs)) {} else {
                            swal({
                                title: "Descreva corretamente a hipótese diagnóstica.",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }

                    if (via == '') {
                        swal({
                            title: "Informe uma via de administração",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('via').focus;
                        return false;
                    } else if (value == '') {
                        swal({
                            title: "Informe um medicamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (aprazamento == '') {
                        swal({
                            title: "Informe um aprazamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (dosagem == '') {
                        swal({
                            title: "Informe a quantidade",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('diluente').focus;
                        return false;
                    } else {
                        var url = 'ajax_prescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolha=' + escolha + '&via=' + via + '&aprazamento=' + aprazamento + '&complemento=' + complemento + '&dosagem=' + dosagem + '&radio=' + rads + '&value=' + value + '&qtd=' + qtd + '&obs=' + obs;
                        $.get(url, function(dataReturn) {
                            $('#tabela').html(dataReturn);
                        });
                    }
                } else {
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var e = document.getElementById("escolha");
                    var escolha = e.options[e.selectedIndex].text;
                    var value = document.getElementById("escolha").value;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var qtd = document.getElementById("aprazamento").value;

                    var url = 'ajax_prescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolha=' + escolha + '&aprazamento=' + aprazamento + '&complemento=' + complemento + '&radio=' + rads + '&value=' + value + '&qtd=' + qtd + '&obs=' + obs;
                    $.get(url, function(dataReturn) {
                        $('#tabela').html(dataReturn);
                    });

                }
                var idade = document.getElementById("idade").value;
                var prioridade = document.getElementById("prioridade").value;
                var url = 'ajax_selectverde.php?radio=' + rads + '&idade=' + idade + '&prioridade=' + prioridade;
                $.get(url, function(dataReturn) {
                    $('#solucoes').html(dataReturn);
                });
            }

            function radio() {
                var idade = document.getElementById("idade").value;
                var prioridade = document.getElementById("prioridade").value;
                var rads = $("input[name='optradio']:checked").val();
                var url = 'ajax_selectverde.php?radio=' + rads + '&idade=' + idade + '&prioridade=' + prioridade;
                $.get(url, function(dataReturn) {
                    $('#solucoes').html(dataReturn);
                });
            }

            function deletaconta(id) {
                var prescricao = document.getElementById("prescricao").value;
                var url = 'ajax_deletaprescricao.php?prescricao=' + prescricao + '&id=' + id;
                $.get(url, function(dataReturn) {
                    $('#tabela').html(dataReturn);
                });
            }

            function editaconta(id, tipo, item_id) {
                var idade = document.getElementById("idade").value;
                var prioridade = document.getElementById("prioridade").value;
                var url = 'ajax_selectedit.php?tipo=' + tipo + '&id=' + id + '&id_item=' + item_id + '&idade=' + idade + '&prioridade=' + prioridade;
                $.get(url, function(dataReturn) {
                    $('#solucoes').html(dataReturn);
                });
            }

            function editar(id) {
                var rads = $("input[name='optradio']:checked").val();
                if (rads == 'solucoes') {
                    var bomba = document.getElementById("bomba");
                    if (bomba.checked == true) {
                        var b = "1";
                    } else {
                        var b = "0";
                    }
                    var s = new Array;
                    escolha = document.getElementsByClassName('teste');
                    for (i = 0; i < escolha.length - 1; i++) {
                        s[i] = escolha[i].value;
                    }
                    var c = new Array;
                    dosagem = document.getElementsByClassName('dosagem');
                    for (i = 0; i < dosagem.length; i++) {
                        c[i] = dosagem[i].value;
                        c[i] = c[i].replace(",", ".");
                    }
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var diluente = document.getElementById("diluente").value;
                    var v = document.getElementById("via");
                    var via = v.options[v.selectedIndex].text;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var obs = document.getElementById("obs_text").value;
                    var obs_final = obs.trim();
                    var alphaExp = /(.*[a-z]){3}/i;
                    //[aux + 1] = diluente;
                    //alert(obs);

                    if ($("#teste").is(":visible")) {
                        if (obs == '') {
                            swal({
                                title: "Informe o Diagnóstico",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }

                    if ($("#teste").is(":visible")) {
                        if (alphaExp.test(obs)) {} else {
                            swal({
                                title: "Descreva corretamente a hipótese diagnóstica.",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }
                    if (via == '') {
                        swal({
                            title: "Informe uma via de administração",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('via').focus;
                        return false;
                    } else if (escolha == '') {
                        swal({
                            title: "Informe um medicamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (aprazamento == '') {
                        swal({
                            title: "Informe um aprazamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (diluente == '') {
                        swal({
                            title: "O diluente deve ser informado",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (diluente == '') {
                        swal({
                            title: "Informe um diluente",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('diluente').focus;
                        return false;
                    } else if (c == '') {
                        swal({
                            title: "Informe a quantidade",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('diluente').focus;
                        return false;
                    } else {
                        var url = 'ajax_editaprescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolhavetor=' + s + '&diluente=' + diluente + '&dosagemvetor=' + c + '&radio=' + rads + '&diluente=' + diluente + '&complemento=' + complemento + '&via=' + via + '&aprazamento=' + aprazamento + '&bomba=' + b + '&id=' + id + '&obs=' + obs;
                        $.get(url, function(dataReturn) {
                            $('#tabela').html(dataReturn);
                        });
                    }

                } else if (rads == 'paciente') {
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var paciente = document.getElementById("paciente").value;
                    var url = 'ajax_editaprescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&paciente=' + paciente + '&radio=' + rads + '&id=' + id;
                    $.get(url, function(dataReturn) {
                        $('#tabela').html(dataReturn);
                    });
                    document.getElementById("paciente").value = '';
                } else if (rads == 'dietas') {
                    var bomba = document.getElementById("bomba");
                    if (bomba.checked == true) {
                        var b = "1";
                    } else {
                        var b = "0";
                    }
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var dosagem = document.getElementById("dosagem").value;
                    var e = document.getElementById("escolha");
                    var escolha = e.options[e.selectedIndex].text;
                    var value = document.getElementById("escolha").value;
                    var v = document.getElementById("via");
                    var via = v.options[v.selectedIndex].text;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var qtd = document.getElementById("aprazamento").value;

                    var url = 'ajax_editaprescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolha=' + escolha + '&via=' + via + '&aprazamento=' + aprazamento + '&complemento=' + complemento + '&dosagem=' + dosagem + '&radio=' + rads + '&value=' + value + '&qtd=' + qtd + '&bomba=' + b + '&id=' + id;
                    $.get(url, function(dataReturn) {
                        $('#tabela').html(dataReturn);
                    });

                } else if (rads == 'medicamentos') {
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var dosagem = document.getElementById("dosagem").value;
                    var e = document.getElementById("escolha");
                    var escolha = e.options[e.selectedIndex].text;
                    var value = document.getElementById("escolha").value;
                    var v = document.getElementById("via");
                    var via = v.options[v.selectedIndex].text;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var qtd = document.getElementById("aprazamento").value;
                    var obs = document.getElementById("obs_text").value;
                    var obs_final = obs.trim();
                    var alphaExp = /(.*[a-z]){3}/i;
                    //[aux + 1] = diluente;
                    //alert(obs);

                    if ($("#teste").is(":visible")) {
                        if (obs == '') {
                            swal({
                                title: "Informe o Diagnóstico",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }

                    if ($("#teste").is(":visible")) {
                        if (alphaExp.test(obs)) {} else {
                            swal({
                                title: "Descreva corretamente a hipótese diagnóstica.",
                                text: "",
                                type: "error",
                                showCancelButton: false,
                                confirmButtonColor: '#428bca',
                                confirmButtonText: 'OK',
                                closeOnConfirm: false,
                                closeOnCancel: false
                            });

                            document.getElementById('teste2').focus;
                            return false;
                        }
                    }
                    if (via == '') {
                        swal({
                            title: "Informe uma via de administração",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('via').focus;
                        return false;
                    } else if (value == '') {
                        swal({
                            title: "Informe um medicamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (aprazamento == '') {
                        swal({
                            title: "Informe um aprazamento",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('aprazamento').focus;
                        return false;
                    } else if (dosagem == '') {
                        swal({
                            title: "Informe a quantidade",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                        document.getElementById('diluente').focus;
                        return false;
                    } else {
                        var url = 'ajax_editaprescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolha=' + escolha + '&via=' + via + '&aprazamento=' + aprazamento + '&complemento=' + complemento + '&dosagem=' + dosagem + '&radio=' + rads + '&value=' + value + '&qtd=' + qtd + '&id=' + id + '&obs=' + obs;
                        $.get(url, function(dataReturn) {
                            $('#tabela').html(dataReturn);
                        });
                    }
                } else {
                    var prescricao = document.getElementById("prescricao").value;
                    var atendimento = document.getElementById("atendimento").value;
                    var medico = document.getElementById("medico").value;
                    var nome = document.getElementById("nome").value;
                    var complemento = document.getElementById("complemento").value;
                    var e = document.getElementById("escolha");
                    var escolha = e.options[e.selectedIndex].text;
                    var value = document.getElementById("escolha").value;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var qtd = document.getElementById("aprazamento").value;
                    var url = 'ajax_editaprescricao.php?medico=' + medico + '&atendimento=' + atendimento + '&nome=' + nome + '&prescricao=' + prescricao + '&escolha=' + escolha + '&aprazamento=' + aprazamento + '&complemento=' + complemento + '&radio=' + rads + '&value=' + value + '&qtd=' + qtd + '&id=' + id;
                    $.get(url, function(dataReturn) {
                        $('#tabela').html(dataReturn);
                    });
                }
                var idade = document.getElementById("idade").value;
                var prioridade = document.getElementById("prioridade").value;
                var url = 'ajax_selectverde.php?radio=' + rads + '&idade=' + idade + '&prioridade=' + prioridade;
                $.get(url, function(dataReturn) {
                    $('#solucoes').html(dataReturn);
                });
            }

            function en() {
                var v = document.getElementById("via");
                var via = v.options[v.selectedIndex].text;
                var idade = document.getElementById("idade").value;
                var prioridade = document.getElementById("prioridade").value;
                if (via == 'Via Endo-venoso - EV') {
                    var rads = $("input[name='optradio']:checked").val();
                    var complemento = document.getElementById("complemento").value;
                    var dosagem = document.getElementById("dosagem").value;
                    var e = document.getElementById("escolha");
                    var escolha = e.options[e.selectedIndex].text;
                    var v = document.getElementById("via");
                    var via = v.options[v.selectedIndex].text;
                    var a = document.getElementById("aprazamento");
                    var aprazamento = a.options[a.selectedIndex].text;
                    var url = 'ajax_selectverde.php?radio=solucoes&complemento=' + complemento + '&dosagem=' + dosagem + '&escolha=' + escolha + '&via=' + via + '&aprazamento=' + aprazamento + '&prescricao=' + prescricao + '&idade=' + idade + '&prioridade=' + prioridade;
                    $.get(url, function(dataReturn) {
                        $('#solucoes').html(dataReturn);
                        var esc = $('[name=escolha]').val();
                        var arrayId = ['105139', '105452', '106577', '105377', '105384', '105352', '105034', '105361', '105354', '105091', '105088', '105457', '106576', '105427', '105426'];
                        if (arrayId.indexOf(esc) != -1) {
                            document.getElementById("teste").style.display = "block";
                            div.innerHTML = '<div class="col-md-12" align="center" id="teste2" name="teste2" >' +
                                '<label>Hipótese Diagnostica</label>' +
                                '<textarea  class="form-control" id="obs_text" name="obs_text"></textarea>' +
                                '<br>' +
                                '</div>';
                        } else {
                            document.getElementById("teste").style.display = "none";
                        }
                    });
                }
            }

            function salvar() {
                var prescricao = document.getElementById("prescricao").value;
                var medico = document.getElementById("medico").value;
                var nome = document.getElementById("nome").value;
                var prioridade = document.getElementById("prioridade").value;
                var idade = document.getElementById("idade").value;
                var atendimento = document.getElementById("atendimento").value;
                var url = 'salvarvalim.php?prescricao=' + prescricao + '&medico=' + medico + '&nome=' + nome + '&atendimento=' + atendimento + '&prioridade=' + prioridade + '&idade=' + idade;
                $.get(url, function(dataReturn) {
                    $('#teste').html(dataReturn);
                });
            }

            function somenteNumeros(num) {
                var value = document.getElementById("escolha").value;
                var er = /[^0-9,.]/;
                er.lastIndex = 0;
                var campo = num;
                var c = campo.value;
                if (value != 105195 && value != 105194 && value != 105019 && value != 105283 && value != 105159 && value != 105041 && value != 105242 && value != 105129 && value != 105338 && value != 105164 && value != 105165 && value != 105000 && value != 105131 && value != 105130) {
                    if (c > 20) {
                        campo.value = "";
                        swal({
                            title: "Colocar somente a quantidade de frascos, ampolas, comprimidos, etc",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                    }
                }
                if (value != 105195 && value != 105194 && value != 105019 && value != 105283 && value != 105159 && value != 105041 && value != 105242 && value != 105129 && value != 105338 && value != 105164 && value != 105165 && value != 105000 && value != 105131 && value != 105130) {
                    if (er.test(campo.value)) {
                        campo.value = "";
                        swal({
                            title: "Informe somente a quantidade de frascos, ampolas, comprimidos e etc",
                            text: "",
                            type: "error",
                            showCancelButton: false,
                            confirmButtonColor: '#428bca',
                            confirmButtonText: 'OK',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        });
                    }
                }
            }

            function numero(num) {
                var er = /[^0-9,.]/;
                er.lastIndex = 0;
                var campo = num;
                var c = campo.value;
                if (c > 20) {
                    campo.value = "";
                    swal({
                        title: "Informe somente a quantidade de frascos, ampolas, comprimidos e etc",
                        text: "",
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: '#428bca',
                        confirmButtonText: 'OK',
                        closeOnConfirm: false,
                        closeOnCancel: false
                    });
                }
                if (er.test(campo.value)) {
                    campo.value = "";
                    swal({
                        title: "Informe somente a quantidade de frascos, ampolas, comprimidos e etc",
                        text: "",
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: '#428bca',
                        confirmButtonText: 'OK',
                        closeOnConfirm: false,
                        closeOnCancel: false
                    });
                }
            }

            $('input').on("input", function(e) {
                $(this).val($(this).val().replace(/'/g, ""));
            });

            function obs_medi() {
                var esc = $('[name=escolha]').val();
                var arrayId = ['105139', '105452', '106577', '105377', '105384', '105352', '105034', '105361', '105354', '105091', '105088', '105457', '106576', '105427', '105426'];
                if (arrayId.indexOf(esc) != -1) {
                    document.getElementById("teste").style.display = "block";
                    div.innerHTML = '<div class="col-md-12" align="center" id="teste2" name="teste2" >' +
                        '<label>Hipótese Diagnostica</label>' +
                        '<textarea  class="form-control" id="obs_text" name="obs_text"></textarea>' +
                        '<br>' +
                        '</div>';
                } else {
                    document.getElementById("teste").style.display = "none";
                }
            }

            // function obs_medic(elem) {
            // 	var esc = $('#escolha').val();
            // 	var idEsc = elem.selectedIndex;
            // 	var vlEsc = elem.options[idEsc].value;
            // 	var arrayId = ['105139', '105452', '106577', '105377', '105384', '105352', '105034', '105361', '105354', '105091', '105088', '105457']; 
            // 	if (arrayId.indexOf(esc) != -1) {
            // 	document.getElementById("teste").style.display="block";
            // } else {
            // 	document.getElementById("teste").style.display="none";
            // }
            // }


            // function obs_medi(elem) {
            // 	var esc = $('#escolha').val();
            // 	var idEsc = elem.selectedIndex;
            // 	var vlEsc = elem.options[idEsc].value;

            // 	//var adc   = document.getElementById("#adicionar").value;
            // 	//var teste = document.getElementById("teste").style.display="block";

            // 	var arrayId = ['105139', '105452', '106577', '105377', '105384', '105352', '105034', '105361', '105354', '105091', '105088', '105457']; 

            // 	if (arrayId.indexOf(vlEsc) != -1) {
            // 		$( "#teste" )[0].style.display="block"
            // 		$( "#adicionar" ).before( $("#teste") );
            // 		//adc.parentElement.insertBefore(adc, teste);
            // } else {
            // 	document.getElementById("teste").style.display="none";
            // }
            // }
        </script>