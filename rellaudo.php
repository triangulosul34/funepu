<?php
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}
/**
 * HTML2PDF Library - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */
error_reporting(0);
$id = $_GET['id'];
include('conexao.php');
$stmtx = "select b.transacao, b.idade, b.dat_cad,  b.status, b.cad_user, b.paciente_id, a.exame_id, a.observacoes, a.exame_nro, a.digitador_hora,a.resultado, a.digitador_data, a.digitador, a.situacao,
			 a.med_analise, a.data_analise, a.hora_analise, c.nome, c.dt_nasc, c.imagem, c.sexo, d.descricao, e.nome as solicitante, laudo_padrao, med_confere,
			 a.data_confere, a.hora_confere from itenspedidos a left join pedidos   b  on a.transacao   =b.transacao left join pessoas c  on b.paciente_id =c.pessoa_id left join procedimentos d 
			 on a.exame_id=d.procedimento_id left join solicitantes e on b.solicitante_id=e.solicitante_id where a.exame_nro=" . $id;
$sth = pg_query($stmtx) or die($stmtx);
$row = pg_fetch_object($sth);
$prontuario = $row->paciente_id;
$transacao = $row->transacao;
$status = $row->status;
$dat_cad = substr($row->dat_cad, 0, 10);
$cad_user = $row->cad_user;
$exame_id = $row->exame_id;
$exame_nro = $row->exame_nro;
$exame_desc = $row->descricao;
$nome = $row->nome;
$dt_nasc = $row->dt_nasc;
$date = new DateTime($dt_nasc); // data de nascimento
$interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
$idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
$sexo = $row->sexo;
$descricao = $row->descricao;
$solicitante = $row->solicitante;
$sol_email = $row->sol_email;
$sol_celular = $row->sol_celular;
$resultado = $row->resultado;
if ($resultado == '') {
    $laudo = $row->laudo_padrao;
} else {
    $laudo = $resultado;
}
$observacoes = $row->observacoes;
$digitador = $row->digitador;
$digitador_data = $row->digitador_data;
$digitador_hora = $row->digitador_hora;
$med_analise =  $row->med_analise;
$med_confere =  $row->med_confere;
$data_confere =  $row->data_confere;
$hora_confere =  $row->hora_confere;
$situacao = $row->situacao;


ob_start();
include("conexao.php");
$stmt = "Select * from configuracoes";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$cabecalho = $row->laudo_cabecalho;
$identificacao = $row->laudo_identificacao;
$qrcode    = $row->laudo_qrcode;
$msg = "Diagnostic Center\r\nhttp://dcenter.ddns.net:8585/dcenter/html/prontuario=$prontuario";
?>
<style type="text/css">
    <!--
    table.page_header {
        width: 100%;
        border: none;
        border-bottom: solid 1mm #AAAADD;
        padding: 2mm
    }

    table.page_footer {
        width: 100%;
        border: none;
        border-top: solid 1mm #AAAADD;
        padding: 2mm
    }

    h1 {
        color: #000033
    }

    h2 {
        color: #000055
    }

    h3 {
        color: #000077
    }

    div.niveau {
        padding-left: 5mm;
    }
    -->
</style>
<page backtop="<?php echo $cabecalho . "mm"; ?>" backbottom="14mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
    <page_header>

        <table class="page_header">

            <tr>
                <td style="width: 25%; height: <?php echo $identificacao . "px"; ?>; text-align: center">
                    <img src="app-assets/img/gallery/logo.jpg" width='150' height='60'>
                </td>
                <td colspan='2' style="text-align: right">
                    <img src="app-assets/img/gallery/LogoPMU.jpg" width='150' height='60'>
                </td>

                <td style="width: 20%; text-align: left">

                </td>
            </tr>
            <tr style="border-top: solid 1mm;">
                <td style="width: 25%; text-align: right">
                    <strong>Nome Paciente:</strong>
                </td>
                <td style="width: 40%; text-align: left">
                    <?php echo $nome; ?>
                </td>
                <td style="width: 15%; text-align: left">
                    <strong>Idade:</strong>
                </td>
                <td style="width: 20%; text-align: left">
                    <?php echo $idade; ?>
                </td>
            </tr>
            <tr>
                <td style="width: 25%; text-align: right">
                    <strong>Solicitante:</strong>
                </td>
                <td style="width: 40%; text-align: left">
                    <?php echo $solicitante; ?>
                </td>
                <td style="width: 15%; text-align: left">
                    <strong>Realização</strong>
                </td>
                <td style="width: 20%; text-align: left">
                    <?php echo inverteData($dat_cad); ?> - <?php echo str_pad($exame_nro, 7, '0', STR_PAD_LEFT); ?>
                </td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    página [[page_cu]]/[[page_nb]]
                </td>
            </tr>
        </table>
    </page_footer>

    <?php
    echo $resultado;
    if ($med_analise != '') {
        include("conexao.php");
        $stmt = "Select * from pessoas where username='$med_analise'";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $medico_analise     = $row->nome;
        $num_conselho_ana   = $row->num_conselho_reg;
        $conselho_reg_ana   = $row->conselho_regional;
        $assinatura_ana        = $row->assinatura;
    }
    if ($med_confere != '') {
        include("conexao.php");
        $stmt = "Select * from pessoas where username='$med_confere'";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $medico_confere      = $row->nome;
        $num_conselho_conf   = $row->num_conselho_reg;
        $conselho_reg_conf   = $row->conselho_regional;
        $assinatura_conf     = $row->assinatura;
    }
    if ($medico_confere != '' and $medico_analise != "" and $medico_analise != $medico_confere and $hora_confere != "") {
        echo "<table>";
        echo "<tr>";
        echo "<td style=\"width: 50%; text-align: center\">";
        //if ($assinatura_ana!="") { echo "<img src=\"imagens/assinaturas/".$assinatura_ana."\" width=\"112\" height=\"75\"><br>";}
        //echo "_____________________________________"."<br>";
        echo "RESULTADO DE EXAME EMITIDO ELETRONICAMENTE POR<br>";
        echo $medico_analise;
        echo "<br>";
        echo $conselho_reg_ana . ' : ' . $num_conselho_ana;
        echo "<br>";


        echo "</td>";
        echo "<td style=\"width: 50%; text-align: center\">";
        //if ($assinatura_conf!="") { echo "<img src=\"imagens/assinaturas/".$assinatura_conf."\" width=\"112\" height=\"75\"><br>";}
        //echo "_____________________________________"."<br>";
        echo "RESULTADO DE EXAME EMITIDO ELETRONICAMENTE POR<br>";
        echo $medico_confere;
        echo "<br>";
        echo $conselho_reg_conf . ' : ' . $num_conselho_conf;
        echo "<br>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "<table style=\"width: 100%;\">";
        echo "<tr>";
        echo "<td style=\"width: 100%; text-align: center\">";

        //echo "<img src=\"./imagens/assinaturas/".$assinatura_ana."\" width=\"115\" height=\"75\"><br>";		
        //echo "___________________________________"."<br>";
        echo "RESULTADO DE EXAME EMITIDO ELETRONICAMENTE POR<br>";
        echo $medico_analise;
        echo "<br>";
        echo $conselho_reg_ana . ' : ' . $num_conselho_ana;
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }


    ?>


</page>

<?php
$content = ob_get_clean();

require_once(dirname(__FILE__) . '/html2pdf/html2pdf.class.php');
try {
    $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', 0);
    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    // $html2pdf->createIndex('Sommaire', 25, 12, false, true, 1);
    $html2pdf->Output('bookmark.pdf');
} catch (HTML2PDF_exception $e) {
    echo $e;
    exit;
}
