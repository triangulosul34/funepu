<?php

include("conexao.php");

require("../vendor/autoload.php");
require_once(dirname(__FILE__) . '/html2pdf/html2pdf.class.php');

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

$atendimento_id = $_POST['atendimento_id_referencia'];
$justificativa_referencia = $_POST['justificativa_referencia'];
$diagnostico_referencia = $_POST['diagnostico_referencia'];
$resultado_referencia = $_POST['resultado_referencia'];
$unidade_referencia = $_POST['unidade_referencia'];

$sql = "select * from contra_referencia where atendimento_id = $atendimento_id";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);
if ($row->atendimento_id) {
    $sql = "update contra_referencia set justificativa = '$justificativa_referencia', diagnostico = '$diagnostico_referencia', resultado='$resultado_referencia', unidade = '$unidade_referencia'  where atendimento_id = $atendimento_id";
    $result = pg_query($sql) or die($sql);
} else {
    $sql = "insert into contra_referencia(atendimento_id, justificativa, diagnostico, resultado, unidade) values($atendimento_id,'$justificativa_referencia','$diagnostico_referencia','$resultado_referencia', '$unidade_referencia')";
    $result = pg_query($sql) or die($sql);
}

$sql = "select a.unidade, c.nome, c.endereco, c.numero, c.bairro, c.cidade, c.dt_nasc, c.sexo, a.justificativa, a.diagnostico, a.resultado
from contra_referencia a
inner join atendimentos b on a.atendimento_id = b.transacao
inner join pessoas c on b.paciente_id = c.pessoa_id
where a.atendimento_id = $atendimento_id";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);
$unidade = $row->unidade;
$nome = $row->nome;
$endereço = $row->endereco . ", " . $row->numero . " - " . $row->bairro;
$cidade = $row->cidade;
$date = new DateTime($row->dt_nasc);
$interval = $date->diff(new DateTime(date('Y-m-d')));
$idade = $interval->format('%Y');
if ($row->sexo == "M") {
    $sexo = "Masculino";
} else if ($row->sexo == "F") {
    $sexo = "Feminino";
}
$justificativa = $row->justificativa;
$diagnostico = $row->diagnostico;
$resultado = $row->resultado;

$html2pdf = new Html2Pdf(
    $orientation = 'P',
    $format = 'A4',
    $lang = 'fr',
    $unicode = true,
    $encoding = 'UTF-8',
    $margins = array(5, 5, 5, 8),
    $pdfa = false
);
$html2pdf->writeHTML('
?>
<style>
table {
    border-collapse: collapse;
}

table,
th,
td {
    border: 1px solid black;
}

th,
td {
    text-align: center;
}

span {
    font-size: 15px;
}

.sansserif {
    font-family: Arial, Helvetica, sans-serif;
}

.borda {
    width: 100%;
    height: 75%;
    border: 1px solid;
}
</style>

<body>
    <table>
        <tr>
            <td style="width:20%;"><img src="app-assets/img/gallery/upa24.png" width="130" height="110"></td>
            <td
                style="width:60%;vertical-align: top;padding-top: 30px;padding-right: 25px;padding-left: 25px;font-size: 18px;line-height: 1.5;">
                <b class="sansserif">GUIA DE REFERÊNCIA / CONTRA REFERÊNCIA</b>
            </td>
            <td style="width:20%;"><img src="app-assets/img/gallery/upa24.png" width="130" height="100"></td>
        </tr>
    </table>
    <br />
    <div class="borda" style="padding-left:15px;">
        <span style="margin-top:1px" class="sansserif">Município: Uberaba</span>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span style="margin-top:1px" class="sansserif">Unidade: ' . $unidade . '</span>
        <br />
        <br />
        <span class="sansserif">I - IDENTIFICAÇÃO DO PACIENTE</span>
        <br />
        <br />
        <b style="font-size: 15px;" class="sansserif">Nome:</b>
        <span class="sansserif">' . $nome . '</span>
        <br />
        <br />
        <b style="font-size: 15px;" class="sansserif">Endereço:</b>
        <span class="sansserif">' . $endereço . '</span>
        <br />
        <br />
        <b style="font-size: 15px;" class="sansserif">Município:</b>
        <span class="sansserif">' . $cidade . '</span>
        <b style="position: relative;left: 30%;font-size: 15px;" class="sansserif">Idade:</b>
        <span class="sansserif">' . $idade . '</span>
        <b style=" position: relative;left: 10%;font-size: 15px;" class="sansserif">Sexo:</b>
        <span class="sansserif">' . $sexo . '</span>
        <br />
        <br />
        <b style="font-size: 15px;" class="sansserif">Ocupação:</b>
        <span class="sansserif"></span>
        <br />
        <br />
        <b style="font-size: 15px;" class="sansserif">Nome do Responsável:</b>
        <span class="sansserif">________________________________________________________________</span>
        <br />
        <br />
        <br />
        <span class="sansserif">II - JUSTIFICATIVA DA REFERÊNCIA</span>
        <br />
        <br />
        <br />
        <span class="sansserif" style="line-height: 1.5;">' . $justificativa . '</span>
        <br />
        <br />
        <b class="sansserif" style="line-height: 1.5;">Diagnóstico ou Hipótese Diagnóstica:</b>
        <span class="sansserif" style="line-height: 1.5;">' . $diagnostico . '</span>
        <br />
        <br />
        <b class="sansserif" style="line-height: 1.5;">Exames Realizados - Data e Resultados:</b>
        <span class="sansserif" style="line-height: 1.5;">' . $resultado . '</span>
    </div>
    <br />
    <br />
    <br />
    <span style="margin-left:80px">
        <img src="app-assets/img/gallery/sus.jpg" width="70" height="50">
    </span>
    <span style="margin-left:80px">
    <img src="app-assets/img/gallery/logo_uberaba.png" width="80" height="50">
    </span>
    <span style="margin-left:80px">
    <img src="app-assets/img/gallery/logo_governo_minas.png" width="80" height="50">
    </span>
    <span style="margin-left:80px">
        <img src="app-assets/img/gallery/logo_brasil.png" width="80" height="50">
    </span>
</body>
');
$html2pdf->output();
