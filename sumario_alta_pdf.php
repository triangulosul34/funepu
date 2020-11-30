<?php

require("../vendor/autoload.php");
require('fpdf/fpdf.php');

$atendimento_id = $_GET['atendimento_id'];

include('conexao.php');
$sql="SELECT * FROM sumario_alta WHERE atendimento_id = $atendimento_id";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);
$responsavel_sumario = $row->responsavel_sumario;
$crm_sumario = $row->crm_sumario;

class PDF extends FPDF
{
    // Page header
    public function Header()
    {
        $this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
        $this->Image('app-assets/img/gallery/sus.jpg', 170, 5, 22);
    }

    public function Footer()
    {
        global $responsavel_sumario,$crm_sumario;
        if ($this->isFinished) {
            $this->SetY(-15);
            $this->SetFont('Arial', '', 10);
            $this->SetX(40);
            $this->Cell(180, 5, '_____________________________________________________________', 0, 1, 'C');
            $this->SetX(40);
            $this->Cell(180, 5, 'Dr. '.$responsavel_sumario.' CRM: '.$crm_sumario, 0, 1, 'C');
        }
    }
}
$pdf=new PDF('P', 'mm', array(210,297));
$pdf->SetMargins(10, 20);
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 14);
$pdf->setXY(0, 20);
$pdf->Cell(210, 10, utf8_decode('SUMÁRIO DE ALTA'), 0, 1, 'C');
$pdf->ln(5);
$pdf->setX(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 6, utf8_decode('1. IDENTIFICAÇÃO'), 1, 1, 'L');
$pdf->ln(1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(37, 5, 'Nome/Nome Social:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(75, 5, $row->nome_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 5, 'CNS:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(34, 5, $row->cns_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(21, 5, utf8_decode('Prontúario:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(12, 5, $row->prontuario_sumario, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(12, 5, 'Idade:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(23, 5, $row->idade_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(33, 5, 'Data Nascimento:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(25, 5, $row->data_nascimento_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(11, 5, 'Sexo:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(27, 5, $row->sexo_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(27, 5, 'Unidade/Leito:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(31, 5, $row->unidade_sumario, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(27, 5, utf8_decode('Nome da Mãe:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(95, 5, $row->nome_mae_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(28, 5, 'Especialidade:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(40, 5, $row->especialidade_sumario, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(47, 5, 'Modalidade Assistencial:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(58, 5, utf8_decode($row->modalidade_assistencial_sumario), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(25, 5, utf8_decode('Procedência:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 5, $row->procedencia_sumario, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(41, 5, utf8_decode('Data/Hora internação:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(35, 5, $row->internacao_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(28, 5, utf8_decode('Data/Hora alta:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(33, 5, $row->alta_sumario, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(36, 5, utf8_decode('Caráter Internação:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(35, 5, $row->carater_internacao_sumario, 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(26, 5, utf8_decode('Permanência:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(15, 5, $row->permanencia_sumario.' dias', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(32, 5, utf8_decode('Resposável Alta:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 5, $row->responsavel_sumario, 0, 1, 'L');
$pdf->ln(5);
$pdf->setX(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 6, utf8_decode('2. DIAGNÓSTICOS'), 1, 1, 'L');
$pdf->ln(1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 5, utf8_decode($row->diagnostico));
$pdf->ln(5);
$pdf->setX(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 6, utf8_decode('3. PROCEDIMENTOS TERAPÊUTICOS'), 1, 1, 'L');
$pdf->ln(1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 5, utf8_decode($row->procedimento_terapeutico));
$pdf->ln(5);
$pdf->setX(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 6, utf8_decode('4. EVOLUÇÃO'), 1, 1, 'L');
$pdf->ln(1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 5, utf8_decode($row->evolucap));
$pdf->ln(5);
$pdf->setX(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 6, utf8_decode('5. PLANO PÓS-ALTA'), 1, 1, 'L');
$pdf->ln(1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 5, utf8_decode($row->pos_alta));
$pdf->ln(5);
$pdf->setX(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 6, utf8_decode('6. SEGUIMENTO DO ATENDIMENTO'), 1, 1, 'L');
$pdf->ln(1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 5, utf8_decode($row->segmento_atendimento));
$pdf->ln(5);
$pdf->setX(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 6, utf8_decode('6. ESTADO DO PACIENTE NA ALTA'), 1, 1, 'L');
$pdf->ln(1);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 5, utf8_decode($row->estado_paciente));
$pdf->isFinished = true;
$pdf->Output();
