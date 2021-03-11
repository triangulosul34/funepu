<?php

include 'Config.php';
require 'tsul_ssl.php';

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

function connect()
{
	return new PDO('pgsql:host=localhost;dbname=' . BANCO_DADOS . '', 'postgres', 'tsul2020##');
}
$pdo = connect();
$keyword = '' . ts_codifica($_POST['keyword']) . '%';
$sql = "SELECT pessoa_id, cpf, num_carteira_convenio as cns, nome, nome_mae, dt_nasc, sexo, telefone, celular, cep, endereco, numero, complemento, bairro, cidade, estado FROM pessoas WHERE nome LIKE '$keyword' ORDER BY nome ASC LIMIT 10 ";
$query = $pdo->prepare($sql);
$query->bindParam(':keyword', $keyword, PDO::PARAM_STR);
$query->execute();
$list = $query->fetchAll();
foreach ($list as $rs) {
	if ($rs['sexo'] == 'M') {
		$sexo = 'masculino';
	} else {
		$sexo = 'feminino';
	}

	$cpf = preg_replace('/[^0-9]/', '', ts_decodifica($rs['cpf']));
	// put in bold the written text
	$country_name = str_replace($_POST['keyword'], '<b>' . $_POST['keyword'] . '</b>', ts_decodifica($rs['nome'])) . ' - ' . inverteData($rs['dt_nasc']) . ' - ' . $cpf;
	// add new option
	echo '<li onclick="set_item(\'' . str_replace("'", "\'", ts_decodifica($rs['nome'])) . '\',\'' . $rs['pessoa_id'] . '\',\'' . inverteData($rs['dt_nasc']) . '\',\'' . $sexo . '\',\'' . $rs['telefone'] . '\',\'' . $rs['celular'] . '\',\'' . $rs['cep'] . '\',\'' . utf8_decode($rs['endereco']) . '\',\'' . $rs['numero'] . '\',\'' . utf8_decode($rs['bairro']) . '\',\'' . $rs['cidade'] . '\',\'' . $rs['estado'] . '\',\'' . $cpf . '\',\'' . ts_decodifica($rs['nome_mae']) . '\',\'' . $rs['cns'] . '\')">' . $country_name . '</li>';
}
