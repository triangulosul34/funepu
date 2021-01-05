<?php

require 'tsul_ssl.php';

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}
error_reporting(0);
$menu_grupo = '3';
$menu_sgrupo = '1';
$nome = '';
$dtnasc = '';
$telefone = '';
$mae = '';
include 'verifica.php';
$RX = '';
$US = '';
$CT = '';
$MM = '';
$RM = '';
$DS = '';
$ECO = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$codigo = $_GET['id'];
	if ($codigo != '') {
		$where = ' pessoa_id =' . $codigo;
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$procedimentox = $_POST['procedimentox'];
	$situacao = $_POST['situacao'];
	$nome = ts_codifica($_POST['nome']);
	$xbox = $_POST['xbox'];
	$CM = $_POST['cb_cm'];
	$PED = $_POST['cb_ped'];
	$start = $_POST['start'];
	$end = $_POST['end'];
	$transfere = $_POST['cb_exame'];
	$profissional = $_POST['prof_transfere'];
	$cb_meus = $_POST['cb_meus'];
	$cb_conf = $_POST['cb_CONFERENCIA'];

	$where = '';

	if (isset($_POST['semana'])) {
		$start = date('d/m/Y', strtotime('-7 days'));
		$end = date('d/m/Y');
	}
	if (isset($_POST['hoje'])) {
		$start = date('d/m/Y');
		$end = date('d/m/Y');
	}
	if (isset($_POST['ontem'])) {
		$start = date('d/m/Y', strtotime('-1 days'));
		$end = date('d/m/Y', strtotime('-1 days'));
	}
	$modalidades = '';

	if ($CM != '') {
		$modalidades = $modalidades . "'Consultorio Adulto',";
	}

	if ($PED != '') {
		$modalidades = $modalidades . "'Consultorio Pediátrico',";
	}

	$modalidades = substr($modalidades, 0, -1);

	if ($nome != '') {
		$where = $where . " c.nome like '%" . $nome . "%' ";
	}

	if ($procedimentox != '') {
		if ($where != '') {
			$where = $where . " and a.exame_id = $procedimentox";
		} else {
			$where = $where . " a.exame_id = $procedimentox";
		}
	}

	if ($xbox != '') {
		if ($where != '') {
			$where = $where . " and box = $xbox";
		} else {
			$where = $where . " box = $xbox";
		}
	}

	if ($modalidades != '') {
		if ($where != '') {
			$where = $where . " and a.especialidade in ($modalidades) ";
		} else {
			$where = $where . " a.especialidade in ($modalidades) ";
		}
	}

	if ($start != '') {
		$data = inverteData($start);
		if ($where != '') {
			$where = $where . " and (a.dat_cad >= '$data')";
		} else {
			$where = $where . " (a.dat_cad >= '$data')";
		}
	}

	if ($end != '') {
		$data = inverteData($end);
		if ($where != '') {
			$where = $where . " and (a.dat_cad <= '$data')";
		} else {
			$where = $where . " (a.dat_cad <= '$data')";
		}
	}

	if ($situacao != '') {
		if ($situacao != 'Pendentes') {
			if ($where != '') {
				$where = $where . " and (a.status = '$situacao')";
			} else {
				$where = $where . " (a.status = '$situacao')";
			}
		} else {
			if ($where != '' and $status == 'Pendentes') {
				$where = $where . " and (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
			} else {
				$where = $where . " (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
			}
		}
	}

	if ($cb_meus != '') {
		if ($where != '') {
			$where = $where . " and (a.med_analise = '$cb_meus' or a.med_confere = '$cb_meus' )";
		} else {
			$where = $where . " (a.med_analise = '$cb_meus' or a.med_confere = '$cb_meus')";
		}
	}

	if ($cb_conf != '') {
		if ($where != '') {
			$where = $where . " and (a.med_confere = '$cb_conf')";
		} else {
			$where = $where . " (a.med_confere = '$cb_conf')";
		}
	}
	$stmtx = 'nao entrei';

	if ($transfere != '') {
		if (isset($_POST['transferir'])) {
			include 'conexao.php';
			$stmty = "Select username from pessoas where pessoa_id = $profissional";

			$sth = pg_query($stmty) or die($stmty);
			$row = pg_fetch_object($sth);
			$username = $row->username;
			if ($username != '') {
				include 'conexao.php';
				$stmtx = "Update itenspedidos set med_analise = '" . $username . "' where exame_nro in (" . implode(',', $transfere) . ") and (situacao='Editado' or situacao='Cadastrado')";
				$sth = pg_query($stmtx) or die($stmtx);

				foreach ($transfere as $item) {
					include 'conexao.php';
					$data = date('Y-m-d H:i:s');
					$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Distribuicao', '$usuario', '$data' )";
					$sth = pg_query($stmtx) or die($stmtx);
				}
			}
		}
		if (isset($_POST['transfconf'])) {
			include 'conexao.php';
			$stmty = "Select username from pessoas where pessoa_id = $profissional";

			$sth = pg_query($stmty) or die($stmty);
			$row = pg_fetch_object($sth);
			$username = $row->username;
			if ($username != '') {
				include 'conexao.php';
				$stmtx = "Update itenspedidos set med_confere = '" . $username . "' where exame_nro in (" . implode(',', $transfere) . ") and (situacao='Laudado' or situacao='Editado')";
				$sth = pg_query($stmtx) or die($stmtx);

				foreach ($transfere as $item) {
					include 'conexao.php';
					$data = date('Y-m-d H:i:s');
					$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Distribuicao', '$usuario', '$data' )";
					$sth = pg_query($stmtx) or die($stmtx);
				}
			}
		}
		if (isset($_POST['imprimir'])) {
			echo "<script>alert('Imprimir')</script>";
		}
		if (isset($_POST['enviar'])) {
			include 'conexao.php';
			$stmtx = "Update itenspedidos set situacao = 'Env.Recepção', envio_recepcao=now(), usu_envio_recepcao='$usuario'
                where exame_nro in (" . implode(',', $transfere) . ") and (situacao='Finalizado' or situacao='Impresso')";
			$sth = pg_query($stmtx) or die($stmtx);

			foreach ($transfere as $item) {
				include 'conexao.php';
				$data = date('Y-m-d H:i:s');
				$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Env Recepcao', '$usuario', '$data' )";
				$sth = pg_query($stmtx) or die($stmtx);
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
    <title>FUNEPU | Aguardando Triagem</title>
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
</style>

<script>
    // function carrega() {
    //     $.get('atualizaTriagem.php', function(dataReturn) {
    //         $('#atualizaTriagem').html(dataReturn);
    //     });
    // }

    // setInterval("carrega()", 5000);

    function teste() {
        $.get('atualizaTriagem.php', function(dataReturn) {
            $('#atualizaTriagem').html(dataReturn);
        });
    }

    setInterval("teste()", 5000);
</script>

<body class="pace-done" cz-shortcut-listen="true">
    <div class="modal fade text-left" id="modalConteudo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
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
                    <input type='button' name='confTriagem' id="confTriagem"
                        class="btn btn-raised btn-primary square btn-min-width mr-1 mb-1" value='Salvar Triagem'
                        onclick="salvar_triagem()">
                    <input type='button' name='cancelarModal' data-dismiss="modal" id="cancelarModal"
                        class="btn btn-raised btn-danger square btn-min-width mr-1 mb-1" value='Cancelar'>
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
                                                        » </p>aguardando triagem
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
                                                <li class="active">Aguardando Triagem</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="teste"></div>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="15%">Solicitação</th>
                                                        <th width="30%">Paciente</th>
                                                        <th width="10%">DT. Nascimento</th>
                                                        <th width="25%">Origem</th>
                                                        <th width="20%">Situação</th>
                                                        <th width="5%">Acao</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="atualizaTriagem">
                                                    <?php
													$i = 0;
													include 'conexao.php';
													$stmt = "select a.transacao, a.paciente_id, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.nec_especiais, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, 
                                                            a.dat_cad, c.nome, c.nome_social, c.dt_nasc, k.origem, a.tipo, a.coronavirus
                                                            from atendimentos a 
                                                            left join pessoas c on a.paciente_id=c.pessoa_id 
                                                            left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
                                                            WHERE status in ('Aguardando Triagem', 'Em Triagem') and dat_cad between '" . date('Y-m-d', strtotime('-1 days')) . "' and '" . date('Y-m-d') . "' and 
                                                            cast(tipo as integer) != '6' and tipo_at is null 
                                                            ORDER by 3, 1";
													$sth = pg_query($stmt) or die($stmt);
													while ($row = pg_fetch_object($sth)) {
														if ($row->prioridade == 'AMARELO') {
															$classe = 'style="background-color:gold"';
														}
														if ($row->prioridade == 'VERMELHO') {
															$classe = "class='bg-danger'";
														}
														if ($row->prioridade == 'VERDE') {
															$classe = "class='bg-success'";
														}
														if ($row->prioridade == 'AZUL') {
															$classe = "class='bg-primary'";
														}
														if ($row->prioridade == 'LARANJA') {
															$classe = "class='bg-warning'";
														}
														if ($row->prioridade == '') {
															$classe = 'style="background-color:Gainsboro"';
														}

														$ip = getenv('REMOTE_ADDR');
														echo '<tr ' . $classe . '>';
														if ($row->coronavirus == 1) {
															echo '<td>' . $row->transacao . '</td>';
															echo "<td class='blink'>" . inverteData(substr($row->dat_cad, 0, 10)) . ' - ' . $row->hora_cad . '</td>';
															if ($row->nome_social == '') {
																echo "<td class='blink'>" . ts_decodifica($row->nome);
															} else {
																echo "<td class='blink'>" . $row->nome_social . ' (' . ts_decodifica($row->nome) . ')';
															}
															if ($row->nec_especiais != 'Nenhuma') {
																echo "<br>Paciente com deficiencia $row->nec_especiais";
															}
															echo '</td>';
															echo "<td class='blink'>" . inverteData($row->dt_nasc) . '</td>';
															echo "<td class='blink'>" . $row->origem . '</td>';
															echo "<td class='blink'>" . $row->status . '</td>';
														} else {
															echo '<td>' . $row->transacao . '</td>';
															echo '<td>' . inverteData(substr($row->dat_cad, 0, 10)) . ' - ' . $row->hora_cad . '</td>';
															if ($row->nome_social == '') {
																echo '<td>' . ts_decodifica($row->nome);
															} else {
																echo '<td>' . $row->nome_social . ' (' . ts_decodifica($row->nome) . ')';
															}
															if ($row->nec_especiais != 'Nenhuma') {
																echo "<br>Paciente com deficiencia $row->nec_especiais";
															}
															echo '</td>';
															echo '<td>' . inverteData($row->dt_nasc) . '</td>';
															echo '<td>' . $row->origem . '</td>';
															echo '<td>' . $row->status . '</td>';
														}
														echo '<td>';
														//if (($perfil == '06' or $perfil == '04' or $perfil == '08' or $perfil == '15') and isset($_SESSION['box'])) {
														echo "<a id=\"triagemmanual\" data-id=\"$row->transacao\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn triagemmanual\" data-target=\"#modalConteudo\" data-toggle=\"modal\" data-original-title=\"Triagem Manual\" onClick=\"valorTriagem(this);\">
                                                            <i class=\"fas fa-hand-holding-medical\"></i></a>";
														//}
														echo '</td>';
														echo '</tr>';
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
        <?php include 'footer.php'; ?>


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
        <script>
            $(".triagemmanual").click(function() {
                $("#confTriagem").attr("disabled", true);
                $('#conteudoModal').html("");
                $('#conteudoModal').append("<h2>CARREGANDO...</h2>");

                setTimeout(function() {
                    $("#confTriagem").attr("disabled", false);
                    // alert("Handler for .click() called.");
                }, 3000);

            });

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

            $("#procedimentox").chosen({
                placeholder_text_single: "Selecione...",
                search_contains: true
            });

            function valorTriagem(valor) {
                transacao = $(valor).attr("data-id");
                $.get('triagemmanual.php?transacao=' + transacao + '&triagem_manual=1', function(dataReturn) {
                    $('#conteudoModal').html(dataReturn);
                });
                event.preventDefault();
            }

            function salvar_triagem() {
                // document.getElementById("confTriagem").disabled = true;
                var transacaoModal = $("#transacaoModal").val();
                var consultorioModal = $("#consultorioModal").val();
                var prioridadeModal = $("#prioridadeModal").val();
                var observacao = $("#observacao").val();
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
                } else if (consultorioModal == '') {
                    sweetAlert("Informe o consultorio para o atendimento!", "", "warning");
                } else {
                    $.post("salvartriagemmanual.php", {
                            transacaoModal: transacaoModal,
                            consultorioModal: consultorioModal,
                            prioridadeModal: prioridadeModal,
                            observacao: observacao,
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
            };
        </script>
</body>

</html>