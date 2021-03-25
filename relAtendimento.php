<?php

require '../vendor/autoload.php';
require 'fpdf/fpdf.php';
function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}
$i = 0;
$transacao = $_GET['id'];
$modalidade = $_GET['modalidade'];
$tipo_relatorio = strtoupper($_GET['tipo_relatorio']);

include 'verifica.php';

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
			$data_destino, $hora_destino, $destino_paciente, $dor, $oxigenio, $nomecad, $medico_atendimento, $nome_social, $origem_chegada, $acompanhante, $tipo_relatorio, $i;
		$this->Image('app-assets/img/gallery/logo.png', 10, 5, 48);
		$this->Image('app-assets/img/gallery/sus.jpg', 80, 3, 22);
		$this->Ln(6);
		$this->SetFont('Arial', '', 9);
		$this->Cell(185, 5, 'SECRETARIA MUNICIPAL DE SAUDE-UBERABA', 0, 0, 'C');
		$this->Ln(4);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(185, 5, UTF8_DECODE(''), 0, 0, 'C');
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
		$this->Line(10, 25, 195, 25);

		$this->Line(10, 275.8, 195, 275.8);

		$this->Line(10, 25, 10, 275.8);
		$this->Line(195, 25, 195, 275.8);

		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, ' UNIDADE.:', 0, 0, 'R');
		$this->SetFont('Arial', '', 9);

		$this->Cell(120, 5, 'UNIDADE DE PRONTO ATENDIMENTO ' . utf8_decode(UNIDADE_CONFIG), 0, 0, 'L');
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, 5, ' CNES:', 0, 'L');
		$this->Cell(40, 5, '2164817', 0, 0, 'L');
		$this->Ln(5);
		$this->Cell(120, 5, '  IMPRESSO EM: ' . date('d/m/Y') . ' AS ' . date('H:i:s'), 0, 0, 'L');
		$this->Ln(7);

		$this->SetFont('Arial', 'BI', 12);

		$start = $_GET['start'];
		$end = $_GET['end'];
		$especialidade = $_GET['especialidade'];

		if ($tipo_relatorio == 'ESPECIALIDADE') {
			$where = '';
			if ($especialidade == '' or $especialidade == 'todos') {
				$where = "where especialidade != '' and dat_cad between '$start' and '$end'";
			} else {
				$where = "where especialidade != '' and dat_cad between  '" . inverteData($start) . "' and '" . inverteData($end) . "'and especialidade = '$especialidade'";
			}

			include 'conexao.php';
			$stmtRelCont = 'SELECT count(*) as qtde
					from atendimentos';
			$stmtRelCont = $stmtRelCont . ' ' . $where;
			$sthRelCont = pg_query($stmtRelCont);
			$rowCount = pg_fetch_object($sthRelCont);
			$qtde_atendimentos = $rowCount->qtde;

			$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ATENDIMENTO - ESPECIALIDADE'), 1, 0, 'C');
			$this->Ln(7);
			$this->Cell(80, 7, utf8_decode('Quantidade:   ' . $qtde_atendimentos), 1, 0, 'C');
			$this->Cell(105, 7, utf8_decode('Período:   ' . $_GET['start'] . '   ATÉ   ' . $_GET['end']), 1, 0, 'C');
		}

		if ($tipo_relatorio == 'TRANSFERENCIA') {
			include 'conexao.php';
			$stmtRelCont = "select count(*) as qtde from destino_paciente d
				  where d.hospital is not null and data between '$start' and '$end'";
			$sthRelCont = pg_query($stmtRelCont);
			$rowCount = pg_fetch_object($sthRelCont);
			$qtde_atendimentos = $rowCount->qtde;

			$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ATENDIMENTO - TRANSFERENCIAS HOSPITALARES'), 1, 0, 'C');
			$this->Ln(7);
			$this->Cell(80, 7, utf8_decode('Quantidade:   ' . $qtde_atendimentos), 1, 0, 'C');
			$this->Cell(105, 7, utf8_decode('Período:   ' . $_GET['start'] . '   ATÉ   ' . $_GET['end']), 1, 0, 'C');
		}

		if ($tipo_relatorio == 'PRIORIDADE') {
			$where = '';
			if ($especialidade == '' or $especialidade == 'todos') {
				$where = "where especialidade != '' and dat_cad between  '" . inverteData($start) . "' and '" . inverteData($end) . "'";
			} else {
				$where = "where especialidade != '' and dat_cad between  '" . inverteData($start) . "' and '" . inverteData($end) . "'and especialidade = '$especialidade'";
			}

			include 'conexao.php';
			$stmtRelCont = 'SELECT count(*) as qtde
					from atendimentos';
			$stmtRelCont = $stmtRelCont . ' ' . $where;
			$sthRelCont = pg_query($stmtRelCont);
			$rowCount = pg_fetch_object($sthRelCont);
			$qtde_atendimentos = $rowCount->qtde;

			if ($especialidade != '') {
				$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ATENDIMENTO - ' . $especialidade), 1, 0, 'C');
			} else {
				$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ATENDIMENTO - TODOS'), 1, 0, 'C');
			}

			$this->Ln(7);
			$this->Cell(80, 7, utf8_decode('Quantidade:   ' . $qtde_atendimentos), 1, 0, 'C');
			$this->Cell(105, 7, utf8_decode('Período:   ' . $_GET['start'] . '   ATÉ   ' . $_GET['end']), 1, 0, 'C');
		}

		if ($tipo_relatorio == 'CLASSIFICACAO') {
			include 'conexao.php';
			$stmtRelCont = "SELECT count(*) as qtde
					from atendimentos a where a.dat_cad between '$start' and '$end'";
			$sthRelCont = pg_query($stmtRelCont);
			$rowCount = pg_fetch_object($sthRelCont);
			$qtde_atendimentos = $rowCount->qtde;

			$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ATENDIMENTO - CLASSIFICAÇÃO'), 1, 0, 'C');
			$this->Ln(7);
			$this->Cell(80, 7, utf8_decode('Quantidade:   ' . $qtde_atendimentos), 1, 0, 'C');
			$this->Cell(105, 7, utf8_decode('Período:   ' . $_GET['start'] . '   ATÉ   ' . $_GET['end']), 1, 0, 'C');
		}
		if ($tipo_relatorio == 'DEMANDA') {
			include 'conexao.php';
			$stmtRelCont = "SELECT count(*) as qtde
					from atendimentos a where a.dat_cad between '$start' and '$end'";
			$sthRelCont = pg_query($stmtRelCont);
			$rowCount = pg_fetch_object($sthRelCont);
			$qtde_atendimentos = $rowCount->qtde;

			$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ATENDIMENTO - DEMANDA'), 1, 0, 'C');
			$this->Ln(7);
			$this->Cell(80, 7, utf8_decode('Quantidade:   ' . $qtde_atendimentos), 1, 0, 'C');
			$this->Cell(105, 7, utf8_decode('Período:   ' . $_GET['start'] . '   ATÉ   ' . $_GET['end']), 1, 0, 'C');
		}

		// if($tipo_relatorio == 'EXAMES'){
		// 	$modalidade = $_GET['modalidade'];
		// 	$where ='';

		// 	if($modalidade != '') {
		// 		if ($modalidade == 5) {
		// 			$where = ' and modalidade_id > '.$modalidade;
		// 		}else {
		// 			$where = ' and modalidade_id = '.$modalidade;
		// 		}

		// 	}
		// 	include('conexao.php');
		// 	$stmtRelCont="select count(*) as qtde
		//                               from itenspedidos a
		//                               left join atendimentos b on a.atendimento_id = b.transacao
		//                               left join procedimentos p on p.procedimento_id = a.exame_id
		//                               where b.dat_cad between  '".inverteData($start)."' and '".inverteData($end)."' $where
		// 							  and a.situacao = 'Finalizado'";

		// 	$sthRelCont = pg_query($stmtRelCont);
		// 	$rowCount = pg_fetch_object($sthRelCont);
		// 	$qtde_atendimentos=$rowCount->qtde;

		// 	$modalidade = $_GET['modalidade'];
		// 	include('conexao.php');
		// 	$stmtTitulo="select descricao as modalidade from modalidades where modalidade_id = $modalidade";
		// 	$sthTitulo = pg_query($stmtTitulo);
		// 	$rowTitulo = pg_fetch_object($sthTitulo);
		// 	$nomeModalidade=$rowTitulo->modalidade;

		// 	if($nomeModalidade == ''){
		// 		$this->Cell(185,7,utf8_decode('RELATÓRIO DE ATENDIMENTO - EXAMES'),1,0,'C');
		// 	}else{
		// 		$this->Cell(185,7,utf8_decode('RELATÓRIO DE ATENDIMENTO - EXAMES ('.$nomeModalidade.')'),1,0,'C');
		// 	}

		// 	$this->Ln(7);
		// 	$this->Cell(80,7,utf8_decode('Quantidade:   '.$qtde_atendimentos),1,0,'C');
		// 	$this->Cell(105,7,utf8_decode('Período:   '.$_GET['start'].'   ATÉ   '.$_GET['end']),1,0,'C');
		// }

		if ($tipo_relatorio == '') {
			include 'conexao.php';
			$stmtRelCont = "SELECT count(*) as quantidade from atendimentos a left join destino_paciente z on z.atendimento_id = a.transacao where dat_cad between '" . $_GET['start'] . "' and '" . $_GET['end'] . "'";
			$sthRelCont = pg_query($stmtRelCont) or die($stmtRelCont);
			$rowCount = pg_fetch_object($sthRelCont);
			$qtde_atendimentos = $rowCount->quantidade;

			$this->Cell(185, 7, utf8_decode('RELATÓRIO DE ATENDIMENTO'), 1, 0, 'C');
			$this->Ln(7);
			$this->Cell(80, 7, utf8_decode('Quantidade:   ' . $qtde_atendimentos), 1, 0, 'C');
			$this->Cell(105, 7, utf8_decode('Período:   ' . inverteData($_GET['start']) . '   ATÉ   ' . inverteData($_GET['end'])), 1, 0, 'C');
		}

		$this->Ln(7);
	}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

//ESPECIALIDADE///////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($tipo_relatorio == 'ESPECIALIDADE') {
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(60, 8, utf8_decode('Especialidade'), 1, 0, 'C');
	$pdf->Cell(35, 8, utf8_decode('Data Entrada'), 1, 0, 'C');
	$pdf->Cell(30, 8, utf8_decode('Hora'), 1, 0, 'C');
	$pdf->Cell(60, 8, utf8_decode('Quantidade'), 1, 0, 'C');
	$pdf->Ln(8);
	$pdf->SetFont('Arial', '', 9);

	$start = $_GET['start'];
	$end = $_GET['end'];
	$especialidade = $_GET['especialidade'];

	$where = '';
	if ($especialidade == '' or $especialidade == 'todos') {
		$where = "where especialidade != '' and dat_cad between '$start' and '$end'";
	} else {
		$where = "where especialidade != '' and dat_cad between  '" . inverteData($start) . "' and '" . inverteData($end) . "'and especialidade = '$especialidade'";
	}

	include 'conexao.php';
	$stmtRel = "select count(*) as qtde,especialidade, 
				CASE
				WHEN hora_cad>='00:00' and hora_cad<'07:00' THEN '00h a 06:59'
				WHEN hora_cad>='07:00' and hora_cad<'13:00' THEN '07h a 12:59'
				WHEN hora_cad>='13:00' and hora_cad<'19:00' THEN '13h a 18:59'
				WHEN hora_cad>='19:00' and hora_cad<='23:59' THEN '19h a 23:59'
				ELSE hora_cad
				END as horas, dat_cad
				from atendimentos";
	$stmtRel = $stmtRel . ' ' . $where;
	$stmtRel = $stmtRel . ' group by 2,3,4
				order by 2,4,3,1';
	$sthRel = pg_query($stmtRel);

	while ($rowRel = pg_fetch_object($sthRel)) {
		$pdf->Cell(60, 5, utf8_decode($rowRel->especialidade), 1, 0, 'C');
		$pdf->Cell(35, 5, date('d/m/Y', strtotime($rowRel->dat_cad)), 1, 0, 'C');
		$pdf->Cell(30, 5, utf8_decode($rowRel->horas), 1, 0, 'C');
		$pdf->Cell(60, 5, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Ln(5);
	}
}

//CLASSIFICACAO///////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($tipo_relatorio == 'CLASSIFICACAO') {
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(90, 8, utf8_decode('Prioriodade'), 1, 0, 'C');
	$pdf->Cell(95, 8, utf8_decode('Quantidade'), 1, 0, 'C');
	$pdf->Ln(8);
	$pdf->SetFont('Arial', '', 10);

	$start = $_GET['start'];
	$end = $_GET['end'];

	include 'conexao.php';

	$stmtRel = "SELECT count(*) as qtde,
			CASE
			WHEN a.prioridade != '' THEN a.prioridade
			WHEN a.prioridade = '' or a.prioridade is null THEN 'NÃO INFORMADO'
			END AS prioridade
			from atendimentos a where a.dat_cad between  '" . inverteData($start) . "' and '" . inverteData($end) . "'group by 2
			order by 2,1";
	$sthRel = pg_query($stmtRel);

	while ($rowRel = pg_fetch_object($sthRel)) {
		$pdf->Cell(90, 7, utf8_decode($rowRel->prioridade), 1, 0, 'C');
		$pdf->Cell(95, 7, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Ln(7);
	}
}

//TRANSFERENCIA///////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($tipo_relatorio == 'TRANSFERENCIA') {
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(50, 8, utf8_decode('Hospital'), 1, 0, 'C');
	$pdf->Cell(25, 8, utf8_decode('Clínica'), 1, 0, 'C');
	$pdf->Cell(20, 8, utf8_decode('Quantidade'), 1, 0, 'C');
	$pdf->Cell(35, 8, utf8_decode('Data de Transferência'), 1, 0, 'C');
	$pdf->Cell(35, 8, utf8_decode('Hora de Transferência'), 1, 0, 'C');
	$pdf->Cell(20, 8, utf8_decode('Média Etária'), 1, 0, 'C');
	$pdf->Ln(8);
	$pdf->SetFont('Arial', '', 7.5);

	$start = $_GET['start'];
	$end = $_GET['end'];

	include 'conexao.php';

	$stmtRel = "select count(*) as qtde, h.hospital, avg(date_part('year', age(p.dt_nasc))) as med_etaria, 
					case clinica
						when 1 then 'Medica'
						when 2 then 'Cirúrgica'
						else '-'
					end as clinica, data,hora
					from destino_paciente d
						left join hospitais h on h.id = d.hospital
						left join atendimentos a on a.transacao = d.atendimento_id
						left join pessoas p on p.pessoa_id = a.paciente_id
					where d.hospital is not null and data between '$start' and '$end'
						group by 2,4,5,6
						order by 2,5,6 asc";
	$sthRel = pg_query($stmtRel);

	while ($rowRel = pg_fetch_object($sthRel)) {
		$pdf->Cell(50, 7, utf8_decode($rowRel->hospital), 1, 0, 'C');
		$pdf->Cell(25, 7, utf8_decode($rowRel->clinica), 1, 0, 'C');
		$pdf->Cell(20, 7, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Cell(35, 7, utf8_decode(date('d/m/Y', strtotime($rowRel->data))), 1, 0, 'C');
		$pdf->Cell(35, 7, utf8_decode($rowRel->hora), 1, 0, 'C');
		$pdf->Cell(20, 7, utf8_decode(number_format($rowRel->med_etaria, 2)), 1, 0, 'C');
		$pdf->Ln(7);
	}

	include 'conexao.php';
	$stmtRel2 = "select count(*) as qtde, h.hospital
					from destino_paciente d
						left join hospitais h on h.id = d.hospital
						left join atendimentos a on a.transacao = d.atendimento_id
						left join pessoas p on p.pessoa_id = a.paciente_id
					where d.hospital is not null and data between '$start' and '$end'
						group by 2";
	$sthRel2 = pg_query($stmtRel2);

	$pdf->Ln(7);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(185, 7, utf8_decode('TOTAL DE TRANSFERENCIAS POR HOSPITAL'), 1, 0, 'C');
	$pdf->Ln(7);
	$pdf->SetFont('Arial', '', 9);
	while ($rowRel = pg_fetch_object($sthRel2)) {
		$pdf->Cell(130, 7, utf8_decode($rowRel->hospital), 1, 0, 'C');
		$pdf->Cell(55, 7, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Ln(7);
	}
}

//DEMANDA///////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($tipo_relatorio == 'DEMANDA') {
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(90, 8, utf8_decode('Prioriodade'), 1, 0, 'C');
	$pdf->Cell(95, 8, utf8_decode('Quantidade'), 1, 0, 'C');
	$pdf->Ln(8);
	$pdf->SetFont('Arial', '', 10);

	$start = $_GET['start'];
	$end = $_GET['end'];

	include 'conexao.php';
	$stmtRel = "SELECT count(*) as qtde, origem
				from atendimentos a
				left join tipo_origem t on t.tipo_id = cast(a.tipo as integer)
				where a.dat_cad between  '" . inverteData($start) . "' and '" . inverteData($end) . "'
				group by 2 ";
	$sthRel = pg_query($stmtRel);

	while ($rowRel = pg_fetch_object($sthRel)) {
		$pdf->Cell(90, 7, utf8_decode($rowRel->origem), 1, 0, 'C');
		$pdf->Cell(95, 7, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Ln(7);
	}
}

//EXAMES///////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($tipo_relatorio == 'EXAMES') {
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(90, 8, utf8_decode('Exames'), 1, 0, 'C');
	$pdf->Cell(95, 8, utf8_decode('Quantidade'), 1, 0, 'C');
	$pdf->Ln(8);
	$pdf->SetFont('Arial', '', 10);

	$start = $_GET['start'];
	$end = $_GET['end'];
	$modalidade = $_GET['modalidade'];

	$where = '';
	if ($modalidade != '') {
		if ($modalidade == '3' or $modalidade == '4') {
			$where = ' and modalidade_id = ' . $modalidade;
		} else {
			$where = " and (a.situacao = 'Realizado' or a.situacao = 'Finalizado') and modalidade_id = " . $modalidade;
		}
	} else {
		$where = " and case when modalidade_id = 1 or modalidade_id = 2 then (a.situacao = 'Realizado' or a.situacao = 'Finalizado') else a.situacao is not null end";
	}

	include 'conexao.php';
	$stmtRel = "select descricao, count(*) as qtde, modalidade_id
    from itenspedidos a
    left join atendimentos b on a.atendimento_id = b.transacao
    left join procedimentos p on p.procedimento_id = a.exame_id
    where b.dat_cad between '" . inverteData($start) . "' and '" . inverteData($end) . "' 
$where
    group by 1,3 order by 1";
	$sthRel = pg_query($stmtRel);

	while ($rowRel = pg_fetch_object($sthRel)) {
		$pdf->SetFont('Arial', '', 8);
		$pdf->Cell(120, 7, utf8_decode($rowRel->descricao), 1, 0, 'L');
		$pdf->Cell(65, 7, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Ln(7);
	}
}

//PRIORIDADE///////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($tipo_relatorio == 'PRIORIDADE') {
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(100, 8, utf8_decode('Prioridade'), 1, 0, 'C');
	$pdf->Cell(85, 8, utf8_decode('Quantidade'), 1, 0, 'C');
	$pdf->Ln(8);
	$pdf->SetFont('Arial', '', 9);

	$start = $_GET['start'];
	$end = $_GET['end'];
	$especialidade = $_GET['especialidade'];

	$where = '';
	if ($especialidade == '' or $especialidade == 'todos') {
		$where = "where especialidade != '' and dat_cad between '$start' and '$end'";
	} else {
		$where = "where especialidade != '' and dat_cad between  '" . inverteData($start) . "' and '" . inverteData($end) . "'and especialidade = '$especialidade'";
	}

	include 'conexao.php';
	$stmtRel = 'select count(*) as qtde,prioridade
					from atendimentos';
	$stmtRel = $stmtRel . ' ' . $where;
	$stmtRel = $stmtRel . ' group by 2
					order by 2';
	$sthRel = pg_query($stmtRel);

	while ($rowRel = pg_fetch_object($sthRel)) {
		$pdf->Cell(100, 5, utf8_decode($rowRel->prioridade), 1, 0, 'C');
		$pdf->Cell(85, 5, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Ln(5);
	}
}

//TODOS///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$totalAT = 0;
if ($tipo_relatorio == '') {
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(120, 8, utf8_decode('Destino'), 1, 0, 'C');
	$pdf->Cell(65, 8, utf8_decode('Quantidade'), 1, 0, 'C');
	$pdf->Ln(8);
	$pdf->SetFont('Arial', '', 10);

	$start = $_GET['start'];
	$end = $_GET['end'];

	$sql = "SELECT case when z.destino_encaminhamento::varchar is null then ltrim(a.destino_paciente,'0') else ltrim(z.destino_encaminhamento::varchar,'0') end as destino_paciente, count(*) as quantidade from atendimentos a left join destino_paciente z on z.atendimento_id = a.transacao where dat_cad between '$start' and '$end' group by 1";
	$result = pg_query($sql) or die($sql);
	while ($row = pg_fetch_object($result)) {
		if ($row->destino_paciente == '01') {
			$df = 'ALTA';
		} elseif ($row->destino_paciente == '02') {
			$df = 'ALTA / ENCAM. AMBUL.';
		} elseif ($row->destino_paciente == '07') {
			$df = 'EM OBSERVAÇÃO / MEDICAÇÃO';
		} elseif ($row->destino_paciente == '10') {
			$df = 'EXAMES / REAVALIACAO';
		} elseif ($row->destino_paciente == '03') {
			$df = 'PERMANÊCIA.';
		} elseif ($row->destino_paciente == '04') {
			$df = 'TRANSF. OUTRA UPA';
		} elseif ($row->destino_paciente == '05') {
			$df = 'TRANSF. INTERN. HOSPITALAR';
		} elseif ($row->destino_paciente == '06') {
			$df = 'ÓBITO';
		} elseif ($row->destino_paciente == '09') {
			$df = 'NAO RESPONDEU CHAMADO';
		} elseif ($row->destino_paciente == '11') {
			$df = 'ALTA EVASÃO';
		} elseif ($row->destino_paciente == '12') {
			$df = 'ALTA PEDIDO';
		} elseif ($row->destino_paciente == '14') {
			$df = 'ALTA / POLICIA';
		} elseif ($row->destino_paciente == '15') {
			$df = 'ALTA / PENITENCIÁRIA';
		} elseif ($row->destino_paciente == '16') {
			$df = 'ALTA / PÓS MEDICAMENTO';
		} elseif ($row->destino_paciente == '20') {
			$df = 'ALTA VIA SISTEMA';
		} elseif ($row->destino_paciente == '21') {
			$df = 'TRANSFERENCIA';
		} else {
			$df = 'NAO SE APLICA';
		}
		$pdf->Cell(120, 7, utf8_decode($df), 0, 0, 'C');
		$pdf->Cell(65, 7, utf8_decode($row->quantidade), 0, 0, 'C');
		$pdf->Ln(7);
	}

	include 'conexao.php';
	$stmtRel = "select count(*) as qtde, 'TRANSFERENCIA HOSPITALAR' as destino
				from destino_paciente d
					left join hospitais h on h.id = d.hospital
					left join atendimentos a on a.transacao = d.atendimento_id
					left join pessoas p on p.pessoa_id = a.paciente_id
				where d.hospital is not null
					group by 2";
	$sthRel = pg_query($stmtRel);

	while ($rowRel = pg_fetch_object($sthRel)) {
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(120, 7, utf8_decode($rowRel->destino), 1, 0, 'C');
		$pdf->Cell(65, 7, utf8_decode($rowRel->qtde), 1, 0, 'C');
		$pdf->Ln(7);

		$valoresDestino = "'$valoresDestino[0]', '$valoresDestino[1]', '$valoresDestino[2]', '$valoresDestino[3]','$valoresDestino[4]','$valoresDestino[5]'";
		$pdf->SetFont('Arial', 'I', 10);

		include 'conexao.php';

		$stmtRel2 = "select count(*) as qtde, h.hospital as destino
				from destino_paciente d
					left join hospitais h on h.id = d.hospital
					left join atendimentos a on a.transacao = d.atendimento_id
					left join pessoas p on p.pessoa_id = a.paciente_id
				where d.hospital is not null and data between '$start' and '$end'
					group by 2";
		$sthRel2 = pg_query($stmtRel2);
		while ($rowRel2 = pg_fetch_object($sthRel2)) {
			$pdf->Cell(120, 7, utf8_decode($rowRel2->destino), 0, 0, 'C');
			$pdf->Cell(65, 7, utf8_decode($rowRel2->qtde), 0, 0, 'C');
			$pdf->Ln(7);
		}
	}
}
$pdf->Output();
