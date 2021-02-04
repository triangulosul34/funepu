<?php
error_reporting(0);
include 'verifica.php';
include 'funcoes.php';
require 'tsul_ssl.php';

// if($apac=="")
//     {
//       include('conexao.php');
//       $sql = "SELECT nextval('apacs_solicitadas_apac_id_seq')";
//       $result = pg_query ( $sql ) or die ( $sql );
//       $row = pg_fetch_object ( $result );
//       $apac_nex = $row->nextval;
//     }

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$pessoa_id = $_GET['id'];

	include 'conexao.php';
	$stmt = "SELECT conselho_regional, num_conselho_reg, nome  from pessoas where tipo_pessoa = 'Medico Laudador' and username='$usuario'";
	$sth = pg_query($stmt) or die($stmt);
	$row = pg_fetch_object($sth);

	$crm = $row->num_conselho_reg;
	$solicitante = ts_decodifica($row->nome);
	$usuario = $row->username;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$usuario = $_POST['usuario'];
	$procedimento = $_POST['procedimento'];
	$pessoa_id = $_POST['prontuario'];
	$nome = ts_codifica($_POST['nome']);
	$crm = $_POST['crm'];
	$cid10 = $_POST['cid'];
	$anannese = $_POST['anannese'];
	$exames_comp = $_POST['exames_comp'];
	$justificativa = $_POST['justificativa'];
	$med_solicitante = $_POST['solicitante'];
	$data = $_POST['data_solicitacao'];
	$raca_cor = $_POST['raca_cor'];

	if ($erro == '') {
		include 'conexao.php';
		$stmt = "INSERT into apacs_solicitadas (pessoa_id, procedimento_id, raca_cor, cid10, justificativa, med_solicitante, crm, data_solicitacao, data_cad)
		values ($pessoa_id, $procedimento,'$raca_cor', '$cid10','$justificativa', '$med_solicitante', '$crm','$data', '" . date('Y-m-d') . "') RETURNING apac_id ";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);

		header("Location:apac.php?prontuario=$pessoa_id&apac_id=$row->apac_id ");
		//echo "<script>window.open('apac.php?prontuario=$pessoa_id&apac_id=$row->apac_id', '_blank')</script>";
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
    <title>FUNEPU | Solicitar APAC</title>
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
                                                        » </p>Nova APAC
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
                                                <li class="active">Solicitar APAC</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" method="POST" id="pedido" name="pedido">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="user-image">
                                                    <img src="app-assets/img/gallery/user-transp.png" height="130"
                                                        width="130" class="img-responsive" alt="usuario" id="blah"
                                                        onclick="window.open('poppac.php', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;">
                                                    <!--Foto deve ter o tamanho 300x300-->
                                                </div>
                                            </div>
                                            <div class="col-10">
                                                <div class="row">
                                                    <div class="col-3"><label for="">Data Solicitação</label>
                                                        <input type="text" name="data_solicitacao" id="data_solicitacao"
                                                            class="form-control square"
                                                            OnKeyPress="formatar('##/##/####', this)"
                                                            value="<?php echo date('d/m/Y') ?>" />
                                                    </div>
                                                    <div class="col-5">
                                                        <div class="form-group">
                                                            <label>Nome</label>
                                                            <input type="hidden" name="usuario"
                                                                value="<?php echo $usuario; ?>">
                                                            <input type="hidden" name="nome_social" id="nome_social">
                                                            <input type="hidden" name="nome_acompanhante"
                                                                id="nome_acompanhante">
                                                            <input type="hidden" name="idade" id="idade">
                                                            <input type="hidden" name="prontuario" id="prontuario"
                                                                value="<?php echo $pessoa_id; ?>">
                                                            <input type="hidden" name="org_expeditor"
                                                                id="org_expeditor">
                                                            <input type="hidden" name="telefone" id="telefone">
                                                            <input type="hidden" name="celular" id="celular">
                                                            <input type="hidden" name="nomeMae" id="nomeMae">
                                                            <input type="hidden" name="origem" id="origem">
                                                            <input type="text" class="form-control square" id="nome"
                                                                name="nome"
                                                                value="<?php echo $nome; ?>"
                                                                onkeyup="maiuscula(this)" readOnly>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="form-group">
                                                            <label>Raca/Cor</label>
                                                            <select class="form-control campo-requerido square"
                                                                name="raca_cor" id="raca_cor">
                                                                <option></option>
                                                                <option value="01" <?php if ($raca_cor == '01') {
	echo 'selected';
} ?>>Branca
                                                                </option>
                                                                <option value="02" <?php if ($raca_cor == '02') {
	echo 'selected';
} ?>>Preta
                                                                </option>
                                                                <option value="03" <?php if ($raca_cor == '03') {
	echo 'selected';
} ?>>Parda
                                                                </option>
                                                                <option value="04" <?php if ($raca_cor == '04') {
	echo 'selected';
} ?>>Amarela
                                                                </option>
                                                                <option value="05" <?php if ($raca_cor == '05') {
	echo 'selected';
} ?>>Indigena
                                                                </option>
                                                                <option value="99" <?php if ($raca_cor == '99') {
	echo 'selected';
} ?>>Nao
                                                                    Informado
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="form-group">
                                                            <label>Sexo</label>
                                                            <input type="text"
                                                                value="<?php echo $sexo; ?>"
                                                                class="form-control square" name="sexo" id="sexo"
                                                                readOnly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <label>Data Nascimento</label>
                                                            <input type="text"
                                                                value="<?php echo $dt_nasc; ?>"
                                                                placeholder="__/__/____" class="form-control square"
                                                                id="dt_nascimento" maxlength="20" name="dt_nascimento"
                                                                readOnly>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class='form-group'>
                                                            <label>CNS</label>
                                                            <input type="text"
                                                                value="<?php echo $cns; ?>"
                                                                class="form-control square" name="cns" id="cns"
                                                                readOnly>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <label> CPF</label>
                                                            <input type="text" placeholder="99999999999"
                                                                value="<?php echo $cpf; ?>"
                                                                onkeypress='return SomenteNumero(event)' maxlength="11"
                                                                class="form-control square" id="cpf" name="cpf"
                                                                readOnly>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="form-group">
                                                            <label>Identidade</label>
                                                            <input type="text" class="form-control square" id="rg"
                                                                name="rg"
                                                                value="<?php echo $identidade; ?>"
                                                                onkeyup="maiuscula(this)" readOnly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12"
                                            style="background-color: #ccc; height: 1px; width: 100%; margin-top: 10px; margin-bottom: 15px">
                                        </div>
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>CEP</label>
                                                    <input class="form-control square" placeholder="99999-999"
                                                        type="text" name="end_cep" maxlength="9" id="end_cep"
                                                        value="<?php echo $cep; ?>"
                                                        OnKeyPress="formatar('#####-###', this)"
                                                        onblur="getEndereco(this.value);" readOnly>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label>Rua</label>
                                                    <input class="form-control square" type="text" name="endereco"
                                                        id="endereco" onkeyup="maiuscula(this)"
                                                        value="<?php echo $rua; ?>"
                                                        readOnly>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>Numero</label>
                                                    <input class="form-control tooltips square" type="text"
                                                        name="end_num" id="end_num"
                                                        value="<?php echo $numero; ?>"
                                                        readOnly>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Complemento</label>
                                                    <input class="form-control square" type="text" name="end_comp"
                                                        id="end_comp" onkeyup="maiuscula(this)"
                                                        value="<?php echo $complemento; ?>"
                                                        readOnly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Bairro</label>
                                                    <input class="form-control square" type="text" name="end_bairro"
                                                        id="end_bairro"
                                                        value="<?php echo $bairro; ?>"
                                                        onkeyup="maiuscula(this)" readOnly>
                                                </div>
                                            </div>
                                            <div class="col-7">
                                                <div class="form-group">
                                                    <label>Cidade</label>
                                                    <input class="form-control square" type="text" name="end_cidade"
                                                        id="end_cidade"
                                                        value="<?php echo $cidade; ?>"
                                                        onkeyup="maiuscula(this)" readOnly>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>UF</label>
                                                    <input class="form-control square" type="text" name="end_uf"
                                                        id="end_uf"
                                                        value="<?php echo $uf; ?>"
                                                        onkeyup="maiuscula(this)" maxlength="2" readOnly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12"
                                            style="background-color: #ccc; height: 1px; width: 100%; margin-top: 10px; margin-bottom: 15px">
                                        </div>
                                        <div class="row">
                                            <div class="col-5">
                                                <div class='form-group'>
                                                    <label>Proc. Solicitado</label>
                                                    <select class="form-control  campo-requerido square"
                                                        name="procedimento" id="procedimento">
                                                        <option></option>
                                                        <?php
														include 'conexao.php';
														$stmt = "SELECT descricao, procedimento_id FROM procedimentos where modalidade_id= 3 and sigtap !='' order by descricao";
														$sth = pg_query($stmt) or die($stmt);
														while ($row = pg_fetch_object($sth)) {
															echo '<option value="' . $row->procedimento_id . '"';
															if ($procedimentos == $row->procedimento_id) {
																echo 'selected';
															}
															echo '>' . $row->descricao . '</option>';
														}
														?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label>CID</label>
                                                    <input type="text" name="cid" id="cid"
                                                        class="form-control campo-requerido square"
                                                        value="<?php echo $cid; ?>"
                                                        onkeyup="maiuscula(this),copiarCid(this)" maxlength='5' <?php echo $rdonly ?>>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label>Diagnóstico Principal</label>
                                                    <input type="text" name="diag_pri" id="diag_pri"
                                                        onkeyup="retornaCid(this),maiuscula(this)"
                                                        class="form-control campo-requerido square"
                                                        value="<?php echo $diag_pri; ?>"
                                                        <?php echo $rdonly ?>>
                                                    <style>
                                                        table #cidTable {
                                                            border-collapse: collapse;
                                                            width: 100%;
                                                        }

                                                        #cidTable th,
                                                        #cidTable td {
                                                            text-aling: left;
                                                            padding: 8px;
                                                        }

                                                        #cidTable tr:nth-child(even) {
                                                            background-color: #f0f0f0;
                                                        }

                                                        #lista_diagnostico {
                                                            height: 150px;
                                                            overflow: scroll;
                                                            display: none;
                                                            overflow-x: hidden;

                                                        }

                                                        #cidTable a {
                                                            text-decoration: none;
                                                        }

                                                        .linha {
                                                            padding: 10px;
                                                            border-top: 1px solid #999999;
                                                        }

                                                        .counttext {
                                                            width: 100%;
                                                            position: absolute;
                                                            text-align: right;
                                                            padding-right: 40px;
                                                            font-size: 11px;
                                                            color: #999999;
                                                            transform: translate(0%, -100%);
                                                        }
                                                    </style>
                                                    <div id="lista_diagnostico" style="display: none;">
                                                        <table id="cidTable"
                                                            class="table table-hover table-striped width-full"></table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Justificativa do procedimento</label>
                                                    <textarea data-minSize="150" maxlength="250" rows="4" cols="50"
                                                        class="form-control form-white campo-requerido minSize square"
                                                        onkeyup="this.value = this.value.toUpperCase(); contChar(this, 'contJustProc', '150')"
                                                        name="justificativa"></textarea>
                                                    <div id="contJustProc" class="counttext">
                                                        0 / 250
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12"
                                            style="background-color: #ccc; height: 1px; width: 100%; margin-top: 10px; margin-bottom: 15px">
                                        </div>
                                        <div class="row">
                                            <div class="col-10">
                                                <div class='form-group'>
                                                    <label>Medico Solicitante</label>
                                                    <input class="form-control square" type="text" name="solicitante"
                                                        id="solicitante"
                                                        value="<?php echo $solicitante; ?>"
                                                        readOnly>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">CRM</label>
                                                    <input class="form-control" type="text" name="crm" id="crm"
                                                        value="<?php echo $crm; ?>"
                                                        onkeyup="maiuscula(this)" readOnly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div align="center" class="col-md-12 margin-bottom-30">
                                                <button type="button" onclick="checkSubmit()"
                                                    class="btn btn-wide btn-primary">Imprimir</button>
                                                <a href="selformapac.php"><button type="button"
                                                        class="btn btn-wide btn-danger">Fechar</button>
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
    <script>
        (function(document, window, $) {
            'use strict';

            var Site = window.Site;
            $(document).ready(function() {
                Site.run();
            });
        })(document, window, jQuery);

        function mascaratel(campo) {
            campo.value = campo.value.replace(/[^\d]/g, '')
                .replace(/^(\d\d)(\d)/, '($1) $2')
                .replace(/(\d{4})(\d)/, '$1-$2');
            if (campo.value.length > 14) campo.value = stop;
            else stop = campo.value;
        }


        function mascara(str) {
            // Caso passe de 14 caracteres será formatado como CNPJ 
            if (str.value.length > 14)
                str.value = cnpj(str.value);
            // Caso contrário como CPF
            else
                str.value = cpf(str.value);
        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        // Funcao de formatacao CPF
        function cpf(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valoralor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o terceiro e o quarto digito
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um ponto entre o terceiro e o quarto dígitos 
            // desta vez para o segundo bloco      
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um hifen entre o terceiro e o quarto dígitos
            valor = valor.replace(/(\d{3})(\d)$/, "$1-$2");
            return valor;
        }

        // Funcao de formatacao CNPJ
        function cnpj(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o segundo e o terceiro dígitos
            valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");

            // Adiciona um ponto entre o quinto e o sexto dígitos
            valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");

            // Adiciona uma barra entre o oitavaloro e o nono dígitos
            valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");

            // Adiciona um hífen depois do bloco de quatro dígitos
            valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
            return valor;
        }

        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }

        function SomenteNumero(e) {
            var tecla = (window.event) ? event.keyCode : e.which;
            if ((tecla > 47 && tecla < 58)) return true;
            else {
                if (tecla == 8 || tecla == 0) return true;
                else return false;
            }
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function mascaraData(campo, e) {
            var kC = (document.all) ? event.keyCode : e.keyCode;
            var data = campo.value;

            if (kC != 8 && kC != 46) {
                if (data.length == 2) {
                    campo.value = data += '/';
                } else if (data.length == 5) {
                    campo.value = data += '/';
                } else
                    campo.value = data;
            }
        }

        function mascara(str) {
            // Caso passe de 14 caracteres será formatado como CNPJ 
            if (str.value.length > 14)
                str.value = cnpj(str.value);
            // Caso contrário como CPF
            else
                str.value = cpf(str.value);
        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        // Funcao de formatacao CPF
        function cpf(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valoralor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o terceiro e o quarto digito
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um ponto entre o terceiro e o quarto dígitos 
            // desta vez para o segundo bloco      
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um hifen entre o terceiro e o quarto dígitos
            valor = valor.replace(/(\d{3})(\d)$/, "$1-$2");
            return valor;
        }

        // Funcao de formatacao CNPJ
        function cnpj(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o segundo e o terceiro dígitos
            valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");

            // Adiciona um ponto entre o quinto e o sexto dígitos
            valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");

            // Adiciona uma barra entre o oitavaloro e o nono dígitos
            valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");

            // Adiciona um hífen depois do bloco de quatro dígitos
            valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
            return valor;
        }


        function retornaCid(valor) { //A fun褯 顰ara retorno do CID 10
            var cid = valor.value;
            $("#lista_diagnostico").css("display", "block");
            $.get('retorno_cid10.php?cid=' + cid, function(dataReturn) {
                $('#cidTable').html(dataReturn);
            });

            //Ocultar a caixa de sugest䯠do CID
            if (cid == "") {
                $("#lista_diagnostico").slideUp(100);
            }

        }

        $("#cid").blur(function() {
            var codcid = $('#cid').val();
            var url = 'ajax_buscar_cidcod.php?cid=' + codcid;
            $.get(url, function(dataReturn) {
                $('#diag_pri').val(dataReturn);
            });
        });

        function preencheCid(cd_cod, cd_descr) {
            $("#cid").val(cd_cod);
            $("#cidAtestado").val(cd_cod);
            $("#diag_pri").val(cd_descr);
            $('#cidTable').empty();
            $("#lista_diagnostico").slideUp(100);
        }

        function copiarCid(cd_cod) {
            var codcid = $('#cid').val();
            var url = 'ajax_buscar_cidcod.php?cid=' + codcid;
            $.get(url, function(dataReturn) {
                $('#diag_pri').val(dataReturn);
            });
        }

        function contChar(elem, path, minSize) { //Conta os caracteres dentro do textarea
            var text = elem.value
            var minSize = minSize || false
            if (minSize != false && (text.length < minSize)) {
                $(elem).css("borderColor", "red")
            } else {
                $(elem).css("borderColor", "")
            }
            $("#" + path).text(text.length + " / 250")
        }

        function checkSubmit() { //Checa se todos os campos obrigatorios foram preenchidos antes de dar o submit, caso nao, gera um swal
            var erro = false
            $(".campo-requerido").each((index, value) => {
                if ($(value).val() == "") {
                    erro = "Preencha o campo (" + $(value).siblings(".control-label").text() + ")"
                } else if ($(value).hasClass("minSize") && value.value.length < $(value).attr("data-minSize")) {
                    erro = $(value).siblings(".control-label").text() + " deve ter no mínimo " + $(value).attr(
                        "data-minSize") + " caracteres"
                }
            })
            if (erro != false) {
                swal({
                    type: "error",
                    title: "Erro",
                    text: erro
                })
            } else {
                $("form").submit()
            }
        }

        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }
    </script>
</body>

</html>