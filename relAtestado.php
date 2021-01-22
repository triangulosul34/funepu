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

require_once 'fpdf/fpdf.php';
require_once 'fpdi/fpdi.php';
require '../vendor/autoload.php';

$data_atendimento = $_POST['data_atendimento'];
$hora_atendimento = $_POST['hora_atendimento'];
$dias_atestado = $_POST['dias_atestado'];
$cidAtestado = $_POST['cidAtestado'];
$atendimento = $_POST['atendimento'];
$isolamento = $_POST['isolamento'];

include 'conexao.php';
$stmt = "SELECT * FROM atendimentos a
			LEFT JOIN pessoas p ON a.paciente_id = p.pessoa_id
		WHERE a.transacao=$atendimento";

$sth = pg_query($stmt) or die($stmt);

$row = pg_fetch_object($sth);

if ($row->nome_social != '') {
	$nome = $row->nome_social;
} else {
	$nome = ts_decodifica($row->nome);
}
$pessoa_id = $row->pessoa_id;
$cpf = $row->cpf;
$rg = $row->identidade;
$med_atendimento = $row->med_atendimento;
$endereco = $row->endereco;
$bairro = $row->bairro;

$next = "select nextval('atestados_atestado_id_seq'::regclass)";
$sthnext = pg_query($next);
$rownext = pg_fetch_object($sthnext);

$sql = "INSERT INTO atestados(atestado_id,pessoa_id,profissional_id,atendimento_id,hora_atendimento,qtd_dias,partir_dia,cid,termo_isolamento) VALUES($rownext->nextval,$pessoa_id,'$med_atendimento',$atendimento,'$hora_atendimento','$dias_atestado','$data_atendimento','$cidAtestado','$isolamento')";
$result = pg_query($sql) or die($sql);

$pdf = new FPDI();

$pageCount = $pdf->setSourceFile('formularios/atestado.pdf');
$tplIdx = $pdf->importPage(1);

$pdf->addPage('L', 'A4');
$pdf->useTemplate($tplIdx, 0, 0, 290);

$pdf->SetFont('Helvetica', 'B', 16);

$pdf->SetXY(115, 59);
$pdf->Write(8, $nome);
$pdf->Image('https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2F' . UNIDADEABV_CONFIG . '.midaspa.com.br%2Fqratestado.php%3Fa%3D' . $rownext->nextval . '%26c%3D' . $atendimento . ';&choe=UTF-8', 18, 105, 20, 20, 'png');

$pdf->SetXY(25, 82);
$pdf->Write(8, $hora_atendimento);

$pdf->SetXY(135, 82);
$pdf->Write(8, $dias_atestado);

$pdf->SetXY(25, 93);
$pdf->Write(8, $data_atendimento);

$pdf->SetXY(200, 93);
$pdf->Write(8, $cidAtestado);

$pdf->SetXY(35, 132);
$pdf->Write(8, date('d/m/Y'));

$pdf->SetFont('Arial', '', 6);
$pdf->SetXY(11, 121);
$pdf->Write(8, utf8_decode('código de verificação de veracidade'));

if ($isolamento) {
	$pdf->AddPage();
	$pdf->Image('formularios/termo_covid.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetXY(33, 68);
	$pdf->Write(8, $nome);
	$pdf->SetXY(125, 81);
	$pdf->Write(8, ts_decodifica($cpf));
	$pdf->SetXY(30, 89);
	$pdf->Write(8, ts_decodifica($rg));
	$pdf->SetXY(135, 89);
	$pdf->Write(8, $endereco);
	$pdf->SetXY(25, 96);
	$pdf->Write(8, $bairro);
	$pdf->SetXY(70, 162);
	$pdf->Write(8, 'UPA ' . UNIDADE_CONFIG);
	$pdf->SetXY(165, 162);
	$pdf->Write(8, UNIDADE_TEL);
	$pdf->SetXY(90, 177);
	$pdf->Write(8, $data_atendimento);
	$pdf->SetXY(120, 177);
	$pdf->Write(8, date('d/m/Y', strtotime('+' . preg_replace('/[^0-9]/', '', $dias_atestado) . ' days', strtotime(inverteData($data_atendimento)))));
	$pdf->SetXY(169, 189);
	$pdf->Write(8, date('d     m'));
	$pdf->AddPage();
	$pdf->Image('formularios/orientecoes_domicilio.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
}

$pdf->Output();
