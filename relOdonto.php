<?php
function inverteData($data)
{
	if (count(explode("/", $data)) > 1) {
		return implode("-", array_reverse(explode("/", $data)));
	} elseif (count(explode("-", $data)) > 1) {
		return implode("/", array_reverse(explode("-", $data)));
	}
}

require_once('fpdf/fpdf.php');
require_once('fpdi/fpdi.php');

$transacao = $_GET['id'];
include('conexao.php');

$stmt = "select *
				from atendimentos a 				
				left join pessoas c on a.paciente_id=c.pessoa_id 
				WHERE a.transacao=$transacao";
$sth = pg_query($stmt) or die($stmt);

$row = pg_fetch_object($sth);
$transacao = str_pad($transacao, 7, '0', STR_PAD_LEFT);
$data_transacao = substr($row->cadastro, 0, 10);
$hora_transacao = $row->hora_transacao;
$prontuario = $row->paciente_id;
$mae = $row->nome_mae;
$descricao = $row->descricao;
$sexo = $row->sexo;
$nome = $row->nome;
$email = $row->email;
$dt_nascimento = inverteData($row->dt_nasc);
$enderecox = $row->endereco;
$end_numero = $row->numero;
$complemento = $row->complemento;
$bairro = $row->bairro;
$cidade = $row->cidade;
$estado = $row->estado;
$cep = $row->cep;
$cpf = $row->cpf;
$telefone = $row->telefone;
$celular = $row->celular;
$dt_nasc = $row->dt_nasc;
$date = new DateTime($dt_nasc); // data de nascimento
$interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
$idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
$procedimento = $row->procedimento_id;
$senha = $row->num_senha;
$deficiencia = $_POST['deficiencia'];
$origem = $row->origem;
$deficiencia = $row->nec_especiais;
$observacao  = $row->observacao;
$oque_faz  = $row->oque_faz;
$com_oqfaz = $row->com_oqfaz;
$tempo_faz = $row->tempo_faz;
$como_faz  = $row->como_faz;
$enfermaria = $row->enfermaria;
$leito = $row->leito;
$imagem = $row->imagem;
$origem = $row->tipo;
$queixa = $row->queixa;
$exame_fisico = $row->exame_fisico;
$cid_principal = $row->cid_principal;
$diagnostico_principal = $row->diagnostico_principal;
$destino  = $row->destino_paciente;
$sigtap = $row->sigtap;
$medico = $row->medico;
$cns = $row->num_carteira_convenio;
$qtde = $row->qtde;

//print_r($row);


$pdf = new FPDI();

$pageCount = $pdf->setSourceFile('formularios/FichaOdonto.pdf');
$tplIdx    = $pdf->importPage(1);

$pdf->addPage('P');
$pdf->useTemplate($tplIdx, 1, 2, 220);

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->SetXY(172.5, 5.75);
$pdf->Write(8, str_pad($transacao, 10, '0', STR_PAD_LEFT)); //Nr

$pdf->SetFont('Helvetica', 'B', 10);
$pdf->SetXY(6, 16);
$pdf->Write(8, UNIDADE_CONFIG); //unidade

$pdf->SetXY(87, 16);
$pdf->Write(8, CNES); //cnes

$pdf->SetXY(118, 16.5);
$pdf->Write(8, utf8_decode(date('d/m/Y', strtotime($row->dat_cad)))); //data

$pdf->SetXY(117, 11);
$pdf->Write(8, utf8_decode($row->hora_cad)); //hora

$pdf->SetXY(145, 16);
$pdf->Write(8, str_pad($prontuario, 8, '0', STR_PAD_LEFT)); //codigo do paciente

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetXY(173.5, 16);
$pdf->Write(8, str_pad($row->num_carteira_convenio, 15, '0', STR_PAD_LEFT)); //cartao sus


$pdf->SetFont('Helvetica', '', 9);
$pdf->SetXY(18, 28.35);
$pdf->Write(8, utf8_decode(strtoupper($nome))); //nome

$pdf->SetXY(26, 33.25);
$pdf->Write(8, utf8_decode(strtoupper($row->endereco . " ," . $row->numero))); //endereco

$pdf->SetXY(20, 37.75);
$pdf->Write(8, utf8_decode(strtoupper($row->bairro))); //bairro

$pdf->SetXY(27.5, 42.75);
$pdf->Write(8, utf8_decode(strtoupper($row->cidade . "-" . $row->estado))); //localidade

$pdf->SetXY(22.5, 47.25);
$pdf->Write(8, utf8_decode(strtoupper($row->nome_mae))); //filiacao

$pdf->SetFont('Helvetica', '', 8);
$pdf->SetXY(86, 42.70);
$pdf->Write(8, $row->telefone); //tel

$pdf->SetXY(112, 42.70);
$pdf->Write(8, $row->telefone2); //cel

$pdf->SetXY(145, 42.70);
$pdf->Write(8, ""); //recados


$pdf->SetXY(182.5, 28);
$pdf->Write(8, date('d/m/Y', strtotime($row->dt_nasc))); //dt.nascim

$pdf->SetXY(173, 34);
if ($row->sexo == 'F') {
	$pdf->Write(8, strtoupper("Feminino")); //sexo
} else if ($row->sexo == 'M') {
	$pdf->Write(8, strtoupper("Masculino")); //sexo
}

$pdf->SetXY(173, 39.5);
$pdf->Write(8, $row->idade); //idade

$pdf->SetXY(171, 46);
$pdf->Write(8, utf8_decode("")); //drs

$pdf->Output();
