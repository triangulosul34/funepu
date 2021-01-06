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
		$this->Cell(185, 7, utf8_decode('RELATÓRIO DE PRESENÇA MÉDICA'), 1, 0, 'C');
		$this->Ln(7);
		$this->Cell(185, 7, utf8_decode('Período:   ' . inverteData($_GET['start'])), 1, 0, 'C');
		$this->Ln(7);
	}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$pdf->SetFont('Arial', 'BI', 12);
$pdf->Cell(185, 7, utf8_decode('ATENDIMENTO EXTERNO'), 1, 0, 'C');
$pdf->Ln(8);

//TODOS///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$totalAT = 0;
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(20, 8, utf8_decode('Data'), 1, 0, 'C');
$pdf->Cell(70, 8, utf8_decode('Profissional'), 1, 0, 'C');
$pdf->Cell(10, 8, utf8_decode('Login'), 1, 0, 'C');

$pdf->Cell(25, 8, utf8_decode('Atendimentos'), 1, 0, 'C');
$pdf->Cell(20, 8, utf8_decode('Entrada'), 1, 0, 'C');
$pdf->Cell(20, 8, utf8_decode('Saida'), 1, 0, 'C');
$pdf->Cell(20, 8, utf8_decode('Checagem'), 1, 0, 'C');

include 'conexao.php';
$stmt1 = "SELECT a.med_atendimento,nome, p.pessoa_id, count(*) as qtd,dat_cad,
		CASE
		WHEN a.hora_destino >= '01:00' and a.hora_destino <'07:00'   THEN '01h'
		WHEN a.hora_destino >= '07:00' and a.hora_destino <'13:00'   THEN '07h'
		WHEN a.hora_destino >= '13:00' and a.hora_destino <'19:00'   THEN '13h'
		WHEN a.hora_destino >= '19:00' 	 THEN '19h'
		END as entrada,
		CASE
		WHEN a.hora_destino >= '01:00' and a.hora_destino <'07:00'   THEN '07:00'
		WHEN a.hora_destino >= '07:00' and a.hora_destino <'13:00'   THEN '13:00'
		WHEN a.hora_destino >= '13:00' and a.hora_destino <'19:00'   THEN '19:00'
		WHEN a.hora_destino >= '19:00' 	 THEN '01:00'
		END as saida,
		(select min(hora) from logs l where a.med_atendimento = l.usuario and (l.data='$start')   ) as login 
		FROM atendimentos a 
		left join pessoas p on p.username = a.med_atendimento
		WHERE nome is not null AND status = 'Atendimento Finalizado' $where and (substring(dat_cad::varchar,0,11) || ' ' || a.hora_destino)::timestamp between '$start 01:00' and '" . date('Y-m-d', strtotime('+1 days', strtotime($end))) . " 01:00'								
		group by 1,2,3,5,6,7
		order by nome";
$sth1 = pg_query($stmt1);

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 7);

$i = 0;
$meuarray = [];

while ($stmt1 = pg_fetch_object($sth1)) {
	$meuarray[$i++] = $stmt1->pessoa_id;

	if ($stmt1->nome <> '') {
		$pdf->Cell(20, 8, date('d/m/Y', strtotime($stmt1->dat_cad)), 1, 0, 'C');
		$pdf->Cell(70, 8, utf8_decode(substr(ts_decodifica($stmt1->nome), 0, 35)), 1, 0, 'L');
		$pdf->Cell(10, 8, utf8_decode($stmt1->login), 1, 0, 'C');
		$pdf->Cell(25, 8, utf8_decode($stmt1->qtd), 1, 0, 'C');
		$pdf->Cell(20, 8, utf8_decode($stmt1->entrada), 1, 0, 'C');
		$pdf->Cell(20, 8, utf8_decode($stmt1->saida), 1, 0, 'C');
		$pdf->Cell(20, 8, utf8_decode(''), 1, 0, 'C');
		$pdf->Ln(8);
	}
}

foreach ($meuarray as $value) {
	if ($leo == '') {
		$leo .= $value;
	} else {
		$leo .= ',' . $value;
	}
}

$pdf->AddPage();
$pdf->SetFont('Arial', 'BI', 12);
$pdf->Cell(185, 7, utf8_decode('ATENDIMENTO INTERNO'), 1, 0, 'C');

include 'conexao.php';
$stmt2 = "SELECT nome, p.pessoa_id, count (*) as qtd, e.usuario, e.data,
				CASE
				WHEN e.hora >= '01:00' and e.hora <'07:00'   THEN '01h'
				WHEN e.hora >= '07:00' and e.hora <'13:00'   THEN '07h'
				WHEN e.hora >= '13:00' and e.hora <'19:00'   THEN '13h'
				WHEN e.hora >= '19:00' 	 THEN '19h'
				END as entrada,
				CASE
				WHEN e.hora >= '01:00' and e.hora <'07:00'   THEN '07:00'
				WHEN e.hora >= '07:00' and e.hora <'13:00'   THEN '13:00'
				WHEN e.hora >= '13:00' and e.hora <'19:00'   THEN '19:00'
				WHEN e.hora >= '19:00' 	 THEN '01:00'
				END as saida,
				(select min(hora) from logs l where e.usuario = l.usuario and (l.data='$start')   ) as login 
				FROM evolucoes e
				left join pessoas p on p.username = e.usuario 			
				where nome is not null and (substring(data::varchar,0,11) || ' ' || e.hora)::timestamp between '$start 01:00' and '" . date('Y-m-d', strtotime('+1 days', strtotime($end))) . " 01:00' $where				
				group by 1,2,4,5,6,7
				order by nome";
$sth2 = pg_query($stmt2);

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 7);

while ($stmt2 = pg_fetch_object($sth2)) {
	if ($stmt2->nome <> '') {
		$pdf->Cell(20, 8, date('d/m/Y', strtotime($stmt2->data)), 1, 0, 'C');
		$pdf->Cell(70, 8, utf8_decode(substr(ts_decodifica($stmt2->nome), 0, 35)), 1, 0, 'L');
		$pdf->Cell(10, 8, utf8_decode($stmt2->login), 1, 0, 'C');
		$pdf->Cell(25, 8, utf8_decode($stmt2->qtd), 1, 0, 'C');
		$pdf->Cell(20, 8, utf8_decode($stmt2->entrada), 1, 0, 'C');
		$pdf->Cell(20, 8, utf8_decode($stmt2->saida), 1, 0, 'C');
		$pdf->Cell(20, 8, utf8_decode(''), 1, 0, 'C');
		$pdf->Ln(8);
	}
}

$pdf->Output();
