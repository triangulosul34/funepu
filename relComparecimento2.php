<?php
$nome = $_POST['nome_comparecimento'];
$identidade = $_POST['identidade_comparecimento'];
$data_atendimento = $_POST['data_atendimento'];
$hora_atendimento = $_POST['hora_atendimento'];
$hora_final = $_POST['hora_final'];
$dia = substr($data_atendimento,0,2);
$mes = substr($data_atendimento,3,2);
$ano = substr($data_atendimento,6,4);
require('fpdf/fpdf.php');
class PDF extends FPDF
{
	function Header()
	{
		global $nome, $identidade, $data_atendimento, $hora_atendimento, $hora_final, $dia, $mes, $ano;
		$this->Line(5, 3, 5, 20);
		$this->Line(60, 3, 60, 20);
		$this->Line(5, 3, 205, 3);
		$this->Line(5, 20, 205, 20);
		$this->Line(205, 3, 205, 20);
		$this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
		$this->SetFont('Arial', 'B', 14);
		$this->Cell(244, 3, utf8_decode('DECLARAÇÃO DE COMPARECIMENTO'), 0, 0, 'C');
		$this->Ln(40);
		$this->SetFont('Arial', '', 12);
		$this->Cell(0, 2, utf8_decode('Declaro que  ' . $nome), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('portador do documento de Identidade nº ' . $identidade . '____________________________________'), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('permaneceu nesta Unidade no dia   ' . $dia . '   de    ' . $mes . '   de   ' . $ano . ', de   ' . $hora_atendimento . '   às   ' . $hora_final . '   horas, '), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('para o fim de:____________________________________________________________________'), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('_______________________________________________________________________________'), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('_______________________________________________________________________________'), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('_______________________________________________________________________________'), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('_______________________________________________________________________________'), 0, 0, 'L');
		$this->Ln(10);
		$this->Cell(0, 2, utf8_decode('_______________________________________________________________________________'), 0, 0, 'L');
		$this->Ln(50);
		$this->Cell(0, 2, utf8_decode('Uberaba, ______/______/______'), 0, 0, 'L');
		$this->Ln(50);
		$this->Cell(0, 2, utf8_decode('                                    ____________________________________________'), 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Ln(5);
		$this->Cell(0, 2, utf8_decode('                                                                                          Assinatura do médico'), 0, 0, 'L');
	}
}
$pdf = new PDF();
$pdf->AddPage();
$pdf->Output();
