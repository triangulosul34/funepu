<?php

error_reporting(0);
include('verifica.php');
//include('funcoes.php');

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

$convenio = "";
$username    = "";
$password    = "";
$num_carteira_convenio = "";
$num_conselho    = "";
$codigo     = "";
$nome        = "";
$sexo        = "";
$cpf        = "";
$dtnasc    = "";
$endereco    = "";
$numero    = "";
$complemento = "";
$bairro    = "";
$cep        = "";
$cidade    = "";
$uf         = "";
$nome_mae    = "";
$nome_pai = "";
$identidade = "";
$org_exped = "";
$raca_cor    = "";
$est_civil = "";
$telefone    = "";
$telefone2 = "";
$celular    = "";
$celular2    = "";
$imagem    = "";
$facebook    = "";
$twitter    = "";
$whatsup    = "";
$email    = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $tipo =     'C';
    $modo =  $_GET['modo'];
    $tipo_pessoa = 'NA';
    if ($tipo == 'A') {
        $tipo_pessoa = 'Administrativo';
        $menu_grupo = '1';
        $menu_sgrupo = '4';
    }
    if ($tipo == 'C') {
        $tipo_pessoa = 'Cliente';
        $menu_grupo = '1';
        $menu_sgrupo = '1';
    }
    if ($tipo == 'M') {
        $tipo_pessoa = 'Medico Laudador';
        $menu_grupo = '1';
        $menu_sgrupo = '2';
    }
    if ($tipo == 'S') {
        $tipo_pessoa = 'Solicitante';
        $menu_grupo = '1';
        $menu_sgrupo = '3';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo     = $_POST['codigo'];
    $nome        = $_POST['nome'];
    $nome_mae    = $_POST['nome_mae'];
    $nome_pai    = $_POST['nome_pai'];
    $sexo        = $_POST['sexo'];
    $cpf        = $_POST['cpf'];
    $dtnasc        = $_POST['dtnasc'];
    $raca_cor    = $_POST['raca_cor'];
    $endereco    = $_POST['endereco'];
    $numero        = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro        = $_POST['bairro'];
    $cep        = $_POST['cep'];
    $cidade        = $_POST['cidade'];
    $uf         = $_POST['uf'];
    $telefone    = $_POST['telefone'];
    $celular    = $_POST['celular'];
    $depto        = $_POST['setor'];
    $ramal        = $_POST['ramal'];
    $tratamento = $_POST['tratamento'];
    $profissao    = $_POST['profissao'];
    $username    = trim($_POST['username']);
    $senha        = $_POST['password'];
    $imagem        = $_FILES["fileToUpload"]["name"];
    $perfil        = $_POST['perfil'];
    $status        = $_POST['status'];
    $facebook    = $_POST['facebook'];
    $twitter    = $_POST['twitter'];
    $email        = $_POST['email'];
    $nome_mae    = $_POST['nome_mae'];
    $nome_pai      = $_POST['nome_pai'];
    $conselho    = $_POST['conselho'];
    $num_conselho = $_POST['num_conselho'];
    $especialidade =    $_POST['especialidade'];
    $identidade    = $_POST['identidade'];
    $org_exped     = $_POST['org_exped'];
    $dtn        = inverteData($_POST['dtnasc']);
    $whatsup    = $_POST['whatsup'];
    $tipo         = $_POST['tipo'];
    $datacad    = date('Y-m-d');
    $lotacao      = $_POST['lotacao'];
    $grupo_user = $_POST['grupo_user'];
    $sgrupo_user = $_POST['sgrupo_user'];
    $convenio   = $_POST['convenio'];
    $nome_social   = $_POST['nome_social'];
    $p_username = $username;
    $p_password = $senha;
    $num_carteira_convenio = $_POST['num_carteira_convenio'];
    $tipo_pessoa = 'NA';
    if ($tipo == 'A') {
        $tipo_pessoa = 'Administrativo';
    }
    if ($tipo == 'C') {
        $tipo_pessoa = 'Cliente';
    }
    if ($tipo == 'M') {
        $tipo_pessoa = 'Medico Laudador';
    }
    if ($tipo == 'S') {
        $tipo_pessoa = 'Solicitante';
    }
    $desc_sexo = "";
    if ($sexo == 'F') {
        $desc_sexo = "Feminino";
    }
    if ($sexo == 'M') {
        $desc_sexo = "Masculino";
    }
    if ($convenio == "") {
        $convenio = '0';
    }
    $erro = "";
    if ($sexo == "") {
        $erro = "Sexo - Campo Obrigatorio";
    }
    if ($nome == "") {
        $erro = "Nome - Campo Obrigatorio";
    }
    // if ($dtn != "") {
    //     if (!(ValidaData($dtnasc))) {
    //         $erro = "Data de Nascimento - Campo Obrigatorio";
    //     }
    // }
    if ($tipo != "C") {
        if ($username == "") {
            $erro = "Usuario - Campo Obrigatorio";
        }
        if ($senha == "") {
            $erro = "Senha - Campo Obrigatorio";
        }
    }
    // if ($cpf != "") {
    //     if (!(validaCPF($cpf))) {
    //         $erro = "CPF - Valor Invalido";
    //     }
    // }

    if ($username != '') {
        include('conexao.php');
        $stmtUser = "select count(*) as total from pessoas where username = '$username'";
        $sthUser = pg_query($stmtUser) or die($stmtUser);
        $rowUser = pg_fetch_object($sthUser);
        $usuario_existe = $rowUser->total;

        if ($usuario_existe > 0) {
            $erro = "Nome de usuário ja cadastrado.";
        }
    }


    if ($especialidade == "") {
        $especialidade = '0';
    }
    if ($lotacao == "") {
        $lotacao = '0';
    }
    if ($grupo_user == "") {
        $grupo_user = '0';
    }
    if ($sgrupo_user == "") {
        $sgrupo_user = '0';
    }
    if ($erro == "") {
        include('conexao.php');
        $stmt = "insert into pessoas (nome, tipo_pessoa, nome_mae, nome_pai, sexo, cpf, dt_nasc, endereco, numero, complemento, bairro, cep, cidade, estado, telefone, telefone2, celular, celular2, imagem, facebook, twitter,
				email, whatsup, conselho_regional, num_conselho_reg, especialidade_id, convenio_padrao, num_carteira_convenio, perfil, lotacao, grupo_user_id, sgrupo_user_id, usuario_cad, data_cad, username, password, situacao,nome_social) 
				values ('$nome', '$tipo_pessoa', '$nome_mae', '$nome_pai', '$sexo', '$cpf',";
        if ($dtn == "") {
            $stmt = $stmt . 'null,';
        } else {
            $stmt = $stmt . "'$dtn',";
        }
        $stmt = $stmt . " '$endereco', '$numero', '$complemento', '$bairro', '$cep', '$cidade', '$uf', '$telefone', '$telefone2', '$celular', '$celular2', '$imagem', '$facebook', '$twitter',
				'$email', '$whatsup', '$conselho', '$num_conselho', '$especialidade', '$convenio', '$num_carteira_convenio', '$perfil', '$lotacao', '$grupo_user', '$sgrupo_user', '$usuario', '$datacad', '$p_username', md5('$p_password'), '0','$nome_social')";
        $sth = pg_query($stmt) or die($stmt);
        if ($imagem != "") {
            $target_dir = "C:/inetpub/wwwroot/dcenter/html/imagens/clientes";
            $target_file = $target_dir . '/' . basename($_FILES["fileToUpload"]["name"]);
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo  '<script type="text/javascript">alert("Arquivo Enviado!");</script>';
            } else {
                $erro = "Arquivo nao Enviado!";
            }
        }

        // if ($modo != "popup") {
        //     if ($tipo_pessoa == 'Cliente') {
        //         header("location: clientes.php");
        //     }
        //     if ($tipo_pessoa == 'Medico Laudador') {
        //         header("location: medicos.php");
        //     }
        //     if ($tipo_pessoa == 'Solicitante') {
        //         header("location: solicitantes.php");
        //     }
        //     if ($tipo_pessoa == 'Administrativo') {
        //         header("location: colaboradores.php");
        //     }
        // } else {
        //header("location: poppac.php");
        // }

        $sql = "select * from pessoas where pessoa_id = (select max(pessoa_id) from pessoas where data_cad is not null)";
        $result = pg_query($sql) or die($sql);
        $row = pg_fetch_object($result);
        $dt_nasc = $row->dt_nasc;
        $date = new DateTime($dt_nasc); // data de nascimento
        $interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida

        $idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
?>
        <script>
            window.opener.document.pedido.prontuario.value = '<?= $row->pessoa_id; ?>';
            window.opener.document.pedido.nome.value = '<?= $row->nome; ?>';
            window.opener.document.pedido.sexo.value = '<?= $row->sexo; ?>';
            window.opener.document.pedido.dt_nascimento.value = '<?= inverteData($row->dt_nasc); ?>';
            window.opener.document.pedido.idade.value = '<?= $idade; ?>';
            window.opener.document.pedido.endereco.value = '<?= $row->endereco; ?>';
            window.opener.document.pedido.end_num.value = ' <?= $row->numero; ?>';
            window.opener.document.pedido.end_comp.value = '<?= $row->complemento; ?>';
            window.opener.document.pedido.end_bairro.value = '<?= $row->bairro; ?>';
            window.opener.document.pedido.end_cidade.value = '<?= $row->cidade; ?>';
            window.opener.document.pedido.end_uf.value = '<?= $row->estado; ?>';
            window.opener.document.pedido.telefone.value = '<?= $row->telefone; ?>';
            window.opener.document.pedido.celular.value = '<?= $row->celular; ?>';
            window.opener.document.pedido.cpf.value = '<?= $row->cpf; ?>';
            window.opener.document.pedido.cns.value = '<?= $row->num_carteira_convenio; ?>';
            window.opener.document.pedido.nome_mae.value = '<?= $row->nome_mae; ?>';
            window.opener.document.pedido.rg.value = '<?= $row->identidade; ?>';
            window.opener.document.pedido.org_expeditor.value = '<?= inverteData($row->org_expeditor); ?>';
            var cep = '<?= $row->cep; ?>';
            if (cep.length == 9) {
                window.opener.document.pedido.end_cep.value = '<?= $row->cep; ?>';
            } else {
                window.opener.document.pedido.end_cep.value = cep.substr(0, 5) + "-" + cep.substr(5, 3);
            }
            // window.opener.document.pedido.nomeMae.value = teste;
            window.close();
        </script>
<?php
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
    <?php if ($tipo == 'C') { ?>
        <title>FUNEPU | Altera Paciente</title>
    <?php } else if ($tipo == 'M') { ?>
        <title>FUNEPU | Altera Medico</title>
    <?php } else if ($tipo == 'A') { ?>
        <title>FUNEPU | Altera Colaborador</title>
    <?php } ?>
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
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
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
    <div class="wrapper">
        <?php include('menu.php'); ?>
        <?php include('header.php'); ?>
        <div class="main-panel">
            <div class="main-content">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h4 class="card-title">
                                                        <p style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                            » </p><?php if ($tipo == 'C') { ?>Alterar Paciente
                                                        <?php } else if ($tipo == 'M') { ?>Alterar
                                                        Medico<?php } else if ($tipo == 'A') { ?>Alterar
                                                        Colaborador<?php } ?>
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
                                                    <?php if ($tipo == 'C') { ?>
                                                        <li><a href="index.php">Home</a></li>
                                                        <li><a href="clientes.php">Pacientes</a></li>
                                                        <li class="active">Alterar Paciente</li>
                                                    <?php } else if ($tipo == 'M') { ?>
                                                        <li><a href="index.php">Home</a></li>
                                                        <li><a href="medicos.php">Medicos</a></li>
                                                        <li class="active">Alterar Medicos</li>
                                                    <?php } else if ($tipo == 'A') { ?>
                                                        <li><a href="index.php">Home</a></li>
                                                        <li><a href="medicos.php">Colaboradores</a></li>
                                                        <li class="active">Alterar Colaborador</li>
                                                    <?php } ?>
                                                </ol>
                                            </div>
                                        </div> -->
                                    </div>
                                    <?php
                                    if ($erro != "") {
                                        echo '<div class="row">
		        <div class="col-sm-12">
								<strong>Erro:!</strong><br><li>' . $erro . '</li>
				</div>		
		  </div>';
                                    } ?>
                                </div>
                                <!-- CORPO DA PAGINA -->
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="#" id="form" method="post" class="form">
                                            <div class="form-body">
                                                <h4 class="form-section"><i class="ft-user"></i> Dados do Paciente</h4>
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Nome</label>
                                                            <input type="hidden" class="form-control square" id="codigo" name="codigo" value="<?php echo $codigo; ?>">
                                                            <input type="text" class="form-control square" id="nome" name="nome" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Nome Social</label>
                                                            <input type="text" class="form-control square" id="nome_social" name="nome_social" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Tipo</label>
                                                            <input type="hidden" class="form-control square" id="tipo" name="tipo" value="<?php echo $tipo; ?>" onkeyup="maiuscula(this)" readonly>
                                                            <input type="text" class="form-control square" id="tipo_pessoa" name="tipo_pessoa" value="<?php echo $tipo_pessoa; ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Nome da Mae</label>
                                                            <input type="text" class="form-control square" id="nome_mae" name="nome_mae" value="<?php echo $nome_mae; ?>" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Nome da Pai</label>
                                                            <input type="text" class="form-control square" id="nome_pai" name="nome_pai" value="<?php echo $nome_pai; ?>" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Dt.Nasc</label>
                                                            <input type="text" placeholder="99/99/9999" value="<?php echo $dtnasc; ?>" OnKeyPress="formatar('##/##/####', this)" class="form-control square" id="dtnasc" maxlength="10" name="dtnasc">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label> CPF</label>
                                                            <input type="text" placeholder="99999999999" value="<?php echo $cpf; ?>" onkeypress='return SomenteNumero(event)' maxlength="11" class="form-control square" id="cpf" name="cpf">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Sexo</label>
                                                            <select name="sexo" id="sexo" class="form-control square">
                                                                <option></option>
                                                                <option value="M" <?php if ($sexo == "M")   echo "selected"; ?>>
                                                                    Masculino</option>
                                                                <option value="F" <?php if ($sexo == "F")    echo "selected"; ?>>
                                                                    Feminino</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Identidade</label>
                                                            <input type="text" class="form-control square" id="identidade" name="identidade" value="<?php echo $identidade; ?>" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="control-label">Expedição</label>
                                                            <input type="text" class="form-control square" id="org_exped" OnKeyPress="formatar('##/##/####', this)" name="org_exped" value="<?php echo $org_exped; ?>" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Raca/Cor</label>
                                                            <select name="raca_cor" id="raca_cor" class="form-control  square">
                                                                <option></option>
                                                                <option value="Branca" <?php if ($raca_cor == "Branca")   echo "selected"; ?>>
                                                                    Branca</option>
                                                                <option value="Preta" <?php if ($raca_cor == "Preta")    echo "selected"; ?>>
                                                                    Preta</option>
                                                                <option value="Parda" <?php if ($raca_cor == "Parda")    echo "selected"; ?>>
                                                                    Parda</option>
                                                                <option value="Amarela" <?php if ($raca_cor == "Amarela")  echo "selected"; ?>>
                                                                    Amarela</option>
                                                                <option value="Indigena" <?php if ($raca_cor == "Indigena") echo "selected"; ?>>
                                                                    Indigena</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4 class="form-section"><i class="fas fa-phone-volume"></i> Contato
                                                </h4>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group row">
                                                            <label class="col-md-3 label-control" style="text-align: right;">Telefone</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control square" type="text" name="telefone" id="telefone" OnKeyPress="formatar('##-#########', this)" value="<?php echo $telefone; ?>" maxlength="11">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group row">
                                                            <label class="col-md-3 label-control" style="text-align: right;">Celular</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control square" type="text" name="celular" id="celular" OnKeyPress="formatar('##-#########', this)" value="<?php echo $celular; ?>" maxlength="12">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <label class="col-md-3 label-control" style="text-align: right;">Email</label>
                                                            <div class="col-md-9">
                                                                <input class="form-control square" type="text" name="email" id="email" value="<?php echo $email; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h4 class="form-section"><i class="fas fa-location-arrow"></i> Endereço
                                                </h4>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>CEP</label>
                                                            <input class="form-control square" placeholder="99999-999" type="text" name="cep" maxlength="9" id="cep" value="<?php echo $cep; ?>" OnKeyPress="formatar('#####-###', this)" onblur="pesquisacep(this.value);">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Endereço</label>
                                                            <input class="form-control square" type="text" name="endereco" id="endereco" value="<?php echo $endereco; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Numero</label>
                                                            <input class="form-control square" type="text" name="numero" id="numero" value="<?php echo $numero; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Complemento</label>
                                                            <input class="form-control square" type="text" name="complemento" id="complemento" value="<?php echo $complemento; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Bairro</label>
                                                            <input class="form-control square" type="text" name="bairro" id="bairro" value="<?php echo $bairro; ?>" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <div class="form-group">
                                                            <label>Cidade</label>
                                                            <input class="form-control square" type="text" name="cidade" id="cidade" value="<?php echo $cidade; ?>" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>UF</label>
                                                            <input class="form-control square" type="text" name="uf" id="uf" value="<?php echo $uf; ?>" onkeyup="maiuscula(this)" maxlength="2">
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if ($tipo == 'M') { ?>
                                                    <h4 class="form-section"><i class="fas fa-id-card-alt"></i> Dados
                                                        Medicos</h4>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class='form-group'>
                                                                <label class="control-label">Conselho Regional</label>
                                                                <select name="conselho" id="conselho" class="form-control">
                                                                    <option></option>
                                                                    <option value='CRA'>CRA</option>
                                                                    <option value='CRBIO'>CRBIO</option>
                                                                    <option value='CRBM'>CRBM</option>
                                                                    <option value='CREFITO'>CREFITO</option>
                                                                    <option value='CRM'>CRM</option>
                                                                    <option value='CRO'>CRO</option>
                                                                    <option value='COREN'>COREN</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    Numero Conselho
                                                                </label>
                                                                <input class="form-control" type="text" name="num_conselho" value="<?php echo $num_conselho; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class='form-group'>
                                                                <label class="control-label">Especialidade</label>
                                                                <select name="especialidade" id="especialidade" class="form-control">
                                                                    <option></option>
                                                                    <?php
                                                                    include('conexao.php');
                                                                    $stmt = "SELECT * FROM especialidade order by descricao";
                                                                    $sth = pg_query($stmt) or die($stmt);
                                                                    while ($row = pg_fetch_object($sth)) {
                                                                        echo "<option value=\"" . $row->especialidade_id . "\" >" . $row->descricao . "</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }
                                                if ($tipo != 'C') { ?>
                                                    <h4 class="form-section"><i class="fas fa-sign-in-alt"></i> Controle de
                                                        Usuario</h4>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class='form-group'>
                                                                <label class="control-label">Perfil de Usuario</label>
                                                                <select name="perfil" id="perfil" class="form-control">
                                                                    <option></option>
                                                                    <option value='01'>Usuário</option>
                                                                    <option value='02'>Gerente</option>

                                                                    <option value='07'>Laudador</option>

                                                                    <option value='03'>Médico</option>
                                                                    <option value='08'>Enfermagem</option>
                                                                    <option value='05'>Tecnico</option>
                                                                    <option value='04'>Administrativo</option>
                                                                    <?php if ($perfil == '06') { ?>
                                                                        <option value='06'>Super Usuário</option>
                                                                    <?php } ?>
                                                                    <option value='09'>Monitoramento</option>

                                                                    <option value='10'>Biomedico</option>
                                                                    <option value='11'>Técnico em Análises Clinicas</option>

                                                                    <option value='12'>Adm. Laboratorio</option>
                                                                    <option value='13'>Sus Fácil</option>
                                                                    <option value='14'>Médico do Trabalho</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class='form-group'>
                                                                <label class="control-label">Grupo de Usuários</label>
                                                                <select name="grupo_user" id="grupo_user" class="form-control">
                                                                    <option></option>
                                                                    <?php
                                                                    include('conexao.php');
                                                                    $stmt = "SELECT * FROM usuarios_grupos order by descricao";
                                                                    $sth = pg_query($stmt) or die($stmt);
                                                                    while ($row = pg_fetch_object($sth)) {
                                                                        echo "<option value=\"" . $row->grupo_user_id . "\" >" . $row->descricao . "</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class='form-group'>
                                                                <label class="control-label">Nome de Usuario</label>
                                                                <input class="form-control" onkeyup="procurauser(this)" type="text" name="username" value="<?php echo $username; ?>">
                                                                <div id="user_exists" style="color:#FF0000; font-weight: bold;" class="col-md-12 margin-top-5"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class='form-group'>
                                                                <label class="control-label">Senha de Usuário</label>
                                                                <input class="form-control" type="password" name="password" value="<?php echo $password; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <div class="row">
                                                    <div align="center" class="col-md-12 margin-bottom-30">
                                                        <button type="submit" class="btn btn-wide btn-primary">Gravar</button>
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
        </div>
    </div>
    <?php include('footer.php'); ?>
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
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script>
        (function(document, window, $) {
            'use strict';

            var Site = window.Site;
            $(document).ready(function() {
                Site.run();
            });
        })(document, window, jQuery);



        function procurauser(valor) {
            username = valor.value;

            var total_usuario;
            if (username != '') {

                $.get('returnUserExists.php?username=' + username, function(dataReturn) {
                    $("#user_exists").html('');
                    if (dataReturn > 0) {
                        $("#user_exists").html('Nome de usuário ja cadastrado.');
                    }
                });

            } else {
                $("#user_exists").html('');
            }
        }

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

        function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('endereco').value = ("");
            document.getElementById('bairro').value = ("");
            document.getElementById('cidade').value = ("");
            document.getElementById('uf').value = ("");
        }

        function meu_callback(conteudo) {
            if (!("erro" in conteudo)) {
                //Atualiza os campos com os valores.
                document.getElementById('endereco').value = (conteudo.logradouro);
                document.getElementById('bairro').value = (conteudo.bairro);
                document.getElementById('cidade').value = (conteudo.localidade);
                document.getElementById('uf').value = (conteudo.uf);
            } //end if.
            else {
                //CEP não Encontrado.
                limpa_formulário_cep();
                alert("CEP não encontrado.");
            }
        }

        function pesquisacep(valor) {

            //Nova variável "cep" somente com dígitos.
            var cep = valor.replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if (validacep.test(cep)) {

                    //Preenche os campos com "..." enquanto consulta webservice.
                    document.getElementById('endereco').value = "...";
                    document.getElementById('bairro').value = "...";
                    document.getElementById('cidade').value = "...";
                    document.getElementById('uf').value = "...";

                    //Cria um elemento javascript.
                    var script = document.createElement('script');

                    //Sincroniza com o callback.
                    script.src = '//viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

                    //Insere script no documento e carrega o conteúdo.
                    document.body.appendChild(script);

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
        };
    </script>
</body>

</html>