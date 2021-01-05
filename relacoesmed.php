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

$i = 0;
$start = $_GET['start'];
$end = $_GET['end'];
$medico = $_GET['medico'];

if ($medico) {
	$where = " and usuario ='$medico' ";
}

if ($end == '') {
	$end = $start;
}

include 'conexao.php';
$stmt1 = "SELECT nome FROM  pessoas where username = '$medico' ";
$sth1 = pg_query($stmt1) or die($stmt1);

$row = pg_fetch_object($sth1);

$profissional = ts_decodifica($row->nome);

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
		$this->Cell(15, 5, ' ', 0, 'L');
		$this->Cell(40, 5, ' ', 0, 0, 'L');
		$this->Ln(5);
		$this->Ln(7);
		$this->SetFont('Arial', 'BI', 12);
		$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ACOES MÉDICA'), 1, 0, 'C');
		$this->Ln(7);
		$this->Cell(185, 7, utf8_decode('Período:   ' . invertedata($_GET['start']) . ' a ' . invertedata($_GET['end'])), 1, 0, 'C');
		$this->Ln(7);
	}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();

$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$pdf->SetFont('Arial', 'BI', 12);
$pdf->Cell(185, 7, utf8_decode('PROFISSIONAL:' . $profissional), 1, 0, 'C');
$pdf->Ln(8);

//TODOS///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$totalAT = 0;
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(20, 8, utf8_decode('Data'), 1, 0, 'C');
$pdf->Cell(10, 8, utf8_decode('Hora'), 1, 0, 'C');
$pdf->Cell(55, 8, utf8_decode('Profissional'), 1, 0, 'L');
$pdf->Cell(20, 8, utf8_decode('Atendimento'), 1, 0, 'C');
$pdf->Cell(80, 8, utf8_decode('Ação'), 1, 0, 'C');

include 'conexao.php';
$stmt1 = 'SELECT l.*, p.nome FROM logs l left join pessoas p on p.username = l.usuario';
$stmt1 = $stmt1 . " where ( l.data >= '$start' and l.data <= '$end' ) " . $where; ;

$stmt1 = $stmt1 . ' order by log_id, l.data desc,l.hora desc';
$sth1 = pg_query($stmt1) or die($stmt1);

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 7);

$i = 0;
$meuarray = [];

while ($stmt1 = pg_fetch_object($sth1)) {
	$meuarray[$i++] = $stmt1->pessoa_id;

	if (ts_decodifica($stmt1->nome) <> '') {
		$pdf->Cell(20, 8, date('d/m/Y', strtotime($stmt1->data)), 1, 0, 'C');
		$pdf->Cell(10, 8, utf8_decode(substr($stmt1->hora, 0, 35)), 1, 0, 'L');
		$pdf->Cell(55, 8, substr(utf8_decode(ts_decodifica($stmt1->nome)), 0, 35), 1, 0, 'L');
		$pdf->Cell(20, 8, utf8_decode($stmt1->atendimento_id), 1, 0, 'C');
		$pdf->Cell(80, 8, substr(utf8_decode($stmt1->tipo_acao), 0, 45), 1, 0, 'L');

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
$stmt2 = "select nome, p.pessoa_id, count (*) as qtd, e.usuario, e.data,
				CASE
				WHEN e.hora >= '00:00' and e.hora < '07:00' THEN '00h'
				WHEN e.hora >= '07:00' and e.hora < '13:00' THEN '07h'
				WHEN e.hora >= '13:00' and e.hora < '19:00' THEN '13h'
				WHEN e.hora >= '19:00' and e.hora < '00:00' THEN '19h'
				END as entrada,
				CASE
				WHEN e.hora >= '00:00' and e.hora < '07:00' THEN '06:59'
				WHEN e.hora >= '07:00' and e.hora < '13:00' THEN '12:59'
				WHEN e.hora >= '13:00' and e.hora < '19:00' THEN '18:59'
				WHEN e.hora >= '19:00' and e.hora < '00:00' THEN '23:59'
				END as saida,
				(select min(hora) from logs l where e.usuario = l.usuario and (l.data='$start')   ) as login 
				FROM evolucoes e
				left join pessoas p on p.username = e.usuario 			
				where nome is not null and data between '$start' and '$end' $where	and p.pessoa_id not in ($leo)				
				group by 1,2,4,5,6,7
				order by nome";
//$sth2 = pg_query($stmt2);

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 7);

while ($stmt2 = pg_fetch_object($sth2)) {
	if (ts_decodifica($stmt2->nome) <> '') {
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
