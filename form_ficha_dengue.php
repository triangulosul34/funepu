<?php

include 'verifica.php';
include 'conexao.php';
include 'tsul_ssl.php';

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$dengue_id = $_GET['id'];

	if ($dengue_id) {
		$sql = "SELECT * FROM ficha_dengue a inner join pessoas b on a.pessoa_id = b.pessoa_id WHERE ficha_dengue_id = $dengue_id";
		$result = pg_query($sql) or die($sql);
		$rowget = pg_fetch_object($result);
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$dengue_id = $_POST['ficha_dengue_id'];

	if ($dengue_id == '') {
		foreach ($_POST as $key => $values) {
			if ($values != '') {
				$value = $value . ",'$values'";
				$columns = $columns . ",$key";
			}
		}

		$sql = 'insert into ficha_dengue(' . substr($columns, 1) . ', data_form) values(' . substr($value, 1) . ', \'' . date('Y-m-d') . '\')';
		$result = pg_query($sql) or die($sql);

		$sql = 'SELECT max(ficha_dengue_id) as id FROM ficha_dengue';
		$result = pg_query($sql) or die($sql);
		$row = pg_fetch_object($result);

		header('location: pdf_form_ficha_dengue.php?id=' . $row->id);
	} else {
		foreach ($_POST as $key => $value) {
			if ($value != '') {
				$set = $set . ",{$key} = '{$value}'";
			}
		}

		$sql = 'update ficha_dengue set ' . substr($set, 1) . ' where ficha_dengue_id = ' . $dengue_id;
		$result = pg_query($sql) or die($sql);

		header('location: pdf_form_ficha_dengue.php?id=' . $dengue_id);
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
  <title>FUNEPU | Ficha Deungue</title>
  <link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
  <link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
  <link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
  <link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
  <link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
    rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
  <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
  <link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
  <script defer src="/your-path-to-fontawesome/js/all.js"></script>
  <!--load all styles -->
</head>
<style>
  .hr {
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

<body class="  pace-done" cz-shortcut-listen="true">
  <div class="pace  pace-inactive">
    <div class="pace-progress" data-progress-text="100%" data-progress="99"
      style="transform: translate3d(100%, 0px, 0px);">
      <div class="pace-progress-inner"></div>
    </div>
    <div class="pace-activity"></div>
  </div>

  <div class="wrapper">
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
                            <p style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                              » </p>FORMULARIO DENGUE E CHIKUNGUNYA
                          </h4>
                        </div>
                        <div class="col-12">
                          <hr class="hr">
                        </div>
                      </div>

                    </div>
                    <div class="col-6">
                      <div class="float-right">
                        <ol class="breadcrumb">
                          <li><a href="../index.html">Home</a></li>
                          <li class="active">Ficha Dengue</li>
                        </ol>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- CORPO DA PAGINA -->
                <div class="card-content">
                  <div class="col-12">
                    <form action="#" method="POST">
                      <input type="hidden" name="ficha_dengue_id"
                        value="<?= $dengue_id; ?>">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group"><label for="">Agravo/Doença</label>
                            <div class="input-group">
                              <div class="custom-control custom-radio d-inline-block">
                                <input type="radio" id="agdo1" name="agdo" <?php if ($rowget->agdo == '1') {
	echo 'checked';
} ?>
                                class="custom-control-input" value="1">
                                <label class="custom-control-label" for="agdo1">1 - DENGUE</label>
                              </div>
                              <div class="custom-control custom-radio d-inline-block ml-2">
                                <input type="radio" id="agdo2" name="agdo" <?php if ($rowget->agdo == '2') {
	echo 'checked';
} ?>
                                class="custom-control-input" value="2">
                                <label class="custom-control-label" for="agdo2">2 - CHIKUNGUNYA</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group"><label for="">CID</label>
                            <div class="input-group">
                              <div class="custom-control custom-radio d-inline-block">
                                <input type="radio" id="cid1" name="cid" <?php if ($rowget->cid == '1') {
	echo 'checked';
} ?>
                                class="custom-control-input" value="1">
                                <label class="custom-control-label" for="cid1">A90</label>
                              </div>
                              <div class="custom-control custom-radio d-inline-block ml-2">
                                <input type="radio" id="cid2" name="cid" <?php if ($rowget->cid == '2') {
	echo 'checked';
} ?>
                                class="custom-control-input" value="2">
                                <label class="custom-control-label" for="cid2">A92</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Data da Notificação</label>
                            <input type="text" name="data_notificacao" id="data_notificacao" class="form-control"
                              value="<?= $rowget->data_notificacao; ?>"
                              OnKeyPress="formatar('##/##/####', this)">
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group">
                            <label for="">UF</label>
                            <input type="text" name="estado" id="uf_notificacao" class="form-control" value="MG"
                              maxlength="2">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Municipio de Notificação</label>
                          <input type="text" name="municipio" id="municipio" class="form-control" value="UBERABA">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Codigo(IBGE)</label>
                          <input type="text" name="ibge" id="ibge" class="form-control" value="317010" maxlength="6">
                        </div>
                        <div class="col-md-2">
                          <label for="">Unidade de Saude</label>
                          <input type="text" name="unidade_saude" id="unidade_saude" class="form-control"
                            value="UPA DO MIRANTE">
                        </div>
                        <div class="col-md-2">
                          <label for="">Codigo</label>
                          <input type="text" name="codigo_unidade" id="codigo_unidade" class="form-control"
                            value="7093284" maxlength="7">
                        </div>
                        <div class="col-md-4">
                          <label for="">Data dos Primeiros Sintomas</label>
                          <input type="text" name="data_primeiro_sintomas" id="data_primeiro_sintomas"
                            class="form-control" OnKeyPress="formatar('##/##/####', this)"
                            value="<?= $rowget->data_primeiro_sintomas; ?>">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <input type="hidden" name="pessoa_id" id="pessoa_id"
                            value="<?= $rowget->pessoa_id; ?>" />
                          <label for="">Nome do Paciente</label>
                          <input type="text" id="nome" onkeyup="autocomplet()" class="form-control"
                            value="<?= ts_decodifica($rowget->nome); ?>">
                          <ul id="lista_nomes"></ul>
                        </div>
                        <div class="col-md-3">
                          <label for="">Data de Nascimento</label>
                          <input type="text" id="dt_nasc" class="form-control" OnKeyPress="formatar('##/##/####', this)"
                            value="<?= $rowget->dt_nasc; ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="">Gestante</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="gestante1" name="gestante" <?php if ($rowget->gestante == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="gestante1">1º trimestre</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="gestante2" name="gestante" <?php if ($rowget->gestante == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="gestante2">2º trimestre</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="gestante3" name="gestante" <?php if ($rowget->gestante == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="gestante3">3º trimestre</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="gestante4" name="gestante" <?php if ($rowget->gestante == '4') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="4">
                              <label class="custom-control-label" for="gestante4">idade gestacional ignorada</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="gestante5" name="gestante" <?php if ($rowget->gestante == '5') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="5">
                              <label class="custom-control-label" for="gestante5">nao</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="gestante6" name="gestante" <?php if ($rowget->gestante == '6') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="6">
                              <label class="custom-control-label" for="gestante6">nao se aplica</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="gestante7" name="gestante" <?php if ($rowget->gestante == '7') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="7">
                              <label class="custom-control-label" for="gestante7">ignorado</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Raca/Cor</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="raca1" name="raca" <?php if ($rowget->raca == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="raca1">Branca</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="raca2" name="raca" <?php if ($rowget->raca == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="raca2">Preta</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="raca3" name="raca" <?php if ($rowget->raca == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="raca3">Amarela</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="raca4" name="raca" <?php if ($rowget->raca == '4') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="4">
                              <label class="custom-control-label" for="raca4">Parda</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="raca5" name="raca" <?php if ($rowget->raca == '5') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="5">
                              <label class="custom-control-label" for="raca5">Indigena</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="raca6" name="raca" <?php if ($rowget->raca == '6') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="6">
                              <label class="custom-control-label" for="raca6">Ignorado</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-10">
                          <label for="">Escolaridade</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="escolaridade0" name="escolaridade" <?php if ($rowget->escolaridade == '0') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="0">
                              <label class="custom-control-label" for="escolaridade0">Analfabeto</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade1" name="escolaridade" <?php if ($rowget->escolaridade == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="escolaridade1">1ª a 4ª série incompleta do EF
                                (antigo primário ou 1º grau)</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade2" name="escolaridade" <?php if ($rowget->escolaridade == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="escolaridade2">4ª série completa do EF (antigo
                                primário ou 1º grau)</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade3" name="escolaridade" <?php if ($rowget->escolaridade == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="escolaridade3">5ª à 8ª série incompleta do EF
                                (antigo ginásio ou 1º grau)</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade4" name="escolaridade" <?php if ($rowget->escolaridade == '4') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="4">
                              <label class="custom-control-label" for="escolaridade4">Ensino fundamental completo
                                (antigo ginásio ou 1º grau) </label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade5" name="escolaridade" <?php if ($rowget->escolaridade == '5') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="5">
                              <label class="custom-control-label" for="escolaridade5">Ensino médio incompleto (antigo
                                colegial ou 2º grau )</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade6" name="escolaridade" <?php if ($rowget->escolaridade == '6') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="6">
                              <label class="custom-control-label" for="escolaridade6">Ensino médio completo (antigo
                                colegial ou 2º grau )</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade7" name="escolaridade" <?php if ($rowget->escolaridade == '7') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="7">
                              <label class="custom-control-label" for="escolaridade7">Educação superior
                                incompleta</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade8" name="escolaridade" <?php if ($rowget->escolaridade == '8') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="8">
                              <label class="custom-control-label" for="escolaridade8">Educação superior completa</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade9" name="escolaridade" <?php if ($rowget->escolaridade == '9') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="9">
                              <label class="custom-control-label" for="escolaridade9">Ignorado</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="escolaridade10" name="escolaridade" <?php if ($rowget->escolaridade == '10') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="10">
                              <label class="custom-control-label" for="escolaridade10">Não se aplica</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Numero do Cartao SUS</label>
                            <input type="text" id="cns" class="form-control" maxlength="15"
                              value="<?= $rowget->num_carteira_convenio; ?>">
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group">
                            <label for="">Nome da mãe</label>
                            <input type="text" id="nome_mae" class="form-control"
                              value="<?= ts_decodifica($rowget->nome_mae); ?>">
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group">
                            <label for="">UF</label>
                            <input type="text" id="uf" class="form-control"
                              value="<?= $rowget->uf; ?>">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Municipio de Residencia</label>
                            <input type="text" id="cidade" class="form-control"
                              value="<?= $rowget->cidade; ?>">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Bairro</label>
                            <input type="text" id="bairro" class="form-control"
                              value="<?= $rowget->bairro; ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="">Logradouro</label>
                            <input type="text" id="rua" class="form-control"
                              value="<?= $rowget->rua; ?>">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="">Numero</label>
                            <input type="text" id="numero" class="form-control"
                              value="<?= $rowget->numero; ?>">
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="form-group">
                            <label for="">Complemento</label>
                            <input type="text" id="complemento" class="form-control"
                              value="<?= $rowget->complemento; ?>">
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="">CEP</label>
                            <input type="text" id="cep" class="form-control"
                              value="<?= $rowget->cep; ?>">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="">(DDD) Telefone</label>
                            <input type="text" id="celular" class="form-control"
                              value="<?= $rowget->telefone; ?>">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <label for="">Zona</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="zona1" name="zona" <?php if ($rowget->zona == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="zona1">Urbana</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="zona2" name="zona" <?php if ($rowget->zona == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="zona2">Rural</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="zona3" name="zona" <?php if ($rowget->zona == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="zona3">Periurbana</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="zona9" name="zona" <?php if ($rowget->zona == '9') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="9">
                              <label class="custom-control-label" for="zona9">Ignorado</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <hr>Dados clinicos e laboratoriais
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="">Data da Investigação</label>
                          <input type="text" name="data_investigacao" id="data_investigacao" class="form-control"
                            OnKeyPress="formatar('##/##/####', this)"
                            value="<?= $rowget->data_investigacao; ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="">Ocupação</label>
                          <input type="text" name="ocupacao" id="ocupacao" class="form-control"
                            value="<?= $rowget->ocupacao; ?>">
                        </div>
                      </div>
                      <div class="row mt-2">
                        <div class="col-md-12">
                          <h5>Sinais clinicos</h5>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Febre</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="febre1" name="febre" <?php if ($rowget->febre == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="febre1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="febre2" name="febre" <?php if ($rowget->febre == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="febre2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Cefaleia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="cefaleia1" name="cefaleia" <?php if ($rowget->cefaleia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="cefaleia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="cefaleia2" name="cefaleia" <?php if ($rowget->cefaleia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="cefaleia2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Vomito</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="vomito1" name="vomito" <?php if ($rowget->vomito == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="vomito1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="vomito2" name="vomito" <?php if ($rowget->vomito == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="vomito2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Dor nas costas</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="dor_costas1" name="dor_costas" <?php if ($rowget->dor_costas == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="dor_costas1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="dor_costas2" name="dor_costas" <?php if ($rowget->dor_costas == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="dor_costas2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Artrite</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="artrite1" name="artrite" <?php if ($rowget->artrite == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="artrite1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="artrite2" name="artrite" <?php if ($rowget->artrite == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="artrite2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Petéquias</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="petequia1" name="petequia" <?php if ($rowget->petequia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="petequia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="petequia2" name="petequia" <?php if ($rowget->petequia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="petequia2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Prova do laço positiva</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="prova_positiva1" name="prova_positiva" <?php if ($rowget->prova_positiva == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="prova_positiva1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="prova_positiva2" name="prova_positiva" <?php if ($rowget->prova_positiva == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="prova_positiva2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Mialgia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="mialgia1" name="mialgia" <?php if ($rowget->mialgia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="mialgia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="mialgia2" name="mialgia" <?php if ($rowget->mialgia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="mialgia2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Exantema</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="exantema1" name="exantema" <?php if ($rowget->exantema == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="exantema1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="exantema2" name="exantema" <?php if ($rowget->exantema == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="exantema2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Nauseas</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="nauseas1" name="nauseas" <?php if ($rowget->nauseas == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="nauseas1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="nauseas2" name="nauseas" <?php if ($rowget->nauseas == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="nauseas2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Conjuntivite</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="conjuntivite1" name="conjuntivite" <?php if ($rowget->conjuntivite == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="conjuntivite1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="conjuntivite2" name="conjuntivite" <?php if ($rowget->conjuntivite == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="conjuntivite2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Artralgia Intensa</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="artralgia_intensa1" name="artralgia_intensa" <?php if ($rowget->artralgia_intensa == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="artralgia_intensa1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="artralgia_intensa2" name="artralgia_intensa" <?php if ($rowget->artralgia_intensa == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="artralgia_intensa2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Leucopenia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="leucopenia1" name="leucopenia" <?php if ($rowget->leucopenia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="leucopenia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="leucopenia2" name="leucopenia" <?php if ($rowget->leucopenia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="leucopenia2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Dor Retroorbital</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="dor_retroorbital1" name="dor_retroorbital" <?php if ($rowget->dor_retroorbital == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="dor_retroorbital1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="dor_retroorbital2" name="dor_retroorbital" <?php if ($rowget->dor_retroorbital == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="dor_retroorbital2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row mt-1">
                        <div class="col-md-12">
                          <h5>Doenças pré-existentes</h5>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Diabetes</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="diabetes1" name="diabetes" <?php if ($rowget->diabetes == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="diabetes1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="diabetes2" name="diabetes" <?php if ($rowget->diabetes == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="diabetes2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Hepatopatias</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hepatopatias1" name="hepatopatias" <?php if ($rowget->hepatopatias == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hepatopatias1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hepatopatias2" name="hepatopatias" <?php if ($rowget->hepatopatias == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hepatopatias2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Hipertensao arterial</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hipertensao_arterial1" name="hipertensao_arterial" <?php if ($rowget->hipertensao_arterial == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hipertensao_arterial1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hipertensao_arterial2" name="hipertensao_arterial" <?php if ($rowget->hipertensao_arterial == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hipertensao_arterial2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Doenças auto-imunes</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="doencas_ai1" name="doencas_ai" <?php if ($rowget->doencas_ai == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="doencas_ai1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="doencas_ai2" name="doencas_ai" <?php if ($rowget->doencas_ai == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="doencas_ai2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Doencas hematológicas</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="doencas_hematologicas1" name="doencas_hematologicas" <?php if ($rowget->doencas_hematologicas == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="doencas_hematologicas1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="doencas_hematologicas2" name="doencas_hematologicas" <?php if ($rowget->doencas_hematologicas == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="doencas_hematologicas2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Doenca renal crônica</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="doenca_rc1" name="doenca_rc" <?php if ($rowget->doenca_rc == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="doenca_rc1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="doenca_rc2" name="doenca_rc" <?php if ($rowget->doenca_rc == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="doenca_rc2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Doença ácido-péptica</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="doenca_ap1" name="doenca_ap" <?php if ($rowget->doenca_ap == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="doenca_ap1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="doenca_ap2" name="doenca_ap" <?php if ($rowget->doenca_ap == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="doenca_ap2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row mt-1">
                        <div class="col-md-8">
                          <h5>Sorologia (IgM) Chikungunya</h5>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="">Data da Coleta da 1º Amostra</label>
                                <input type="text" name="data_coleta_s1" id="data_coleta_s1" class="form-control"
                                  value="<?= $rowget->data_coleta_s1; ?>"
                                  OnKeyPress="formatar('##/##/####', this)">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="">Data da Coleta da 2º Amostra</label>
                                <input type="text" name="data_coleta_s2" id="data_coleta_s2" class="form-control"
                                  value="<?= $rowget->data_coleta_s2; ?>"
                                  OnKeyPress="formatar('##/##/####', this)">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <h5>Exame PRNT</h5>
                          <div class="form-group">
                            <label for="">Data Coleta</label>
                            <input type="text" name="data_prnt" id="data_prnt" class="form-control"
                              value="<?= $rowget->data_prnt; ?>"
                              OnKeyPress="formatar('##/##/####', this)">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <h5>Resultado</h5>
                          <div class="row">
                            <div class="col-md-4">
                              <label for="">S1</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="resultado_s11" name="resultado_s1" <?php if ($rowget->resultado_s1 == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="resultado_s11">Reagente</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_s12" name="resultado_s1" <?php if ($rowget->resultado_s1 == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="resultado_s12">Não Reagente</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_s13" name="resultado_s1" <?php if ($rowget->resultado_s1 == '3') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="3">
                                  <label class="custom-control-label" for="resultado_s13">Inconclusivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_s14" name="resultado_s1" <?php if ($rowget->resultado_s1 == '4') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="4">
                                  <label class="custom-control-label" for="resultado_s14">Não Realizado</label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <label for="">S2</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="resultado_s21" name="resultado_s2" <?php if ($rowget->resultado_s2 == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="resultado_s21">Reagente</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_s22" name="resultado_s2" <?php if ($rowget->resultado_s2 == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="resultado_s22">Não Reagente</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_s23" name="resultado_s2" <?php if ($rowget->resultado_s2 == '3') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="3">
                                  <label class="custom-control-label" for="resultado_s23">Inconclusivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_s24" name="resultado_s2" <?php if ($rowget->resultado_s2 == '4') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="4">
                                  <label class="custom-control-label" for="resultado_s24">Não Realizado</label>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <label for="">PRNT</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="resultado_prnt1" name="resultado_prnt" <?php if ($rowget->resultado_prnt == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="resultado_prnt1">Reagente</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_prnt2" name="resultado_prnt" <?php if ($rowget->resultado_prnt == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="resultado_prnt2">Não Reagente</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_prnt3" name="resultado_prnt" <?php if ($rowget->resultado_prnt == '3') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="3">
                                  <label class="custom-control-label" for="resultado_prnt3">Inconclusivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_prnt4" name="resultado_prnt" <?php if ($rowget->resultado_prnt == '4') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="4">
                                  <label class="custom-control-label" for="resultado_prnt4">Não Realizado</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <h5>Sorologia (IgM) Dengue</h5>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="">Data da Coleta</label>
                                <input type="text" name="data_coleta_dengue" id="data_coleta_dengue"
                                  class="form-control"
                                  value="<?= $rowget->data_coleta_dengue; ?>"
                                  OnKeyPress="formatar('##/##/####', this)">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <label for="">Resultado</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="resultado_dengue1" name="resultado_dengue" <?php if ($rowget->resultado_dengue == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="resultado_dengue1">Positivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_dengue2" name="resultado_dengue" <?php if ($rowget->resultado_dengue == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="resultado_dengue2">Negativo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_dengue3" name="resultado_dengue" <?php if ($rowget->resultado_dengue == '3') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="3">
                                  <label class="custom-control-label" for="resultado_dengue3">Inconclusivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_dengue4" name="resultado_dengue" <?php if ($rowget->resultado_dengue == '4') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="4">
                                  <label class="custom-control-label" for="resultado_dengue4">Não realizado</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <h5>Exame NS1</h5>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="">Data Coleta</label>
                                <input type="text" name="data_ns1" id="data_ns1" class="form-control"
                                  value="<?= $rowget->data_ns1; ?>"
                                  OnKeyPress="formatar('##/##/####', this)">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <label for="">Resultado</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="resultado_ns11" name="resultado_ns1" <?php if ($rowget->resultado_ns1 == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="resultado_ns11">Positivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_ns12" name="resultado_ns1" <?php if ($rowget->resultado_ns1 == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="resultado_ns12">Negativo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_ns13" name="resultado_ns1" <?php if ($rowget->resultado_ns1 == '3') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="3">
                                  <label class="custom-control-label" for="resultado_ns13">Inconclusivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_ns14" name="resultado_ns1" <?php if ($rowget->resultado_ns1 == '4') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="4">
                                  <label class="custom-control-label" for="resultado_ns14">Não realizado</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <h5>Isolamento</h5>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="">Data Coleta</label>
                                <input type="text" name="data_isolamento" id="data_isolamento" class="form-control"
                                  value="<?= $rowget->data_isolamento; ?>"
                                  OnKeyPress="formatar('##/##/####', this)">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <label for="">Resultado</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="resultado_isomanento1" name="resultado_isomanento" <?php if ($rowget->resultado_isomanento == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="resultado_isomanento1">Positivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_isomanento2" name="resultado_isomanento" <?php if ($rowget->resultado_isomanento == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="resultado_isomanento2">Negativo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_isomanento3" name="resultado_isomanento" <?php if ($rowget->resultado_isomanento == '3') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="3">
                                  <label class="custom-control-label" for="resultado_isomanento3">Inconclusivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_isomanento4" name="resultado_isomanento" <?php if ($rowget->resultado_isomanento == '4') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="4">
                                  <label class="custom-control-label" for="resultado_isomanento4">Não
                                    realizado</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <h5>RT-PCR</h5>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="">Data Coleta</label>
                                <input type="text" name="data_rtpcr" id="data_rtpcr" class="form-control"
                                  value="<?= $rowget->data_rtpcr; ?>"
                                  OnKeyPress="formatar('##/##/####', this)">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <label for="">Resultado</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="resultado_rtpcr1" name="resultado_rtpcr" <?php if ($rowget->resultado_rtpcr == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="resultado_rtpcr1">Positivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_rtpcr2" name="resultado_rtpcr" <?php if ($rowget->resultado_rtpcr == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="resultado_rtpcr2">Negativo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_rtpcr3" name="resultado_rtpcr" <?php if ($rowget->resultado_rtpcr == '3') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="3">
                                  <label class="custom-control-label" for="resultado_rtpcr3">Inconclusivo</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="resultado_rtpcr4" name="resultado_rtpcr" <?php if ($rowget->resultado_rtpcr == '4') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="4">
                                  <label class="custom-control-label" for="resultado_rtpcr4">Não realizado</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <label for="">Sorotipo</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="sorotipo1" name="sorotipo" <?php if ($rowget->sorotipo == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="sorotipo1">DENV 1</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="sorotipo2" name="sorotipo" <?php if ($rowget->sorotipo == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="sorotipo2">DENV 2</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="sorotipo3" name="sorotipo" <?php if ($rowget->sorotipo == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="sorotipo3">DEMV 3</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="sorotipo4" name="sorotipo" <?php if ($rowget->sorotipo == '4') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="4">
                              <label class="custom-control-label" for="sorotipo4">DENV 4</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <label for="">Histopatologia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="histopatologia1" name="histopatologia" <?php if ($rowget->histopatologia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="histopatologia1">Compatível</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="histopatologia2" name="histopatologia" <?php if ($rowget->histopatologia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="histopatologia2">Incompatível</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="histopatologia3" name="histopatologia" <?php if ($rowget->histopatologia == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="histopatologia3">Inconclusivo</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="histopatologia4" name="histopatologia" <?php if ($rowget->histopatologia == '4') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="4">
                              <label class="custom-control-label" for="histopatologia4">Não realizado</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <label for="">Imunohistoquímica</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="imunohistoquimica1" name="imunohistoquimica" <?php if ($rowget->imunohistoquimica == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="imunohistoquimica1">Positivo</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="imunohistoquimica2" name="imunohistoquimica" <?php if ($rowget->imunohistoquimica == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="imunohistoquimica2">Negativo</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="imunohistoquimica3" name="imunohistoquimica" <?php if ($rowget->imunohistoquimica == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="imunohistoquimica3">Inconclusivo</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="imunohistoquimica4" name="imunohistoquimica" <?php if ($rowget->imunohistoquimica == '4') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="4">
                              <label class="custom-control-label" for="imunohistoquimica4">Não realizado</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="">Ocorreu Hospitalização?</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hospitalizacao1" name="hospitalizacao" <?php if ($rowget->hospitalizacao == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hospitalizacao1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hospitalizacao2" name="hospitalizacao" <?php if ($rowget->hospitalizacao == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hospitalizacao2">Não</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hospitalizacao9" name="hospitalizacao" <?php if ($rowget->hospitalizacao == '9') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="9">
                              <label class="custom-control-label" for="hospitalizacao9">Ignorado</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Data da Internação</label>
                            <input type="text" name="data_internacao" id="data_internacao" class="form-control"
                              value="<?= $rowget->data_internacao; ?>"
                              OnKeyPress="formatar('##/##/####', this)">
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group">
                            <label for="">UF</label>
                            <input type="text" name="uf_internacao" id="uf_internacao" class="form-control" value="MG"
                              maxlength="2">
                          </div>
                        </div>
                        <div class="col-md-5">
                          <label for="">Municipio de Notificação</label>
                          <input type="text" name="municipio_internacao" id="municipio_internacao" class="form-control"
                            value="UBERABA">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Codigo(IBGE)</label>
                          <input type="text" name="ibge_internacao" id="ibge_internacao" class="form-control"
                            value="317010" maxlength="6">
                        </div>
                        <div class="col-md-6">
                          <label for="">Nome do Hospital</label>
                          <input type="text" name="nome_hospital" id="nome_hospital" class="form-control"
                            value="<?= $rowget->nome_hospital ?>">
                        </div>
                        <div class="col-md-4">
                          <label for="">Codigo</label>
                          <input type="text" name="codigo_hospital" id="codigo_hospital" class="form-control"
                            value="<?= $rowget->codigo_hospital ?>"
                            maxlength="7">
                        </div>
                        <div class="col-md-3">
                          <label for="">(DDD) Telefone</label>
                          <input type="text" name="telefone_internacao" id="telefone_internacao" class="form-control"
                            value="" maxlength="11"
                            value="<?= $rowget->telefone_internacao ?>">
                        </div>
                      </div>
                      <div class="row mt-2">
                        <div class="col-md-12">
                          <h4>Local Provável de Infecção(no período de 15 dias)</h4>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <label for="">O caso é autóctone do município de residência</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="autoctone1" name="autoctone" <?php if ($rowget->autoctone == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="autoctone1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="autoctone2" name="autoctone" <?php if ($rowget->autoctone == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="autoctone2">Não</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="autoctone3" name="autoctone" <?php if ($rowget->autoctone == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="autoctone3">Indeterminado</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group">
                            <label for="">UF</label>
                            <input type="text" name="uf_caso" id="uf_caso" class="form-control" value="MG"
                              maxlength="2">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">País</label>
                          <input type="text" name="pais_caso" id="pais_caso" class="form-control" value="BRASIL">
                        </div>
                        <div class="col-md-4">
                          <label for="">Municipio</label>
                          <input type="text" name="municipio_caso" id="municipio_caso" class="form-control"
                            value="UBERABA">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Codigo(IBGE)</label>
                          <input type="text" name="ibge_caso" id="ibge_caso" class="form-control" value="317010"
                            maxlength="6">
                        </div>
                        <div class="col-md-5">
                          <label for="">Distrito</label>
                          <input type="text" name="distrito_caso" id="distrito_caso" class="form-control"
                            value="<?= $rowget->distrito_caso ?>">
                        </div>
                        <div class="col-md-5">
                          <label for="">Bairro</label>
                          <input type="text" name="bairro_caso" id="bairro_caso" class="form-control"
                            value="<?= $rowget->bairro_caso ?>">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Classificação</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="classificacao5" name="classificacao" <?php if ($rowget->classificacao == '5') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="5">
                              <label class="custom-control-label" for="classificacao5">Descartado</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="classificacao10" name="classificacao" <?php if ($rowget->classificacao == '10') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="10">
                              <label class="custom-control-label" for="classificacao10">Dengue</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="classificacao11" name="classificacao" <?php if ($rowget->classificacao == '11') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="11">
                              <label class="custom-control-label" for="classificacao11">Dengue com Sinais de
                                Alarme</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="classificacao12" name="classificacao" <?php if ($rowget->classificacao == '12') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="12">
                              <label class="custom-control-label" for="classificacao12">Dengue Grave</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="classificacao13" name="classificacao" <?php if ($rowget->classificacao == '13') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="13">
                              <label class="custom-control-label" for="classificacao13">Chikungunya</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <label for="">Critério de Confirmação/Descarte</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="criteriocd1" name="criteriocd" <?php if ($rowget->criteriocd == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="criteriocd1">Laboratório</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="criteriocd2" name="criteriocd" <?php if ($rowget->criteriocd == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="criteriocd2">Clinico-Epidemiológico</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="criteriocd3" name="criteriocd" <?php if ($rowget->criteriocd == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="criteriocd3">Em Investigação</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Apresentação clínica</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="apresentacao_clinica1" name="apresentacao_clinica" <?php if ($rowget->apresentacao_clinica == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="apresentacao_clinica1">Aguda</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="apresentacao_clinica2" name="apresentacao_clinica" <?php if ($rowget->apresentacao_clinica == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="apresentacao_clinica2">Crônica</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Evolução do Caso</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="evolucao_caso1" name="evolucao_caso" <?php if ($rowget->evolucao_caso == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="evolucao_caso1">Cura</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="evolucao_caso2" name="evolucao_caso" <?php if ($rowget->evolucao_caso == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="evolucao_caso2">Óbito pelo agravo</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="evolucao_caso3" name="evolucao_caso" <?php if ($rowget->evolucao_caso == '3') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="3">
                              <label class="custom-control-label" for="evolucao_caso3">Óbito por outras
                                causas</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="evolucao_caso4" name="evolucao_caso" <?php if ($rowget->evolucao_caso == '4') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="4">
                              <label class="custom-control-label" for="evolucao_caso4">Óbito em
                                Investigação</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="evolucao_caso5" name="evolucao_caso" <?php if ($rowget->evolucao_caso == '5') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="5">
                              <label class="custom-control-label" for="evolucao_caso5">Ignorado</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Data do Óbito</label>
                            <input type="text" name="data_obito" id="data_obito" class="form-control"
                              value="<?= $rowget->data_obito; ?>"
                              OnKeyPress="formatar('##/##/####', this)">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Data do Encerramento</label>
                            <input type="text" name="data_encerramento" id="data_encerramento" class="form-control"
                              value="<?= $rowget->data_encerramento; ?>"
                              OnKeyPress="formatar('##/##/####', this)">
                          </div>
                        </div>
                      </div>
                      <div class="row mt-1 mb-1">
                        <div class="col-md-12">
                          <h4>
                            <center>Preencher os sinais clinicos para Dengue com Sinais de Alarme e Dengue Grave
                            </center>
                          </h4>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <h5>Dengue com sinais de alarme</h5>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="">Hipotensão postural e/ou lipotpimia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hipotensao1" name="hipotensao" <?php if ($rowget->hipotensao == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hipotensao1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hipotensao2" name="hipotensao" <?php if ($rowget->hipotensao == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hipotensao2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Queda abrupta de plaquetas</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="abrupta_plaquetas1" name="abrupta_plaquetas" <?php if ($rowget->abrupta_plaquetas == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="abrupta_plaquetas1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="abrupta_plaquetas2" name="abrupta_plaquetas" <?php if ($rowget->abrupta_plaquetas == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="abrupta_plaquetas2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Vômitos persistentes</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="vomito_persistentes1" name="vomito_persistentes" <?php if ($rowget->vomito_persistentes == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="vomito_persistentes1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="vomito_persistentes2" name="vomito_persistentes" <?php if ($rowget->vomito_persistentes == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="vomito_persistentes2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Dor abdominal intensa e contínua</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="dor_abdominal1" name="dor_abdominal" <?php if ($rowget->dor_abdominal == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="dor_abdominal1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="dor_abdominal2" name="dor_abdominal" <?php if ($rowget->dor_abdominal == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="dor_abdominal2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="">Letargia ou irritabilidade</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="letargia_irritabilidade1" name="letargia_irritabilidade" <?php if ($rowget->letargia_irritabilidade == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="letargia_irritabilidade1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="letargia_irritabilidade2" name="letargia_irritabilidade" <?php if ($rowget->letargia_irritabilidade == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="letargia_irritabilidade2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Sangramento de mucosa/outras hemorragias</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="sangramento_mucosa1" name="sangramento_mucosa" <?php if ($rowget->sangramento_mucosa == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="sangramento_mucosa1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="sangramento_mucosa2" name="sangramento_mucosa" <?php if ($rowget->sangramento_mucosa == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="sangramento_mucosa2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Aumento progressivo do hematócrito</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hematocrito1" name="hematocrito" <?php if ($rowget->hematocrito == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hematocrito1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hematocrito2" name="hematocrito" <?php if ($rowget->hematocrito == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hematocrito2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Hepatomegalia >= 2cm</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hepatomegalia21" name="hepatomegalia2" <?php if ($rowget->hepatomegalia2 == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hepatomegalia21">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hepatomegalia22" name="hepatomegalia2" <?php if ($rowget->hepatomegalia2 == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hepatomegalia22">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="">Acumulo de liquidos</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="acumulo_liquidos1" name="acumulo_liquidos" <?php if ($rowget->acumulo_liquidos == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="acumulo_liquidos1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="acumulo_liquidos2" name="acumulo_liquidos" <?php if ($rowget->acumulo_liquidos == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="acumulo_liquidos2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="">Data do inicio dos sinais de alarme</label>
                            <input type="text" name="data_sinais_alarme" id="data_sinais_alarme" class="form-control"
                              value="<?= $rowget->data_sinais_alarme; ?>"
                              OnKeyPress="formatar('##/##/####', this)">
                          </div>
                        </div>
                      </div>
                      <div class="row mt-1">
                        <div class="col-md-12">
                          <h5>Dengue grave</h5>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <h6>Extravasamento grave de plasma</h6>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="">Pulso débil ou indetectável</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="pulso_indetectavel1" name="pulso_indetectavel" <?php if ($rowget->pulso_indetectavel == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="pulso_indetectavel1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="pulso_indetectavel2" name="pulso_indetectavel" <?php if ($rowget->pulso_indetectavel == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="pulso_indetectavel2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">PA convergente <= 20mmHg</label>
                              <div class="input-group">
                                <div class="custom-control custom-radio d-inline-block">
                                  <input type="radio" id="paconvergente1" name="paconvergente" <?php if ($rowget->paconvergente == '1') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="1">
                                  <label class="custom-control-label" for="paconvergente1">Sim</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block ml-2">
                                  <input type="radio" id="paconvergente2" name="paconvergente" <?php if ($rowget->paconvergente == '2') {
	echo 'checked';
} ?>
                                  class="custom-control-input" value="2">
                                  <label class="custom-control-label" for="paconvergente2">Nao</label>
                                </div>
                              </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Tempo de enchimento capilar</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="tempo_capilar1" name="tempo_capilar" <?php if ($rowget->tempo_capilar == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="tempo_capilar1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="tempo_capilar2" name="tempo_capilar" <?php if ($rowget->tempo_capilar == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="tempo_capilar2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Acúmulo de líquidos com insuficiência respiratória</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="acumulo_liquidos_repiratoria1" name="acumulo_liquidos_repiratoria"
                                <?php if ($rowget->acumulo_liquidos_repiratoria == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="acumulo_liquidos_repiratoria1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="acumulo_liquidos_repiratoria2" name="acumulo_liquidos_repiratoria"
                                <?php if ($rowget->acumulo_liquidos_repiratoria == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="acumulo_liquidos_repiratoria2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <label for="">Taquicardia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="taquicardia1" name="taquicardia" <?php if ($rowget->taquicardia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="taquicardia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="taquicardia2" name="taquicardia" <?php if ($rowget->taquicardia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="taquicardia2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Extremidades frias</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="extremidades_frias1" name="extremidades_frias" <?php if ($rowget->extremidades_frias == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="extremidades_frias1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="extremidades_frias2" name="extremidades_frias" <?php if ($rowget->extremidades_frias == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="extremidades_frias2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Hipotensão arterial em fase tardia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hipotensao_tardia1" name="hipotensao_tardia" <?php if ($rowget->hipotensao_tardia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hipotensao_tardia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hipotensao_tardia2" name="hipotensao_tardia" <?php if ($rowget->hipotensao_tardia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hipotensao_tardia2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row mt-1">
                        <div class="col-md-3">
                          <h6>Sangramento grave</h6>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">Hematêmese</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="hematemese1" name="hematemese" <?php if ($rowget->hematemese == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="hematemese1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="hematemese2" name="hematemese" <?php if ($rowget->hematemese == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="hematemese2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Melena</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="melena1" name="melena" <?php if ($rowget->melena == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="melena1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="melena2" name="melena" <?php if ($rowget->melena == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="melena2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Metrorragia volumosa</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="metrorragia1" name="metrorragia" <?php if ($rowget->metrorragia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="metrorragia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="metrorragia2" name="metrorragia" <?php if ($rowget->metrorragia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="metrorragia2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Sangramento do SNC</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="sangramentosnc1" name="sangramentosnc" <?php if ($rowget->sangramentosnc == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="sangramentosnc1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="sangramentosnc2" name="sangramentosnc" <?php if ($rowget->sangramentosnc == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="sangramentosnc2">Nao</label>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <h6>Comprometimento grave de órgaos</h6>
                      </div>
                      <div class="row">
                        <div class="col-md-2">
                          <label for="">AST/ALT > 1000</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="ast_alt1" name="ast_alt" <?php if ($rowget->ast_alt == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="ast_alt1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="ast_alt2" name="ast_alt" <?php if ($rowget->ast_alt == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="ast_alt2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Miocardite</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="miocardite1" name="miocardite" <?php if ($rowget->miocardite == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="miocardite1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="miocardite2" name="miocardite" <?php if ($rowget->miocardite == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="miocardite2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Alteracao da consciencia</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="alteracao_consciencia1" name="alteracao_consciencia" <?php if ($rowget->alteracao_consciencia == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="alteracao_consciencia1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="alteracao_consciencia2" name="alteracao_consciencia" <?php if ($rowget->alteracao_consciencia == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="alteracao_consciencia2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <label for="">Outros órgaos</label>
                          <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                              <input type="radio" id="outros_orgaos1" name="outros_orgaos" <?php if ($rowget->outros_orgaos == '1') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="1">
                              <label class="custom-control-label" for="outros_orgaos1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                              <input type="radio" id="outros_orgaos2" name="outros_orgaos" <?php if ($rowget->outros_orgaos == '2') {
	echo 'checked';
} ?>
                              class="custom-control-input" value="2">
                              <label class="custom-control-label" for="outros_orgaos2">Nao</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">especificar:</label>
                          <input type="text" class="form-control" name="especificar_orgao"
                            value="<?= $rowget->especificar_orgao ?>">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="">Data do inicio dos sinais de gravidade</label>
                            <input type="text" name="data_sinais_gravidade" id="data_sinais_gravidade"
                              class="form-control"
                              value="<?= $rowget->data_sinais_gravidade; ?>"
                              OnKeyPress="formatar('##/##/####', this)">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <h4>
                            <center>Informações complementares e observações
                            </center>
                          </h4>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="">observações Adicionais</label><textarea id="observacoes_adicionais"
                              name="observacoes_adicionais" class="form-control" rows="5"
                              cols="33"><?= $rowget->observacoes_adicionais ?></textarea>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Municipio/Unidade de Saúde</label>
                            <input type="text" name="municipio_unidade_observacao" id="municipio_unidade_observacao"
                              class="form-control"
                              value="<?= $rowget->municipio_unidade_observacao; ?>">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="">Codigo da Unid. de Saúde</label>
                          <input type="text" name="codigo_unidade_observacao" id="codigo_unidade_observacao"
                            class="form-control" value="7093284" maxlength="7">
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Nome</label>
                            <input type="text" name="nome_investigador" id="nome_investigador" class="form-control"
                              value="<?= $rowget->nome_investigador; ?>">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="">Função</label>
                            <input type="text" name="funcao_investigador" id="funcao_investigador" class="form-control"
                              value="<?= $rowget->funcao_investigador; ?>">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <button type="submit" class="btn btn-success">Gravar</button>
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





</body>
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
  function formatar(mascara, documento) {
    var i = documento.value.length;
    var saida = mascara.substring(0, 1);
    var texto = mascara.substring(i)

    if (texto.substring(0, 1) != saida) {
      documento.value += texto.substring(0, 1);
    }
  }

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

  function set_item(item, paciente_id, dt_nasc, sexo, telefone, celular, cep, endereco, numero, bairro, cidade,
    uf, cpf, nome_mae, cns) {
    // change input value
    $('#dt_nasc').val(dt_nasc);
    $('#nome').val(item);
    $('#nome_mae').val(nome_mae);
    //$('#cpf').val(cpf);
    $('#pessoa_id').val(paciente_id);
    $('#cns').val(cns);
    // if (sexo == 'masculino') {
    //   document.getElementById("psexo1").checked = true;
    // } else {
    //   document.getElementById("psexo2").checked = true;
    // }
    // $('#telefone').val(telefone);
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
</script>
</body>

</html>