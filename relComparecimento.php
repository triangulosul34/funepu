<?php
require('fpdf/fpdf.php');
class PDF extends FPDF{
	function Header(){
		$this->Line(5, 3, 5, 20);
		$this->Line(60, 3, 60, 20);
		$this->Line(5, 3, 205, 3);
		$this->Line(5, 20, 205, 20);
		$this->Line(205, 3, 205, 20);
		$this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
		$this->SetFont('Arial','B',14);
		$this->Cell(244,3,utf8_decode('DECLARAÇÃO DE COMPARECIMENTO'), 0,0,'C');
		$this->Ln(40);
		$this->SetFont('Arial','',12);
		$this->Cell(0,2,utf8_decode('Declaro que _____________________________________________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('_______________________________________________________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('portador do documento de Identidade nº_______________________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('permaneceu nesta Unidade no dia________de ____________________________de__________,'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('de___________às___________horas, para o fim de:_____________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('_______________________________________________________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('_______________________________________________________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('_______________________________________________________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('_______________________________________________________________________________'),0,0,'L');
		$this->Ln(10);
		$this->Cell(0,2,utf8_decode('_______________________________________________________________________________'),0,0,'L');
		$this->Ln(50);
		$this->Cell(0,2,utf8_decode('Uberaba, ______/______/______'),0,0,'L');
		$this->Ln(50);
		$this->Cell(0,2,utf8_decode('                                    ____________________________________________'),0,0,'L');
		$this->SetFont('Arial','',9);
		$this->Ln(5);
		$this->Cell(0,2,utf8_decode('                                                                                          Assinatura do médico'),0,0,'L');
	}
}
$pdf = new PDF();
$pdf->AddPage();
$pdf->Output();
