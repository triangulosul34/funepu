<?php

error_reporting(0);
include('verifica.php');
include('funcoes.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id  = $_GET['id'];

    include('conexao.php');
    $stmt = "select * from procedimentos where procedimento_id=" . $id;
    $sth = pg_query($stmt) or die($stmt);
    $row = pg_fetch_object($sth);
    $codigox    = $row->procedimento_id;
    $descricao     = $row->descricao;
    $modalidade    = $row->modalidade_id;
    $exame     = $row->exame_id;
    $exame02     = $row->exame02;
    $exame03     = $row->exame03;
    $tempo     = $row->dias_execucao;
    $unid_tempo    = $row->unidade_tempo;
    $laudo     = $row->laudo_padrao;
    $preparo     = $row->preparo;
    $preparo_msn = $row->preparo_msn;
    $titulo     = $row->titulo_laudo;
    $codSigtap         = $row->sigtap;
    $qtde_mes       = $row->qtde_mes;
    $vr_proc       = $row->valor;
    $material = $row->material;
    $metodo = $row->metodo;
    $escalaInicial = $row->escala_ini;
    $intervalo = $row->intervalo;
    $escalaFinal = $row->escala_fim;
    $autorizacao    = $row->necessita_autorizacao;
    $formulario    = $row->formulario;
    $cod_os     = $row->cod_os;

    $stmt = "select * from cid_procedimentos where procedimento_id=" . $id;
    $sth = pg_query($stmt) or die($stmt);
    while ($row = pg_fetch_object($sth)) {
        $cid = $cid . ";" . $row->cid;
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao  = $_POST['descricao'];
    $modalidade = $_POST['modalidade'];
    $tempo         = $_POST['tempo'];
    $titulo     = $_POST['titulo'];
    $preparo    = $_POST['preparo'];
    $preparo_msn = $_POST['preparo_msn'];
    $codigo        = $_POST['codigox'];
    $unid_tempo    = $_POST['unid_tempo'];
    $qtde_mes   = $_POST['qtde_mes'];
    $vr_proc       = $_POST['vr_proc'];
    $autorizacao = $_POST['autorizacao'];
    $formulario    = $_POST['formulario'];
    $metodo = $_POST['metodo'];
    $material = $_POST['material'];
    $cid = explode(";", $_POST['cid']);

    $escalaInicial = $_POST['escalaInicial'];
    if ($escalaInicial == "") {
        $escalaInicial = '0';
    }

    $intervalo = $_POST['intervalo'];
    if ($intervalo == "") {
        $intervalo = '0';
    }

    $escalaFinal = $_POST['escalaFinal'];
    if ($escalaFinal == "") {
        $escalaFinal = '0';
    }

    $codSigtap     = $_POST['codSigtap'];
    $cod_os     = $_POST['cod_os'];
    $erro = "";
    if ($codigo == "") {
        $erro = "Código - Campo Obrigatorio";
    }
    if ($descricao == "") {
        $erro = "Descricao - Campo Obrigatorio";
    }
    if ($modalidade == "") {
        $erro = "Modalidade - Campo Obrigatorio";
    }
    if ($tempo == "") {
        $erro = "Dias para conclusão - Campo Obrigatorio";
    }
    //if ($titulo==""		)	{$erro="Título no Laudo - Campo Obrigatorio";}

    include('conexao.php');
    $stmt = "delete from cid_procedimentos where procedimento_id = $codigo";
    $sth = pg_query($stmt) or die($stmt);

    foreach ($cid as $value) {
        if ($value != "") {
            $stmt = "insert into cid_procedimentos(procedimento_id, cid) values ($codigo, '$value')";
            $sth = pg_query($stmt) or die($stmt);
        }
    }

    if ($erro == "") {
        $valor = str_replace('.', '', $vr_proc);
        $valor = str_replace(',', '.', $valor);
        if ($unid_tempo == '') {
            $unid_tempo = '1';
        }
        if ($qtde_mes  == '1') {
            $qtde_mes  = '0';
        }
        if ($qtde_mes  == '') {
            $qtde_mes  = '0';
        }
        include('conexao.php');
        $stmt = "update procedimentos set sigtap='$codSigtap', descricao='$descricao', necessita_autorizacao='$autorizacao', 
				formulario='$formulario', metodo='$metodo', material='$material', escala_ini='$escalaInicial', intervalo='$intervalo',
				escala_fim='$escalaFinal', modalidade_id='$modalidade',  valor=$valor, qtde_mes=$qtde_mes, dias_execucao='$tempo', 
				preparo='$preparo', preparo_msn='$preparo_msn', titulo_laudo='$titulo', unidade_tempo='$unid_tempo', 
				cod_os = '$cod_os' where procedimento_id=$codigo";
        $sth = pg_query($stmt) or die($stmt);

        header('location:procedimentos.php');
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
    <title>FUNEPU | Cadastra de Procedimento</title>
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
                                                        » </p>Cadastro Procedimento
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
                                                <li><a href="procedimentos.php">Procedimentos</a></li>
                                                <li class="active">Cadastro Procedimento</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" method="post">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group">
                                                <input type="hidden"  class="form-control"   id="codigox"   name="codigox"   value="<?php echo $codigox;?>"  >
                                                    <label for="">Codigo SIGTAP</label>
                                                    <input type="text" class="form-control square" id="codSigtap" name="codSigtap" value="<?php echo $codSigtap; ?>" maxlength='10'>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label for="">Descricao</label>
                                                    <input type="text" class="form-control square" id="descricao" name="descricao" value="<?php echo $descricao; ?>" maxlength='10'>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="">Codigo de Ordem de Serviço</label>
                                                    <input type="text" class="form-control square" id="cod_os" name="cod_os" value="<?php echo $cod_os; ?>" maxlength='10'>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Valor Procedimento</label>
                                                    <input type="text" class="form-control square" id="vr_proc" name="vr_proc" value="<?php echo $vr_proc; ?>" onKeyPress="return(MascaraMoeda(this,'.',',',event))">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Quant/Mes</label>
                                                    <input type="text" class="form-control square" id="qtde_mes" name="qtde_mes" value="<?php echo $qtde; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Autorizacao</label>
                                                    <select class="form-control" id="autorizacao" name="autorizacao">
                                                        <option value='S' <?php if ($autorizacao == 'S') echo "selected"; ?>>Sempre</option>
                                                        <option value='Q' <?php if ($autorizacao == 'Q') echo "selected"; ?>>Quando atingir a quantidade/mes</option>
                                                        <option value='N' <?php if ($autorizacao == 'N') echo "selected"; ?>>Autorizacao nao necessaria</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Formulario/Solicitacão</label>
                                                    <select class="form-control square" id="formulario" name="formulario">
                                                        <option value='0' <?php if ($formulario == '0') echo "selected"; ?>>Nenhum</option>
                                                        <option value='F' <?php if ($formulario == 'F') echo "selected"; ?>>FAA</option>
                                                        <option value='B' <?php if ($formulario == 'B') echo "selected"; ?>>BPA Individualizado</option>
                                                        <option value='A' <?php if ($formulario == 'A') echo "selected"; ?>>Apac</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Modalidade</label>
                                                    <select class="form-control" id="modalidade" name="modalidade">
                                                        <?php
                                                        include('conexao.php');
                                                        $stmt = "Select * from modalidades where situacao='0' order by descricao";
                                                        $sth = pg_query($stmt) or die($stmt);
                                                        while ($row = pg_fetch_object($sth)) {
                                                            echo "<option value=\"" . $row->modalidade_id . "\"";
                                                            if ($row->modalidade_id == $modalidade) {
                                                                echo "selected";
                                                            }
                                                            echo ">" . $row->descricao . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Dias para conclusão</label>
                                                    <select class="form-control square" id="tempo" name="tempo">
                                                        <option value="1" <?php if ($tempo == '1') echo "selected"; ?>>01 dia</option>
                                                        <option value="2" <?php if ($tempo == '2') echo "selected"; ?>>02 dias</option>
                                                        <option value="3" <?php if ($tempo == '3') echo "selected"; ?>>03 dias</option>
                                                        <option value="4" <?php if ($tempo == '4') echo "selected"; ?>>04 dias</option>
                                                        <option value="5" <?php if ($tempo == '5') echo "selected"; ?>>05 dias</option>
                                                        <option value="6" <?php if ($tempo == '6') echo "selected"; ?>>06 dias</option>
                                                        <option value="7" <?php if ($tempo == '7') echo "selected"; ?>>07 dias</option>
                                                        <option value="8" <?php if ($tempo == '8') echo "selected"; ?>>08 dias</option>
                                                        <option value="9" <?php if ($tempo == '9') echo "selected"; ?>>09 dias</option>
                                                        <option value="10" <?php if ($tempo == '10') echo "selected"; ?>>10 dias</option>
                                                        <option value="11" <?php if ($tempo == '11') echo "selected"; ?>>11 dias</option>
                                                        <option value="12" <?php if ($tempo == '12') echo "selected"; ?>>12 dias</option>
                                                        <option value="13" <?php if ($tempo == '13') echo "selected"; ?>>13 dias</option>
                                                        <option value="14" <?php if ($tempo == '14') echo "selected"; ?>>14 dias</option>
                                                        <option value="15" <?php if ($tempo == '15') echo "selected"; ?>>15 dias</option>
                                                        <option value="16" <?php if ($tempo == '16') echo "selected"; ?>>16 dias</option>
                                                        <option value="17" <?php if ($tempo == '17') echo "selected"; ?>>17 dias</option>
                                                        <option value="18" <?php if ($tempo == '18') echo "selected"; ?>>18 dias</option>
                                                        <option value="19" <?php if ($tempo == '19') echo "selected"; ?>>19 dias</option>
                                                        <option value="20" <?php if ($tempo == '20') echo "selected"; ?>>20 dias</option>
                                                        <option value="21" <?php if ($tempo == '21') echo "selected"; ?>>21 dias</option>
                                                        <option value="22" <?php if ($tempo == '22') echo "selected"; ?>>22 dias</option>
                                                        <option value="23" <?php if ($tempo == '23') echo "selected"; ?>>23 dias</option>
                                                        <option value="24" <?php if ($tempo == '24') echo "selected"; ?>>24 dias</option>
                                                        <option value="25" <?php if ($tempo == '25') echo "selected"; ?>>25 dias</option>
                                                        <option value="26" <?php if ($tempo == '26') echo "selected"; ?>>26 dias</option>
                                                        <option value="27" <?php if ($tempo == '27') echo "selected"; ?>>27 dias</option>
                                                        <option value="28" <?php if ($tempo == '28') echo "selected"; ?>>28 dias</option>
                                                        <option value="29" <?php if ($tempo == '29') echo "selected"; ?>>29 dias</option>
                                                        <option value="30" <?php if ($tempo == '30') echo "selected"; ?>>30 dias</option>
                                                        <option value="31" <?php if ($tempo == '25') echo "selected"; ?>>31 dias</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Título do Laudo</label>
                                                    <input type="text" class="form-control square" id="titulo" name="titulo" value="<?php echo $titulo; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">Preparo</label>
                                                    <textarea class="form-control square" id="summernote2" name="content2" rows="18"><?php echo $preparo; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div align="center" class="col-md-12 margin-bottom-30">
                                                <button type="submit" class="btn btn-raised btn-primary square">Gravar</button>
                                                <button type="button" class="btn btn-raised btn-danger square" onclick="location.href='procedimentos.php'">Cancelar</button>
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
        <script defer src="/your-path-to-fontawesome/js/all.js"></script>
        <script>
            stop = '';

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

            function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e) {
                var sep = 0;
                var key = '';
                var i = j = 0;
                var len = len2 = 0;
                var strCheck = '0123456789';
                var aux = aux2 = '';
                var whichCode = (window.Event) ? e.which : e.keyCode;
                if (whichCode == 13) return true;
                key = String.fromCharCode(whichCode); // Valor para o código da Chave
                if (strCheck.indexOf(key) == -1) return false; // Chave inválida
                len = objTextBox.value.length;
                for (i = 0; i < len; i++)
                    if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;
                aux = '';
                for (; i < len; i++)
                    if (strCheck.indexOf(objTextBox.value.charAt(i)) != -1) aux += objTextBox.value.charAt(i);
                aux += key;
                len = aux.length;
                if (len == 0) objTextBox.value = '';
                if (len == 1) objTextBox.value = '0' + SeparadorDecimal + '0' + aux;
                if (len == 2) objTextBox.value = '0' + SeparadorDecimal + aux;
                if (len > 2) {
                    aux2 = '';
                    for (j = 0, i = len - 3; i >= 0; i--) {
                        if (j == 3) {
                            aux2 += SeparadorMilesimo;
                            j = 0;
                        }
                        aux2 += aux.charAt(i);
                        j++;
                    }
                    objTextBox.value = '';
                    len2 = aux2.length;
                    for (i = len2 - 1; i >= 0; i--)
                        objTextBox.value += aux2.charAt(i);
                    objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);
                }
                return false;
            }
        </script>
</body>

</html>