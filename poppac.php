<?php

require 'tsul_ssl.php';

error_reporting(0);
function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

$pesquisa = 'qwwwqq';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$termo = $_GET['pesquisa'];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$pesquisa = strtoupper($_POST['pesquisa']);
	if ($pesquisa == '') {
		$pesquisa = 'qwwwqq';
	}
	$palavras = explode(' ', $pesquisa);
	$pos = explode('/', $pesquisa);

	if ((count($palavras) < 2) and (count($pos) < 3) and !is_numeric($pesquisa)) {
		$pesquisa = 'qwwwqq';
		echo  "<script>alert('Consulta Invalida! Seja Especifico');</script>";
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
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <!--load all styles -->
    <script type="text/javascript">
        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }

        //função javascript que retornará o codigo 
        function retorna(id, nome, sexo, dtnasc, idade, endereco, numero, complemento, bairro, cidade, estado, telefone,
            celular, cep, ocorrencia, cpf, imagem, nome_mae, carteirinha, identidade, org_expeditor, doc
        ) //passando um parametro 
        {
            window.opener.document.pedido.prontuario.value =
                id; //a janela mãe recebe o id, você precisa passar o nome do formulario e do textfield que receberá o valor passado por parametro. 
            window.opener.document.pedido.nome.value = nome;
            window.opener.document.pedido.sexo.value = sexo;
            window.opener.document.pedido.dt_nascimento.value = dtnasc;
            window.opener.document.pedido.idade.value = idade;
            window.opener.document.pedido.endereco.value = endereco;
            window.opener.document.pedido.end_num.value = numero;
            window.opener.document.pedido.end_comp.value = complemento;
            window.opener.document.pedido.end_bairro.value = bairro;
            window.opener.document.pedido.end_cidade.value = cidade;
            window.opener.document.pedido.end_uf.value = estado;
            window.opener.document.pedido.telefone.value = telefone;
            window.opener.document.pedido.celular.value = celular;
            window.opener.document.pedido.cpf.value = cpf;
            window.opener.document.pedido.cns.value = carteirinha;
            window.opener.document.pedido.nome_mae.value = nome_mae;
            window.opener.document.pedido.rg.value = identidade;
            window.opener.document.pedido.org_expeditor.value = org_expeditor;

            if (cep.length == 9) {
                window.opener.document.pedido.end_cep.value = cep;
            } else {
                window.opener.document.pedido.end_cep.value = cep.substr(0, 5) + "-" + cep.substr(5, 3);
            }
            // window.opener.document.pedido.nomeMae.value = email;



            if (imagem != "") {
                // window.opener.document.pedido.blah.src = 'imagens/clientes/' + imagem;

            } else {
                // window.opener.document.pedido.blah.src = 'app-assets/img/gallery/user-transp.png';
            }

            if (doc) {
                window.opener.document.getElementById('fdoc').innerHTML =
                    '<div class="row mt-3"><div class="col-md-12"><a href="documents/' + doc +
                    '" class="btn btn-primary btn-lg mr-5" target="_blank" id="doc">Visualizar Documentos <i class="far fa-address-card"></i></a></div></div>';
            }

            arr_idade = idade.split('');
            controle_idade = '';

            for (i = 0; !isNaN(arr_idade[i]); i++) {
                controle_idade = controle_idade + arr_idade[i];
            }

            if (controle_idade < 14) {
                Swal.fire("Paciente tem menos de 14 anos").then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        window.close(); //fecha a janla popup
                    }
                });
            } else {
                window.close(); //fecha a janla popup
            }
        }
    </script>
    <script>
        function buscar_agenda(data) {
            var estado = $('#modalidade').val();
            if (estado) {
                var url = 'ajax_buscar_agenda.php?data=' + data + '&modalidade=' + estado;
                $.get(url, function(dataReturn) {
                    $('#load_agenda').html(dataReturn);
                });
            }
        }

        function buscar_sgrupos() {
            var estado = $('#cod_id').val();
            if (estado) {
                var url = 'ajax_buscar_proc.php?estado=' + estado;
                $.get(url, function(dataReturn) {
                    $('#load_cidades').html(dataReturn);
                });
            }
        }


        function doConfirm(id) {

            var ok = confirm("Confirma a exclusao?<?php echo $transacao; ?>")
            if (ok) {

                if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else { // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.location =
                            "agendaexame.php?data=<?php echo date('Y-m-d'); ?>";
                        window.location.reload()
                    }
                }

                xmlhttp.open("GET",
                    "apagaagendatemp.php?id=<?php echo $transacao; ?>");
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
    </script>


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
    <?php include 'menu.php'; ?>
    <?php include 'header.php'; ?>
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
                                    <!-- <div class="col-6">
                                        <div class="float-right">
                                            <ol class="breadcrumb">
                                                <li><a href="../index.html">Home</a></li>
                                                <li><a href="#">Atendimentos</a></li>
                                                <li class="active">Atendimentos Cadastrados</li>
                                            </ol>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>

                        <!-- CORPO DA PAGINA -->
                        <div class="card-content">
                            <div class="col-12">
                                <div class="card-content">
                                    <form method="post" id="form_pesquisa" autocomplete="off">
                                        <div class="col-sm-12">
                                            <label class="control-label">Paciente</label>
                                            <input type="text" name="pesquisa" id="pesquisa"
                                                value='<?php echo $termo; ?>'
                                                class="form-control" autofocus>
                                        </div>


                                        <div class="col-sm-12">

                                            <div class="col-md-12 col-sm-4 col-3 mt-3">
                                                <div class="form-group">
                                                    <table id="dttable"
                                                        class="table table-hover table-condensed table-striped width-full col-sm-12">
                                                        <thead>
                                                            <tr>
                                                                <th>Ação
                                                                <th>Nome</th>
                                                                <th>Telefone</th>
                                                                <th>Nascimento</th>
                                                                <th>Mãe</th>
                                                                <th>cpf</th>
                                                                <th>DT. Ultimo Atendimento</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
															$x = 0;
															include 'conexao.php';
															$stmt = "SELECT a.*, (select count(*) as qtde from ocorrencias b where b.pessoa_id=a.pessoa_id and situacao='Pendente') 
									                                 FROM pessoas a where ";

															if (is_numeric($pesquisa)) {
																$stmt = $stmt . " a.cpf = '" . ts_codifica($pesquisa) . "' order by a.nome";
															} else {
																$pos = strpos($pesquisa, '/');
																if ($pos === false) {
																	$stmt = $stmt . " a.nome like '" . ts_codifica($pesquisa) . "%' order by a.nome, a.cpf desc ";
																} else {
																	$stmt = $stmt . " a.dt_nasc = '" . inverteData($pesquisa) . "' order by a.nome, a.cpf desc";
																}
															}

															$sth = pg_query($stmt) or die($stmt);
															while ($row = pg_fetch_object($sth)) {
																$stmtAT = "SELECT dat_cad from atendimentos where paciente_id = $row->pessoa_id order by transacao desc";
																$sthAT = pg_query($stmtAT) or die($stmtAT);
																$rowAT = pg_fetch_object($sthAT);

																$x = $x + 1;
																$dt_nasc = $row->dt_nasc;
																$date = new DateTime($dt_nasc); // data de nascimento
																$interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida

																$idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
																echo '<tr>';
																echo "<td><a href=\"javascript:retorna('" . $row->pessoa_id . "','" . ts_decodifica($row->nome) . "','" . $row->sexo . "','" . inverteData($row->dt_nasc) . "','" . $idade . "','" . $row->endereco . "','" . $row->numero . "','" . $row->complemento . "','" . $row->bairro . "','" . $row->cidade . "','" . $row->estado . "','" . $row->telefone . "','" . $row->celular . "','" . $row->cep . "','" . $row->qtde . "','" . ts_decodifica($row->cpf) . "','" . $row->imagem . "','" . ts_decodifica($row->nome_mae) . "','" . $row->num_carteira_convenio . "','" . ts_decodifica($row->identidade) . "','" . $row->org_expeditor . "','" . $row->documento . "')\" <i class=\"icon fas fa-check-circle\"></i></a></td>";
																echo '<td>' . ts_decodifica($row->nome) . '</td>';
																echo '<td>' . $row->qtde . '</td>';
																echo '<td>' . inverteData($row->dt_nasc) . '</td>';
																echo '<td>' . ts_decodifica($row->nome_mae) . '</td>';
																echo '<td>' . ts_decodifica($row->cpf) . '</td>';
																echo '<td>' . inverteData(substr($rowAT->dat_cad, '0', '10')) . '</td>';
																echo '</tr>';
															}
															?>
                                                        </tbody>
                                                    </table>
                                                </div>
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
        <?php if ($x == 0 && $pesquisa != '') {
																if ($pesquisa != 'qwwwqq') {
																	echo '<div class="row">';
																	echo '<div align="center" class="col-md-12 margin-bottom-30">';
																	echo "<button type=\"button\" class=\"btn btn-wide btn-success\" 	onClick=\"location.href='cadastropopup.php'\">Novo Cliente</button>";
																	echo  '</div></div>';
																}
															}
		?>
        <?php include 'footer.php'; ?>
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
        <script defer src="/your-path-to-fontawesome/js/all.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js" type="text/javascript">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
            type="text/javascript"></script>
        <script>
            $('#dttable').DataTable({
                "order": [
                    [1, "asc"]
                ]
            });
            $("#pesquisa").keydown(function() {
                if (window.event.keyCode == 13) {
                    document.getElementById("form_pesquisa").submit();
                    return false;
                }

                try {
                    $("#pesquisa").unmask();
                } catch (e) {}

                var tamanho = $("#pesquisa").val().length;

                if ($.isNumeric($("#pesquisa").val()[0]) && tamanho < 8) {
                    $("#pesquisa").mask("99/99/9999");
                    // ajustando foco
                    var elem = this;
                    setTimeout(function() {
                        // mudo a posição do seletor
                        elem.selectionStart = elem.selectionEnd = 10000;
                    }, 0);
                    // reaplico o valor para mudar o foco
                    var currentValue = $(this).val();
                    $(this).val('');
                    $(this).val(currentValue);
                } else if ($.isNumeric($("#pesquisa").val()[0])) {
                    $("#pesquisa").mask("99999999999");
                    // ajustando foco
                    var elem = this;
                    setTimeout(function() {
                        // mudo a posição do seletor
                        elem.selectionStart = elem.selectionEnd = 10000;
                    }, 0);
                    // reaplico o valor para mudar o foco
                    var currentValue = $(this).val();
                    $(this).val('');
                    $(this).val(currentValue);
                } else {
                    $("#pesquisa").unmask();
                }
            });

            function reset() {
                $("#toggleCSS").attr("href", "../themes/alertify.default.css");
                alertify.set({
                    labels: {
                        ok: "OK",
                        cancel: "Cancel"
                    },
                    delay: 5000,
                    buttonReverse: false,
                    buttonFocus: "ok"
                });
            }

            // ==============================
            // Standard Dialogs

            $("#prompt").on('click', function() {
                reset();
                alertify.prompt("This is a prompt dialog", function(e, str) {
                    if (e) {
                        alertify.success("You've clicked OK and typed: " + str);
                    } else {
                        alertify.error("You've clicked Cancel");
                    }
                }, "Default Value");
                return false;
            });

            // ==============================
            // Ajax
            $("#ajax").on("click", function() {
                reset();
                alertify.confirm("Confirm?", function(e) {
                    if (e) {
                        alertify.alert("Successful AJAX after OK");
                    } else {
                        alertify.alert("Successful AJAX after Cancel");
                    }
                });
            });

            // ==============================
            // Standard Dialogs
            $("#notification").on('click', function() {
                reset();
                alertify.log("Standard log message");
                return false;
            });

            $("#success").on('click', function() {
                reset();
                alertify.success("Success log message");
                return false;
            });

            $("#error").on('click', function() {
                reset();
                alertify.error("Error log message");
                return false;
            });

            // ==============================
            // Custom Properties
            $("#delay").on('click', function() {
                reset();
                alertify.set({
                    delay: 10000
                });
                alertify.log("Hiding in 10 seconds");
                return false;
            });

            $("#forever").on('click', function() {
                reset();
                alertify.log("Will stay until clicked", "", 0);
                return false;
            });

            $("#labels").on('click', function() {
                reset();
                alertify.set({
                    labels: {
                        ok: "Accept",
                        cancel: "Deny"
                    }
                });
                alertify.confirm("Confirm dialog with custom button labels", function(e) {
                    if (e) {
                        alertify.success("You've clicked OK");
                    } else {
                        alertify.error("You've clicked Cancel");
                    }
                });
                return false;
            });

            $("#focus").on('click', function() {
                reset();
                alertify.set({
                    buttonFocus: "cancel"
                });
                alertify.confirm("Confirm dialog with cancel button focused", function(e) {
                    if (e) {
                        alertify.success("You've clicked OK");
                    } else {
                        alertify.error("You've clicked Cancel");
                    }
                });
                return false;
            });

            $("#order").on('click', function() {
                reset();
                alertify.set({
                    buttonReverse: true
                });
                alertify.confirm("Confirm dialog with reversed button order", function(e) {
                    if (e) {
                        alertify.success("You've clicked OK");
                    } else {
                        alertify.error("You've clicked Cancel");
                    }
                });
                return false;
            });

            // ==============================
            // Custom Log
            $("#custom").on('click', function() {
                reset();
                alertify.custom = alertify.extend("custom");
                alertify.custom("I'm a custom log message");
                return false;
            });

            // ==============================
            // Custom Themes
            $("#bootstrap").on('click', function() {
                reset();
                $("#toggleCSS").attr("href", "../themes/alertify.bootstrap.css");
                alertify.prompt("Prompt dialog with bootstrap theme", function(e) {
                    if (e) {
                        alertify.success("You've clicked OK");
                    } else {
                        alertify.error("You've clicked Cancel");
                    }
                }, "Default Value");
                return false;
            });
        </script>
</body>

</html>