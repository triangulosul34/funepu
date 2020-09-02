<?php

require("../vendor/autoload.php");
require('fpdf/fpdf.php');

function formatCnpjCpf($value)
{
    $cnpj_cpf = preg_replace("/\D/", '', $value);

    if (strlen($cnpj_cpf) === 11) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
    }

    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
}

$transacao = $_GET['id'];

include('conexao.php');
$stmt = "SELECT c.cpf, c.nome
		FROM atendimentos a 
		LEFT JOIN pessoas c ON a.paciente_id=c.pessoa_id 
		LEFT JOIN tipo_origem k ON CAST(k.tipo_id AS VARCHAR)=a.tipo 
		LEFT JOIN classificacao x ON ltrim(x.atendimento_id, '0')= '$transacao' 
		WHERE a.transacao=$transacao";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);

$pdf = new FPDF("P", "pt", "A4");

$pdf->AddPage();
$pdf->Image('formularios/ficha de registro individual - casos de sindrome respiratoria aguda grave page 1.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
$pdf->SetFont('arial', '', 10);
$pdf->Text(73, 164, date('d'));
$pdf->Text(100, 164, date('m'));
$pdf->Text(130, 164, date('Y'));
$pdf->Text(73, 193, "M   G");
$pdf->Text(135, 193, "UBERABA");
//$pdf->Text(446, 193, "3    1    7    0   1    0    7");
$pdf->Text(73, 220, "UPA " . UNIDADE_CONFIG);
for ($a = 0, $v = 446; $a < strlen(CNES); $a++) {
    $pdf->Text($v, 220, CNES[$a]);
    $v = $v + 16;
}
$cpf = formatCnpjCpf(preg_replace('~[.-]~', "", $row->cpf));
for ($a = 0, $v = 169; $a < strlen($cpf); $a++) {
    $pdf->Text($v, 235, $cpf[$a]);
    $v = $v + 16;
}
$pdf->Text(130, 252, $row->nome);
$pdf->AddPage();
$pdf->Image('formularios/ficha de registro individual - casos de sindrome respiratoria aguda grave page 2.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
$pdf->Output();
