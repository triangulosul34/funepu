<?php
require('fpdf/fpdf.php');
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}
$id = $_GET['id'];

include('conexao_laboratorio.php');
$stmt = "SELECT distinct a.pedido_id, a.data, c.medico_solicitante_id, a.horario, b.pessoa_id, b.nome, b.data_nascimento, b.sexo, c.origem, d.exame_id, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, d.situacao, e.descricao 
FROM pedidos a
INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
LEFT JOIN modalidades f ON f.modalidade_id = e.setor where pedido_item_id in ($id) order by a.data, a.horario";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);

$transacao = $row->pedido_id;
$nome_paciente = $row->nome;
$dt_nasc = $row->data_nascimento;
$sexo = $row->sexo;
if ($sexo == '2') {
    $sexo = 'Feminino';
}
if ($sexo == '1') {
    $sexo = 'Masculino';
}
$origem = UNIDADE_CONFIG;
// $origem = $row->origem;
// if ($origem == '02') {
//     $origem = 'UPA SAO BENEDITO';
// } else if ($origem == '01') {
//     $origem = 'UPA MIRANTE';
// }
$dt_solicitacao = inverteData($row->data);

//$solicitante;

class PDF extends FPDF
{
    // Page header
    function Header()
    {

        global $transacao, $nome_paciente, $dt_nasc, $sexo, $origem, $dt_solicitacao;
        $this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 14);
        // Move to the right
        $this->Cell(50);
        // Title
        //$this->Cell(200,5,'Pedido Numero',0,0,'C');
        // Line break
        $this->SetFont('Arial', 'B', 20);
        $this->Ln(7);
        //$this->Cell(300,5,str_pad($transacao,7,"0",STR_PAD_LEFT),0,0,'C');
        $this->SetFont('Arial', 'B', 7);
        $this->Ln(5);
        $this->Cell(65, 5, 'Conde Prados, 211 - Bairro Abadia', 0, 0, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Ln(2);
        $this->Cell(300, 5, '', 0, 0, 'C');
        $this->SetFont('Arial', 'B', 7);
        $this->Ln(1);
        $this->Cell(65, 5, ' CEP: 38025-360 - Uberaba/MG', 0, 0, 'C');

        $this->Ln(7);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(15, 5, ' NOME.:', 1, 0, 'L');
        $this->SetFont('Arial', 'BI', 9);
        $this->Cell(79, 5, utf8_decode($nome_paciente), 1, 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(8, 5, ' ID.:', 1, 0, 'L');
        $this->SetFont('Arial', 'BI', 9);
        $this->Cell(13, 5, '', 1, 0, 'L');
        $this->Cell(17, 5, ' Dt.Nasc.:', 1, 0, 'L');
        $this->SetFont('Arial', 'BI', 9);
        $this->Cell(20, 5, inverteData($dt_nasc), 1, 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(15, 5, ' SEXO.:', 1, 0, 'L');
        $this->SetFont('Arial', 'BI', 9);
        $this->Cell(18, 5, $sexo, 1, 0, 'L');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(32, 5, ' DT.SOLICITACAO:', 1, 0, 'L');
        $this->SetFont('Arial', 'BI', 9);
        $this->Cell(30, 5, $dt_solicitacao, 1, 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, 5, ' SOLICITANTE:', 1, 0, 'L');
        $this->SetFont('Arial', 'BI', 9);
        $this->Cell(98, 5, utf8_decode($solicitante), 1, 0, 'L');


        $this->Ln(5);
        $this->Cell(32, 5, ' PROCEDENCIA:', 1, 0, 'L');
        $this->Cell(80, 5, $origem, 1, 0, 'L');
        $this->Cell(25, 5, ' CATEGORIA:', 1, 0, 'L');
        $this->Cell(48, 5, $nome_convenio, 1, 0, 'L');
        $this->Ln(7);
        $this->Cell(185, 5, ' PEDIDO', 1, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Ln(5);
        $this->Cell(90, 5, ' EXAME:', 1, 0, 'C');
        $this->Cell(30, 5, ' CODIGO', 1, 0, 'C');
        $this->Cell(20, 5, ' A.N', 1, 0, 'C');
        $this->Cell(20, 5, ' QTD', 1, 0, 'C');
        $this->Cell(25, 5, ' AUTORIZACAO', 1, 0, 'C');
        $this->Ln(5);
    }

    // Page footer
    function Footer()
    {
        global $transacao, $usuario_transacao, $data_transacao, $hora_transacao;
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        //$this->Cell(0,10,'Transacao - '.str_pad($transacao,7,"0",STR_PAD_LEFT).' - '.$usuario_transacao.' '.$data_transacao.' '.$hora_transacao.'/{nb}',0,0,'C');
    }

    function Codabar($xpos, $ypos, $code, $start = 'A', $end = 'A', $basewidth = 0.35, $height = 10)
    {
        $barChar = array(
            '0' => array(6.5, 10.4, 6.5, 10.4, 6.5, 24.3, 17.9),
            '1' => array(6.5, 10.4, 6.5, 10.4, 17.9, 24.3, 6.5),
            '2' => array(6.5, 10.0, 6.5, 24.4, 6.5, 10.0, 18.6),
            '3' => array(17.9, 24.3, 6.5, 10.4, 6.5, 10.4, 6.5),
            '4' => array(6.5, 10.4, 17.9, 10.4, 6.5, 24.3, 6.5),
            '5' => array(17.9,    10.4, 6.5, 10.4, 6.5, 24.3, 6.5),
            '6' => array(6.5, 24.3, 6.5, 10.4, 6.5, 10.4, 17.9),
            '7' => array(6.5, 24.3, 6.5, 10.4, 17.9, 10.4, 6.5),
            '8' => array(6.5, 24.3, 17.9, 10.4, 6.5, 10.4, 6.5),
            '9' => array(18.6, 10.0, 6.5, 24.4, 6.5, 10.0, 6.5),
            '$' => array(6.5, 10.0, 18.6, 24.4, 6.5, 10.0, 6.5),
            '-' => array(6.5, 10.0, 6.5, 24.4, 18.6, 10.0, 6.5),
            ':' => array(16.7, 9.3, 6.5, 9.3, 16.7, 9.3, 14.7),
            '/' => array(14.7, 9.3, 16.7, 9.3, 6.5, 9.3, 16.7),
            '.' => array(13.6, 10.1, 14.9, 10.1, 17.2, 10.1, 6.5),
            '+' => array(6.5, 10.1, 17.2, 10.1, 14.9, 10.1, 13.6),
            'A' => array(6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
            'T' => array(6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
            'B' => array(6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
            'N' => array(6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
            'C' => array(6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
            '*' => array(6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
            'D' => array(6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
            'E' => array(6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
        );
        $this->SetFont('Arial', '', 13);
        $this->Text($xpos, $ypos + $height + 4, $code);
        $this->SetFillColor(0);
        $code = strtoupper($start . $code . $end);
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            if (!isset($barChar[$char])) {
                $this->Error('Invalid character in barcode: ' . $char);
            }
            $seq = $barChar[$char];
            for ($bar = 0; $bar < 7; $bar++) {
                $lineWidth = $basewidth * $seq[$bar] / 6.5;
                if ($bar % 2 == 0) {
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
            $xpos += $basewidth * 10.4 / 6.5;
        }
    }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);
include('conexao_laboratorio.php');
$stmt = "SELECT distinct e.descricao, e.codigo, a.data, a.horario
FROM pedidos a
INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
LEFT JOIN modalidades f ON f.modalidade_id = e.setor where pedido_item_id in ($id) order by a.data, a.horario";
$sth = pg_query($stmt) or die($stmt);


while ($row = pg_fetch_object($sth)) {

    $pdf->Cell(95, 6, utf8_decode($row->descricao), 0, 0, 'L');
    $pdf->Cell(25, 6, str_pad($row->codigo, 8, "0", STR_PAD_LEFT), 0, 0, 'C');
    $pdf->Cell(18, 6, '', 0, 0, 'C');
    $pdf->Cell(20, 6, '001', 0, 0, 'C');
    $pdf->Cell(35, 6, '', 0, 0, 'C');
    $pdf->Ln(6);
}




$pdf->Codabar(145, 12, $transacao);






$pdf->Output();
