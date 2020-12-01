<?php
require("../vendor/autoload.php");
require('fpdf/fpdf.php');
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}
$transacao = $_GET['id'];
include('conexao.php');
include('conexao.php');
$stmt = "
		select a.transacao, a.cid_internacao,a,hora_cad, a.destino_paciente, a.data_destino, a.queixa, a.exame_fisico, a.obs_modal as modal, a.diagnostico_principal,a.prioridade,
		a.paciente_id, a.status, a.tipo, a.tipo_leito, a.dat_cad as cadastro, c.nome, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco, a.oque_faz, a.com_oqfaz, 
		a.tempo_faz, a.como_faz, c.numero, c.complemento, c.bairro, c.nome_mae, c.num_carteira_convenio, c.cep, c.cpf, c.cidade, c.estado, a.observacao, k.origem,  
		x.peso, x.pressaodiastolica, x.pressaosistolica, x.queixa as relato, x.pulso, x.temperatura,x.discriminador, x.prioridade as atendprioridade,x.glicose as glicemia,
		a.data_destino,a.hora_destino,a.data_internacao,a.hora_internacao, a.destino_paciente, pa_sis_internacao, pa_dist_internacao, temperatura_internacao, dor_internacao, oxigenio_internacao, pulso_internacao, glicose_internacao, ecg_internacao, frequencia_respiratoria
		from atendimentos a 
		left join pessoas c on a.paciente_id=c.pessoa_id 
		left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
		left join classificacao x ON ltrim(x.atendimento_id, '0')= '$transacao' 
		where a.transacao=$transacao";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$transacao = str_pad($transacao, 7, '0', STR_PAD_LEFT);
$data_transacao = substr($row->cadastro, 0, 10);
$hora_transacao = $row->hora_transacao;
$data_destino = $row->data_destino;
$data_internacao = inverteData($row->data_internacao);
$hora_destino = $row->hora_destino;
$hora_internacao = $row->hora_internacao;
$destino_paciente = $row->destino_paciente;
$horacad = $row->hora_cad;
$datacad = date('d/m/Y', strtotime($row->cadastro));
$prontuario = $row->paciente_id;
$sexo = $row->sexo;
$nome = $row->nome;
$email = $row->email;
$dt_nascimento = inverteData($row->dt_nasc);
$enderecox = $row->endereco;
$end_numero = $row->numero;
$complemento = $row->complemento;
$bairro = $row->bairro;
$cns    = $row->num_carteira_convenio;
$cidade = $row->cidade;
$estado = $row->estado;
$cep = $row->cep;
$cpf = $row->cpf;
$telefone = $row->telefone;
$celular = $row->celular;
$nome_mae = $row->nome_mae;
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
$obs_modal = $row->modal;
$exame_fisico = $row->exame_fisico;
$cid_internacao = $row->cid_internacao;
$destino  = $row->destino_paciente;
$tipo_leito  = $row->tipo_leito;
$pressaodiastolica = $row->pressaodiastolica;
$pressaosistolica = $row->pressaosistolica;
$peso = $row->peso;
$temperatura = $row->temperatura;
//$pulso = $row->pulso;
$relato = $row->relato;
$discriminador = $row->discriminador;
$prioridade = $row->prioridade;
$atendprioridade = $row->atendprioridade;
$diagnostico_principal = $row->diagnostico_principal;
$pulso= $row->pulso_internacao;
$glicemia= $row->glicose_internacao;
$pa_sitolica= $row->pa_sis_internacao;
$pa_distolica= $row->pa_dist_internacao;
$oxigenio= $row->oxigenio_internacao;
$temperatura= $row->temperatura_internacao;
$dor = $row->dor_internacao;
$ecg= $row->ecg_internacao;
$frequencia_respiratoria = $row->frequencia_respiratoria;


$dia    = date('d', strtotime($data_transacao));
$mes    = date('m', strtotime($data_transacao));
$ano    = date('Y', strtotime($data_transacao));
$semana = date('w', strtotime($data_transacao));


// configuração mes

switch ($mes) {
    case 1:
        $mes = "Janeiro";
        break;
    case 2:
        $mes = "Fevereiro";
        break;
    case 3:
        $mes = "Março";
        break;
    case 4:
        $mes = "Abril";
        break;
    case 5:
        $mes = "Maio";
        break;
    case 6:
        $mes = "Junho";
        break;
    case 7:
        $mes = "Julho";
        break;
    case 8:
        $mes = "Agosto";
        break;
    case 9:
        $mes = "Setembro";
        break;
    case 10:
        $mes = "Outubro";
        break;
    case 11:
        $mes = "Novembro";
        break;
    case 12:
        $mes = "Dezembro";
        break;
}
switch ($semana) {


    case 0:
        $semana = "Domingo";
        break;
    case 1:
        $semana = "Segunda Feira";
        break;
    case 2:
        $semana = "Terça Feira";
        break;
    case 3:
        $semana = "Quarta Feira";
        break;
    case 4:
        $semana = "Quinta Feira";
        break;
    case 5:
        $semana = "Sexta Feira";
        break;
    case 6:
        $semana = "Sábado";
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
            $oque_faz,    $com_oqfaz, $tempo_faz,    $como_faz, $queixa, $exame_fisico, $cid_internacao, $pressaodiastolica, $pressaosistolica, $peso,
            $temperatura, $relato, $discriminador, $destino, $tipo_leito, $prioridade, $atendprioridade, $cns, $diagnostico_principal, $horacad, $datacad,
            $data_destino, $hora_destino, $destino_paciente, $obs_modal, $dor, $pulso,$glicemia,$pa_sitolica,$pa_distolica,$oxigenio,$temperatura,$ecg,$frequencia_respiratoria,$data_internacao,$hora_internacao;
        $this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
        $this->Image('app-assets/img/gallery/sus.jpg', 80, 3, 22);
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
        $this->Cell(185, 5, 'SECRETARIA MUNICIPAL DE SAUDE-UBERABA', 0, 0, 'C');
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(185, 5, 'FORMULARIO - DIRETORIA TECNICA', 0, 0, 'C');
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(185, 5, utf8_decode('SOLICITAÇÃO DE INTERNAÇÃO / URGÊNCIA'), 0, 0, 'C');

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
        $this->Line(10, 30, 195, 30);


        $this->Line(157, 270, 157, 290);
        $this->Line(10, 280, 195, 280);
        $this->Line(10, 290, 195, 290);
        $this->Line(10, 30, 10, 290);
        $this->Line(195, 30, 195, 290);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 5, ' UNIDADE.:', 0, 0, 'R');
        $this->SetFont('Arial', '', 9);
        $this->Cell(126, 5, 'UNIDADE DE PRONTO ATENDIMENTO UPA ' . utf8_decode(UNIDADE_CONFIG), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 5, ' CNES:', 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(40, 5, '2164817', 0, 0, 'L');
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 5, ' DATA:', 0, 0, 'R');
        $this->SetFont('Arial', '', 9);
        //$this->Cell(25,5, date('d/m/Y'),0,0,'L');
        $this->Cell(25, 5, $data_internacao, 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 5, 'HORA:', 0, 'R');
        $this->SetFont('Arial', '', 9);
        //$this->Cell(20,5,date('H:i:s'),0,0,'L');
        $this->Cell(20, 5, $hora_internacao, 0, 0, 'L');

        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, 'CARTAO SUS:', 0, 'R');
        $this->SetFont('Arial', '', 9);
        $this->Cell(35, 5, $cns, 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 5, 'Prontuario:', 0, 'R');
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 5, $prontuario, 0, 0, 'L');
        $this->Ln(5);
        $this->SetFont('Arial', 'BI', 12);
        $this->Cell(185, 8, utf8_decode('IDENTIFICAÇÃO DO PACIENTE'), 1, 0, 'C');
        $this->Ln(8);
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 8, ' NOME:', 0, 0, 'L');
        $this->SetFont('Arial', 'BI', 12);
        $this->Cell(115, 8, utf8_decode($nome), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 8, ' IDADE.:', 0, 0, 'R');
        $this->Cell(40, 8, $idade, 0, 0, 'L');
        $this->Ln(7);
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, utf8_decode(' NOME DA MAE:'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(110, 5, utf8_decode($nome_mae), 0, 0, 'L');
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, utf8_decode(' ENDEREÇO:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(110, 5, utf8_decode($enderecox) . ' ' . $end_numero, 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 5, ' SEXO.:', 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        if ($sexo == 'F') {
            $this->Cell(25, 5, 'Feminino', 0, 0, 'L');
        } else {
            $this->Cell(25, 5, 'Masculino', 0, 0, 'L');
        }
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
        $this->Cell(18, 5, ' BAIRRO:', 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(50, 5, $bairro, 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, ' LOCALIDADE:', 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(37, 5, $cidade . ' ' . $uf, 0, 0, 'L');

        $this->Cell(18, 5, 'DT. NASC:', 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, inverteData($dt_nasc), 0, 0, 'L');
        $this->Ln(5);
        $this->Cell(30, 5, ' TELEFONE:', 0, 0, 'L');
        $this->Cell(20, 5, $telefone, 0, 0, 'L');
        $this->Cell(20, 5, ' CELULAR:', 0, 0, 'L');
        $this->Cell(20, 5, $celular, 0, 0, 'L');
        $this->Cell(20, 5, ' RECADOS:', 0, 0, 'L');
        $this->Cell(19, 5, $telefone2, 0, 0, 'L');
        $this->Cell(19, 5, ' DRS:', 0, 0, 'R');
        $this->Cell(25, 5, 'UBERABA', 0, 0, 'L');
        $this->Ln(7);
        $this->Cell(190, 5, utf8_decode(' ASSINATURA DO PACIENTE /RESPONSÁVEL:______________________________________________________'), 0, 0, 'L');
        $this->Ln(5);


        $this->SetFont('Arial', 'B', 9);
        $this->Cell(185, 5, utf8_decode(' OBSERVAÇÕES IMPORTANTES/ ANAMNESE'), 1, 0, 'C');
        $this->Ln(7);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(185, 3, utf8_decode(str_replace('/p', "\n", $obs_modal)), '', 'J', 0);
        //$this->MultiCell(185,3,utf8_decode($queixa.".\n". $obs_modal), '', 'J', 0);
        //$this->MultiCell(185,3,utf8_decode($queixa." - ". $exame_fisico), '', 'J', 0);
        $this->Ln(27);


        //$this->SetFont('Arial','B',9);
        $this->SetXY(10, 185);
        $this->Cell(185, 5, utf8_decode('SINAIS VITAIS         | Pulsos: '.$pulso.'            Glicemia: '.$glicemia.'           PA: '.$pa_sitolica.'x'.$pa_distolica.'       FR:'. $frequencia_respiratoria.'      dor: '.$dor.'      SpO2: '.$oxigenio.'        T.axiliar: '.$temperatura.'        Glasgow: '.$ecg.'          '), 1, 0, 'L');
        $this->Ln(7);
        $this->Cell(185, 5, utf8_decode(' MEDICAMENTOS EM USO'), 0, 0, 'L');
        $this->Line(50, 190, 50, 205);


        //$this->SetFont('Arial','',9);
        $this->SetXY(10, 205);

        //$this->Cell(90, 9, utf8_decode(' CLINICA :   ' . $destino), 1, 0, 'L');
        $this->Cell(90, 9, utf8_decode(' CLINICA :   '), 1, 0, 'L');

        $this->Cell(60, 9, utf8_decode(' TIPO LEITO:   '.$tipo_leito), 1, 0, 'L');
        $this->Cell(35, 9, utf8_decode(' CID:   ' . $cid_internacao), 1, 0, 'L');

        $this->Ln(9);
        $this->SetFont('Arial', '', 8);
        $this->Cell(185, 9, utf8_decode(' PROCEDIMENTO A SER REALIZADO (de acordo com o CID): '), 1, 0, 'L');
        $this->Ln(9);
        $this->SetFont('Arial', '', 9);
        $this->Cell(185, 5, utf8_decode(' Carater de Internação:     (   ) Urgência          (   ) Sofrimento Intenso         (   ) Eletiva            (   ) Risco de Morte'), 1, 0, 'L');
        $this->Ln(7);
        $this->Cell(185, 5, utf8_decode(' Condições que justifiquem'), 0, 0, 'L');
        $this->Ln(7);
        $this->Cell(185, 5, utf8_decode(' a internação'), 0, 0, 'L');
        $this->Line(50, 228, 50, 245);
        $this->Line(10, 242, 195, 242);
        $this->Ln(7);
        $this->Cell(185, 5, utf8_decode(' Principais Resultados de'), 0, 0, 'L');
        $this->Ln(7);
        $this->Cell(185, 5, utf8_decode(' Provas Diagnósticas'), 0, 0, 'L');
        $this->Line(50, 245, 50, 270);
        $this->Line(50, 251, 195, 251);
        $this->Line(50, 260, 195, 260);
        $this->Line(10, 270, 195, 270);
        $this->Ln(20);
        $this->SetFont('Arial', '', 9);
        $this->Cell(146, 5, utf8_decode(' Assinatura e Carimbo:'), 0, 0, 'L');
        $this->Cell(25, 5, utf8_decode(' Data:_____/_____/_____'), 0, 0, 'L');
        $this->Ln(4);
        $this->Cell(146, 5, utf8_decode(' Médico Solicitante:'), 0, 0, 'L');
        $this->Cell(25, 5, utf8_decode(' Hora: ______:______'), 0, 0, 'L');
        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
        $this->Cell(146, 5, utf8_decode(' Assinatura do Responsável:'), 0, 0, 'L');
        $this->Cell(25, 5, utf8_decode(' Data:_____/_____/_____'), 0, 0, 'L');
        $this->Ln(4);
        $this->Cell(146, 5, utf8_decode(' por inserir SISREG:'), 0, 0, 'L');
        $this->Cell(25, 5, utf8_decode(' Hora: ______:______'), 0, 0, 'L');
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
        //$this->Cell(0,10,'Transacao - '.str_pad($transacao,7,"0",STR_PAD_LEFT).' - '.$usuario_transacao.' '.$data_transacao.' '.$hora_transacao.'/{nb}',0,0,'C');
    }

    public function Codabar($xpos, $ypos, $code, $start = 'A', $end = 'A', $basewidth = 0.35, $height = 10)
    {
        $barChar = array(
            '0' => array(6.5, 10.4, 6.5, 10.4, 6.5, 24.3, 17.9),
            '1' => array(6.5, 10.4, 6.5, 10.4, 17.9, 24.3, 6.5),
            '2' => array(6.5, 10.0, 6.5, 24.4, 6.5, 10.0, 18.6),
            '3' => array(17.9, 24.3, 6.5, 10.4, 6.5, 10.4, 6.5),
            '4' => array(6.5, 10.4, 17.9, 10.4, 6.5, 24.3, 6.5),
            '5' => array(17.9,    10.4, 6.5, 10.4, 6.5, 24.3, 6.5),
            '6' => array(6.5, 24.3, 6.5, 10.4, 6.5, 10.4, 17.9),
            '7' => array(6.5, 24.3, 6.5, 10.4, 17.9, 10.4, 6.5),
            '8' => array(6.5, 24.3, 17.9, 10.4, 6.5, 10.4, 6.5),
            '9' => array(18.6, 10.0, 6.5, 24.4, 6.5, 10.0, 6.5),
            '$' => array(6.5, 10.0, 18.6, 24.4, 6.5, 10.0, 6.5),
            '-' => array(6.5, 10.0, 6.5, 24.4, 18.6, 10.0, 6.5),
            ':' => array(16.7, 9.3, 6.5, 9.3, 16.7, 9.3, 14.7),
            '/' => array(14.7, 9.3, 16.7, 9.3, 6.5, 9.3, 16.7),
            '.' => array(13.6, 10.1, 14.9, 10.1, 17.2, 10.1, 6.5),
            '+' => array(6.5, 10.1, 17.2, 10.1, 14.9, 10.1, 13.6),
            'A' => array(6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
            'T' => array(6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
            'B' => array(6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
            'N' => array(6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
            'C' => array(6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
            '*' => array(6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
            'D' => array(6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
            'E' => array(6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
        );
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





$pdf->Codabar(149, 6, $transacao);






$pdf->Output();
