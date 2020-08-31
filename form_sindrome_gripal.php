<?php

include "verifica.php";

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('conexao.php');

    $uf = strtoupper($_POST['uf']);
    $municipio_notificacao = strtoupper($_POST['municipio_notificacao']);
    $pcpf = $_POST['pcpf'];
    $pestrangeiro = $_POST['pestrangeiro'];
    $psaude = $_POST['psaude'];
    $pseguranca = $_POST['pseguranca'];
    $cbo = $_POST['cbo'];
    $cpf = $_POST['cpf'];
    $cns = $_POST['cns'];
    $pessoa_id = $_POST['pessoa_id'];
    $nome = strtoupper($_POST['nome']);
    $nome_mae = strtoupper($_POST['nome_mae']);
    $data_nascimento = inverteData($_POST['data_nascimento']);
    $pais_origem = strtoupper($_POST['pais_origem']);
    $psexo = $_POST['psexo'];
    $praca = $_POST['praca'];
    $passaporte = $_POST['passaporte'];
    $cep = $_POST['cep'];
    $estado = strtoupper($_POST['estado']);
    $cidade = strtoupper($_POST['cidade']);
    $logradouro = strtoupper($_POST['logradouro']);
    $numero = $_POST['numero'];
    $bairro = strtoupper($_POST['bairro']);
    $complemento = strtoupper($_POST['complemento']);
    $celular = $_POST['celular'];
    $telefone = $_POST['telefone'];
    $data_notificacao = inverteData($_POST['data_notificacao']);
    $sfebre = $_POST['sfebre'];
    $sdor_garganta = $_POST['sdor_garganta'];
    $stosse = $_POST['stosse'];
    $sdispneia = $_POST['sdispneia'];
    $soutros = $_POST['soutros'];
    $stoutros = $_POST['sToutros'];
    $data_inicio_sintomas = inverteData($_POST['data_inicio_sintomas']);
    $cdoencas_respiratorias = $_POST['cdoencas_respiratorias'];
    $cdoencas_renais = $_POST['cdoencas_renais'];
    $cdoencas_cromossomicas = $_POST['cdoencas_cromossomicas'];
    $cdiabetes = $_POST['cdiabetes'];
    $cimunossupressão = $_POST['cimunossupressão'];
    $cdoencas_cardiacas = $_POST['cdoencas_cardiacas'];
    $cgestante = $_POST['cgestante'];
    $eteste = $_POST['eteste'];
    $data_coleta_teste = inverteData($_POST['data_coleta_teste']);
    $tipo_teste = $_POST['tipo_teste'];
    $resultado = $_POST['resultado'];
    $classificacao_final = $_POST['classificacao_final'];
    $evolucao_caso = $_POST['evolucao_caso'];
    $data_encerramento = inverteData($_POST['data_encerramento']);

    if (!$pessoa_id) $pessoa_id = 0;

    $sql = "INSERT INTO sindrome_gripal(uf,municipio_notificacao,pcpf,pestrangeiro,psaude,pseguranca,cbo,cpf,cns,nome,nome_mae,data_nascimento,pais_origem,psexo,praca,passaporte,cep,estado,cidade,logradouro,numero,bairro,complemento,celular,telefone,data_notificacao,sfebre,sdor_garganta,stosse,sdispneia,soutros,stoutros,data_inicio_sintomas,cdoencas_respiratorias,cdoencas_renais,cdoencas_cromossomicas,cdiabetes,cimunossupressão,cdoencas_cardiacas,cgestante,eteste,data_coleta_teste,tipo_teste,resultado,classificacao_final,evolucao_caso,data_encerramento, pessoa_id, usuario, data_form, hora_form) VALUES('$uf','$municipio_notificacao','$pcpf','$pestrangeiro','$psaude','$pseguranca','$cbo','$cpf','$cns','$nome','$nome_mae','$data_nascimento','$pais_origem','$psexo','$praca','$passaporte','$cep','$estado','$cidade','$logradouro','$numero','$bairro','$complemento','$celular','$telefone','$data_notificacao','$sfebre','$sdor_garganta','$stosse','$sdispneia','$soutros', '$stoutros','$data_inicio_sintomas','$cdoencas_respiratorias','$cdoencas_renais','$cdoencas_cromossomicas','$cdiabetes','$cimunossupressão','$cdoencas_cardiacas','$cgestante','$eteste','$data_coleta_teste','$tipo_teste','$resultado','$classificacao_final','$evolucao_caso','$data_encerramento', $pessoa_id, '$usuario', '" . date('Y-m-d') . "','" . date('H:i') . "')";
    $result = pg_query($sql) or die($sql);

    $sql = "SELECT max(sindrome_gripal_id) as id FROM sindrome_gripal";
    $result = pg_query($sql) or die($sql);
    $row = pg_fetch_object($result);

    header("location: form_sindrome_gripal_pdf.php?id=" . $row->id);
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
    <title>FUNEPU | Formulário síndrome gripal</title>
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
                                                        » </p>ficha sindrime gripal
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
                                                <li class="active">Sindrome Gripal</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="#" method="post">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Uf de Notificação</label>
                                                    <input type="text" name="uf" class="form-control" maxlength="2" value="MG">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="">Município de Notificação</label>
                                                    <input type="text" name="municipio_notificacao" class="form-control" value="Uberaba">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Tem CPF?</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="pcpf1" name="pcpf" class="custom-control-input" value="sim">
                                                            <label class="custom-control-label" for="pcpf1">Sim</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="pcpf2" checked="" name="pcpf" class="custom-control-input" value="nao">
                                                            <label class="custom-control-label" for="pcpf2">Nao</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Estrangeiro</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="pestrangeiro1" name="pestrangeiro" class="custom-control-input" value="sim">
                                                            <label class="custom-control-label" for="pestrangeiro1">Sim</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="pestrangeiro2" checked="" name="pestrangeiro" class="custom-control-input" value="nao">
                                                            <label class="custom-control-label" for="pestrangeiro2">Nao</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">É profissional de saúde</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="psaude1" name="psaude" class="custom-control-input" value="sim">
                                                            <label class="custom-control-label" for="psaude1">Sim</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="psaude2" checked="" name="psaude" class="custom-control-input" value="nao">
                                                            <label class="custom-control-label" for="psaude2">Nao</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">É profissional de segurança</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="pseguranca1" name="pseguranca" class="custom-control-input" value="sim">
                                                            <label class="custom-control-label" for="pseguranca1">Sim</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="pseguranca2" checked="" name="pseguranca" class="custom-control-input" value="nao">
                                                            <label class="custom-control-label" for="pseguranca2">Nao</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">CBO</label>
                                                    <input type="text" name="cbo" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">CPF</label>
                                                    <input type="text" name="cpf" id="cpf" class="form-control" maxlength="11">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="">CNS</label>
                                                    <input type="text" name="cns" class="form-control" maxlength="14">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <input type="hidden" name="pessoa_id" id="pessoa_id" readonly />
                                                    <label for="">Nome Completo</label>
                                                    <input type="text" name="nome" id="nome" onkeyup="autocomplet()" class="form-control">
                                                    <ul id="lista_nomes"></ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Nome Completo da Mae</label>
                                                    <input type="text" name="nome_mae" id="nome_mae" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Data de Nascimento</label>
                                                    <input type="text" name="data_nascimento" id="dt_nasc" class="form-control" OnKeyPress="formatar('##/##/####', this)">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">País de Origem</label>
                                                    <input type="text" name="pais_origem" class="form-control" value="Brasil">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Sexo</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="psexo1" name="psexo" class="custom-control-input" value="masculino">
                                                            <label class="custom-control-label" for="psexo1">Masculino</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="psexo2" checked="" name="psexo" class="custom-control-input" value="feminino">
                                                            <label class="custom-control-label" for="psexo2">Feminino</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Raça/Cor</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="praca1" name="praca" class="custom-control-input" value="branca">
                                                            <label class="custom-control-label" for="praca1">Branca</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="praca2" checked="" name="praca" class="custom-control-input" value="preta">
                                                            <label class="custom-control-label" for="praca2">Preta</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="praca3" checked="" name="praca" class="custom-control-input" value="amarela">
                                                            <label class="custom-control-label" for="praca3">Amarela</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="praca4" checked="" name="praca" class="custom-control-input" value="parda">
                                                            <label class="custom-control-label" for="praca4">Parda</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="praca5" checked="" name="praca" class="custom-control-input" value="indigena">
                                                            <label class="custom-control-label" for="praca5">Indigena</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Passaporte</label>
                                                    <input type="text" name="passaporte" class="form-control" maxlength="8">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">CEP</label>
                                                    <input type="text" name="cep" id="cep" class="form-control" OnKeyPress="formatar('#####-###', this)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Estado de residência</label>
                                                    <input type="text" name="estado" id="uf" class="form-control" maxlength="2">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="">Município de residência</label>
                                                    <input type="text" name="cidade" id="cidade" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Logradouro</label>
                                                    <input type="text" name="logradouro" id="rua" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="">Numero</label>
                                                    <input type="text" name="numero" id="numero" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Bairro</label>
                                                    <input type="text" name="bairro" id="bairro" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Complemento</label>
                                                    <input type="text" name="complemento" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Telefone celular</label>
                                                    <input type="text" name="celular" id="celular" class="form-control" OnKeyPress="formatar('##-#########', this)" maxlength="12">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Telefone de contato</label>
                                                    <input type="text" name="telefone" id="telefone" class="form-control" OnKeyPress="formatar('##-#########', this)" maxlength="12">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Data de Notificação</label>
                                                    <input type="text" name="data_notificacao" class="form-control" OnKeyPress="formatar('##/##/####', this)" value="<?= date('d/m/Y'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Sintomas</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-checkbox d-inline-block">
                                                            <input type="checkbox" id="sfebre" name="sfebre" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="sfebre">Febre</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block ml-2">
                                                            <input type="checkbox" id="sdor_garganta" name="sdor_garganta" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="sdor_garganta">Dor de Garganta</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block ml-2">
                                                            <input type="checkbox" id="stosse" name="stosse" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="stosse">Tosse</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block ml-2">
                                                            <input type="checkbox" id="sdispneia" name="sdispneia" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="sdispneia">Dispneia</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block ml-2">
                                                            <input type="checkbox" id="soutros" name="soutros" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="soutros">Outros</label>
                                                            <input type="text" name="stoutros">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Data do inicio dos sintomas</label>
                                                    <input type="text" name="data_inicio_sintomas" class="form-control" OnKeyPress="formatar('##/##/####', this)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Condições</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                            <input type="checkbox" id="cdoencas_respiratorias" name="cdoencas_respiratorias" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="cdoencas_respiratorias">Doenças respiratórias cronicas descompensada</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                            <input type="checkbox" id="cdoencas_renais" name="cdoencas_renais" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="cdoencas_renais">Doenças renais cronicas em estagio avançado(graus 3,4 e 5)</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                            <input type="checkbox" id="cdoencas_cromossomicas" name="cdoencas_cromossomicas" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="cdoencas_cromossomicas">Portador de doenças cromossómicas ou estado de fragilidade imunológica</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                            <input type="checkbox" id="cdiabetes" name="cdiabetes" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="cdiabetes">Diabetes</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                            <input type="checkbox" id="cimunossupressão" name="cimunossupressão" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="cimunossupressão">Imunossupressão</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                            <input type="checkbox" id="cdoencas_cardiacas" name="cdoencas_cardiacas" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="cdoencas_cardiacas">Doenças cardíacas cronicas</label>
                                                        </div>
                                                        <div class="custom-control custom-checkbox d-inline-block mr-2">
                                                            <input type="checkbox" id="cgestante" name="cgestante" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="cgestante">Gestante</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Estado do teste</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="esolicitado" name="eteste" class="custom-control-input" value="solicitado">
                                                            <label class="custom-control-label" for="esolicitado">Solicitado</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="ecoleta" name="eteste" class="custom-control-input" value="coletado">
                                                            <label class="custom-control-label" for="ecoleta">Coletado</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="econcluido" name="eteste" class="custom-control-input" value="concluido">
                                                            <label class="custom-control-label" for="econcluido">Concluído</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="enao_solicitado" name="eteste" class="custom-control-input" value="exame nao coletado">
                                                            <label class="custom-control-label" for="enao_solicitado">Exame não Solicitado</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Data da Coleta do Teste</label>
                                                    <input type="text" name="data_coleta_teste" class="form-control" OnKeyPress="formatar('##/##/####', this)">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Tipo de Teste</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="tpcr" name="tipo_teste" class="custom-control-input" value="rt-pcr">
                                                            <label class="custom-control-label" for="tpcr">RT-PCR</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="tteste_igg" name="tipo_teste" class="custom-control-input" value="teste rapido - anticorpo">
                                                            <label class="custom-control-label" for="tteste_igg">Teste rápido - anticorpo</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="tteste_igm" name="tipo_teste" class="custom-control-input" value="teste rapido - antigeno">
                                                            <label class="custom-control-label" for="tteste_igm">Teste rápido - antigeno</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="tenzima" name="tipo_teste" class="custom-control-input" value="enzimaimunoensaio - elisa">
                                                            <label class="custom-control-label" for="tenzima">Enzimaimunoensaio - ELISA</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="teletroquimioluminescencia" name="tipo_teste" class="custom-control-input" value="imunoensaio por eletroquimioluminescencia - eclia">
                                                            <label class="custom-control-label" for="teletroquimioluminescencia">Imunoensaio por Eletroquimioluminescencia - ECLIA</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="">Resultado do teste</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block">
                                                            <input type="radio" id="rnegativo" name="resultado" class="custom-control-input" value="negativo">
                                                            <label class="custom-control-label" for="rnegativo">Negativo</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block ml-2">
                                                            <input type="radio" id="rpositivo" name="resultado" class="custom-control-input" value="positivo">
                                                            <label class="custom-control-label" for="rpositivo">Positivo</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Classificação Final</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="cfdescartado" name="classificacao_final" class="custom-control-input" value="descartado">
                                                            <label class="custom-control-label" for="cfdescartado">Descartado</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="cfconfirmadoce" name="classificacao_final" class="custom-control-input" value="confirmado clinico-epidemiologico">
                                                            <label class="custom-control-label" for="cfconfirmadoce">Confirmado Clinico-Epidemiologico</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="cfconfirmado_laboratorial" name="classificacao_final" class="custom-control-input" value="confirmado laboratorial">
                                                            <label class="custom-control-label" for="cfconfirmado_laboratorial">Confirmado Laboratorial</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="cfsindromenr" name="classificacao_final" class="custom-control-input" value="sindrome gripal nao especificada">
                                                            <label class="custom-control-label" for="cfsindromenr">Sindrome Gripal não Especificada</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="cfconfirmadoci" name="classificacao_final" class="custom-control-input" value="confirmado clinico imagem">
                                                            <label class="custom-control-label" for="cfconfirmadoci">Confirmado Clinico Imagem</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="cfconfirmadocc" name="classificacao_final" class="custom-control-input" value="confirmado por criterio clinico">
                                                            <label class="custom-control-label" for="cfconfirmadocc">Confirmado por Criterio Clinico</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Evolução do Caso</label>
                                                    <div class="input-group">
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="eccancelado" name="evolucao_caso" class="custom-control-input" value="cancelado">
                                                            <label class="custom-control-label" for="eccancelado">Cancelado</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="ecignorado" name="evolucao_caso" class="custom-control-input" value="ignorado">
                                                            <label class="custom-control-label" for="ecignorado">Ignorado</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="ectratamento_domiciliar" name="evolucao_caso" class="custom-control-input" value="em tratamento domiciliar">
                                                            <label class="custom-control-label" for="ectratamento_domiciliar">Em tratamento domiciliar</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="ecinternado_uti" name="evolucao_caso" class="custom-control-input" value="internado em uti">
                                                            <label class="custom-control-label" for="ecinternado_uti">Internado em UTI</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="ecinternado" name="evolucao_caso" class="custom-control-input" value="internado">
                                                            <label class="custom-control-label" for="ecinternado">Internado</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="ecobito" name="evolucao_caso" class="custom-control-input" value="obito">
                                                            <label class="custom-control-label" for="ecobito">Obito</label>
                                                        </div>
                                                        <div class="custom-control custom-radio d-inline-block mr-2">
                                                            <input type="radio" id="eccura" name="evolucao_caso" class="custom-control-input" value="cura">
                                                            <label class="custom-control-label" for="eccura">Cura</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Data de Encerramento</label>
                                                    <input type="text" name="data_encerramento" class="form-control" OnKeyPress="formatar('##/##/####', this)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" align="center">
                                                <button type="submit" class="btn btn-success">Salvar</button></div>
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
        function autocomplet() {
            var min_length = 0; // min caracters to display the autocomplete
            var keyword = $('#nome').val().toUpperCase();
            if (keyword.length >= min_length) {
                $.ajax({
                    url: 'ajax_refresh.php',
                    type: 'POST',
                    data: {
                        keyword: keyword
                    },
                    success: function(data) {
                        $('#lista_nomes').show();
                        $('#lista_nomes').html(data);
                    }
                });
            } else {
                $('#lista_nomes').hide();
            }
        }

        // set_item : this function will be executed when we select an item
        function set_item(item, paciente_id, dt_nasc, sexo, telefone, celular, cep, endereco, numero, bairro, cidade, uf, cpf, nome_mae) {
            // change input value
            $('#nome').val(item);
            $('#nome_mae').val(nome_mae);
            $('#cpf').val(cpf);
            $('#pessoa_id').val(paciente_id);
            $('#dt_nasc').val(dt_nasc);
            if (sexo == 'masculino') {
                document.getElementById("psexo1").checked = true;
            } else {
                document.getElementById("psexo2").checked = true;
            }
            $('#telefone').val(telefone);
            $('#celular').val(celular);
            $('#cep').val(cep);
            $('#rua').val(endereco);
            $('#numero').val(numero);
            $('#bairro').val(bairro);
            $('#cidade').val(cidade);
            $('#uf').val(uf);
            // hide proposition list
            $('#lista_nomes').hide();
        }

        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }

        $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#rua").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#uf").val("");
                $("#ibge").val("");
            }

            //Quando o campo cep perde o foco.
            $("#cep").blur(function() {

                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;

                    //Valida o formato do CEP.
                    if (validacep.test(cep)) {

                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("#rua").val("...");
                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#uf").val("...");
                        $("#ibge").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                $("#rua").val(dados.logradouro);
                                $("#bairro").val(dados.bairro);
                                $("#cidade").val(dados.localidade);
                                $("#uf").val(dados.uf);
                                $("#ibge").val(dados.ibge);
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                alert("CEP não encontrado.");
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            });
        });
    </script>
</body>

</html>