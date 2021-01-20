<?php

include 'Config.php';

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

$where = '';

$nome = $_GET['nome'];
$data_notificacao_inicial = $_GET['data_notificacao'];
$data_notificacao_final = $_GET['data_notificacao_final'];
$data_secretaria_inicial = $_GET['data_secretaria_inicial'];
$data_secretaria_final = $_GET['data_secretaria_final'];
$tipo = $_GET['tipo'];
$resultados = $_GET['resultado'];
$unidade = $_GET['unidade'];
$arquivo = $_GET['arquivo'];

(empty($nome)) ?: $where = $where . " AND nome = '$nome'";
(empty($tipo)) ?: $where = $where . " AND tipo = '$tipo'";
(empty($arquivo)) ?: $where = $where . " AND arquivo = '$arquivo'";
(empty($resultados)) ?: $where = $where . " AND resultados = '$resultados'";

if ($data_notificacao_inicial) {
	$where = $where . " AND data_notificacao >= '" . inverteData($data_notificacao_inicial) . "'";

	if ($data_notificacao_final) {
		$where = $where . " AND data_notificacao <= '" . inverteData($data_notificacao_final) . "'";
	} else {
		$where = $where . " AND data_notificacao <= '" . inverteData($data_notificacao_inicial) . "'";
	}
}

if ($data_secretaria_inicial) {
	$where = $where . " AND data_secretaria >= '" . inverteData($data_secretaria_inicial) . "'";

	if ($data_secretaria_final) {
		$where = $where . " AND data_secretaria <= '" . inverteData($data_secretaria_final) . "'";
	} else {
		$where = $where . " AND data_secretaria <= '" . inverteData($data_secretaria_inicial) . "'";
	}
}

(!empty($where)) ?: $where = $where . " AND data_notificacao = '" . date('d/m/Y') . "'";

$arquivo = 'Relatorio Tempo Permanencia.xls';
$html = '';
$html .= '<table style="font-size:12px" border="1">';
$html .= '<tr>';
$html .= '<td colspan="8" align=\'center\'>' . utf8_decode('REGISTRO DE CONTROLE NOTIFICAÇÕES ENVIADAS À SECRETARIA DE SAÚDE') . '</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<tr align=\'center\'>';
$html .= '<td><b>Nome do Paciente</b></td>';
$html .= '<td><b>Data de Nascimento</b></td>';
$html .= '<td><b>' . utf8_decode('Nome da Mãe') . '</b></td>';
$html .= '<td><b>CPF</b></td>';
$html .= '<td><b>' . utf8_decode('Tipo de Notificação') . '</b></td>';
$html .= '<td><b>' . utf8_decode('Data da Notificação') . '</b></td>';
$html .= '<td><b>Resultados</b></td>';
$html .= '<td><b>Data do Envio a Secretaria</b></td>';
$html .= '</tr>';
if ($unidade == 1) {
	if (UNIDADEABV_CONFIG == 'mr') {
		include 'conexao.php';
	} else {
		include 'conexao2.php';
	}
	if (UNIDADEABV_CONFIG == 'mr') {
		$un = CON2;
	} else {
		$un = CON1;
	}
	$sql = "SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados 
                                 FROM excel_notificacao 
                                 WHERE controle = 1 $where 
                                 UNION 
                                 SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados
                                 FROM dblink('host=localhost
                                             user=postgres
                                             password=tsul2020## 
                                             dbname=$un', 'SELECT nome,data_nascimento,nome_mae,cpf,data_notificacao,data_secretaria,tipo,resultados
                                                               FROM excel_notificacao 
                                                               WHERE controle = 1 " . str_replace("'", "''", $where) . "') 
                                                               AS a(nome character varying,data_nascimento character varying,nome_mae character varying,cpf character varying,data_notificacao character varying,data_secretaria character varying,tipo character varying,resultados character varying)";
	$result = pg_query($sql) or die($sql);
} elseif ($unidade == 2) {
	if (UNIDADEABV_CONFIG == 'mr') {
		include 'conexao.php';
	} else {
		include 'conexao2.php';
	}
	$sql = "SELECT * FROM excel_notificacao WHERE controle = 1 $where order by data_notificacao";
	$result = pg_query($sql) or die($sql);
} elseif ($unidade == 3) {
	if (UNIDADEABV_CONFIG == 'mr') {
		include 'conexao2.php';
	} else {
		include 'conexao.php';
	}
	$sql = "SELECT * FROM excel_notificacao WHERE controle = 1 $where order by data_notificacao";
	$result = pg_query($sql) or die($sql);
}
while ($row = pg_fetch_object($result)) {
	$html .= '<tr>';
	$html .= '<td>' . utf8_decode($row->nome) . '</td>';
	$html .= '<td>' . $row->data_nascimento . '</td>';
	$html .= '<td>' . utf8_decode($row->nome_mae) . '</td>';
	$html .= '<td>' . $row->cpf . '</td>';
	$html .= '<td>' . utf8_decode($row->tipo) . '</td>';
	$html .= '<td>' . $row->data_notificacao . '</td>';
	$html .= '<td>' . $row->resultados . '</td>';
	$html .= '<td>' . $row->data_secretaria . '</td>';
	$html .= '</tr>';
}
$html .= '</table>';

// Configurações header para forçar o download
header('Expires: Mon, 26 Jul 2017 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D,d M YH:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-type: application/x-msexcel');
header("Content-Disposition: attachment; filename=\"{$arquivo}\"");
header('Content-Description: PHP Generated Data');
// Envia o conteúdo do arquivo
echo $html;
exit;
