<?php

require '../vendor/autoload.php';
require 'tsul_ssl.php';
require 'fpdf/fpdf.php';
function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}
$transacao = $_GET['id'];
include 'verifica.php';
include 'conexao.php';
$stmt = 'SELECT a.assistente_social_id,a.atendimento_id,a.data,a.hora,b.nome,a.relatorio, d.nome as paciente, d.pessoa_id
			FROM assistente_social a
				left join pessoas b ON b.username = a.usuario
				left join atendimentos c on c.transacao = a.atendimento_id
				left join pessoas d on d.pessoa_id = c.paciente_id
			WHERE a.assistente_social_id =' . $transacao . ' order by 1 desc';
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);

class PDF extends FPDF
{
	public function Header()
	{
		$this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
		$this->Ln(15);
	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, utf8_decode('EVOLUÇÃO DIÁRIA MULTIPROFISSIONAL'), 0, 0, 'C');
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 10, utf8_decode('Paciente: ' . ts_decodifica($row->paciente)), 0, 0, 'L');
$pdf->Ln(7);
$pdf->Cell(190, 10, utf8_decode('Registro: ' . $row->pessoa_id), 0, 0, 'L');
$pdf->Ln(7);
$pdf->Cell(100, 10, utf8_decode('Unidade: ' . UNIDADE_CONFIG), 0, 0, 'L');
$pdf->Cell(190, 10, utf8_decode('Leito: ________________________________'), 0, 0, 'L');
$pdf->Ln(15);
$pdf->Cell(190, 10, inverteData($row->data), 0, 0, 'L');
$pdf->Ln(15);
$pdf->Multicell(190, 10, utf8_decode($row->relatorio), 0, 'J');
$pdf->Ln(15);
$pdf->Cell(190, 10, utf8_decode('_____________________________________'), 0, 0, 'C');
$pdf->Ln(5);
$pdf->Cell(190, 10, utf8_decode(ts_decodifica($row->nome)), 0, 0, 'C');
$pdf->Output();
