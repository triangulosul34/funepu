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

function Codabar($xpos, $ypos, $code, $start = 'A', $end = 'A', $basewidth = 0.35, $height = 10)
{
	$barChar = [
		'0' => [6.5, 10.4, 6.5, 10.4, 6.5, 24.3, 17.9],
		'1' => [6.5, 10.4, 6.5, 10.4, 17.9, 24.3, 6.5],
		'2' => [6.5, 10.0, 6.5, 24.4, 6.5, 10.0, 18.6],
		'3' => [17.9, 24.3, 6.5, 10.4, 6.5, 10.4, 6.5],
		'4' => [6.5, 10.4, 17.9, 10.4, 6.5, 24.3, 6.5],
		'5' => [17.9,    10.4, 6.5, 10.4, 6.5, 24.3, 6.5],
		'6' => [6.5, 24.3, 6.5, 10.4, 6.5, 10.4, 17.9],
		'7' => [6.5, 24.3, 6.5, 10.4, 17.9, 10.4, 6.5],
		'8' => [6.5, 24.3, 17.9, 10.4, 6.5, 10.4, 6.5],
		'9' => [18.6, 10.0, 6.5, 24.4, 6.5, 10.0, 6.5],
		'$' => [6.5, 10.0, 18.6, 24.4, 6.5, 10.0, 6.5],
		'-' => [6.5, 10.0, 6.5, 24.4, 18.6, 10.0, 6.5],
		':' => [16.7, 9.3, 6.5, 9.3, 16.7, 9.3, 14.7],
		'/' => [14.7, 9.3, 16.7, 9.3, 6.5, 9.3, 16.7],
		'.' => [13.6, 10.1, 14.9, 10.1, 17.2, 10.1, 6.5],
		'+' => [6.5, 10.1, 17.2, 10.1, 14.9, 10.1, 13.6],
		'A' => [6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5],
		'T' => [6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5],
		'B' => [6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6],
		'N' => [6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6],
		'C' => [6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6],
		'*' => [6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6],
		'D' => [6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5],
		'E' => [6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5],
	];
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

if (isset($_GET['id'])) {
	$transacao = $_GET['id'];

	$data = $_GET['data'];
	$hora = '';
	$sequencias = $_GET['sequencia'];
}

if (isset($_POST['transacao'])) {
	$transacao = $_POST['transacao'];
	$prescricao_id = $_POST['vl_prescricao'];
	$data = date('d/m/Y');
	$hora = date('H:i');
}

include 'verifica.php';
include 'conexao.php';

$stmt = "
		select a.transacao, a.cid_principal,a,hora_cad,l.nome as nomecad, a.destino_paciente, a.data_destino, a.queixa, a.exame_fisico, a.diagnostico_principal,a.prioridade,
		a.paciente_id, a.status, a.tipo, a.dat_cad as cadastro, c.nome,c.nome_social, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco, a.oque_faz, a.com_oqfaz, 
		a.tempo_faz, a.como_faz, c.numero, c.complemento, c.bairro, c.nome_mae, c.num_carteira_convenio, c.cep, c.cpf, c.cidade, c.estado, a.observacao, k.origem as origem_chegada,  
		x.peso, x.pressaodiastolica, x.pressaosistolica,x.oxigenio,x.dor, x.queixa as relato, x.pulso, x.temperatura,x.discriminador, x.prioridade as atendprioridade,x.glicose as glicemia,
		a.data_destino,a.hora_destino,a.destino_paciente, z.nome as medico_atendimento
		from atendimentos a 
		left join pessoas c on a.paciente_id=c.pessoa_id
		left join pessoas l on l.username = a.cad_user
		left join pessoas z on z.username = a.med_atendimento
		left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
		left join classificacao x ON ltrim(x.atendimento_id, '0')= '$transacao' 
		where a.transacao=$transacao";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$transacao = str_pad($transacao, 7, '0', STR_PAD_LEFT);
$data_transacao = substr($row->cadastro, 0, 10);
$hora_transacao = $row->hora_transacao;
$data_destino = $row->data_destino;
$hora_destino = $row->hora_destino;
$destino_paciente = $row->destino_paciente;
$horacad = $row->hora_cad;
$datacad = date('d/m/Y', strtotime($row->cadastro));
$prontuario = $row->paciente_id;
$dor = $row->dor;
$oxigenio = $row->oxigenio;
$sexo = $row->sexo;
$nome = ts_decodifica($row->nome);
$nome_social = $row->nome_social;
$email = $row->email;
$dt_nascimento = inverteData($row->dt_nasc);
$enderecox = $row->endereco;
$end_numero = $row->numero;
$complemento = $row->complemento;
$bairro = $row->bairro;
$cns = $row->num_carteira_convenio;
$cidade = $row->cidade;
$estado = $row->estado;
$nomecad = ts_decodifica($row->nomecad);
$cep = $row->cep;
$cpf = ts_decodifica($row->cpf);
$telefone = $row->telefone;
$celular = $row->celular;
$nome_mae = ts_decodifica($row->nome_mae);
$dt_nasc = $row->dt_nasc;
$date = new DateTime($dt_nasc); // data de nascimento
$interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
$idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
$procedimento = $row->procedimento_id;
$senha = $row->num_senha;
$deficiencia = $_POST['deficiencia'];
$origem = $row->origem;
$origem_chegada = $row->origem_chegada;
$deficiencia = $row->nec_especiais;
$observacao = $row->observacao;
$oque_faz = $row->oque_faz;
$com_oqfaz = $row->com_oqfaz;
$tempo_faz = $row->tempo_faz;
$como_faz = $row->como_faz;
$enfermaria = $row->enfermaria;
$leito = $row->leito;
$imagem = $row->imagem;
$origem = $row->tipo;
$queixa = $row->queixa;
$exame_fisico = $row->exame_fisico;
$cid_principal = $row->cid_principal;
$destino = $row->destino_paciente;
$pressaodiastolica = $row->pressaodiastolica;
$pressaosistolica = $row->pressaosistolica;
$peso = $row->peso;
$temperatura = $row->temperatura;
$pulso = $row->pulso;
$relato = $row->relato;
$discriminador = $row->discriminador;
$prioridade = $row->prioridade;
$atendprioridade = $row->atendprioridade;
$diagnostico_principal = $row->diagnostico_principal;
$glicemia = $row->glicemia;
$medico_atendimento = ts_decodifica($row->medico_atendimento);
$dia = date('d', strtotime($data_transacao));
$mes = date('m', strtotime($data_transacao));
$ano = date('Y', strtotime($data_transacao));
$semana = date('w', strtotime($data_transacao));

$data = date('Y-m-d');
$hora = date('H:i');
$atendimento_id = $_GET['id'];
$ip = $_SERVER['REMOTE_ADDR'];
include 'conexao.php';
$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora, ip) 
			values ('$usuario','GEROU PRESCRIÇÃO NA EVOLUÇÃO','$transacao','$data','$hora', '$ip')";
$sthLogs = pg_query($stmtLogs) or die($stmtLogs);

// configuração mes

switch ($mes) {
	case 1:
		$mes = 'Janeiro';
		break;
	case 2:
		$mes = 'Fevereiro';
		break;
	case 3:
		$mes = 'Março';
		break;
	case 4:
		$mes = 'Abril';
		break;
	case 5:
		$mes = 'Maio';
		break;
	case 6:
		$mes = 'Junho';
		break;
	case 7:
		$mes = 'Julho';
		break;
	case 8:
		$mes = 'Agosto';
		break;
	case 9:
		$mes = 'Setembro';
		break;
	case 10:
		$mes = 'Outubro';
		break;
	case 11:
		$mes = 'Novembro';
		break;
	case 12:
		$mes = 'Dezembro';
		break;
}
switch ($semana) {
	case 0:
		$semana = 'Domingo';
		break;
	case 1:
		$semana = 'Segunda Feira';
		break;
	case 2:
		$semana = 'Terça Feira';
		break;
	case 3:
		$semana = 'Quarta Feira';
		break;
	case 4:
		$semana = 'Quinta Feira';
		break;
	case 5:
		$semana = 'Sexta Feira';
		break;
	case 6:
		$semana = 'Sábado';
		break;
}

$data_dia = "$semana, $dia de $mes de $ano";

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
			$data_destino, $hora_destino, $destino_paciente, $dor, $oxigenio, $nomecad, $medico_atendimento, $nome_social, $origem_chegada, $prescricao_id, $data, $hora, $sequencias;

		$this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
		$this->Image('app-assets/img/gallery/sus.jpg', 80, 3, 22);
		$this->Ln(6);
		$this->SetFont('Arial', '', 9);
		$this->Cell(270, 5, 'SECRETARIA MUNICIPAL DE SAUDE-UBERABA', 0, 0, 'C');
		$this->Ln(4);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(155, 5, UTF8_DECODE('PRESCRIÇÃO MÉDICA'), 0, 0, 'R');
		$this->Cell(190, 5, UTF8_DECODE($prioridade), 0, 0, 'C');
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

		//TOPO
		$this->Line(10, 25, 285, 25);

		//ESQUERDA
		$this->Line(10, 25, 10, 200);

		//DIREITA
		$this->Line(285, 25, 285, 200);

		//BAIXO
		$this->Line(10, 200, 285, 200);

		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, ' UNIDADE.:', 0, 0, 'R');
		$this->SetFont('Arial', '', 9);

		$this->Cell(97, 5, 'UNIDADE DE PRONTO ATENDIMENTO ' . utf8_decode(UNIDADE_CONFIG), 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(12, 5, ' CNES:', 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(12, 5, '2164817', 0, 0, 'L');

		include 'conexao.php';
		$sql = "select * from prescricoes where atendimento_id = $transacao";
		$stl = pg_query($sql) or die($sql);
		$rowd = pg_fetch_object($stl);

		$this->SetFont('Arial', '', 9);
		$this->Cell(20, 5, ' DATA:', 0, 0, 'R');
		$this->SetFont('Arial', '', 9);
		//$this->Cell(25,5, date('d/m/Y'),0,0,'L');
		$this->Cell(25, 5, inverteData($rowd->data), 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, 5, 'HORA:', 0, 'R');
		$this->SetFont('Arial', '', 9);
		//$this->Cell(20,5,date('H:i:s'),0,0,'L');
		$this->Cell(20, 5, $rowd->hora, 0, 0, 'L');

		$this->SetFont('Arial', '', 9);
		$this->Cell(18, 5, 'Prontuario:', 0, 'R');
		$this->SetFont('Arial', '', 9);
		$this->Cell(20, 5, str_pad($prontuario, 7, '0', STR_PAD_LEFT), 0, 0, 'L');
		$this->Ln(5);
		$this->SetFont('Arial', 'BI', 12);

		$this->Cell(275, 8, utf8_decode('IDENTIFICAÇÃO DO PACIENTE'), 1, 0, 'C');
		$this->Ln(8);
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, 8, ' NOME:', 0, 0, 'L');
		if ($nome_social == '') {
			$this->SetFont('Arial', 'BI', 12);
			$this->Cell(111.5, 8, utf8_decode($nome), 0, 0, 'L');
		} else {
			$this->SetFont('Arial', 'BI', 9);
			$this->Cell(115, 8, utf8_decode($nome_social . ' (' . $nome . ')'), 0, 0, 'L');
		}
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, 8, utf8_decode('DIAGNÓSTICO:'), 0, 0, 'R');

		$this->Ln(7);
		$this->SetFont('Arial', '', 9);
		$this->Cell(25, 5, utf8_decode(' NOME DA MAE:'), 0, 0, 'L');
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(91, 5, utf8_decode($nome_mae), 0, 0, 'L');

		$this->SetFont('Arial', '', 9);

		$this->Cell(30, 5, utf8_decode('LEITO:'), 0, 0, 'L');
		$this->Cell(40, 5, utf8_decode('POSTO:'), 0, 0, 'L');
		$this->Cell(30, 5, utf8_decode('CÓDIGO DO POSTO:'), 0, 0, 'L');

		$this->Ln(7);
		$this->Cell(190, 5, utf8_decode(' ASSINATURA DO FARMACÊUTICO:______________________________________________________'), 0, 0, 'L');

		$this->Ln(10);
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(275, 5, UTF8_DECODE(' PRESCRIÇÃO '), 1, 0, 'C');
		$this->SetFont('Arial', '', 8);
		$this->Ln(5);

		$this->SetFont('Arial', 'BI', 8);
		$this->Cell(115, 5, UTF8_DECODE(' PRESCRIÇÃO '), 1, 0, 'C');
		$this->Cell(28, 5, UTF8_DECODE(' VIA '), 1, 0, 'C');

		$this->Cell(8.5, 5, UTF8_DECODE(' 12 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 14 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 16 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 18 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 20 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 22 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 00 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 02 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 04 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 06 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 08 '), 1, 0, 'C');
		$this->Cell(8.5, 5, UTF8_DECODE(' 10 '), 1, 0, 'C');

		$this->Cell(30, 5, UTF8_DECODE(' OBS. DA FARMÁCIA '), 1, 0, 'C');

		if ($this->page != 1) {
			$this->Ln(5);
		}
	}

	public function Footer()
	{
		$this->SetFont('Arial', 'BI', 9);

		$this->SetXY(10, 189);
		$this->Cell(275, 5, utf8_decode(' TÉCNICO '), 1, 0, 'C');

		$this->SetXY(12, 195);
		$this->Cell(90, 5, utf8_decode('MONTAGEM:'), 0, 0, 'L');
		$this->Cell(90, 5, utf8_decode('CONFERÊNCIA:'), 0, 0, 'L');
		$this->Cell(30, 5, utf8_decode('EMBALAGEM:'), 0, 0, 'L');
	}

	public function Codabar($xpos, $ypos, $code, $start = 'A', $end = 'A', $basewidth = 0.35, $height = 10)
	{
		$barChar = [
			'0' => [6.5, 10.4, 6.5, 10.4, 6.5, 24.3, 17.9],
			'1' => [6.5, 10.4, 6.5, 10.4, 17.9, 24.3, 6.5],
			'2' => [6.5, 10.0, 6.5, 24.4, 6.5, 10.0, 18.6],
			'3' => [17.9, 24.3, 6.5, 10.4, 6.5, 10.4, 6.5],
			'4' => [6.5, 10.4, 17.9, 10.4, 6.5, 24.3, 6.5],
			'5' => [17.9,    10.4, 6.5, 10.4, 6.5, 24.3, 6.5],
			'6' => [6.5, 24.3, 6.5, 10.4, 6.5, 10.4, 17.9],
			'7' => [6.5, 24.3, 6.5, 10.4, 17.9, 10.4, 6.5],
			'8' => [6.5, 24.3, 17.9, 10.4, 6.5, 10.4, 6.5],
			'9' => [18.6, 10.0, 6.5, 24.4, 6.5, 10.0, 6.5],
			'$' => [6.5, 10.0, 18.6, 24.4, 6.5, 10.0, 6.5],
			'-' => [6.5, 10.0, 6.5, 24.4, 18.6, 10.0, 6.5],
			':' => [16.7, 9.3, 6.5, 9.3, 16.7, 9.3, 14.7],
			'/' => [14.7, 9.3, 16.7, 9.3, 6.5, 9.3, 16.7],
			'.' => [13.6, 10.1, 14.9, 10.1, 17.2, 10.1, 6.5],
			'+' => [6.5, 10.1, 17.2, 10.1, 14.9, 10.1, 13.6],
			'A' => [6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5],
			'T' => [6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5],
			'B' => [6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6],
			'N' => [6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6],
			'C' => [6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6],
			'*' => [6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6],
			'D' => [6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5],
			'E' => [6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5],
		];
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
$pdf->AddPage('L');
$pdf->SetFont('Arial', '', 9);

$pdf->SetFont('Arial', '', 8);
$pdf->Ln(5);

if (isset($_POST['transacao'])) {
	if ($prescricao_id == '') {
		echo '
			<script>
				window.close();
			</script>';
	}

	include 'conexao.php';
	$query2 = "	SELECT 
					CASE 
						WHEN sequencia is null THEN 0
						WHEN sequencia is not null THEN sequencia 
					END as sequencia 
					FROM prescricao_itens WHERE atendimento_id= $transacao group by 1 order by sequencia desc";
	$sthquery2 = pg_query($query2) or die($query2);
	$rows2 = pg_fetch_object($sthquery2);

	$sequencia = $rows2->sequencia + 1;

	$dt = date('Y-m-d');
	$hr = date('H:i');
	$novalista = '';
	foreach ($prescricao_id as $item) {
		$novalista = $novalista . $item . ',';

		include 'conexao.php';
		$queryNomePrescricao = "insert into prescricao_itens (atendimento_id, sequencia, data, hora, descricao, dosagem, aprazamento, codigo_medicamento, medico, via, cuidados)
						select b.atendimento_id, '$sequencia', '$dt', '$hr', b.descricao, b.dosagem, b.aprazamento, b.codigo_medicamento, '$usuario', b.via, b.cuidados 
						FROM prescricao_itens b where prescricao_id = $item";
		pg_query($queryNomePrescricao) or die($queryNomePrescricao);
	}
	$novalista = rtrim($novalista, ',');

	include 'conexao.php';
	$query = 'SELECT  a.prescricao_id,a.hora,a.data,a.descricao,b.nome, a.dosagem,a.aprazamento,a.via, a.cuidados
					FROM prescricao_itens a
					left join pessoas b ON b.username = a.medico
					WHERE a.prescricao_id in (' . $novalista . ') order by a.tipo,a.prescricao_id ASC';
} else {
	$seq = 'sequencia is null';
	if ($sequencias == 0) {
		$seq = 'sequencia is null';
	} else {
		$seq = 'sequencia = ' . $sequencias . '';
	}

	include 'conexao.php';
	$query = "select * from prescricoes a
					left join prescricao_item b on a.prescricao_id = b.prescricao_id
					where a.atendimento_id = $transacao order by tipo";
}

$sthquery = pg_query($query) or die($query);
$i = 0;
$pdf->SetFont('Arial', 'B', 6.9);
$titulomedpaciente = 'n';
while ($rows = pg_fetch_object($sthquery)) {
	if ($rows->tipo == 5) {
		$pdf->Cell(115, 5, utf8_decode($rows->descricao . ' - ' . $rows->dosagem . ' - ' . $rows->aprazamento . ' - ' . $rows->complemento), 1, 'R');

		$pdf->Cell(28, 5, UTF8_DECODE($rows->via), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(30, 5, '', 1, 0, 'C');
		$i = $i + 5;
		$pdf->ln(5);
	}

	if ($rows->tipo == 6) {
		$descricaof = str_replace('++', "\n", $rows->descricao, $n);
		$n = (($n + 1) * 5) + 5;
		$yy = $pdf->GetX();
		$xx = $pdf->GetY();
		if ($rows->bomba == '1') {
			$pdf->MultiCell(115, 5, utf8_decode($descricaof . ' - ' . $rows->aprazamento . ' - ' . $rows->complemento . "\n DILUENTE -> " . $rows->diluente . "\nEm Bomba de Infusao"), '1', 'L');
			$n = $n + 5;
		} else {
			$pdf->MultiCell(115, 5, utf8_decode($descricaof . ' - ' . $rows->aprazamento . ' - ' . $rows->complemento . "\n DILUENTE -> " . $rows->diluente), '1', 'L');
		}
		$yyy = $pdf->GetY();
		$pdf->SetXY($yy + 115, $xx);
		$pdf->Cell(28, $n, UTF8_DECODE($rows->via), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, $n, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(30, $n, '', 1, 0, 'C');
		$pdf->SetY($yyy);

		$i = $i + 5;
	}

	/*if($rows->tipo == 3){

				$pdf->Cell(115,5,utf8_decode($rows->descricao),1,'R');
				$pdf->Cell(28,5,UTF8_DECODE($rows->via),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(30,5,'',1,0,'C');

				if($rows->componente1 != "" && ($rows->componente2 == "" && $rows->componente3 == "")){

					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->componente1),0,'R');
					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->administracao),0,'R');
					$i = $i + 15;

				} else if(($rows->componente1 != "" && $rows->componente2 != "") && $rows->componente3 == ""){

					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->componente1),0,'R');
					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->componente2),0,'R');
					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->administracao),0,'R');
					$i = $i + 20;

				} else if($rows->componente1 != "" && $rows->componente2 != "" && $rows->componente3 != "") {

					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->componente1),0,'R');
					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->componente2),0,'R');
					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->componente3),0,'R');
					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->administracao),0,'R');
					$i = $i + 25;

				} else {

					$i = $i + 10;

					$pdf->ln(5);
					$pdf->Cell(131,5,utf8_decode($rows->administracao),0,'R');

				}
				$pdf->ln(5);
			}*/

	/*if($rows->tipo == 1){
				$pdf->Cell(115,5,utf8_decode($rows->descricao),1,'R');
				$pdf->Cell(28,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(30,5,'',1,0,'C');
				$i = $i + 5;
				$pdf->ln(5);
			}*/

	if ($rows->tipo == 1) {
		if ($rows->bomba == '1') {
			$pdf->Cell(115, 5, utf8_decode($rows->descricao . ' - ' . $rows->dosagem . ' - ' . $rows->aprazamento . ' - ' . $rows->complemento . ' - Em Bomba de Infusao'), 1, 'R');
		} else {
			$pdf->Cell(115, 5, utf8_decode($rows->descricao . ' - ' . $rows->dosagem . ' - ' . $rows->aprazamento . ' - ' . $rows->complemento), 1, 'R');
		}

		$pdf->Cell(28, 5, UTF8_DECODE($rows->via), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(30, 5, '', 1, 0, 'C');
		$i = $i + 5;
		$pdf->ln(5);
	}

	/*if($rows->tipo == 10){
				$pdf->Cell(115,5,utf8_decode($rows->descricao),1,'R');
				$pdf->Cell(28,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(8.5,5,UTF8_DECODE('  '),1,0,'C');
				$pdf->Cell(30,5,'',1,0,'C');
				$i = $i + 5;
				$pdf->ln(5);
			}*/

	if ($rows->tipo == 10) {
		$pdf->Cell(115, 5, utf8_decode($rows->descricao . ' - ' . $rows->dosagem . ' - ' . $rows->aprazamento . ' - ' . $rows->complemento), 1, 'R');

		$pdf->Cell(28, 5, UTF8_DECODE($rows->via), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(30, 5, '', 1, 0, 'C');
		$i = $i + 5;
		$pdf->ln(5);
	}

	if ($rows->tipo == 11) {
		if ($titulomedpaciente == 'n') {
			$pdf->Cell(275, 5, UTF8_DECODE('*** MEDICAMENTO DE POSSE DO PACIENTE ***'), 1, 0, 'C');
			$titulomedpaciente = 's';
			$pdf->ln(5);
		}
		$pdf->Cell(115, 5, utf8_decode($rows->descricao), 1, 'R');
		$pdf->Cell(28, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(8.5, 5, UTF8_DECODE('  '), 1, 0, 'C');
		$pdf->Cell(30, 5, '', 1, 0, 'C');
		$i = $i + 5;
		$pdf->ln(5);
	}
}

//$pdf->Line(141, 64, 141, 69 + $i);
//$pdf->Line(10, 69 + $i, 285, 69 + $i);

$pdf->Codabar(240, 6, $transacao);

$pdf->Output();
