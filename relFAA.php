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
include('verifica.php');
include('conexao.php');
$stmt = "select a.transacao, a.cid_principal,a,hora_cad,l.nome as nomecad, a.destino_paciente, a.data_destino, a.queixa, a.exame_fisico, a.diagnostico_principal,a.prioridade,
		a.paciente_id, a.status, a.tipo, a.dat_cad as cadastro, c.nome,c.nome_social, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco, a.oque_faz, a.com_oqfaz, 
		a.tempo_faz, a.como_faz, c.numero, c.complemento, c.bairro, c.nome_mae, c.num_carteira_convenio, c.cep, c.cpf, c.cidade, c.estado, a.observacao, k.origem as origem_chegada,  
		x.peso, x.pressaodiastolica, x.pressaosistolica,x.oxigenio,x.dor, x.queixa as relato, x.pulso, x.temperatura,x.discriminador, x.prioridade as atendprioridade,x.glicose as glicemia,
		a.data_destino,a.hora_destino,a.destino_paciente, z.nome as medico_atendimento, f.nome as medico_finalizador, a.acompanhante, w.nome as usuario_triagem, a.hora_triagem, w.num_conselho_reg as coren, w.perfil
		from atendimentos a 
		left join pessoas c on a.paciente_id=c.pessoa_id
		left join pessoas l on l.username = a.cad_user
		left join pessoas z on z.username = a.med_atendimento
		left join pessoas f on f.username = a.med_finalizador
		left join tipo_origem k on cast(k.tipo_id as varchar)=a.tipo 
		left join classificacao x ON ltrim(x.atendimento_id, '0')= '$transacao'
		left join pessoas w ON w.username =  x.usuario
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
$datacad = date('d/m/Y',  strtotime($row->cadastro));
$prontuario = $row->paciente_id;
$dor = $row->dor;
$oxigenio = $row->oxigenio;
$sexo = $row->sexo;
$nome = $row->nome;
$nome_social = $row->nome_social;
$email = $row->email;
$dt_nascimento = inverteData($row->dt_nasc);
$enderecox = $row->endereco;
$end_numero = $row->numero;
$complemento = $row->complemento;
$bairro = $row->bairro;
$cns    = $row->num_carteira_convenio;
$cidade = $row->cidade;
$estado = $row->estado;
$nomecad = $row->nomecad;
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
$origem_chegada = $row->origem_chegada;
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
$destino  = $row->destino_paciente;
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
if ($row->medico_finalizador) {
    $medico_atendimento = $row->medico_finalizador;
} else {
    $medico_atendimento = $row->medico_atendimento;
}
$acompanhante = $row->acompanhante;
$usuario_triagem = $row->usuario_triagem;
$hora_triagem = $row->hora_triagem;
$coren = $row->coren;
$perfil = $row->perfil;
$dia    = date('d', strtotime($data_transacao));
$mes    = date('m', strtotime($data_transacao));
$ano    = date('Y', strtotime($data_transacao));
$semana = date('w', strtotime($data_transacao));


$data = date('Y-m-d');
$hora = date('H:i');
$atendimento_id = $_GET['id'];
$ip = $_SERVER['REMOTE_ADDR'];
include('conexao.php');
$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora, ip) 
			values ('$usuario','FEZ ACESSO A FICHA DE ATENDIMENTO','$atendimento_id','$data','$hora', '$ip')";
$sthLogs = pg_query($stmtLogs) or die($stmtLogs);


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
    function Header()
    {

        // Logo
        global $transacao, $prontuario, $nome, $nome_mae, $sexo, $idade, $nome_convenio, $origem, $enfermaria, $leito, $solicitante, $dt_nasc,
            $telsolicitante, $senha, $dt_solicitacao, $enderecox, $end_numero, $complemento, $bairro, $cep, $cpf, $cidade, $estado, $telefone, $celular,
            $oque_faz,    $com_oqfaz, $tempo_faz,    $como_faz, $queixa, $exame_fisico, $cid_principal, $pressaodiastolica, $pressaosistolica, $peso,
            $temperatura, $pulso, $relato, $discriminador, $destino, $prioridade, $atendprioridade, $cns, $diagnostico_principal, $horacad, $datacad, $glicemia,
            $data_destino, $hora_destino, $destino_paciente, $dor, $oxigenio, $nomecad, $medico_atendimento, $nome_social, $origem_chegada, $acompanhante, $usuario_triagem,
            $coren, $hora_triagem, $perfil;
        $this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
        $this->Image('app-assets/img/gallery/sus.jpg', 80, 3, 22);
        $this->Ln(6);
        $this->SetFont('Arial', '', 9);
        $this->Cell(185, 5, 'SECRETARIA MUNICIPAL DE SAUDE - UBERABA', 0, 0, 'C');
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(133, 5, 'FAU - FICHA DE ATENDIMENTO de URGENCIA', 0, 0, 'R');
        $this->Cell(0, 5, $prioridade, 0, 0, 'C');
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
        $this->Line(10,  25, 195, 25);




        $this->Line(160, 267, 160, 285);
        $this->Line(10, 276, 195, 276);

        $this->Line(10, 285, 195, 285);

        $this->Line(10, 25,  10, 285);
        $this->Line(195, 25, 195, 285);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 5, ' UNIDADE.:', 0, 0, 'R');
        $this->SetFont('Arial', '', 9);

        /*
	if ($_SESSION['unidade']=='1')
	{	
		$this->Cell(126,5,'UNIDADE DE PRONTO ATENDIMENTO UPA PARQUE DO MIRANTE',0,0,'L');
		$this->SetFont('Arial','',9);
		$this->Cell(15,5,' CNES:',0,'L');
		$this->SetFont('Arial','',9);
		$this->Cell(40,5,'7093284',0,0,'L');
	}
	if ($_SESSION['unidade']=='2')
	{*/
        $this->Cell(126, 5, 'UNIDADE DE PRONTO ATENDIMENTO UPA ' . utf8_decode(UNIDADE_CONFIG), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 5, ' CNES:', 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(40, 5, CNES, 0, 0, 'L');
        //}

        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
        $this->Cell(20, 5, ' DATA:', 0, 0, 'R');
        $this->SetFont('Arial', '', 9);
        //$this->Cell(25,5, date('d/m/Y'),0,0,'L');
        $this->Cell(25, 5, $datacad, 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 5, 'HORA:', 0, 'R');
        $this->SetFont('Arial', '', 9);
        //$this->Cell(20,5,date('H:i:s'),0,0,'L');
        $this->Cell(20, 5, $horacad, 0, 0, 'L');

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
        if ($nome_social == '') {
            $this->SetFont('Arial', 'BI', 12);
            $this->Cell(111.5, 8, utf8_decode($nome), 0, 0, 'L');
        } else {
            $this->SetFont('Arial', 'BI', 9);
            $this->Cell(115, 8, utf8_decode($nome_social . ' (' . $nome . ')'), 0, 0, 'L');
        }
        $this->SetFont('Arial', '', 9);
        $this->Cell(15, 8, ' IDADE.:', 0, 0, 'R');
        $this->Cell(40, 8, $idade, 0, 0, 'L');
        $this->Ln(7);
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, utf8_decode(' NOME DA MAE:'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(103, 5, utf8_decode($nome_mae), 0, 0, 'L');

        if ($acompanhante != '') {
            $this->SetFont('Arial', '', 9);
            $this->Cell(30, 5, utf8_decode('ACOMPANHANTE:'), 0, 0, 'L');
            $this->SetFont('Arial', 'B', 9);
            $acompanhante = explode(" ", $acompanhante);
            $this->Cell(110, 5, utf8_decode($acompanhante[0] . ' ' . $acompanhante[1]), 0, 0, 'L');
        }


        $this->Ln(5);
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, utf8_decode(' ENDEREÇO:'), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(102, 5, utf8_decode($enderecox) . ', ' . $end_numero, 0, 0, 'L');
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
        $this->Cell(35.1, 5, $cidade . ' ' . $uf, 0, 0, 'L');

        $this->Cell(18, 5, 'DT. NASC:', 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 5, inverteData($dt_nasc), 0, 0, 'L');
        $this->Ln(5);
        $this->Cell(30, 5, ' TELEFONE:', 0, 0, 'L');
        $this->Cell(20, 5, $telefone, 0, 0, 'L');
        $this->Cell(20, 5, ' CELULAR:', 0, 0, 'L');
        $this->Cell(20, 5, $celular, 0, 0, 'L');
        //$this->Cell(20,5,' RECADOS:',0,0,'L');
        $this->Cell(29, 5, $telefone2, 0, 0, 'L');
        $this->Cell(19, 5, ' DRS:', 0, 0, 'R');
        $this->Cell(25, 5, 'UBERABA', 0, 0, 'L');
        $this->Ln(7);
        $this->Cell(190, 5, utf8_decode(' ASSINATURA DO PACIENTE /RESPONSÁVEL:______________________________________________________'), 0, 0, 'L');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(185, 5, ' CLASSIFICACAO DE RISCO', 1, 0, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Ln(5);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(185, 5, 'ORIGEM: ' . $origem_chegada, 1, 0, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Ln(2);

        //classificação de risco
        $this->Cell(30, 13, '1 - DISCRIMINADOR', 0, 0, 'L');
        $this->Cell(60, 13, utf8_decode($discriminador), 0, 0, 'L');

        $this->Cell(30, 13, '2 - PRIORIDADE', 0, 0, 'L');
        $this->Cell(20, 13, $atendprioridade, 0, 0, 'L');


        $this->Cell(30, 13, '3 - PA Diastolica', 0, 0, 'L');
        $this->Cell(60, 13, $pressaodiastolica, 0, 0, 'L');
        $this->Ln(8);


        $this->Cell(30, 7, utf8_decode('4 - PA Sistolica'), 0, 0, 'L');
        $this->Cell(20, 7, $pressaosistolica, 0, 0, 'L');

        $this->Cell(20, 7, '5 - Peso', 0, 0, 'L');
        $this->Cell(20, 7, $peso, 0, 0, 'L');

        $this->Cell(30, 7, utf8_decode('6 - Temperatura:'), 0, 0, 'L');
        $this->Cell(20, 7, $temperatura, 0, 0, 'L');

        $this->Cell(25, 7, '7 - Pulso', 0, 0, 'L');
        $this->Cell(30, 7, $pulso, 0, 0, 'L');

        $this->Ln(5);
        $this->Cell(30, 7, '8 - Glicemia', 0, 0, 'L');
        $this->Cell(20, 7, $glicemia, 0, 0, 'L');


        $this->Cell(20, 7, '9 - Dor', 0, 0, 'L');
        $this->Cell(20, 7, $dor, 0, 0, 'L');

        $this->Cell(30, 7, utf8_decode('10 - Oxigênio:'), 0, 0, 'L');
        $this->Cell(20, 7, $oxigenio, 0, 0, 'L');


        $this->Ln(6);
        $this->Cell(17, 5, utf8_decode('11 - Queixa: '), 0, 0, 'L');
        //$this->Cell(20,5, utf8_decode($relato),0,0,'L');

        $this->SetXY(27, 105.5);
        $this->MultiCell(165, 4, utf8_decode($relato), 0, 'J');

        if ($perfil == '03') {
            $this->Cell(120, 7, utf8_decode('Medica(o): ' . $usuario_triagem . ' - CRM: ' . $coren), 0, 0, 'L');
        } else {
            $this->Cell(120, 7, utf8_decode('Enfermeira(o): ' . $usuario_triagem . ' - COREN: ' . $coren), 0, 0, 'L');
        }
        $this->Cell(65, 7, utf8_decode('hora triagem: ' . $hora_triagem), 0, 0, 'R');

        $this->Ln(6);


        $this->SetFont('Arial', 'B', 9);
        $this->Cell(185, 5, utf8_decode(' INFORMAÇÕES DOS ATENDIMENTOS REALIZADOS '), 1, 0, 'C');
        $this->Ln(7);
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(185, 3, utf8_decode(strtoupper($queixa) . " - " . strtoupper($exame_fisico)), '', 'J', 0);
        $this->Ln(27);


        //$this->SetFont('Arial','B',9);
        // $this->SetXY(10, 183);
        // $this->Cell(185, 5, utf8_decode(' PRESCRIÇÃO MEDICA '), 1, 0, 'C');
        // $this->Ln(5.5);


        include('conexao.php');


        $query = "SELECT 
				CASE 
					WHEN a.cuidados is null THEN 1
					WHEN a.cuidados != '' THEN 2
				END as prioridade, a.prescricao_id,a.hora,a.data,a.descricao,b.nome, a.dosagem,a.aprazamento,a.via, a.cuidados
				FROM prescricao_itens a
				left join pessoas b ON b.username = a.medico
				WHERE a.atendimento_id =" . $transacao . " order by prioridade, a.prescricao_id DESC LIMIT 6";

        $sthquery = pg_query($query) or die($query);
        $i = 0;
        while ($rows = pg_fetch_object($sthquery)) {
            $this->SetFont('Arial', '', 8);

            if ($rows->descricao != '') {
                $this->Cell(185, 6, utf8_decode($rows->descricao . " - " . $rows->dosagem . " " . $rows->aprazamento), 0, 0, 'L');
            }

            if ($rows->cuidados != '') {
                $this->Cell(185, 6, utf8_decode($rows->cuidados), 0, 0, 'L');
            }

            $this->Ln(4);
            $i++;
        }
        $this->Ln(35 - ($i * 4));

        //$this->SetFont('Arial','',9);
        $this->SetXY(10, 215);



        $destino = '';
        if ($destino_paciente == '01') {
            $destino = 'ALTA';
        } else if ($destino_paciente == '02') {
            $destino = 'ALTA / ENCAM. AMBUL.';
        } else if ($destino_paciente == '07') {
            $destino = 'EM OBSERVAÇÃO / MEDICAÇÃO';
        } else if ($destino_paciente == '10') {
            $destino = 'EXAMES / REAVALIACAO';
        } else if ($destino_paciente == '03') {
            $destino = 'PERMANÊCIA.';
        } else if ($destino_paciente == '04') {
            $destino = 'TRANSF. OUTRA UPA';
        } else if ($destino_paciente == '05') {
            $destino = 'TRANSF. INTERN. HOSPITALAR';
        } else if ($destino_paciente == '06') {
            $destino = 'ÓBITO';
        } else if ($destino_paciente == '08') {
            $destino = 'NAO RESPONDEU CHAMADO';
        } else if ($destino_paciente == '09') {
            $destino = 'NAO RESPONDEU CHAMADO';
        } else if ($destino_paciente == '11') {
            $destino = 'ALTA EVASÃO';
        } else if ($destino_paciente == '12') {
            $destino = 'ALTA PEDIDO';
        } else if ($destino_paciente == '14') {
            $destino = 'ALTA / PM';
        } else if ($destino_paciente == '15') {
            $destino = 'ALTA / PENITENCIÁRIA';
        }


        $this->Cell(115, 9, utf8_decode(' DESTINO DADO AO USUÁRIO :   ' . $destino), 1, 0, 'L');

        if ($data_destino != '' and $hora_destino != '') {
            $this->Cell(40, 9, utf8_decode(' DATA:   ' . date('d/m/Y', strtotime($data_destino))), 1, 0, 'L');

            $this->Cell(30, 9, utf8_decode(' HORA:   ' . $hora_destino), 1, 0, 'L');
        } else {
            $this->Cell(40, 9, utf8_decode(' DATA:   '), 1, 0, 'L');

            $this->Cell(30, 9, utf8_decode(' HORA:   '), 1, 0, 'L');
        }





        $this->Ln(9);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(155, 9, utf8_decode(' DIAGNÓSTICO PRINCIPAL: ') . ' ' . utf8_decode(substr($diagnostico_principal, 0, 69)), 1, 0, 'L');

        $this->Cell(10, 9, utf8_decode(' CID: '), 1, 0, 'L');
        $this->Cell(20, 9, $cid_principal, 1, 0, 'C');
        $this->Ln(9);
        $this->SetFont('Arial', '', 9);
        $this->Cell(157, 5, utf8_decode(' CODIGO: SIA/SUS (PROCEDIMENTOS E CODIGO): '), 0, 0, 'L');
        $this->Ln(15);
        //$this->SetFont('Arial','B',9);
        $this->SetXY(10, 245);
        $this->Cell(185, 5, utf8_decode(' IDENTIFICAÇÃO DO PROFISSIONAL '), 1, 0, 'C');
        $this->Ln(6);
        $this->SetFont('Arial', '', 9);
        $this->Cell(155, 5, utf8_decode(' ASSINATURA: '), 0, 0, 'L');
        $this->Cell(25, 5, utf8_decode(' CARIMBO: '), 0, 0, 'L');
        $this->Ln(20);
        $this->SetXY(10, 262);
        $this->Cell(185, 5, utf8_decode(' ANOTAÇÕES DO SERVIÇO DE CONTROLE E AVALIZAÇÃO DO SUS '), 1, 0, 'C');
        $this->Ln(6);
        $this->SetFont('Arial', '', 9);
        $this->Cell(155, 5, utf8_decode(' ASSINATURA E CARIMBO: (REVISOR TÉCNICO)'), 0, 0, 'L');
        $this->Cell(25, 5, utf8_decode(' DATA: '), 0, 0, 'L');
        $this->Ln(10);
        $this->SetFont('Arial', '', 9);
        $this->Cell(155, 5, utf8_decode(' ASSINATURA E CARIMBO: (REVISOR ADMINISTRATIVO) '), 0, 0, 'L');
        $this->Cell(25, 5, utf8_decode(' DATA: '), 0, 0, 'L');
        $this->Ln(8);

        $this->SetFont('Arial', '', 7);
        $this->Cell(150, 5, utf8_decode('FICHA CADASTRADA POR: ' . $nomecad . '                           ATENDIDO POR: ' . $medico_atendimento), 0, 0, 'L');
        $this->Ln(15);
    }

    // Page footer
    function Footer()
    {
        global $transacao, $usuario_transacao, $data_transacao, $hora_transacao;
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        //$this->Cell(0,10,'Transacao - '.str_pad($transacao,7,"0",STR_PAD_LEFT).' - '.$usuario_transacao.' '.$data_transacao.' '.$hora_transacao.'/{nb}',0,0,'C');
    }

    function Codabar($xpos, $ypos, $code, $start = 'A', $end = 'A', $basewidth = 0.35, $height = 10)
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
