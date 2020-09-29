<?php
$nome = $_POST['nome_comparecimento'];
$identidade = $_POST['identidade_comparecimento'];
$data_atendimento = $_POST['data_atendimento'];
$hora_atendimento = $_POST['hora_atendimento'];
$hora_final = $_POST['hora_final'];
$dia = substr($data_atendimento, 0, 2);
$mes = substr($data_atendimento, 3, 2);
$ano = substr($data_atendimento, 6, 4);
$relato = $_POST['relato_comparecimento'];
require('fpdf/fpdf.php');
class PDF extends FPDF
{
    function Header()
    {
        global $nome, $identidade, $data_atendimento, $hora_atendimento, $hora_final, $dia, $mes, $ano;
        $this->Image('app-assets/img/gallery/upa24.png', 8, 1, 40, 50);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(40, 30, '', 1, 0, 'L');
        $this->Cell(110, 30, utf8_decode('Declaração de Comparecimento'), 1, 0, 'C');
        $this->Cell(40, 30, '', 1, 0, 'L');
    }

    function Footer()
    {
        $this->Image('app-assets/img/gallery/sus2.png', 10, 250, 30);
        $this->Image('app-assets/img/gallery/logo_uberaba.png', 62, 250, 30);
        $this->Image('app-assets/img/gallery/logo_governo_minas.png', 114, 250, 30);
        $this->Image('app-assets/img/gallery/logo_brasil.png', 166, 250, 30);
    }
}
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 14);
$pdf->Line(10, 45, 10, 220);
$pdf->Line(10, 45, 200, 45);
$pdf->Line(200, 45, 200, 220);
$pdf->Line(10, 220, 200, 220);
$pdf->setY(50);
$pdf->setX(15);
$pdf->Cell(30, 5, utf8_decode('Declaro que '), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(155, 5, utf8_decode($nome), 0, 0, 'L');
$pdf->ln(10);
$pdf->SetFont('Arial', '', 14);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('portador do documento de Identidade nº ' . $identidade), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(78, 5, utf8_decode('permaneceu nesta Unidade no dia  '), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(8, 5, utf8_decode($dia), 0, 0, 'L');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(8, 5, utf8_decode('do'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(8, 5, utf8_decode($mes), 0, 0, 'L');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(8, 5, utf8_decode('de'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(11, 5, utf8_decode($ano), 0, 0, 'L');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(9, 5, utf8_decode(',de'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(14, 5, utf8_decode($hora_atendimento), 0, 0, 'L');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(6, 5, utf8_decode('às'), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(14, 5, utf8_decode($hora_final), 0, 0, 'L');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(8, 5, utf8_decode('horas,'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('para o fim de:_______________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(10);
$pdf->setX(15);
$pdf->Cell(155, 5, utf8_decode('__________________________________________________________________'), 0, 0, 'L');
$pdf->ln(20);
$pdf->setX(15);
$pdf->Cell(180, 5, utf8_decode('________________________________________'), 0, 0, 'C');
$pdf->ln(5);
$pdf->setX(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(180, 5, utf8_decode('Assinatura'), 0, 0, 'C');
$pdf->setY(88);
$pdf->setX(15);
$pdf->MultiCell(180, 10, utf8_decode($relato), 0, 'J');
$pdf->Output();
