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

$id = $_GET['id'];

include('conexao.php');
$sql = "SELECT * FROM sindrome_gripal WHERE sindrome_gripal_id = $id";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);

$pdf = new FPDF('P', 'pt', 'A4');
$pdf->AddPage();
$pdf->Image('formularios/sindrome registro individual.png', 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
$pdf->SetFont('Arial', '', 9);
$pdf->Text(82, 265, $row->uf[0]);
$pdf->Text(96, 265, $row->uf[1]);
$pdf->Text(170, 265, $row->municipio_notificacao);
if ($row->pcpf == "sim") {
    $pdf->Text(106, 290, "X");
} else {
    $pdf->Text(144, 290, "X");
}
if ($row->pestrangeiro == "sim") {
    $pdf->Text(210, 302, "X");
} else {
    $pdf->Text(247, 302, "X");
}
if ($row->psaude == "sim") {
    $pdf->Text(311, 302, "X");
} else {
    $pdf->Text(356, 302, "X");
}
if ($row->pseguranca == "sim") {
    $pdf->Text(430, 302, "X");
} else {
    $pdf->Text(476, 302, "X");
}
$pdf->Text(125, 317, $row->cbo);
$pdf->Text(324, 317, $row->cpf[0]);
$pdf->Text(338, 317, $row->cpf[1]);
$pdf->Text(352, 317, $row->cpf[2]);
$pdf->Text(366, 317, $row->cpf[3]);
$pdf->Text(380, 317, $row->cpf[4]);
$pdf->Text(394, 317, $row->cpf[5]);
$pdf->Text(408, 317, $row->cpf[6]);
$pdf->Text(422, 317, $row->cpf[7]);
$pdf->Text(436, 317, $row->cpf[8]);
$pdf->Text(450, 317, $row->cpf[9]);
$pdf->Text(464, 317, $row->cpf[10]);
$pdf->Text(124, 330, $row->cns[0]);
$pdf->Text(137, 330, $row->cns[1]);
$pdf->Text(150, 330, $row->cns[2]);
$pdf->Text(164, 330, $row->cns[3]);
$pdf->Text(177, 330, $row->cns[4]);
$pdf->Text(191, 330, $row->cns[5]);
$pdf->Text(204, 330, $row->cns[6]);
$pdf->Text(218, 330, $row->cns[7]);
$pdf->Text(231, 330, $row->cns[8]);
$pdf->Text(244, 330, $row->cns[9]);
$pdf->Text(257, 330, $row->cns[10]);
$pdf->Text(271, 330, $row->cns[11]);
$pdf->Text(285, 330, $row->cns[12]);
$pdf->Text(300, 330, $row->cns[13]);
$pdf->Text(170, 345, $row->nome);
$pdf->Text(200, 360, $row->nome_mae);
$pdf->Text(190, 374, $row->data_nascimento[8] . $row->data_nascimento[9]);
$pdf->Text(213, 374, $row->data_nascimento[5] . $row->data_nascimento[6]);
$pdf->Text(235, 374, $row->data_nascimento[0] . $row->data_nascimento[1] . $row->data_nascimento[2] . $row->data_nascimento[3]);
$pdf->Text(365, 374, $row->pais_origem);
if ($row->psexo == "masculino") {
    $pdf->Text(107, 400, "X");
} else {
    $pdf->Text(175, 400, "X");
}
if ($row->praca == "branca") {
    $pdf->Text(246, 400, "X");
} else if ($row->praca == "preta") {
    $pdf->Text(301, 400, "X");
} else if ($row->praca == "amarela") {
    $pdf->Text(351, 400, "X");
} else if ($row->praca == "parda") {
    $pdf->Text(246, 411, "X");
} else if ($row->praca == "amarela") {
    $pdf->Text(301, 411, "X");
}
$pdf->Text(413, 400, $row->passaporte[0]);
$pdf->Text(426, 400, $row->passaporte[1]);
$pdf->Text(440, 400, $row->passaporte[2]);
$pdf->Text(455, 400, $row->passaporte[3]);
$pdf->Text(468, 400, $row->passaporte[4]);
$pdf->Text(481, 400, $row->passaporte[5]);
$pdf->Text(495, 400, $row->passaporte[6]);
$pdf->Text(507, 400, $row->passaporte[7]);
$pdf->Text(125, 424, $row->cep[0]);
$pdf->Text(138, 424, $row->cep[1]);
$pdf->Text(151, 424, $row->cep[2]);
$pdf->Text(164, 424, $row->cep[3]);
$pdf->Text(179, 424, $row->cep[4]);
$pdf->Text(196, 424, $row->cep[6]);
$pdf->Text(209, 424, $row->cep[7]);
$pdf->Text(223, 424, $row->cep[8]);
$pdf->Text(189, 443, $row->estado[0]);
$pdf->Text(202, 443, $row->estado[1]);
$pdf->Text(330, 443, $row->cidade);
$pdf->Text(100, 465, $row->logradouro);
$pdf->Text(370, 465, $row->numero);
$pdf->Text(420, 465, $row->bairro);
$pdf->SetFont('Arial', '', 6);
$pdf->Text(100, 483, $row->complemento);
$pdf->SetFont('Arial', '', 9);
$pdf->Text(169, 493, $row->celular[0]);
$pdf->Text(177, 493, $row->celular[1]);
$pdf->Text(190, 493, $row->celular[3]);
$pdf->Text(199, 493, $row->celular[4]);
$pdf->Text(208, 493, $row->celular[5]);
$pdf->Text(217, 493, $row->celular[6]);
$pdf->Text(226, 493, $row->celular[7]);
$pdf->Text(235, 493, $row->celular[8]);
$pdf->Text(244, 493, $row->celular[9]);
$pdf->Text(253, 493, $row->celular[10]);
$pdf->Text(262, 493, $row->celular[11]);
$pdf->Text(412, 493, $row->telefone[0]);
$pdf->Text(421, 493, $row->telefone[1]);
$pdf->Text(432, 493, $row->telefone[3]);
$pdf->Text(441, 493, $row->telefone[4]);
$pdf->Text(450, 493, $row->telefone[5]);
$pdf->Text(459, 493, $row->telefone[6]);
$pdf->Text(468, 493, $row->telefone[7]);
$pdf->Text(477, 493, $row->telefone[8]);
$pdf->Text(486, 493, $row->telefone[9]);
$pdf->Text(495, 493, $row->telefone[10]);
$pdf->Text(504, 493, $row->telefone[11]);
$pdf->Text(183, 507, $row->data_nascimento[8] . $row->data_nascimento[9]);
$pdf->Text(202, 507, $row->data_nascimento[5] . $row->data_nascimento[6]);
$pdf->Text(217, 507, $row->data_nascimento[0] . $row->data_nascimento[1] . $row->data_nascimento[2] . $row->data_nascimento[3]);
if ($row->sfebre) {
    $pdf->Text(106, 530, "X");
}
if ($row->sdor_garganta) {
    $pdf->Text(148, 530, "X");
}
if ($row->sdor_garganta) {
    $pdf->Text(106, 540, "X");
}
if ($row->stosse) {
    $pdf->Text(148, 540, "X");
}
if ($row->sdipineia) {
    $pdf->Text(148, 540, "X");
}
if ($row->soutros) {
    $pdf->Text(196, 540, "X");
}
$pdf->Text(235, 540, $row->stoutros);
$pdf->Text(415, 529, $row->data_inicio_sintomas[8] . $row->data_inicio_sintomas[9]);
$pdf->Text(433, 529, $row->data_inicio_sintomas[5] . $row->data_inicio_sintomas[6]);
$pdf->Text(450, 529, $row->data_inicio_sintomas[0] . $row->data_inicio_sintomas[1] . $row->data_inicio_sintomas[2] . $row->data_inicio_sintomas[3]);
if ($row->cdoencas_respiratorias) {
    $pdf->Text(106, 562, "X");
}
if ($row->cdoencas_renais) {
    $pdf->Text(385, 562, "X");
}
if ($row->cdoencas_cromossomicas) {
    $pdf->Text(483, 562, "X");
}
if ($row->cdiabetes) {
    $pdf->Text(106, 573, "X");
}
if ($row->cimunossupressão) {
    $pdf->Text(385, 573, "X");
}
if ($row->cdoencas_cardiacas) {
    $pdf->Text(106, 584, "X");
}
if ($row->cgestante) {
    $pdf->Text(385, 584, "X");
}
if ($row->eteste == "solicitado") {
    $pdf->Text(105, 615, "X");
} else if ($row->eteste == "coletado") {
    $pdf->Text(105, 626, "X");
} else if ($row->eteste == "concluido") {
    $pdf->Text(105, 636, "X");
} else if ($row->eteste == "exame nao coletado") {
    $pdf->Text(105, 647, "X");
}
$pdf->Text(210, 647, $row->data_coleta_teste[8] . $row->data_coleta_teste[9]);
$pdf->Text(230, 647, $row->data_coleta_teste[5] . $row->data_coleta_teste[6]);
$pdf->Text(250, 647, $row->data_coleta_teste[0] . $row->data_coleta_teste[1] . $row->data_coleta_teste[2] . $row->data_coleta_teste[3]);
if ($row->tipo_teste == "rt-pcr") {
    $pdf->Text(306, 606, "X");
} else if ($row->tipo_teste == "teste rapido - anticorpo") {
    $pdf->Text(306, 615, "X");
} else if ($row->tipo_teste == "teste rapido - antigeno") {
    $pdf->Text(306, 626, "X");
} else if ($row->tipo_teste == "enzimaimunoensaio - elisa") {
    $pdf->Text(306, 636, "X");
} else if ($row->tipo_teste == "imunoensaio por eletroquimioluminescencia - eclia") {
    $pdf->Text(306, 647, "X");
}
if ($row->resultado == "negativo") {
    $pdf->Text(451, 630, "X");
} else {
    $pdf->Text(451, 641, "X");
}
if ($row->classificacao_final == "descartado") {
    $pdf->Text(104, 685, "X");
} else if ($row->classificacao_final == "confirmado clinico-epidemiologico") {
    $pdf->Text(104, 695, "X");
} else if ($row->classificacao_final == "confirmado laboratorial") {
    $pdf->Text(104, 704, "X");
} else if ($row->classificacao_final == "sindrome gripal nao especificada") {
    $pdf->Text(104, 713, "X");
} else if ($row->classificacao_final == "confirmado clinico imagem") {
    $pdf->Text(237, 685, "X");
} else if ($row->classificacao_final == "confirmado por criterio clinico") {
    $pdf->Text(238, 695, "X");
}
if ($row->evolucao_caso == "cancelado") {
    $pdf->Text(363, 685, "X");
} else if ($row->evolucao_caso == "ignorado") {
    $pdf->Text(363, 695, "X");
} else if ($row->evolucao_caso == "em tratamento domiciliar") {
    $pdf->Text(363, 706, "X");
} else if ($row->evolucao_caso == "internado em uti") {
    $pdf->Text(363, 717, "X");
} else if ($row->evolucao_caso == "internado") {
    $pdf->Text(475, 685, "X");
} else if ($row->evolucao_caso == "obito") {
    $pdf->Text(475, 695, "X");
} else if ($row->evolucao_caso == "cura") {
    $pdf->Text(475, 706, "X");
}
$pdf->Text(200, 733, $row->data_encerramento[8] . $row->data_encerramento[9]);
$pdf->Text(225, 733, $row->data_encerramento[5] . $row->data_encerramento[6]);
$pdf->Text(245, 733, $row->data_encerramento[0] . $row->data_encerramento[1] . $row->data_encerramento[2] . $row->data_encerramento[3]);
$pdf->Output();
