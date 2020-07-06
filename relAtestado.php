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
require("../vendor/autoload.php");



$data_atendimento = $_POST['data_atendimento'];
$hora_atendimento = $_POST['hora_atendimento'];
$dias_atestado = $_POST['dias_atestado'];
$cidAtestado = $_POST['cidAtestado'];
$atendimento = $_POST['atendimento'];

include('conexao.php');
$stmt = "SELECT * FROM atendimentos a
			LEFT JOIN pessoas p ON a.paciente_id = p.pessoa_id
		WHERE a.transacao=$atendimento";

$sth = pg_query($stmt) or die($stmt);

$row = pg_fetch_object($sth);

$nome = $row->nome;
$pessoa_id = $row->pessoa_id;
$med_atendimento = $row->med_atendimento;

$next = "select nextval('atestados_atestado_id_seq'::regclass)";
$sthnext = pg_query($next);
$rownext = pg_fetch_object($sthnext);

$sql = "INSERT INTO atestados(atestado_id,pessoa_id,profissional_id,atendimento_id,hora_atendimento,qtd_dias,partir_dia,cid) VALUES($rownext->nextval,$pessoa_id,'$med_atendimento',$atendimento,'$hora_atendimento','$dias_atestado','$data_atendimento','$cidAtestado')";
$result = pg_query($sql) or die($sql);


$pdf = new FPDI();

$pageCount = $pdf->setSourceFile('formularios/atestado.pdf');
$tplIdx    = $pdf->importPage(1);

$pdf->addPage('L', 'A4');
$pdf->useTemplate($tplIdx, 0, 0, 290);

$pdf->SetFont('Helvetica', 'B', 16);

$pdf->SetXY(115, 59);
$pdf->Write(8, $nome);
$pdf->Image("https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2F201.48.4.90%2F" . UNIDADEABV_CONFIG . "%2Ffunepu%2Fqratestado.php%3Fa%3D" . $rownext->nextval . "%26c%3D" . $atendimento . ";&choe=UTF-8", 18, 105, 20, 20, 'png');

$pdf->SetXY(25, 82);
$pdf->Write(8, $hora_atendimento);

$pdf->SetXY(135, 82);
$pdf->Write(8, $dias_atestado);

$pdf->SetXY(25, 93);
$pdf->Write(8, $data_atendimento);

$pdf->SetXY(200, 93);
$pdf->Write(8, $cidAtestado);


$pdf->SetXY(35, 132);
$pdf->Write(8, $data_atendimento);

$pdf->SetFont('Arial', '', 6);
$pdf->SetXY(11, 121);
$pdf->Write(8, utf8_decode("código de verificação de veracidade"));


$pdf->Output();
