<?php
error_reporting(0);
function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}
$pesquisa = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$pesquisa = $_POST['pesquisa'];
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
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/pickadate/pickadate.css">
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <!--load all styles -->



</head>
<script type="text/javascript">
    //função javascript que retornará o codigo 
    function retorna(id, nome, telefone, celular, email) //passando um parametro 
    {
        window.opener.document.pedido.solicitante.value =
            id; //a janela mãe recebe o id, você precisa passar o nome do formulario e do textfield que receberá o valor passado por parametro. 
        window.opener.document.pedido.nomesolicitante.value = nome;
        window.opener.document.pedido.telsolicitante.value = telefone;
        window.opener.document.pedido.celsolicitante.value = celular;
        window.opener.document.pedido.emailsolicitante.value = email;
        window.close(); //fecha a janla popup 
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
                                    <form method="post">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="control-label">Solicitante</label>
                                                <input type="text" name="pesquisa" id="pesquisa" class="form-control"
                                                    onkeyup="maiuscula(this)" autofocus>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <table
                                                        class="table table-hover table-condensed table-striped width-full">
                                                        <thead>
                                                            <tr>
                                                                <th width='10%'>Id</th>
                                                                <th width='35%'>Nome</th>
                                                                <th width='15%'>Telefone</th>
                                                                <th width='15%'>Especialidade</th>
                                                                <th width="5%">Ação
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
														$x = 0;
														if ($pesquisa != '') {
															include 'conexao.php';
															$stmt = "SELECT * FROM solicitantes a left join especialidade b on a.especialidade_id=b.especialidade_id where a.nome like '$pesquisa%' order by a.nome ";

															$sth = pg_query($stmt) or die($stmt);
															while ($row = pg_fetch_object($sth)) {
																$x = $x + 1;
																echo '<tr>';
																echo '<td>' . $row->solicitante_id . '</td>';
																echo '<td>' . $row->nome . '</td>';
																echo '<td>' . $row->telefone . '</td>';
																echo '<td>' . $row->descricao . '</td>';
																echo "<td><a href=\"javascript:retorna('" . $row->solicitante_id . "','" . $row->nome . "','" . $row->telefone . "','" . $row->celular . "','" . $row->email . "')\" <i class=\"fas fa-angle-down\"></i></a></td>";
																echo '</tr>';
															}
														}
													?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($x == 0 && $pesquisa != '') {
														echo '<div class="row">';
														echo '<div align="center" class="col-md-12 margin-bottom-30">';
														echo "<button type=\"button\" class=\"btn btn-wide btn-success\" 	onClick=\"location.href='cadastropopsol.php'\">Novo Solicitantes</button>";
														echo  '</div></div>';
													}
								?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
    <script src="app-assets/js/scripts.js" type="text/javascript"></script>
    <script src="app-assets/js/popover.js" type="text/javascript"></script>
    <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</body>

</html>