<?php

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
$transacao = $_GET['transacao'];
include 'conexao.php';
$stmt = "select a.transacao, a.dat_cad, a.hora_cad, a.num_socio, a.num_senha, a.tipo, a.cad_user, a.dt_solicitacao, a.paciente_id, a.solicitante_id, a.tel_solicitante, a.email_solicitante, a.convenio_id, a.dum, a.nec_especiais,
a.peso, c.sexo, a.idade, a.leito, a.enfermaria, a.num_carteirinha, a.observacao, b.sigla, c.nome as paciente, c.dt_nasc, c.endereco, e.origem, c.numero, c.complemento,
c.bairro,  c.cidade, c.estado, c.telefone, c.celular, d.nome as solicitante, d.celular as telsolicitante from  pedidos a left join convenios b on a.convenio_id=b.convenio_id
left join pessoas c on a.paciente_id=c.pessoa_id left join solicitantes d on a.solicitante_id=d.solicitante_id left join tipo_origem e on cast(e.tipo_id as character varying)=a.tipo where transacao=$transacao";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);

$transacao = str_pad($row->transacao, 7, '0', STR_PAD_LEFT);
$data_transacao = $row->dat_cad;
$usuario_transacao = $row->cad_user;
$hora_transacao = $row->hora_cad;
$prontuario = $row->paciente_id;
$nome_paciente = ts_decodifica($row->paciente);
$autorizacao = $row->num_autorizacao;
$sexo = $row->sexo;
$nascimento = $row->dt_nasc;
if ($sexo == 'F') {
	$sexo = 'Feminino';
}
if ($sexo == 'M') {
	$sexo = 'Masculino';
}
$dum = inverteData(substr($row->dum, 0, 10));
$peso = $row->peso;
$deficiencia = $row->nec_especiais;
$solicitante_id = $row->solicitante_id;
$solicitante = $row->solicitante;
$dt_solicitacao = inverteData(substr($row->dt_solicitacao, 0, 10));
$convenio = $row->convenio_id;
$nome_convenio = $row->sigla;
$num_carterinha = $row->num_socio;
$num_guia = $row->num_guia;
$num_autorizacao = $row->num_autorizacao;
$origem = $row->origem;
$enfermaria = $row->enfermaria;
$leito = $row->leito;
$telsolicitante = $row->telsolicitante;
$emailsolicitante = $row->email_solicitante;
$idade = $row->idade;
$observacao = $row->observacao;
$senha = $row->num_senha;
$hr_agenda = $row->hora_agenda;
$med_executante = $row->med_executante;

$dia = date('d', strtotime($data_transacao));
$mes = date('m', strtotime($data_transacao));
$ano = date('Y', strtotime($data_transacao));
$semana = date('w', strtotime($data_transacao));

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
		global $transacao, $prontuario, $nome_paciente, $sexo, $idade, $nome_convenio, $origem, $enfermaria, $leito, $solicitante,
			$telsolicitante, $senha, $dt_solicitacao, $nascimento;
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
		$this->Cell(300, 5, $senha, 0, 0, 'C');
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
		$this->Cell(13, 5, $prontuario, 1, 0, 'L');
		$this->Cell(17, 5, ' Dt.Nasc.:', 1, 0, 'L');
		$this->SetFont('Arial', 'BI', 9);
		$this->Cell(20, 5, inverteData($nascimento), 1, 0, 'L');
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
	public function Footer()
	{
		global $transacao, $usuario_transacao, $data_transacao, $hora_transacao;
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Transacao - ' . str_pad($transacao, 7, '0', STR_PAD_LEFT) . ' - ' . $usuario_transacao . ' ' . $data_transacao . ' ' . $hora_transacao . '/{nb}', 0, 0, 'C');
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
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);
include 'conexao.php';
$stmt = "select a.exame_nro, a.exame_id, a.qtde, b.descricao, b.sigtap,a.situacao
from itenspedidos a left join procedimentos b on b.procedimento_id=a.exame_id left join pedidos c on a.transacao=c.transacao where a.transacao=$transacao";
$sth = pg_query($stmt) or die($stmt);
$data_entrega = '';
while ($row = pg_fetch_object($sth)) {
	$pdf->Cell(90, 6, utf8_decode($row->descricao), 0, 0, 'L');
	$pdf->Cell(30, 6, str_pad($row->sigtap, 8, '0', STR_PAD_LEFT), 0, 0, 'C');
	$pdf->Cell(20, 6, str_pad($row->exame_nro, 7, '0', STR_PAD_LEFT), 0, 0, 'C');
	$pdf->Cell(20, 6, str_pad($row->qtde, 3, '0', STR_PAD_LEFT), 0, 0, 'C');
	$pdf->Cell(25, 6, $row->situacao, 0, 0, 'C');
	$pdf->Ln(6);
}

$pdf->Codabar(145, 12, $transacao);

$pdf->Output();
