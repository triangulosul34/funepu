<?php
require("../vendor/autoload.php");
require('fpdf/fpdf.php');
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}
$i = 0;
$start          = $_GET['start'];
$end          = $_GET['end'];
$profissional = $_GET['profissional'];

include('verifica.php');

class PDF extends FPDF
{
    // Page header
    public function Header()
    {

        // Logo
        global $transacao, $prontuario, $nome, $nome_mae, $sexo, $idade, $nome_convenio, $origem, $enfermaria, $leito, $solicitante, $dt_nasc,
            $telsolicitante, $senha, $dt_solicitacao, $enderecox, $end_numero, $complemento, $bairro, $cep, $cpf, $cidade, $estado, $telefone, $celular,
            $oque_faz,    $com_oqfaz, $tempo_faz,    $como_faz, $queixa, $exame_fisico, $cid_principal, $pressaodiastolica, $pressaosistolica, $peso,
            $temperatura, $pulso, $relato, $discriminador, $destino, $prioridade, $atendprioridade, $cns, $diagnostico_principal, $horacad, $datacad, $glicemia,
            $data_destino, $hora_destino, $destino_paciente, $dor, $oxigenio, $nomecad, $medico_atendimento, $nome_social, $origem_chegada, $acompanhante, $tipo_relatorio, $i;
        $this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
        $this->Image('app-assets/img/gallery/sus.jpg', 80, 3, 22);
        $this->Ln(6);
        $this->SetFont('Arial', '', 9);
        $this->Cell(185, 5, 'SECRETARIA MUNICIPAL DE SAUDE-UBERABA', 0, 0, 'C');
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(185, 5, UTF8_DECODE(''), 0, 0, 'C');
        // Arial bold 15
        $this->SetFont('Arial', 'B', 14);
        // Move to the right
        $this->Cell(50);
        $this->SetFont('Arial', 'B', 20);
        $this->Ln(6);
        //$this->Cell(300,5,str_pad($transacao,7,"0",STR_PAD_LEFT),0,0,'C');
        $this->SetFont('Arial', 'B', 7);
        $this->Cell(300, 5, $senha, 0, 0, 'C');
        $this->Ln(1);
        $this->Line(10, 25, 195, 25);





        $this->Line(10, 275.8, 195, 275.8);

        $this->Line(10, 25, 10, 275.8);
        $this->Line(195, 25, 195, 275.8);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 5, ' UNIDADE.:', 0, 0, 'R');
        $this->SetFont('Arial', '', 9);


        $this->Cell(120, 5, 'UNIDADE DE PRONTO ATENDIMENTO '. utf8_decode(UNIDADE_CONFIG), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 5, ' CNES:', 0, 'L');
        $this->Cell(40, 5, '2164817', 0, 0, 'L');
        $this->Ln(5);
        $this->Ln(7);

        $this->SetFont('Arial', 'BI', 12);




        if ($tipo_relatorio == '') {
            include('conexao.php');
            $stmtRelCont = "SELECT count(*) as qtde from atendimentos a
				where destino_paciente in ('01','11', '02', '12','14','15','06', '03', '07') and dat_cad between '$start' and '$end'";
            $sthRelCont = pg_query($stmtRelCont);
            $rowCount = pg_fetch_object($sthRelCont);
            $qtde_atendimentos = $rowCount->qtde;

            $this->Cell(185, 7, utf8_decode('RELATÓRIO DE PRODUCAO MEDICA / ATENDIMENTOS'), 1, 0, 'C');
            $this->Ln(7);
            $this->Cell(185, 7, utf8_decode('Período:   ' . $_GET['start'] . '   ATÉ   ' . $_GET['end']), 1, 0, 'C');
        }

        $this->Ln(7);
    }
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);


//TODOS///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$totalAT = 0;

$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(13, 8, utf8_decode('Data'), 1, 0, 'C');
$pdf->Cell(53, 8, utf8_decode('Empresa'), 1, 0, 'C');
$pdf->Cell(57, 8, utf8_decode('Profissional'), 1, 0, 'C');
$pdf->Cell(17, 8, utf8_decode('Atendimentos'), 1, 0, 'C');
$pdf->Cell(15, 8, utf8_decode('Entrada'), 1, 0, 'C');
$pdf->Cell(15, 8, utf8_decode('Saida'), 1, 0, 'C');
$pdf->Cell(15, 8, utf8_decode('Checagem'), 1, 0, 'C');

include('conexao.php');
$stmtRel = "SELECT a.dat_cad, p.nome, p.tipo_pessoa, p.empresa, count(*) as qtde
				from atendimentos a left join pessoas p on a.med_atendimento=p.username where p.tipo_pessoa='Medico Laudador' and a.dat_cad between '$start' and '$end' ";
if ($profissional != "") {
    $stmtRel = $stmtRel . " and a.med_atendimento='$profissional'";
} else {
    $stmtRelCont = $stmtRelCont . " and a.med_atendimento is not null ' ";
}
$stmtRel = $stmtRel . "	group by 1,2,3,4 order by 1,2,3";
$sthRel = pg_query($stmtRel);
include('conexao.php');
$stmtRelCont = "SELECT count(*) as qtde from atendimentos a left join pessoas p on a.med_atendimento=p.username where a.dat_cad between '$start' and '$end'";
if ($profissional != "") {
    $stmtRelCont = $stmtRelCont . " and a.med_atendimento='$profissional' ";
} else {
    $stmtRelCont = $stmtRelCont . " and a.med_atendimento is not null ' ";
}
$sthRelCont = pg_query($stmtRelCont);
$rowCount = pg_fetch_object($sthRelCont);
$qtde_atendimentos = $rowCount->qtde;
$pdf->Ln(8);
$pdf->SetFont('Arial', '', 7);

$data = '';
while ($rowRel = pg_fetch_object($sthRel)) {
    if ($rowRel->nome <> '') {
        if ($data == '' or strtotime($data) != strtotime(date('d/m/Y', strtotime($rowRel->dat_cad)))) {
            if ($data != '') {
                $pdf->addPage();
            }
            $data = date('d/m/Y', strtotime($rowRel->dat_cad));
        }
        $pdf->Cell(13, 8, $data, 1, 0, 'C');
        $pdf->Cell(53, 8, utf8_decode($rowRel->tipo_pessoa), 1, 0, 'L');
        $pdf->Cell(57, 8, utf8_decode(substr($rowRel->nome, 0, 35)), 1, 0, 'L');
        $pdf->Cell(17, 8, utf8_decode($rowRel->qtde), 1, 0, 'C');
        $pdf->Cell(15, 8, utf8_decode(''), 1, 0, 'C');
        $pdf->Cell(15, 8, utf8_decode(''), 1, 0, 'C');
        $pdf->Cell(15, 8, utf8_decode(''), 1, 0, 'C');
        $pdf->Ln(8);
    }
}






$pdf->Output();
