<?php

function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

require_once 'fpdf/fpdf.php';
require '../vendor/autoload.php';
require 'tsul_ssl.php';

$atendimento_id = $_GET['atendimento_id'];

include 'conexao.php';
$sql = "SELECT * FROM relatorio_pmmg a 
INNER JOIN atendimentos b ON a.atendimento_id = b.transacao 
INNER JOIN pessoas c ON b.paciente_id = c.pessoa_id
WHERE atendimento_id = $atendimento_id";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);

$pdf = new FPDF('P', 'pt', 'A4');
$pdf->AddPage();
$pdf->Image('formularios/relatorio pmmg.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
$pdf->SetFont('Arial', '', 12);
$pdf->Text(40, 200, ts_decodifica($row->nome));
$pdf->Text(400, 223, UNIDADE_CONFIG);
$pdf->Text(40, 243, $row->dat_cad[8] . $row->dat_cad[9]);
$pdf->Text(61, 243, $row->dat_cad[5] . $row->dat_cad[6]);
$pdf->Text(85, 243, $row->dat_cad[0] . $row->dat_cad[1] . $row->dat_cad[2] . $row->dat_cad[3]);
$pdf->Text(173, 242, $row->hora_cad[0] . $row->hora_cad[1]);
$pdf->Text(190, 242, $row->hora_cad[3] . $row->hora_cad[4]);
$pdf->SetXY(40, 300);
$pdf->multicell(540, 20, utf8_decode($row->queixa_paciente), 0, 'L', 0);
$pdf->SetXY(40, 386);
$pdf->multicell(540, 20, utf8_decode($row->diagnostico_medico), 0, 'L', 0);
$pdf->SetXY(40, 460);
$pdf->multicell(540, 20, utf8_decode($row->orientacao_paciente), 0, 'L', 0);
$pdf->Text(250, 720, date('d'));
$mes = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$pdf->Text(300, 720, $mes[(date('m') * 1)]);
$pdf->Text(380, 720, date('Y'));
$pdf->Output();
