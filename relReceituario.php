<?php

require_once 'fpdf/fpdf.php';
require_once 'fpdi/fpdi.php';
require 'tsul_ssl.php';

$transacao = $_GET['transacao'];
$data = date('d-m-Y');

include 'verifica.php';
$pdf = new FPDI();

$pageCount = $pdf->setSourceFile('formularios/receituario.pdf');
$tplIdx = $pdf->importPage(1);

include 'conexao.php';
$stmt = "select nome,pessoa_id as prontuario from atendimentos a
			left join pessoas p on p.pessoa_id = a.paciente_id
			where transacao = $transacao";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);

$pdf->addPage('L');
$pdf->useTemplate($tplIdx, 0, 0, 302);

$pdf->SetFont('Helvetica', 'B', 9);

$pdf->SetXY(30.5, 33.5);
$pdf->Write(8, utf8_decode(ts_decodifica($row->nome)));

$pdf->SetXY(182, 33.5);
$pdf->Write(8, utf8_decode(ts_decodifica($row->nome)));

$pdf->SetXY(20, 38);
$pdf->Write(8, utf8_decode('Prontuário: ' . $row->prontuario));

$pdf->SetXY(171.2, 38);
$pdf->Write(8, utf8_decode('Prontuário: ' . $row->prontuario));

$pdf->SetXY(98.7, 38);
$pdf->Write(8, utf8_decode('Data: ' . date('d/m/Y', strtotime($data))));

$pdf->SetXY(250, 38);
$pdf->Write(8, utf8_decode('Data: ' . date('d/m/Y', strtotime($data))));

$pdf->Ln(10);
$i = 0;

$pdf->SetFont('Arial', 'B', 8);
include 'conexao.php';
$stmt2 = "select medicamentos,quantidade,modo_usar 
			from receituario_remedio 
			where transacao = $transacao";
$sth2 = pg_query($stmt2) or die($stmt2);
while ($row = pg_fetch_object($sth2)) {
	$pdf->Cell(10, 5, '', 0, 0, 'L');
	$pdf->Cell(80, 5, utf8_decode($row->medicamentos), 0, 0, 'L');
	$pdf->Cell(71, 5, $row->quantidade, 0, 0, 'L');

	$pdf->Cell(80, 5, utf8_decode($row->medicamentos), 0, 0, 'L');
	$pdf->Cell(25, 5, $row->quantidade, 0, 0, 'L');
	$pdf->Ln(5);

	$pdf->Cell(10, 5, '', 0, 0, 'L');
	$pdf->Cell(105, 5, utf8_decode($row->modo_usar), 0, 0, 'L');
	$pdf->Cell(46, 5, '', 0, 0, 'L');
	$pdf->Cell(105, 5, utf8_decode($row->modo_usar), 0, 0, 'L');
	$pdf->Ln(7);

	$i = $i + 7;
}

$pdf->Output();
