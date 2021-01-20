<?php

require '../vendor/autoload.php';
require 'fpdf/fpdf.php';
require 'tsul_ssl.php';
function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}

$i = 0;
$start = $_GET['start'];
$end = $_GET['end'];
$medico = $_GET['medico'];

if ($medico) {
	$where = " and pessoa_id = $medico";
}

if ($end == '') {
	$end = $start;
}
$profissional = $_GET['profissional'];

include 'verifica.php';

class PDF extends FPDF
{
	// Page header
	public function Header()
	{
		// Logo
		global $transacao, $prontuario, $nome, $nome_mae, $sexo, $idade, $nome_convenio, $origem, $enfermaria, $leito, $solicitante, $dt_nasc,
			$telsolicitante, $senha, $dt_solicitacao, $enderecox, $end_numero, $complemento, $bairro, $cep, $cpf, $cidade, $estado, $telefone, $celular,
			$oque_faz,	$com_oqfaz, $tempo_faz,	$como_faz, $queixa, $exame_fisico, $cid_principal, $pressaodiastolica, $pressaosistolica, $peso,
			$temperatura, $pulso, $relato, $discriminador, $destino, $prioridade, $atendprioridade, $cns, $diagnostico_principal, $horacad, $datacad, $glicemia,
			$data_destino, $hora_destino, $destino_paciente, $dor, $oxigenio, $nomecad, $medico_atendimento, $nome_social, $origem_chegada, $acompanhante, $tipo_relatorio, $i;
		$this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
		$this->Image('app-assets/img/gallery/sus.jpg', 80, 3, 22);
		$this->Ln(6);
		$this->SetFont('Arial', '', 9);
		$this->Cell(185, 5, 'SECRETARIA MUNICIPAL DE SAUDE - UBERABA', 0, 0, 'C');
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
		$this->Cell(20, 5, ' UNIDADE:', 0, 0, 'R');
		$this->SetFont('Arial', '', 9);
		$this->Cell(120, 5, 'UNIDADE DE PRONTO ATENDIMENTO ' . utf8_decode(UNIDADE_CONFIG), 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, 5, ' CNES:', 0, 'L');
		$this->Cell(40, 5, '7093284', 0, 0, 'L');
		$this->Ln(5);
		$this->Ln(7);
		$this->SetFont('Arial', 'BI', 12);
		$this->Cell(185, 7, utf8_decode('RELATÓRIO DE COMPARECIMENTO MÉDIC0'), 1, 0, 'C');
		$this->Ln(7);
		$this->Cell(185, 7, utf8_decode('Período:   ' . inverteData($_GET['start']).' a '.inverteData($_GET['end'])), 1, 0, 'C');
		$this->Ln(7);
	}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$pdf->SetFont('Arial', 'BI', 12);
$pdf->Cell(185, 7, utf8_decode('COMPARECIMENTO MEDICO'), 1, 0, 'C');
$pdf->Ln(8);

//TODOS///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$totalAT = 0;
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(20, 8, utf8_decode('Data'), 1, 0, 'C');
$pdf->Cell(120, 8, utf8_decode('Profissional'), 1, 0, 'C');
$pdf->Cell(22, 8, utf8_decode('Login'), 1, 0, 'C');
$pdf->Cell(23, 8, utf8_decode('Logout'), 1, 0, 'C');


include 'conexao.php';
$stmt1 = "select l.data, p.nome,l.usuario,p.perfil, min(l.hora) as login, max(l.hora) as logout from logs l
left join pessoas p on p.username = l.usuario
where data between '$start' and '$end' $where and p.perfil = '03' and username is not null
group by 1,2,3,4";
$sth1 = pg_query($stmt1);

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 7);

$i = 0;
$meuarray = [];

while ($stmt1 = pg_fetch_object($sth1)) {


	if ($stmt1->nome <> '') {
		$pdf->Cell(20, 8, date('d/m/Y', strtotime($stmt1->data)), 1, 0, 'C');
		$pdf->Cell(120, 8, utf8_decode(substr(ts_decodifica($stmt1->nome), 0, 35)), 1, 0, 'L');
		$pdf->Cell(22, 8, utf8_decode($stmt1->login), 1, 0, 'C');
		$pdf->Cell(23, 8, utf8_decode($stmt1->logout), 1, 0, 'C');
		$pdf->Ln(8);
	}
}



$pdf->Output();
