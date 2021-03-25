<?php
include 'verifica.php';
require 'tsul_ssl.php';
function inverteData($data)
{
	if (count(explode('/', $data)) > 1) {
		return implode('-', array_reverse(explode('/', $data)));
	} elseif (count(explode('-', $data)) > 1) {
		return implode('/', array_reverse(explode('-', $data)));
	}
}
$hora_transacao = '';
function validaCPF($cpf = null)
{
	// Verifica se um número foi informado
	if (empty($cpf)) {
		return false;
	}

	// Elimina possivel mascara
	$cpf = preg_replace('[^0-9]', '', $cpf);
	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

	// Verifica se o numero de digitos informados é igual a 11
	if (strlen($cpf) != 11) {
		return false;
	}  // Verifica se nenhuma das sequências invalidas abaixo
	// foi digitada. Caso afirmativo, retorna falso
	elseif ($cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
		return false;
	// Calcula os digitos verificadores para verificar se o
		// CPF é válido
	} else {
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{
					$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{
				$c} != $d) {
				return false;
			}
		}

		return true;
	}
}

//include('verifica.php');

error_reporting(0);

date_default_timezone_set('America/Sao_Paulo');
$menu_grupo = '1';
$data_transacao = inverteData(date('Y-m-d'));
$usuario_transacao = $usuario;
$situacao = 'Pesquisando';
$descricao = '';
$dtnasc = '';
$telefone = '';
$mae = '';
$where = 'nome is null';
$tipoConv = '3';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$transacao = $_GET['id'];
	$senha = $_GET['senha'];
	$agendamento = $_GET['ag'];
	$med = $_GET['med'];
	$texto = '';
	$data = date('Y-m-d');
	$hora = date('H:i');
	include 'conexao.php';
	$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
			values ('$usuario','FEZ ACESSO AO ATENDIMENTO','$transacao','$data','$hora')";
	$sthLogs = pg_query($stmtLogs) or die($stmtLogs);

	include 'conexao.php';
	$bloqueiaAt = "SELECT transacao,STATUS, count(*) AS total,nome
					FROM atendimentos a
					LEFT JOIN pessoas p ON p.pessoa_id = a.paciente_id
					WHERE med_atendimento = '$usuario' AND transacao != $transacao AND STATUS = 'Em Atendimento' AND dat_cad = '" . date('Y-m-d') . "'
					GROUP BY 1,2,4";
	$sthBlock = pg_query($bloqueiaAt) or die($bloqueiaAt);
	$valBlock = pg_fetch_object($sthBlock);
	if ($valBlock->total > 0 && isset($_GET['continue'])) {
		if (!isset($_GET['estadia'])) {
			echo "
			<script>
				alert('Finalize o atendimento de " . ts_decodifica($valBlock->nome) . " que esta em aberto. Você será redirecionado para o paciente em questão.');
				location.href='atendimentoclinico.php?id=" . $valBlock->transacao . "&continue=1';
			</script>";
		}
	} else {
		include 'conexao.php';
		$validaAtendimento = "select a.status from atendimentos a where a.transacao=$transacao";
		$sthAt = pg_query($validaAtendimento) or die($validaAtendimento);
		$valAt = pg_fetch_object($sthAt);
		if ($valAt->status == 'Aguardando Atendimento' && $perfil == '03') {
			$stmt = "update atendimentos set status='Em Atendimento', med_atendimento='$usuario' WHERE transacao = $transacao";
			$sth = pg_query($stmt) or die($stmt);
		}
	}

	if ($transacao != '') {
		include 'conexao.php';
		$stmt = "SELECT a.transacao,a.hora_cad, a.cid_principal, case when z.destino_encaminhamento::varchar is null then a.destino_paciente else z.destino_encaminhamento::varchar end as destino_paciente, a.data_destino, a.queixa, a.exame_fisico, a.diagnostico_principal,a.prioridade,
		a.paciente_id, a.status, a.tipo, a.dat_cad AS cadastro, a.obs_modal, c.nome, c.nome_social, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco, a.oque_faz, a.com_oqfaz, 
		a.tempo_faz, a.como_faz, c.numero, c.complemento, c.bairro, c.num_carteira_convenio, c.cep, c.cpf, c.cidade, c.estado, a.observacao, k.origem,  
		x.peso, x.pressaodiastolica, x.usuario, x.fimclassificacao, x.pressaosistolica, x.queixa AS relato, x.pulso, x.temperatura, x.discriminador, x.prioridade AS atendprioridade, x.glicose, x.dor, x.oxigenio, a.coronavirus, a.pa_sis_internacao,a.pa_dist_internacao,a.temperatura_internacao,a.dor_internacao,a.oxigenio_internacao,a.pulso_internacao,a.pulso_internacao,a.glicose_internacao,a.ecg_internacao,a.frequencia_respiratoria, a.cid_internacao, a.tipo_leito
		FROM atendimentos a 
		LEFT JOIN pessoas c ON a.paciente_id=c.pessoa_id 
		LEFT JOIN tipo_origem k ON CAST(k.tipo_id AS VARCHAR)=a.tipo 
		LEFT JOIN classificacao x ON ltrim(x.atendimento_id, '0')= '$transacao' 
        LEFT JOIN destino_paciente z on a.transacao = z.atendimento_id
		WHERE a.transacao=$transacao";
		$sth = pg_query($stmt) or die($stmt);
		$row = pg_fetch_object($sth);
		$data_transacao = substr($row->cadastro, 0, 10);
		$hora_transacao = $row->hora_cad;
		$prontuario = $row->paciente_id;

		$destino = $row->destino_paciente;
		$destinox = $row->destino_paciente;
		$status = $row->status;

		if (($status == 'Atendimento Finalizado') && ($destino != '07' && $destino != '10' && $destino != '09')) {
			$liberacao = 'readonly';
			$liberacaox = 'disabled';
		} else {
			$liberacao = '';
			$liberacaox = '';
		}
		$usuario_enf = $row->usuario;
		$datahora = $row->fimclassificacao;
		$data_destino = $row->data_destino;
		$prioridade = $row->prioridade;
		$sexo = $row->sexo;
		$nome = ts_decodifica($row->nome);
		$nome_social = $row->nome_social;
		$paciente_id = $row->paciente_id;
		$email = $row->email;
		$dt_nascimento = inverteData($row->dt_nasc);
		$sexo = $row->sexo;
		$enderecox = $row->endereco;
		$end_numero = $row->numero;
		$complemento = $row->complemento;
		$bairro = $row->bairro;
		$cidade = $row->cidade;
		$estado = $row->estado;
		$atendprioridade = $row->atendprioridade;
		$peso = $row->peso;
		$oxigenio = $row->oxigenio;
		$dor = $row->dor;
		$glicose = $row->glicose;
		$pressaodiastolica = $row->pressaodiastolica;
		$pressaosistolica = $row->pressaosistolica;
		$relato = $row->relato;
		$pulso = $row->pulso;
		$temperatura = $row->temperatura;
		$cns = $row->num_carteira_convenio;
		$cep = $row->cep;
		$cpf = ts_decodifica($row->cpf);
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
		$observacao = $row->relato . PHP_EOL;
		$observacao = $observacao . 'PA DIAST:' . $pressaodiastolica . PHP_EOL . 'PA SIST.:' . $pressaosistolica . PHP_EOL;
		$observacao = $observacao . 'PESO:' . $peso . PHP_EOL . 'Temperatura:' . $temperatura . PHP_EOL;
		$observacao = $observacao . 'GLICEMIA:' . $glicose . PHP_EOL . 'Dor:' . $dor . PHP_EOL;

		$oque_faz = $row->oque_faz;
		$com_oqfaz = $row->com_oqfaz;
		$tempo_faz = $row->tempo_faz;
		$como_faz = $row->como_faz;
		$enfermaria = $row->enfermaria;
		$leito = $row->leito;
		$imagem = $row->imagem;
		$origem = $row->tipo;
		$alta = inverteData($row->data_destino);
		$CID = $row->cid_principal;
		$diag_pri = $row->diagnostico_principal;
		$queixa = $row->queixa;
		$exame_fisico = $row->exame_fisico;
		$hora_dest = $row->hora_destino;
		$obs_modal = $row->obs_modal;
		$coronavirus = $row->coronavirus;
		$pressaosistolicai = $row->pa_sis_internacao;
		$pressaodiastolicai = $row->pa_dist_internacao;
		$oxigenioi = $row->oxigenio_internacao;
		$frequencia_respiratoriai = $row->frequencia_respiratoria;
		$pulsoi = $row->pulso_internacao;
		$temperaturai = $row->temperatura_internacao;
		$glicosei = $row->glicose_internacao;
		$ecgi = $row->ecg_internacao;
		$cid_internacaoi = $row->cid_internacao;
		$tipo_leitoi = $row->tipo_leito;
	} else {
		$data_transacao = date('Y-m-d');
		$hora_transacao = date('H:i');
		$usuario_transacao = $usuario;
	}

	$sql = "SELECT * FROM atendimentos WHERE paciente_id = $paciente_id AND destino_paciente IN ('01','02','11','12','14','15') AND NOW() - data_destino < '8'";
	$res = pg_query($sql) or die($sql);

	include 'conexao.php';
	$sqlc = "select * from presintomas_covid where atendimento_id = $transacao";
	$resultc = pg_query($sqlc) or die($sqlc);
	$rowc = pg_fetch_object($resultc);
	$febre_alta = $rowc->febre_alta;
	$fadiga = $rowc->fadiga;
	$dificuldade_respirar = $rowc->dificuldade_respirar;
	$tosse_secracao = $rowc->tosse_secracao;
	$congestao_nasal = $rowc->congestao_nasal;
	$cefaleia = $rowc->cefaleia;
	$dor_garganta = $rowc->dor_garganta;
	$diarreia = $rowc->diarreia;
	$nausea_vomito = $rowc->nausea_vomito;
	$mialgia_artralgia = $rowc->mialgia_artralgia;
	$calafrios = $rowc->calafrios;
	$anosmia_hiposmia = $rowc->anosmia_hiposmia;
	$diabetes = $rowc->diabetes;
	$has = $rowc->has;
	$obesidade = $rowc->obesidade;
	$doenca_coronariana = $rowc->doenca_coronariana;
	$dpoc_asma = $rowc->dpoc_asma;
	$cancer = $rowc->cancer;
	$drc = $rowc->drc;
	$imunodeficiencia = $rowc->imunodeficiencia;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['extornar_alta'])) {
		$destino = $_POST['destino'];
		$motivo_extorno = $_POST['motivo_extorno'];
		$atendimento = $_POST['atendimento'];
		$data = date('Y-m-d');
		$hora = date('H:i');

		include 'conexao.php';
		$stmtLogs = "INSERT INTO logs (usuario, tipo_acao,atendimento_id, data, hora) 
					 VALUES ('$usuario','Extornou a Alta do atendimento para o destino $destino pelo motivo $motivo_extorno','$atendimento','$data','$hora')";
		$sthLogs = pg_query($stmtLogs) or die($stmtLogs);

		include 'conexao.php';
		$sqlv = "SELECT * FROM destino_paciente WHERE atendimento_id = $atendimento";
		$resultv = pg_query($sqlv) or die($sqlv);
		$rowv = pg_fetch_object($resultv);
		if ($rowv) {
			include 'conexao.php';
			$sql = "UPDATE destino_paciente SET destino_encaminhamento = $destino WHERE atendimento_id = $atendimento";
			$result = pg_query($sql) or die($sql);
		} else {
			include 'conexao.php';
			$sql = "UPDATE atendimentos SET destino_paciente = '$destino', status='Atendimento Finalizado' WHERE transacao = $atendimento";
			$result = pg_query($sql) or die($sql);
		}

		header('Location: atendimentoclinico.php?id=' . $atendimento);
	} else {
		$transacao = stripslashes(pg_escape_string($_POST['transacao']));
		$senha = stripslashes(pg_escape_string($_POST['senha']));
		$data_transacao = stripslashes(pg_escape_string($_POST['data_transacao']));
		$hora_transacao = stripslashes(pg_escape_string($_POST['hora_transacao']));
		$usuario_transacao = stripslashes(pg_escape_string($_POST['usuario_transacao']));
		$acao = stripslashes(pg_escape_string($_POST['acao']));
		$idade = stripslashes(pg_escape_string($_POST['idade']));
		$prontuario = stripslashes(pg_escape_string($_POST['prontuario']));
		$nome = ts_codifica(stripslashes(pg_escape_string($_POST['nome'])));
		$dt_nascimento = stripslashes(pg_escape_string($_POST['dt_nascimento']));
		$enderecox = stripslashes(pg_escape_string($_POST['endereco']));
		$end_numero = stripslashes(pg_escape_string($_POST['end_num']));
		$complemento = stripslashes(pg_escape_string($_POST['end_comp']));
		$bairro = stripslashes(pg_escape_string($_POST['end_bairro']));
		$cidade = stripslashes(pg_escape_string($_POST['end_cidade']));
		$estado = stripslashes(pg_escape_string($_POST['end_uf']));
		$cep = stripslashes(pg_escape_string($_POST['end_cep']));
		$cpf = ts_codifica(stripslashes(pg_escape_string($_POST['cpf'])));
		$cns = stripslashes(pg_escape_string($_POST['cns']));
		$telefone = stripslashes(pg_escape_string($_POST['telefone']));
		$celular = stripslashes(pg_escape_string($_POST['celular']));
		$deficiencia = stripslashes(pg_escape_string($_POST['deficiencia']));
		$observacao = stripslashes(pg_escape_string($_POST['observacao']));
		$origem = $_POST['origem'];
		$evolucao = stripslashes(pg_escape_string($_POST['evolucao']));

		$enfermaria = stripslashes(pg_escape_string($_POST['enfermaria']));
		$leito = stripslashes(pg_escape_string($_POST['leito']));
		$oque_faz = stripslashes(pg_escape_string($_POST['oque_faz']));
		$com_oqfaz = stripslashes(pg_escape_string($_POST['com_oqfaz']));
		$tempo_faz = stripslashes(pg_escape_string($_POST['tempo_faz']));
		$como_faz = stripslashes(pg_escape_string($_POST['como_faz']));
		$destino = stripslashes(pg_escape_string($_POST['destino']));
		$destinox = stripslashes(pg_escape_string($_POST['destinox']));

		$alta = date('Y-m-d');
		$hora_destino = date('H:i');
		$CID = stripslashes(pg_escape_string($_POST['CID']));
		$diag_pri = stripslashes(pg_escape_string($_POST['diag_pri']));
		$situacao = stripslashes(pg_escape_string($_POST['situacao']));
		$queixa = stripslashes(pg_escape_string($_POST['queixa']));
		$exame_fisico = stripslashes(pg_escape_string($_POST['exame_fisico']));
		$procedimento = stripslashes(pg_escape_string($_POST['procedimento']));
		$transfere = $_POST['cb_exame'];
		$obs_modal = stripslashes(pg_escape_string($_POST['obs_modal']));
		$coronavirus = $_POST['coronavirus'];

		if ($coronavirus == 1 or $coronavirus == 10) {
			if ($_POST['febre_alta']) {
				$febre_alta = 1;
			} else {
				$febre_alta = 0;
			}
			if ($_POST['fadiga']) {
				$fadiga = 1;
			} else {
				$fadiga = 0;
			}
			if ($_POST['dificuldade_respirar']) {
				$dificuldade_respirar = 1;
			} else {
				$dificuldade_respirar = 0;
			}
			if ($_POST['tosse_secracao']) {
				$tosse_secracao = 1;
			} else {
				$tosse_secracao = 0;
			}
			if ($_POST['congestao_nasal']) {
				$congestao_nasal = 1;
			} else {
				$congestao_nasal = 0;
			}
			if ($_POST['cefaleia']) {
				$cefaleia = 1;
			} else {
				$cefaleia = 0;
			}
			if ($_POST['dor_garganta']) {
				$dor_garganta = 1;
			} else {
				$dor_garganta = 0;
			}
			if ($_POST['diarreia']) {
				$diarreia = 1;
			} else {
				$diarreia = 0;
			}
			if ($_POST['nausea_vomito']) {
				$nausea_vomito = 1;
			} else {
				$nausea_vomito = 0;
			}
			if ($_POST['mialgia_artralgia']) {
				$mialgia_artralgia = 1;
			} else {
				$mialgia_artralgia = 0;
			}
			if ($_POST['calafrios']) {
				$calafrios = 1;
			} else {
				$calafrios = 0;
			}
			if ($_POST['anosmia_hiposmia']) {
				$anosmia_hiposmia = 1;
			} else {
				$anosmia_hiposmia = 0;
			}
			if ($_POST['diabetes']) {
				$diabetes = 1;
			} else {
				$diabetes = 0;
			}
			if ($_POST['has']) {
				$has = 1;
			} else {
				$has = 0;
			}
			if ($_POST['obesidade']) {
				$obesidade = 1;
			} else {
				$obesidade = 0;
			}
			if ($_POST['doenca_coronariana']) {
				$doenca_coronariana = 1;
			} else {
				$doenca_coronariana = 0;
			}
			if ($_POST['dpoc_asma']) {
				$dpoc_asma = 1;
			} else {
				$dpoc_asma = 0;
			}
			if ($_POST['cancer']) {
				$cancer = 1;
			} else {
				$cancer = 0;
			}
			if ($_POST['drc']) {
				$drc = 1;
			} else {
				$drc = 0;
			}
			if ($_POST['imunodeficiencia']) {
				$imunodeficiencia = 1;
			} else {
				$imunodeficiencia = 0;
			}

			include 'conexao.php';
			$sql = "select * from presintomas_covid where atendimento_id = $transacao";
			$result = pg_query($sql) or die($sql);
			$row = pg_fetch_object($result);

			if ($row) {
				include 'conexao.php';
				$sql = "update presintomas_covid set febre_alta=$febre_alta,fadiga=$fadiga,dificuldade_respirar=$dificuldade_respirar,tosse_secracao=$tosse_secracao,congestao_nasal=$congestao_nasal,cefaleia=$cefaleia,dor_garganta=$dor_garganta,diarreia=$diarreia,nausea_vomito=$nausea_vomito,mialgia_artralgia=$mialgia_artralgia,calafrios=$calafrios,anosmia_hiposmia=$anosmia_hiposmia,diabetes=$diabetes,has=$has,obesidade=$obesidade,doenca_coronariana=$doenca_coronariana,dpoc_asma=$dpoc_asma,cancer=$cancer,drc=$drc,imunodeficiencia=$imunodeficiencia where atendimento_id = $transacao";
				$result = pg_query($sql) or die($sql);
			} else {
				include 'conexao.php';
				$sql = "insert into presintomas_covid(atendimento_id, febre_alta,fadiga,dificuldade_respirar,tosse_secracao,congestao_nasal,cefaleia,dor_garganta,diarreia,nausea_vomito,mialgia_artralgia,calafrios,anosmia_hiposmia,diabetes,has,obesidade,doenca_coronariana,dpoc_asma,cancer,drc,imunodeficiencia) values($transacao,$febre_alta,$fadiga,$dificuldade_respirar,$tosse_secracao,$congestao_nasal,$cefaleia,$dor_garganta,$diarreia,$nausea_vomito,$mialgia_artralgia,$calafrios,$anosmia_hiposmia,$diabetes,$has,$obesidade,$doenca_coronariana,$dpoc_asma,$cancer,$drc,$imunodeficiencia)";
				$result = pg_query($sql) or die($sql);
			}
		}

		if ($erro == '') {
			$xdum = '';

			include 'conexao.php';
			$stmt = "SELECT * FROM atendimentos WHERE transacao=$transacao and (destino_paciente = '09' or destino_paciente is null)";
			$result = pg_query($stmt) or die($stmt);
			$row = pg_fetch_object($result);
			$atendimento = $row->transacao;

			include 'conexao.php';
			$dt_transacao = inverteData($data_transacao);
			$alta = inverteData($alta);
			$dt_solicitacao = inverteData($dt_nsolicitacao);
			$horacad = date('H:i');
			$stmt = "UPDATE atendimentos SET transacao=$transacao, observacao='$observacao', box='1', local='1', 
				oque_faz='$oque_faz', como_faz='$como_faz', tempo_faz='$tempo_faz', com_oqfaz='$com_oqfaz', queixa='$queixa', exame_fisico='$exame_fisico', 
				diagnostico_principal='$diag_pri', cid_principal='$CID',";
			if (str_replace(' ', '', $destino) != '') {
				$stmt = $stmt . " destino_paciente='$destino',";
				// if (($destino == '01' or $destino == '02' or $destino == '11' or $destino == '12' or $destino == '14' or $destino == '15') && $coronavirus == '1') {
				//     $stmt = $stmt . " coronavirus=10,";
				// }
				if ($destino != '09') {
					$stmt = $stmt . " med_finalizador = (case when med_finalizador is null then '$usuario' else med_finalizador end),";
				}
			}
			if ($alta != '') {
				if (!$atendimento) {
					$stmt = $stmt . " status='Atendimento Finalizado' ";
				} else {
					$stmt = $stmt . " data_destino='$alta', hora_destino='$hora_destino', status='Atendimento Finalizado' ";
				}
			} else {
				$stmt = $stmt . " data_destino=null, hora_destino=null, status='Em Atendimento' ";
			}

			$stmt = $stmt . " where transacao=$transacao ";
			$sth = pg_query($stmt) or die($stmt);

			if ($evolucao != '') {
				$sql = "select nextval('evolucoes_evolucao_id_seq'::regclass)";
				$result = pg_query($sql) or die($sql);
				$row = pg_fetch_object($result);

				$horacad = date('H:i');
				$datacad = date('Y-m-d');

				$stmt = "INSERT INTO evolucoes (evolucao_id, atendimento_id, tipo, data, hora, usuario,evolucao)
						VALUES ($row->nextval, $transacao,'$perfil','$datacad','$horacad','$usuario','$evolucao')";
				$sth = pg_query($stmt) or die($stmt);
				header('location: relevolucao.php?id=' . $row->nextval);
			}

			$df = '';
			if ($destino == '01') {
				$df = 'ALTA';
			} elseif ($destino == '02') {
				$df = 'ALTA / ENCAM. AMBUL.';
			} elseif ($destino == '07') {
				$df = 'EM OBSERVAÇÃO / MEDICAÇÃO';
			} elseif ($destino == '10') {
				$df = 'EXAMES / REAVALIACAO';
			} elseif ($destino == '03') {
				$df = 'PERMANÊCIA.';
			} elseif ($destino == '04') {
				$df = 'TRANSF. OUTRA UPA';
			} elseif ($destino == '05') {
				$df = 'TRANSF. INTERN. HOSPITALAR';
			} elseif ($destino == '06') {
				$df = 'ÓBITO';
			} elseif ($destino == '09') {
				$df = 'NAO RESPONDEU CHAMADO';
			} elseif ($destino == '11') {
				$df = 'ALTA EVASÃO';
			} elseif ($destino == '12') {
				$df = 'ALTA PEDIDO';
			} elseif ($destino == '14') {
				$df = 'ALTA / POLICIA';
			} elseif ($destino == '15') {
				$df = 'ALTA / PENITENCIÁRIA';
			} elseif ($destino == '16') {
				$df = 'ALTA / PÓS MEDICAMENTO';
			} elseif ($destino == '20') {
				$df = 'ALTA VIA SISTEMA';
			} elseif ($destino == '21') {
				$df = 'TRANSFERENCIA';
			}

			$data = date('Y-m-d');
			$hora = date('H:i');
			include 'conexao.php';
			$stmtLogs = "INSERT INTO logs (usuario, tipo_acao,atendimento_id, data, hora) 
					 VALUES ('$usuario','FINALIZOU O ATENDIMENTO - DESTINO $df','$transacao','$data','$hora')";
			$sthLogs = pg_query($stmtLogs) or die($stmtLogs);
		}

		if (isset($_POST['encerrar']) != '') {
			include 'conexao.php';
			$dt_transacao = inverteData(substr($data_transacao, 0, 10));
			$dt_solicitacao = inverteData($dt_nsolicitacao);
			$horacad = date('H:i');
			$stmt = "UPDATE pedidos SET STATUS='Cadastrado' WHERE transacao=$transacao ";
			$sth = pg_query($stmt) or die($stmt);

			include 'conexao.php';
			$dt_transacao = inverteData(substr($data_transacao, 0, 10));
			$dt_solicitacao = inverteData($dt_nsolicitacao);
			$horacad = date('H:i');
			$stmt = "UPDATE itenspedidos SET situacao='Cadastrado' WHERE transacao=$transacao ";
			$sth = pg_query($stmt) or die($stmt);

			header('location: atendimentos.php');
		}

		if (isset($_POST['novo_exame']) != '') {
			include 'conexao.php';
			$dataTransacao = date('Y-m-d');
			$horacad = date('H:i');
			$stmt = 'INSERT INTO pedidos ';
			$sth = pg_query($stmt) or die($stmt);

			include 'conexao.php';
			$dt_transacao = inverteData(substr($data_transacao, 0, 10));
			$dt_solicitacao = inverteData($dt_nsolicitacao);
			$horacad = date('H:i');
			$stmt = "UPDATE itenspedidos SET situacao='Cadastrado' WHERE transacao=$transacao ";
			$sth = pg_query($stmt) or die($stmt);

			header('location: atendimentos.php');
		}

		if (isset($_POST['req_exame'])) {
			$exames = '';
			foreach ($transfere as $item) {
				$exames = $exames . $item . ',';
			}
			$exames = rtrim($exames, ',');

			if ($exames != '') {
				echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('impexame.php?id=$exames');</script>";
			}
		}

		if (isset($_POST['req_exame_lab'])) {
			$exames = '';
			foreach ($transfere as $item) {
				$exames = $exames . $item . ',';
			}
			$exames = rtrim($exames, ',');

			if ($exames != '') {
				echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('impexamelab.php?id=$exames');</script>";
			}
		}

		if (isset($_POST['imprimir']) != '') {
			echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('relFAA.php?id=$transacao');</script>";
		}

		echo "<script type=\"text/javascript\" language=\"Javascript\">location.href = 'atendimentoclinico.php?id=$transacao';</script>";
	}
}

include 'conexao.php';
$stmtCns = "SELECT * FROM controle_epidemiologico
		    WHERE cns = '$cns' ORDER BY notificacao_id DESC LIMIT 1";
$sthCns = pg_query($stmtCns) or die($stmtCns);
$rowcns = pg_fetch_object($sthCns);

$rdonly = '';
$disable = '';
$hidden = false;
if ($destino != '') {
	$rdonly = ' readonly ';
	$disable = ' disabled ';
	$hidden = 'hidden';
}
?>
<!DOCTYPE html>
<html lang="pt-br" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="tsul" content="tsul">
    <meta name="keywords" content="tsul">
    <meta name="author" content="TSUL">
    <title>FUNEPU | Pagina Padrao</title>
    <link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link
        href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.css" />



    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"> -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


    <link rel="stylesheet "
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">

    <!--load all styles -->

</head>
<style>
    .slidecontainer {
        width: 100%;
    }

    .slider {
        -webkit-appearance: none;
        width: 90%;
        height: 10px;
        border-radius: 5px;
        background: #e4e9f2;
        outline: none;
        opacity: 0.7;
        -webkit-transition: .2s;
        transition: opacity .2s;
    }

    .slider:hover {
        opacity: 1;
    }

    .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #12A1A6;
        cursor: pointer;
    }

    .slider::-moz-range-thumb {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #12A1A6;
        cursor: pointer;
    }

    .scroll {
        max-height: 500px;
        height: 350px;
        overflow-y: auto;
    }

    .dropdown-menu {
        max-width: 500px;
    }

    .dropdown-menu>li>a:hover {
        background: #12A1A6;
        color: white
    }

    .nav-tabs {
        color: black;
    }

    .nav-tabs>li>a {
        color: #12A1A6;
    }

    .nav-tabs>li>a:hover {
        font-size: 12pt;
        font-weight: 400;
        color: #12A1A6;
    }

    .nav-tabs .nav-link.active {
        background: #12a1a6;
        color: white;

    }

    hr {
        color: #12A1A6;
        background-color: #12A1A6;
        margin-top: 0px;
        margin-bottom: 0px;
        height: 4px;
        width: 300px;
        margin-left: 0px;
        border-top-width: 0px;
    }

    /* .item {
        flex: 1; 
        margin: 5px;
        padding: 0 10px;
        text-align: center;
        font-size: 1.5em;
    } */

    .flex-end {
        align-content: flex-end;
    }

    .contain {
        /* max-width: 420px; */
        max-height: 50px;
        margin: 0 auto;
        display: flex;
        /* border: 1px solid #ccc; */
        flex-wrap: wrap;
    }

    .flex-start {
        align-content: flex-start;
    }

    #texto {
        -webkit-box-shadow: 0px 0px 17px 2px rgba(0, 0, 0, 0.34);
        box-shadow: 0px 0px 17px 2px rgba(0, 0, 0, 0.34);
    }

    a {
        color: black;
    }
</style>

<body class="pace-done" cz-shortcut-listen="true">
    <div id="modal"></div>
    <div class="modal fade text-left" id="convenio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title" id="myModalLabel8">Informação Obrigatoria</h4>
                </div>

                <div class="modal-body col-md-12">
                    <div class="form-group">
                        <label for="">Paciente possui convenio?</label>
                        <div class="input-group">
                            <div class="custom-control custom-radio d-inline-block">
                                <input type="radio" id="pconvenio1" name="pconvenio" <?php if ($rowget->pconvenio == 'sim') {
	echo 'checked';
} ?>
                                class="custom-control-input" value="sim">
                                <label class="custom-control-label" for="pconvenio1">Sim</label>
                            </div>
                            <div class="custom-control custom-radio d-inline-block ml-2">
                                <input type="radio" id="pconvenio2" name="pconvenio" <?php if ($rowget->pconvenio == 'nao') {
	echo 'checked';
} ?>
                                class="custom-control-input" value="nao">
                                <label class="custom-control-label" for="pconvenio2">Nao</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type='button' name='confconvenio' id="confconvenio"
                        onclick="salvar_convenio(<?php echo $transacao; ?>)"
                        class="btn btn-success width-full" value='Salvar'>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="modalFimEvolucao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title" id="myModalLabel8">Evolução</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="#">
                        <div class="col-md-12" style="padding: 0;">

                            <div class="col-sm-12">
                                <label class="control-label  margin-top-10">Destino dado ao Paciente</label>
                                <select class="form-control" name="destino" id="destino2"
                                    onchange="seleciona_setor(this)" required>
                                    <option value="">Escolha um Destino
                                    </option>;
                                    <option value="01">ALTA
                                    </option>;
                                    <option value="07">EM
                                        OBSERVAÇÃO / MEDICAÇÃO</option>;
                                    <option value="10">EXAMES /
                                        REAVALIACAO</option>;
                                    <option value="03">PERMANÊNCIA.
                                    </option>
                                </select>
                            </div>

                            <div class="col-sm-12" id="setor_transferencia_destino">

                            </div>

                            <div class="col-sm-12 margin-top-20">
                                <label class="control-label">Motivo do Extorno</label>
                                <textarea name="motivo_extorno" rows="5" class="form-control" onkeyup="maiuscula(this)"
                                    required></textarea>
                                <input type="hidden" name="atendimento"
                                    value="<?php echo $transacao; ?>">
                            </div>
                        </div>

                        <div class="col-md-12 margin-top-10 padding-0">

                            <div class="col-md-6">
                                <input type='submit' name='extornar_alta' id="extornar_alta"
                                    class="btn btn-success width-full" value='Salvar'>
                            </div>
                            <div class="col-md-6">
                                <input type='button' name='cancelarModal' id="cancelarModal" data-dismiss="modal"
                                    class="btn btn-danger width-full" value='Cancelar'>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleTabs" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog"
        tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="exampleModalTabs">Atestado</h4>
                </div>



                <form method="post" enctype="multipart/form-data" action="relAtestado.php" target="_blank">
                    <div class="modal-body" id='modalbody'>

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Data do atendimento</label>
                                    <input type="text" name="data_atendimento" id="data_atendimento"
                                        class="form-control"
                                        value="<?php echo inverteData($data_transacao); ?>"
                                        onKeyPress="formata(this,'##/##/####')" maxlength="10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Hora do atendimento</label>
                                    <input type="text" name="hora_atendimento" id="hora_atendimento"
                                        class="form-control"
                                        value="<?php echo $hora_transacao; ?>"
                                        onKeyPress="formata(this,'##:##')" maxlength="5">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Dias de atestado</label>
                                    <input type="text" name="dias_atestado" id="dias_atestado" class="form-control"
                                        value="">
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">CID</label>
                                    <input type="text" name="cidAtestado" id="cidAtestado" class="form-control"
                                        value="<?php echo $CID; ?>"
                                        onkeyup="maiuscula(this)">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox ">
                                    <input type="checkbox" class="custom-control-input" name="isolamento"
                                        id="isolamento" value='isolamento' <?php if ($isolamento == 1) {
	echo 'checked';
} ?>>
                                    <label class="custom-control-label" style="font-size: 10pt" for="isolamento">Termos
                                        de Responsabilidade de Isolamento</label>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="atendimento" id="atendimento"
                            value="<?= $_GET['id'] ?>">
                        <input type="hidden" name="profissional" id="profissional"
                            value="<?php echo $usuario ?>">
                        <input type="hidden" name="paciente" id="paciente"
                            value="<?php echo $prontuario ?>">

                        <button type="submit" name="enviar" class="btn btn-default">Imprimir</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>

                    </div>
                </form>


            </div>
        </div>
    </div>
    <div class="modal fade" id="comparecimento" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog"
        tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="exampleModalTabs">Atestado</h4>
                </div>



                <form method="post" enctype="multipart/form-data" action="relComparecimento.php" target="_blank">
                    <div class="modal-body" id='modalbody'>
                        <input type="hidden" name="nome_comparecimento"
                            value="<?= $nome; ?>">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Data do atendimento</label>
                                    <input type="text" name="data_atendimento" id="data_atendimento"
                                        class="form-control"
                                        value="<?php echo inverteData($data_transacao); ?>"
                                        onKeyPress="formata(this,'##/##/####')" maxlength="10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Hora do atendimento</label>
                                    <input type="text" name="hora_atendimento" id="hora_atendimento"
                                        class="form-control"
                                        value="<?php echo $hora_transacao; ?>"
                                        onKeyPress="formata(this,'##:##')" maxlength="5">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Fim do atendimento</label>
                                    <input type="text" style="border-color:red" name="hora_final" id="hora_final"
                                        class="form-control"
                                        value="<?php echo date('H:m'); ?>"
                                        value="" onKeyPress="formata(this,'##:##')" maxlength="5" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Afim de:</label>
                                    <textarea name="relato_comparecimento" id="relato_comparecimento"
                                        class="form-control" cols="30" rows="10"></textarea>
                                </div>
                            </div>

                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="enviar" class="btn btn-default">Imprimir</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="modalLaboratorio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title" id="myModalLabel8">Exames</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body col-md-12" id="conteudoEvolucao">
                    <select class="" id="procedimento_laboratorio" multiple>
                        <?php

						include 'conexao_laboratorio.php';
						if ($prioridade == 'AZUL' or $prioridade == 'VERDE') {
							$sql = 'select a.procedimentos_id as procedimento_id, a.descricao, a.codigo from procedimentos a
                inner join modalidades b on a.setor = b.modalidade_id
                inner join tabela_precos c on a.procedimentos_id = c.procedimento_id where modalidade_id not in (32 , 22) and a.procedimentos_id in (746, 769) and c.convenio_id = 1 ORDER BY a.descricao';
						} else {
							$sql = "select a.procedimentos_id as procedimento_id, a.descricao, a.codigo from procedimentos a
                inner join modalidades b on a.setor = b.modalidade_id where modalidade_id not in (32 , 22) and a.descricao not in('GLICOSE', 'PROTEINA C REATIVA - [ULTRA-SENSIVEL]','HIV 1 E 2 - ANTÍGENO E ANTICORPOS - ECLIA - TESTE DE TRIAGEM') ORDER BY a.descricao";
						}
						$sth = pg_query($sql) or die($sql);
						echo '<option value="">Selecione o Procedimento</option>';
						while ($row = pg_fetch_object($sth)) {
							echo '<option value="' . $row->procedimento_id . '"';
							echo '>' . $row->descricao . '</option>';
						}

						?>
                    </select>
                </div>

                <div class="modal-footer">
                    <input type='button' name='soclab' id="soclab" class="btn btn-success width-full" value='Salvar'>
                    <input type='button' name='cancelarModal' id="cancelarModal" data-dismiss="modal"
                        class="btn btn-danger width-full" value='Cancelar'>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSolicitaReceituario" aria-hidden="true" aria-labelledby="exampleModalTabs"
        role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalTabs">Receituário</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id='modalbody'>
                    <div id="bloco_receituario">
                        <div class="row">

                            <div class="col-4">
                                <div class="form-group">
                                    <label>Item/Medicamento</label>
                                    <input id="medicamento-1" maxlength="100" name="medicamento-1" class="form-control"
                                        value="" onkeyup="maiuscula(this)">
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="form-group">
                                    <label>Quantidade</label>
                                    <input id="quantidade-1" maxlength="50" name="quantidade-1" class="form-control"
                                        value="" onkeyup="maiuscula(this)">
                                </div>
                            </div>

                            <div class="col-5">
                                <div class="form-group">
                                    <label>Modo de usar</label>
                                    <textarea id="usar-1" name="usar-1" class="form-control" value=""
                                        onkeyup="maiuscula(this)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="botao">
                        <div class="col-12" style="text-aling: center">
                            <input type='button' style="margin: 0 auto;" id="novo_receituario" class="btn btn-success"
                                value="Adicionar Item">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="salvar_receituario" value="" class="btn btn-default"
                        onclick="salvar_prescricao(this)">Salvar</button>
                    <button type="button" id="closemodal" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>



            </div>
        </div>
    </div>
    <div class="modal" id="ExemploModalCentralizado" tabindex="-1" role="dialog"
        aria-labelledby="TituloModalCentralizado" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalCentralizado">Solicitação de Internação</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- PA Sistolica -->
                        <div class="col-3 form-group">
                            <label>PA Sistolica</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" class="form-control square" name="pa_sis"
                                    value="<?php echo $pressaosistolicai ?>"
                                    id="pa_sis">
                                <div class="form-control-position" style="top: 0px">
                                    <img src="app-assets/img/svg/nano.png" alt="\" height="20" width="20">
                                </div>
                            </div>
                        </div>

                        <!-- PA Distolica -->
                        <div class="col-3 form-group">
                            <label>PA Distolica</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" class="form-control square" name="pa_dis"
                                    value="<?php echo $pressaodiastolicai ?>"
                                    id="pa_dis">
                                <div class="form-control-position" style="top: 0px">
                                    <img src="app-assets/img/svg/nano.png" alt="\" height="20" width="20">
                                </div>
                            </div>
                        </div>

                        <!-- Oxigênio -->
                        <div class="col-3 form-group">
                            <label>Oxigênio</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" id="oxigenio" class="form-control" name="oxigenio"
                                    value="<?php echo $oxigenioi ?>"
                                    id="oxigenio">
                                <div class="form-control-position" style="top: 0px">
                                    <img src="app-assets/img/svg/o2.png" alt="\" height="20" width="20">
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group">
                            <label>Freq. Resp.</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" id="frequencia_respiratoria" class="form-control"
                                    name="frequencia_respiratoria"
                                    value="<?php echo $frequencia_respiratoriai ?>"
                                    id="frequencia_respiratoria">
                                <div class="form-control-position" style="top: 0px">
                                    <!-- <img src="app-assets/img/svg/o2.png" alt="\" height="20" width="20"> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group">
                            <!-- Pulso -->
                            <label>Pulso</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" id="pulso" class="form-control square" name="pulso"
                                    value="<?php echo $pulsoi ?>"
                                    id="pulso">
                                <div class="form-control-position" style="top: 0px">
                                    <i class="fas fa-stethoscope"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group">
                            <!-- Temperatura -->
                            <label>Temperatura</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" class="form-control square" name="temp"
                                    value="<?php echo $temperaturai ?>"
                                    id="temp">
                                <div class="form-control-position" style="top: 0px">
                                    <i class="fas fa-thermometer" style="font-size: 15pt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group">
                            <!-- Glicemia -->
                            <label>Glicemia</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" id="glicose" class="form-control square"
                                    value="<?php echo $glicosei ?>"
                                    name="glicose" id="glicose">
                                <div class="form-control-position" style="top: 0px">
                                    <img src="app-assets/img/svg/glicose.png" alt="\" height="25" width="18">
                                </div>
                            </div>
                        </div>
                        <div class="col-3 form-group">
                            <!-- Glicemia -->
                            <label>Glasgow:</label>
                            <div class="position-relative has-icon-left">
                                <input type="text" class="form-control square"
                                    value="<?php echo $ecgi ?>"
                                    name="ecg" id="ecg">
                                <div class="form-control-position" style="top: 0px">
                                    <!-- <img src="app-assets/img/svg/glicose.png" alt="\" height="25" width="18"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <p class="text-center"><img src="app-assets/img/svg/dor.png" alt="\" height="25" width="25">
                                Dor</p>
                            <input type="range" id="dor" class="slider mr-3" name="dor" min="0" max="9"
                                value="<?php echo $dor_internacaoi; ?>">
                            <strong id="valor" style="font-size: 20pt;"></strong>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <!-- <textarea class="form-control" name="obs_modal" id="obs_modal" style="resize: none" rows="10" cols="60" form="usrform" static><?php echo $obs_modal; ?></textarea>
                        -->
                        <div class="col-md-3 ml-3">
                            <label class="control-label">CID</label>
                            <input type="text" name="CID_permanencia" id="CID_permanencia" class="form-control"
                                value="<?= $cid_internacao; ?>"
                                onkeyup="maiuscula(this)" onblur="buscaCidpermanencia(this)" maxlength='5'>
                        </div>
                        <div class="col-md-8">
                            <label class="control-label">Diagnóstico Principal</label>
                            <input type="text" name="diag_pri_permanencia" id="diag_pri_permanencia"
                                onkeyup="retornaCidpermanencia(this)" class="form-control"
                                value="<?= $diag_pri_permanencia; ?>">

                            <!-- Está parte do codigo é referente a busca do CID -->

                            <style>
                                table #cidTable {
                                    border-collapse: collapse;
                                    width: 100%;
                                }

                                #cidTable th,
                                #cidTable td {
                                    text-align: left;
                                    padding: 8px;
                                }

                                #cidTable tr:nth-child(even) {
                                    background-color: #f0f0f0;
                                }

                                #lista_diagnostico {
                                    height: 150px;
                                    overflow: scroll;
                                    display: none;
                                    overflow-x: hidden;

                                }

                                #cidTable a {
                                    text-decoration: none;
                                }

                                .linha {
                                    padding: 10px;
                                    border-top: 1px solid #999999;
                                }
                            </style>
                            <div id="lista_diagnostico_permanencia">
                                <table id="cidTablepermanencia" class="table table-hover table-striped width-full">

                                </table>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="">Tipo de Leito:</label>
                            <select name="tipo_leito" id="tipo_leito">
                                <option value=""></option>
                                <option value="clinica" <?php ($tipo_leitoi == 'clinica') ? 'selected' : ''; ?>>clinica
                                </option>
                                <option value="cirurgica" <?php ($tipo_leitoi == 'cirurgica') ? 'selected' : ''; ?>>cirurgica
                                </option>
                                <option value="uti" <?php ($tipo_leitoi == 'uti') ? 'selected' : ''; ?>>uti
                                </option>
                                <option value="psiquiatria" <?php ($tipo_leitoi == 'psiquiatria') ? 'selected' : ''; ?>>psiquiatria
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <textarea name="obs_modal" id="obs_modal" cla3ss="form-control" rows="15"
                                style="resize: none" rows="10" cols="60" form="usrform"
                                static><?php echo $obs_modal; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" data-dismiss="modal"
                        onclick="modal_obs(<?php echo $transacao; ?> );"
                        class="btn btn-primary">Salvar mudanças</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="relatorio_pmmg" tabindex="-1" role="dialog" aria-labelledby="TituloModalCentralizado"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalCentralizado">Relatorio PMMG</h5>

                </div>
                <?php
				include 'conexao.php';
				$sql = "SELECT * FROM relatorio_pmmg WHERE atendimento_id = $transacao";
				$result = pg_query($sql) or die($sql);
				$row = pg_fetch_object($result);
				?>
                <form action="relatorio_pmmg.php" id="form_pmmg" target="_blank" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="atendimento_id_pmmg"
                            value="<?= $transacao ?>">
                        <label for="">Queixa do Paciente</label>
                        <textarea rows="3" onkeydown="limitLines(this, 3)" style="resize:none;overflow: hidden;"
                            maxlength="300" name="queixa_paciente_pmmg" id="queixa_paciente_pmmg"
                            class="form-control"><?= $row->queixa_paciente; ?></textarea>
                        <label for="">Diagnostico Medico</label>
                        <textarea rows="2" onkeydown="limitLines(this, 2)" style="resize:none;overflow: hidden;"
                            maxlength="200" name="diagnostico_medico_pmmg" id="diagnostico_medico_pmmg"
                            class="form-control"><?= $row->diagnostico_medico; ?></textarea>
                        <label for="">Orientação Paciente</label>
                        <textarea rows="3" onkeydown="limitLines(this, 3)" style="resize:none;overflow: hidden;"
                            maxlength="300" name="orientacao_paciente_pmmg" id="orientacao_paciente_pmmg"
                            class="form-control"><?= $row->orientacao_paciente; ?></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" onclick="this.form.submit();" data-dismiss="modal"
                            class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="referencia_contra" tabindex="-1" role="dialog" aria-labelledby="TituloModalCentralizado"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="TituloModalCentralizado">Referencia e contra referencia</h5>

                </div>
                <?php
				include 'conexao.php';
				$sql = "SELECT * FROM contra_referencia WHERE atendimento_id = $transacao";
				$result = pg_query($sql) or die($sql);
				$row = pg_fetch_object($result);
				?>
                <form action="referenciaecontra.php" id="form_referencia" target="_blank" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="atendimento_id_referencia"
                            value="<?= $transacao ?>">
                        <label for="">Unidade</label>
                        <select name="unidade_referencia" id="unidade_referencia">
                            <option value="UNIDADE BASICA DE SAUDE">UNIDADE BASICA DE SAUDE</option>
                            <option value="UNIDADE DIA">UNIDADE DIA</option>
                            <option value="COT">COT</option>
                            <option value="CAPS">CAPS</option>
                            <option value="SIAP">SIAP</option>
                            <option value="CTA">CTA</option>
                        </select>
                        <br>
                        <label for="">Justificativa</label>
                        <textarea rows="15" style="resize:none;overflow: hidden;" maxlength="500"
                            name="justificativa_referencia" id="justificativa_referencia"
                            class="form-control"><?= $row->justificativa; ?></textarea>
                        <label for="">Diagnostico ou hipótese diagnostica</label>
                        <textarea rows="2" style="resize:none;overflow: hidden;" maxlength="300"
                            name="diagnostico_referencia" id="diagnostico_referencia"
                            class="form-control"><?= $row->diagnostico; ?></textarea>
                        <label for="">Resultado Exames Realizados</label>
                        <textarea rows="5" style="resize:none;overflow: hidden;" maxlength="300"
                            name="resultado_referencia" id="resultado_referencia"
                            class="form-control"><?= $row->resultado; ?></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" onclick="this.form.submit();" data-dismiss="modal"
                            class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

    <!-- <div class="wrapper"> -->
    <!-- <?php include 'menu.php'; ?> -->
    <?php include 'header2.php'; ?>
    <div class="main-panel">
        <div class="">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- <div class="card-header" style="display: flex;align-items: center;justify-content: space-between; background: #00777a"> -->
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-12">
                                                <h4 class="card-title">
                                                    <p
                                                        style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                        » </p>Atendimento Clínico
                                                </h4>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="float-right ">
                                            <ol class="breadcrumb">
                                                <li><a href="../index.html">Home</a></li>
                                                <li><a href="atendimentos.php">Atendimentos</a></li>
                                                <li class="active">Novo Atendimento</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CORPO DA PAGINA -->
                        <form method="post" id="pedido" name='pedido' autocomplete="off">
                            <input type="hidden"
                                value="<?php echo $prioridade; ?>"
                                id="prioridade">
                            <input type="hidden"
                                value="<?php echo $coronavirus; ?>"
                                id="coronavirus" name="coronavirus">
                            <input type="hidden" name="transacao" id="transacao" class="form-control"
                                value="<?php echo $transacao; ?>"
                                readonly>
                            <div class="col-12">
                                <div style="display: flex; width: 100%;max-height: 280px">
                                    <div class="user text-center d-flex flex-column justify-content-center align-items-center"
                                        style="background-color: #12a1a6;border-radius: 20px; margin-right: 20px; padding:15px; max-width: 170px; justify-content: space-evenly;">
                                        <div class="col-12 d-flex flex-column align-items-center"
                                            style="justify-content: space-between; padding: 0">
                                            <img src="app-assets/img/gallery/user-circle.png" alt="\" height="70"
                                                width="70" style="border-radius: 50%;">
                                            <h6 style="color: white"><b id="nome"><?php if ($nome_social == '') {
					echo $nome;
				} else {
					echo $nome_social;
				} ?>
                                                </b></h6>
                                            <h6 style="color: white"><b id="sexo"><?php echo $sexo; ?></b>
                                            </h6>
                                            <h6 style="color: white"><b id="cns"><?php echo $cns; ?></b>
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="quadros"
                                        style="background-color: #EEEEEE;border-radius: 20px; border: #12a1a6 solid 2px;flex: 1;">
                                        <div class="" style="display: flex; justify-content: space-around; margin: 8px">
                                            <div class="item">
                                                <div class="d-flex">
                                                    <i class="fas fa-weight" aria-hidden="true"
                                                        style="font-size: 20pt"></i>
                                                    <h2 style="font-weight: 700;margin: 0 10px;color: #12a1a6; ">
                                                        <?php echo $peso; ?>KG
                                                    </h2>
                                                </div>

                                                <div class="d-flex">
                                                    <i class="fas fa-heartbeat" style="font-size: 20pt;"></i>
                                                    <h2 style="font-weight: 700;margin: 0 10px;color: #12a1a6;  ">
                                                        <?php echo $pulso; ?>
                                                    </h2>
                                                </div>
                                            </div>

                                            <div class="item">
                                                <div class="d-flex">
                                                    <img src="app-assets/img/svg/glicose.png" alt="\" height="30"
                                                        width="20">
                                                    <h2 style="font-weight: 700;margin: 0 10px;color: #12a1a6; ">
                                                        <?php echo $glicose; ?>
                                                    </h2>
                                                </div>
                                                <div class="d-flex">

                                                    <img src="app-assets/img/svg/o2.png" alt="\" height="25" width="25">
                                                    <h2 style="font-weight: 700;margin: 0 10px;color: #12a1a6;">
                                                        <?php echo $oxigenio; ?>
                                                    </h2>

                                                </div>
                                            </div>

                                            <div class="item ">
                                                <div class="d-flex">
                                                    <i class="fas fa-thermometer" style="font-size: 20pt"></i>
                                                    <h2 style="font-weight: 700;margin: 0 10px;color: #12a1a6; ">
                                                        <?php echo $temperatura; ?>º
                                                    </h2>
                                                </div>
                                                <div class="d-flex">

                                                    <img src="app-assets/img/svg/dor.png" alt="\" height="25"
                                                        width="25">
                                                    <h2 style="font-weight: 700;margin: 0 10px;color: #12a1a6; ">
                                                        <?php echo $dor; ?>
                                                    </h2>

                                                </div>
                                            </div>
                                            <div class="item ">
                                                <div class="d-flex">
                                                    <img src="app-assets/img/svg/nano.png" alt="\" height="25"
                                                        width="25">
                                                    <h2 style="font-weight: 700;margin: 0 10px;color: #12a1a6; ">
                                                        <?php echo $pressaosistolica . 'X' . $pressaodiastolica; ?>
                                                    </h2>
                                                </div>
                                                <div class="d-flex">
                                                    <img src="app-assets/img/svg/pulso.png" alt="\" height="30"
                                                        width="20">

                                                    <?php
													$data_finalizado = substr($datahora, 0, -8);
													$hora_finalizada = substr($datahora, -9);
													$cor = '#FFC107';
													if ($prioridade == 'AZUL') {
														$cor = '#2196F3';
													} elseif ($prioridade == 'LARANJA') {
														$cor = '#FF9800';
													} elseif ($prioridade == 'VERMELHO') {
														$cor = '#B71C1C';
													} elseif ($prioridade == 'VERDE') {
														$cor = '#4CAF50';
													}
													?>
                                                    <h2
                                                        style="font-weight: 700;margin: 0 10px;color: <?php echo $cor; ?> ">
                                                        <?php echo $prioridade; ?>
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="teste"></div>

                                        <div class="d-flex flex-column justify-content-center align-items-center"
                                            style="background-color: #12a1a6; height: 20px">
                                            <h6 style="color: white;font-weight: bold;margin-bottom: 0px;">Observações
                                            </h6>
                                        </div>


                                        <div class="texto text-center">
                                            <textarea disabled name="" id="texto" cols="" rows="4"
                                                style="width: 95.9%;height: 115px; border-radius: 0 0 20px 20px; border: 1px; padding: 10px 10px 0 10px;margin: 15px 20px 10px 15px;resize: none"><?php echo $relato; ?></textarea>
                                            <div class="d-flex flex-column justify-content-center align-items-center"
                                                style="background-color: #12a1a6; height: 20px;border-radius: 0 0 17px 17px;">
                                                <h8 style="color: white;margin-bottom: 0px;">
                                                    <?php echo 'Realizado na data: <b>' . inverteData(trim($data_finalizado)) . '</b> no horário: <b>' . $hora_finalizada . '</b>  pelo username: <b>' . $usuario_enf . '</b>' ?>
                                                </h8>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($coronavirus == 1 or $coronavirus == 10) {
														?>
                                <div class="row mt-3">
                                    <div class="col-md-2">
                                        <h4 class="form-section-center"><img src="virus-home.png"> CheckList Covid</h4>
                                    </div>
                                    <div class="col-3 col">
                                        <div class="col-sm-12"><br>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="tosse_secracao" name="tosse_secracao" <?php if ($tosse_secracao == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="tosse_secracao">Tosse/Secreção
                                                    Catarral</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="dificuldade_respirar" name="dificuldade_respirar" <?php if ($dificuldade_respirar == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label"
                                                    for="dificuldade_respirar">Dificuldade de Respirar</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="fadiga" name="fadiga" <?php if ($fadiga == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="fadiga">Fadiga</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="febre_alta" name="febre_alta" <?php if ($febre_alta == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="febre_alta">Febre Alta</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="congestao_nasal" name="congestao_nasal" <?php if ($congestao_nasal == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="congestao_nasal">Congestão
                                                    Nasal</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="cefaleia" name="cefaleia" <?php if ($cefaleia == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="cefaleia">Cefaléia</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="covid2" class="col-3">
                                        <div class="col-12"><br>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="dor_garganta" name="dor_garganta" <?php if ($dor_garganta == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="dor_garganta">Dor de
                                                    Garganta</label>
                                            </div>



                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="diarreia" name="diarreia" <?php if ($diarreia == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="diarreia">Diarréia</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="nausea_vomito" name="nausea_vomito" <?php if ($nausea_vomito == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label"
                                                    for="nausea_vomito">Nausea/Vomitos</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="mialgia_artralgia" name="mialgia_artralgia" <?php if ($mialgia_artralgia == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="mialgia_artralgia">Mialgia ou
                                                    Artralgia</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="calafrios" name="calafrios" <?php if ($calafrios == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="calafrios">Calafrios</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="anosmia_hiposmia" name="anosmia_hiposmia" <?php if ($anosmia_hiposmia == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label"
                                                    for="anosmia_hiposmia">Anosmia/Hiposmia/Digeusia</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div id="covid2" class="col-3">
                                        <div class="col-12"><br>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="diabetes" name="diabetes" <?php if ($diabetes == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="diabetes">Diabetes</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="has" name="has" <?php if ($has == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="has">HAS</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="obesidade" name="obesidade" <?php if ($obesidade == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="obesidade">Obesidade</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="doenca_coronariana" name="doenca_coronariana" <?php if ($doenca_coronariana == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="doenca_coronariana">Doenca
                                                    Coronariana</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="dpoc_asma" name="dpoc_asma" <?php if ($dpoc_asma == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="dpoc_asma">DPOC ou Asma</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="cancer" name="cancer" <?php if ($cancer == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="cancer">Cancer</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="drc" name="drc" <?php if ($drc == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label" for="drc">DRC</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" <?php echo $rdonly ?>class="custom-control-input"
                                                id="imunodeficiencia" name="imunodeficiencia" <?php if ($imunodeficiencia == '1') {
															echo 'checked';
														} ?>>
                                                <label class="custom-control-label"
                                                    for="imunodeficiencia">Imunodeficiência</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
													} ?>
                                <div class="card-body row">
                                    <!-- iniciando tabs -->
                                    <ul class="nav nav-tabs nav-justified col-12" style="padding-right: 0px;">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active"
                                                aria-controls="active" aria-expanded="true">Informaçoes do
                                                Atendimento</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="link-tab" data-toggle="tab" href="#link"
                                                aria-controls="link" aria-expanded="false">Exames de
                                                Imagem/Laboratoriais</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="click-tab" data-toggle="tab" href="#click"
                                                aria-controls="click" aria-expanded="false">Prescrições</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="linkOpt-tab" data-toggle="tab" href="#linkOpt"
                                                aria-controls="linkOpt">Estadias/Receituário</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="linkOpt-tab" data-toggle="tab" href="#linkAtestados"
                                                aria-controls="linkOpt">Historico Atestados</a>
                                        </li>
                                    </ul>
                                    <input type="hidden" name="atendimento" id="atendimento"
                                        value="<?= $_GET['id'] ?>">
                                    <input type="hidden" name="profissional" id="profissional"
                                        value="<?php echo $usuario ?>">
                                    <input type="hidden" name="paciente" id="paciente"
                                        value="<?php echo $prontuario ?>">
                                    <input type="hidden" id="id"
                                        value="<?php echo $paciente_id; ?>">
                                    <input type="hidden" name="idade" id="idade" class="form-control"
                                        value="<?php echo $idade; ?>"
                                        readonly>
                                    <!-- Informaçoes do Atendimento Realizado -->
                                    <div class="tab-content px-1 col-12" style="height: 600px">
                                        <div role="tabpanel" class="tab-pane active show" id="active"
                                            aria-labelledby="active-tab" aria-expanded="true">

                                            <div class="col-12 text-center mt-4 mb-4">
                                                <h4 class="form-section-center"><i class="fas fa-info-circle"></i>
                                                    Informações do Atendimento Realizado</h4>
                                                <!-- <h3 class="title" align="center">Identificação do Paciente</h3> -->
                                                <hr style="margin: auto;width: 450px">
                                            </div>
                                            <br>
                                            <div class="col-sm-12">
                                                <label class="control-label">Queixa</label>
                                                <input type="text" name="queixa" id="queixa" class="form-control"
                                                    value="<?php echo $queixa; ?>"
                                                    maxlength="80" onkeyup="maiuscula(this)" <?php echo $rdonly ?>>
                                            </div>
                                            <br>
                                            <div class="col-sm-12 margin-top-10">
                                                <label class="control-label">Exame Físico</label>
                                                <textarea name="exame_fisico" id="exame_fisico" class="form-control"
                                                    rows="15" maxlength="1200"
                                                    <?php echo $rdonly ?> required><?php echo $exame_fisico; ?></textarea>
                                                </br>
                                            </div>

                                            <input id="usuario-autorizado" name="usuario-autorizado" type="hidden" />

                                            <?php
											if ($transacao != '') {
												include 'conexao.php';
												$stmt = "SELECT count(*) as qtde FROM arquivos_documentos where transacao=$transacao";
												$sth = pg_query($stmt) or die($stmt);
												$row = pg_fetch_object($sth);
												if ($row->qtde > 0) {
													echo '<div class="col-sm-12">';
													echo '<h3 class="page-title" align="center">Anexos</h3>';
													echo '<hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />';
													echo '<div class="col-sm-12" id="anexos"';
													echo '<div class="col-md-12">';
													echo '<div class="form-group">';
													echo '<table class="table table-hover table-striped width-full">';
													echo '<thead><tr>';
													echo "<th width='15%'>Data</th><th width='30%'>Tipo</th><th width='20%'>Descricao</th><th width='25%'>Usuario</th><th width='10%'>Açao<th>";
													echo '</tr></thead><tbody>';
													$x = 0;
													include 'conexao.php';
													$stmt = 'select a.pessoa_id, b.dat_cad, a.situacao, a.exame_id, c.descricao from itenspedidos a left join pedidos b on a.transacao=b.transacao left join procedimentos c on a.exame_id=c.procedimento_id where a.pessoa_id=' . $prontuario;
													$sth = pg_query($stmt) or die($stmt);
													while ($row = pg_fetch_object($sth)) {
														echo '<tr>';
														echo '<td>' . inverteData(substr($row->dat_cad, 0, 10)) . '</td>';
														echo '<td>UPA ' . utf8_decode(UNIDADE_CONFIG) . '</td>';
														echo "<td><a href='atendimentoclinico.php?id=" . $row->transacao . "' target='_blank' class=\"fas fa-search\"></a></td>";
														echo '</tr>';
													}

													echo '</tbody></table>';
													echo '</div>';
													echo '</div>';
													echo '<br>';
													echo '</div>';
													echo '</div>';
												}
											}
											?>
                                        </div>

                                        <!-- EXAMES -->
                                        <div class="tab-pane" id="link" role="tabpanel" aria-labelledby="link-tab"
                                            aria-expanded="false">
                                            <style>
                                                input[type=checkbox] {
                                                    width: 20px;
                                                    height: 20px;
                                                    cursor: pointer;
                                                    font-size: 27px;
                                                }

                                                input[type=checkbox]:after {
                                                    content: " ";
                                                    background-color: #9FFF9D;
                                                    display: inline-block;
                                                    visibility: visible;
                                                }
                                            </style>
                                            <div class="d-flex">
                                                <div class="col-sm-6"><br>
                                                    <div class="col-12 text-center mb-4">
                                                        <h4 class="form-section-center"><i class="fas fa-radiation"></i>
                                                            Exames de Imagem</h4>

                                                        <hr style="margin: auto;width: 250px">
                                                    </div>

                                                    <div class="col-sm-12 scroll">
                                                        <table class="table condensed width-full"
                                                            id="exames_atendimentos">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th width='25%'>Data / Situação</th>
                                                                    <th width='65%'>Descrição</th>
                                                                    <th width='10%'>Laudo</th>
                                                                    <!--<th width='10%'>Ação</th>-->
                                                                </tr>
                                                            </thead>

                                                            <body>
                                                                <?php
																include 'conexao.php';
																$stmt = 'select b.dat_cad,c.formulario, a.exame_nro,a.arquivo_upload, a.pessoa_id,a.transacao, c.descricao, a.situacao, a.versao, c.exames_laboratoriais, c.procedimento_id from itenspedidos a 
													left join pedidos b on a.transacao=b.transacao 
													left join procedimentos c on a.exame_id=c.procedimento_id where a.pessoa_id=' . $paciente_id . ' and c.exames_laboratoriais is null order by dat_cad desc, a.exame_id';
																$sth = pg_query($stmt) or die($stmt);
																//echo $stmt;
																$x = 0;
																$data = '';
																$procedimento_lab = '';
																while ($row = pg_fetch_object($sth)) {
																	if ($row->exames_laboratoriais == 1 and substr($row->dat_cad, 0, 10) >= '2019-01-08') {
																		//if(($data != substr($row->dat_cad, 0, 10) or $data != date('Y-m-d', strtotime("+1 days",strtotime(substr($row->dat_cad, 0, 10))))) and $procedimento_lab != $row->procedimento_id){
																		include 'conexao_laboratorio.php';
																		$sql = 'SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, e.descricao 
																FROM pedidos a
																INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
																INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
																INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
																LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
																LEFT JOIN modalidades f ON f.modalidade_id = e.setor where d.exame_id = ' . $row->procedimento_id . " and a.data = '" . substr($row->dat_cad, 0, 10) . "' and c.origem = '01' and b.origem = 1 and pessoa_id_origem = $prontuario order by a.data, a.horario";
																		$result = pg_query($sql) or die($sql);
																		while ($rows = pg_fetch_object($result)) {
																			if ($rows->exame_id == $row->procedimento_id) {
																				echo '<tr>';
																				echo '<td><div><input type="checkbox" name="cb_exame[]" value="' . $row->exame_nro . '"><label></label></div></td>';
																				echo '<td>' . inverteData($rows->data) . '</td>';
																				echo "<td>$row->descricao</td>";
																				if ($rows->liberado == 1) {
																					echo "<td><a href='http://sgupa.com.br/mr/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"fas fa-search fas fa-search\"></a></td>";
																				}
																				echo '</tr>';
																			}
																		}
																		$data = $row->dat_cad;
																		$procedimento_lab = $row->procedimento_id;
																		$x = $x + 1;
																	//}
																	} else {
																		$x = $x + 1;
																		if ($row->situacao == 'Aut.Pendente') {
																			echo "<tr class='bg-danger' >";
																		} else {
																			echo '<tr>';
																		}
																		echo '<td><div><input type="checkbox" name="cb_exame[]"    value="' . $row->exame_nro . '"><label></label></div></td>';
																		echo '<td>' . inverteData(substr($row->dat_cad, 0, 10)) . ' ' . $row->situacao . $row->versao . '</td>';
																		echo '<td>' . $row->descricao . '</td>';
																		include 'conexao.php';
																		$sqlDetalhe = 'SELECT * FROM arquivos_documentos WHERE transacao = ' . $row->exame_nro;
																		$sthDet = pg_query($sqlDetalhe) or die($sqlDetalhe);
																		$rowDetalhe = pg_fetch_object($sthDet);
																		echo '<td>';
																		if ($rowDetalhe->arquivo != '') {
																			echo "<a href='imagens/documentos/$rowDetalhe->arquivo' target='_blank' class=\"fas fa-search fas fa-search\"></a>";
																		}

																		if ($row->arquivo_upload != '') {
																			echo "<a href='arquivos/exames/$row->arquivo_upload' target='_blank' class=\"fas fa-search fas fa-search\"></a>";
																		}

																		if ($row->formulario == 'A') {
																			echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\"><i class=\"icon wb-print\" aria-hidden=\"true\" onclick=\"openInNewTab('relApac.php?id=$row->exame_nro')\"></i></button>";
																		}
																		if ($row->situacao == 'Finalizado') {
																			if ($row->versao != '2') {
																				echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"fas fa-search fas fa-search\"></a>";
																			} else {
																				echo "<a href='http://sgupa.com.br/laboratorio/html/relExamemr.php?local=MR&transacao=$row->exame_nro'' target='_blank' class=\"fas fa-search fas fa-search\"></a>";
																			}
																		}

																		if ($row->situacao == 'Impresso') {
																			echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"fas fa-search fas fa-search\"></a>";
																		}
																		if (HABILITAR_PACS) {
																			include 'conexao_pacs.php';
																			$stmt = "select a.pat_id, b.study_iuid, b.study_datetime from patient a, study b where b.patient_fk=a.pk and b.accession_no='$row->exame_nro' ";
																			$sthx = pg_query($stmt) or die($stmt);
																		}
																		//echo $stmt;
																		$rowst = pg_fetch_object($sthx);
																		$studyid = $rowst->study_iuid;
																		if ($studyid != '') {
																			$hora_rea = date('H:i:s', strtotime($rowst->study_datetime));
																		}
																		if ($studyid != '') {
																			if (substr($ip, 0, 3) == '192') {
																				echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Imagens\"><i class=\"fas fa-x-ray\" aria-hidden=\"true\" onclick=\"window.open('" . URL_PACS . '/oviyam2/viewer.html?studyUID=' . $studyid . '&serverName=' . SERVER_PACS . "', 'Visualizador', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;\"></i></button>";
																			} else {
																				echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Imagens\"><i class=\"fas fa-x-ray\" aria-hidden=\"true\" onclick=\"window.open('http://" . URL_PACS . '/oviyam2/viewer.html?studyUID=' . $studyid . '&serverName=' . SERVER_PACS . "', 'Visualizador', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;\")\"></i></button>";
																			}
																		}
																		echo '</td>';

																		//echo"<td class='small'><a href=\"deletarexames.php?id=$row->transacao&atendimento=$transacao\" data-toggle=\"tooltip\" data-original-title=\"Deletar Pedido de Exame\"><i class=\"fa fa-times text-danger\" aria-hidden=\"true\"></i></a></td>";

																		echo '</tr>';
																	}
																}

																?>
                                                            </body>
                                                        </table>
                                                    </div>

                                                    <?php //if ($perfil == '06' or $perfil == '03') {
													?>
                                                    <div class="col-12">
                                                        <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '07' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '10' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '03' or $destino == '') { ?>
                                                        <table
                                                            class="table table-hover table-striped condensed width-full">
                                                            <tr>
                                                                <td class="text-center" colspan="2"><label
                                                                        class="control-label">
                                                                        <font color='#12A1A6'>Adicionar
                                                                            Exames/Procedimentos</font>
                                                                    </label></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <select name="procedimento" id="procedimento"
                                                                        title="Selecione o Procedimento">
                                                                        <?php
																			include 'conexao.php';
																			$sql = "SELECT procedimento_id, descricao, sigtap FROM procedimentos a 
                                                                        WHERE case when modalidade_id = 2 then sigtap in ('0205020038','0205020046','0205020054','0205020062','0205020070','0205020127','0205020160','0205010040','0205020135') and valor <> 0 else modalidade_id is not null end and descricao <> '%EXCLUIDO%' AND procedimento_id NOT IN (729,730,822,821,779) AND descricao NOT IN ('DOSAGEM DE FOLATO',
                                                                        'DOSAGEM DE PROTEINAS TOTAIS',
                                                                        'TROPONINA T',
                                                                        'DOSAGEM DE FERRITINA',
                                                                        'DOSAGEM DE 25 HIDROXIVITAMINA D',
                                                                        'DOSAGEM DE VITAMINA B12',
                                                                        'EXCLUIDO',
                                                                        'EXCLUIDO',
                                                                        'EXCLUIDO',
                                                                        'FOSFORO',
                                                                        'WWW-EXCLUIDO',
                                                                        'DOSAGEM DE TRIGLICERIDEOS',
                                                                        'SANGUE OCULTO NAS FEZES IMUNOLOGICO',
                                                                        'ERITROGRAMA',
                                                                        'FOSFATASE ALACALINA',
                                                                        'ZHHHHH',
                                                                        'EXCLUIDO',
                                                                        'WTL -EXCLUIDO',
                                                                        'BACTERIOSCOPIA (GRAM)	',
                                                                        'DOSAGEM DE FERRO SERICO',
                                                                        'DETERMINACAO DE CAPACIDADE DE FIXACAO DO FERRO',
                                                                        'DOSAGEM DE HEMOGLOBINA GLICOSILADA',
                                                                        'HCG - GONADOTROFINA CORIÔNICA QUALITATIVO',
                                                                        'HEPATITE B - ANTI-HBs',
                                                                        'GASOMETRIA VENOSA',
                                                                        'MAGNESIO',
                                                                        'ALT - TRANSAMINASE PIRUVICA, [TGP]',
                                                                        'AST - TRANSAMINASE OXALACETICA, [TGO]',
                                                                        'GAMA-GLUTAMIL-TRANSFERASE, [GAMA GT]',
                                                                        'AMILASE',
                                                                        'LACTATO',
                                                                        'ACIDO URICO',
                                                                        'CALCIO',
                                                                        'BACILOSCOPIA - [PESQUISA]',
                                                                        'CREATINOFOSFOQUINASE FRACAO MB, [MASSA]',
                                                                        'ROTINA DE LIQUIDO ENCEFALORAQUIDIANO, [LCR]',
                                                                        'TEMPO DE TROMBOPLASTINA PARCIAL ATIVADA - [TTPA]',
                                                                        'GRUPO SANGUÍNEO E FATOR RH',
                                                                        'TEMPO DE PROTROMBINA, [TAP]',
                                                                        'PARASITOLOGICO DE FEZES',
                                                                        'SODIO',
                                                                        'POTASSIO',
                                                                        'DOSAGEM DE BILIRRUBINA TOTAL E FRACOES',
                                                                        'PROTEINA C REATIVA - [ULTRA-SENSIVEL]',
                                                                        'PROTEINAS TOTAIS E FRACOES',
                                                                        'VDRL, [LUES]',
                                                                        'ANTI-HIV I/II, [TRIAGEM - QUALITATIVO]',
                                                                        'SOROLOGIA PARA HCV',
                                                                        'UREIA',
                                                                        'ALBUMINA',
                                                                        'PROTEINAS TOTAIS',
                                                                        'GLICOSE',
                                                                        'CREATININA',
                                                                        'GASOMETRIA ARTERIAL',
                                                                        'DESIDROGENASE LATICA, [LDH]',
                                                                        'CREATINOFOSFOQUINASE TOTAL, [CPK]',
                                                                        'HEMOGRAMA COMPLETO',
                                                                        'ROTINA DE URINA  DE 2º JATO, [JATO MÉDIO]',
                                                                        'GONADOTROFINA CORIÓNICA HUMANA, [BETA HCG QUANTITATIVO]',
                                                                        'GONADOTROFINA CORIÓNICA HUMANA, [BETA HCG QUALITATIVO]',
                                                                        'GLICOSE, (GLICEMIA DE JEJUM)',
                                                                        'SOROLOGIA ANTI-HIV I/II',
                                                                        'CLORO',
                                                                        'ROTINA ACIDENTE DE TRABALHO, [FUNEPU]') order by descricao";
																			$sth = pg_query($sql) or die($sql);

																			while ($row = pg_fetch_object($sth)) {
																				echo '<option value="' . $row->procedimento_id . '"';
																				echo '>' . $row->descricao . '</option>';
																			}
																			?>
                                                                    </select>

                                                                </td>
                                                                <td><input type='button' name='novo_exame'
                                                                        id="novo_exame" class="btn btn-primary"
                                                                        value='Solicitar'>
                                                                    <input type='submit' name='req_exame' id="req_exame"
                                                                        class="btn btn-success" value='Imprimir'
                                                                        onclick="document.getElementById('destino').removeAttribute('required');">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <?php } ?>
                                                    </div>
                                                    <?php //}
													?>
                                                </div>
                                                <!-- Exames Laboratoriais -->
                                                <div class="col-6">
                                                    <div class="col-12 text-center mb-4"><br>
                                                        <h4 class="form-section-center"><i
                                                                class="fas fa-microscope"></i> Exames Laboratoriais</h4>
                                                        <hr style="margin: auto;width: 260px">
                                                    </div>
                                                    <!-- <i class="fas fa-microscope white font-large-2 float-left"></i>
                                            <h3 class="title" align="center" style="margin-bottom: -15px;">Exames Laboratoriais</h3> -->
                                                    <div id="exames_laboratorio" class="scroll">
                                                        <!-- <div class="col-sm-12" style="height: 255px; overflow-y: auto; overflow-x: hidden; padding:0"><br> -->
                                                        <table class="table condensed width-full" style="height: 255px">
                                                            <thead>
                                                                <tr>
                                                                    <th width='5%'></th>
                                                                    <th width='30%'>Data / Situação</th>
                                                                    <th width='60%'>Descrição</th>
                                                                    <th width='10%'>Laudo</th>
                                                                    <!--<th width='10%'>Ação</th>-->
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php
																include 'conexao_laboratorio.php';
																$sql = "SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, d.situacao, e.descricao, d.pedido_item_id 
                                                                FROM pedidos a
                                                                INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
                                                                INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
                                                                INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
                                                                LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
                                                                LEFT JOIN modalidades f ON f.modalidade_id = e.setor where c.origem = '" . ORIGEM_CONFIG . "' and b.origem = '" . PORIGEM_CONFIG . "' and pessoa_id_origem = $prontuario order by a.data desc, a.horario";
																$result = pg_query($sql) or die($sql);
																while ($rows = pg_fetch_object($result)) {
																	echo '<tr>';
																	if ($rows->situacao == '' or $rows->situacao == 'Coletado') {
																		echo '<td><div><input type="checkbox" name="cb_exame[]" value="' . $rows->pedido_item_id . '"><label></label></div></td>';
																	} else {
																		echo '<td></td>';
																	}
																	echo '<td>' . inverteData($rows->data) . '</td>';
																	echo "<td>$rows->descricao</td>";
																	if ($rows->situacao == 'Liberado') {
																		echo "<td><a href='http://" . IP_CONFIG . "/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"fas fa-search\"></a></td>";
																	} elseif ($rows->situacao != 'Liberado') {
																		echo '<td>' . $rows->situacao . '</td>';
																	}
																	echo '</tr>';
																}
																?>
                                                            </tbody>
                                                        </table>

                                                        <!-- </div> -->
                                                    </div>
                                                    <?php if ($perfil == '06' or $perfil == '03' or $perfil == '04' or $perfil == '08') { ?>
                                                    <div class="col-md-12 text-center mt-5">
                                                        <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '07' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '10' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '03' or $destino == '' or $perfil == '06' or $perfil == '04') { ?>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <input type='button' id="solicita_laboratorio" href="#"
                                                                    data-target="#modalLaboratorio"
                                                                    value='Solicitar Laboratorio'
                                                                    class="btn btn-success" data-toggle="modal">
                                                                <input type='button' name='req_exame_lab'
                                                                    id="req_exame_lab" class="btn btn-warning"
                                                                    value='Imprimir Solicitados'
                                                                    onclick="imprimir_laboratorio();">
                                                            </div>
                                                        </div>
                                                        <?php } ?>
                                                        <div class="row">
                                                            <div class="col-md-12 text-center">
                                                                <input type="date" name="data" id="data">
                                                                <input type='button' name='imprimir_exames'
                                                                    id="imprimir_exames" class="btn btn-primary"
                                                                    value='Imprimir Exames' onclick="laboratorio()">
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Prescrições/Receituário -->
                                        <div class="tab-pane" id="click" role="tabpanel" aria-labelledby="click-tab"
                                            aria-expanded="false">
                                            <div class="col-sm-12">
                                                <div id="retorno_prescricao">
                                                    <div class="col-12 text-center"><br>
                                                        <h4 class="form-section-center"><i
                                                                class="fas fa-notes-medical"></i> Prescrições</h4>
                                                        <hr style="margin: auto;width: 260px">
                                                    </div>

                                                    <div class="col-sm-12"
                                                        style="height: 295px; overflow-y: auto; overflow-x: hidden;">
                                                        <br>

                                                        <table
                                                            class="table table-hover table-striped condensed width-full">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" name="todos" id='todos'
                                                                            onclick='marcardesmarcar();' value="T"></th>
                                                                    <th width="10%">Data</th>
                                                                    <th>Prescrição/Profissional</th>
                                                                    <th width="40%"><input type="button"
                                                                            name="atualizart" id="atualizart"
                                                                            onclick="atualizat(<?php echo $transacao; ?>)"
                                                                            class="form-control" value="Atualizar"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="conteudoPrescricao">
                                                                <?php
																include 'conexao.php';
																$stmt = 'SELECT a.prescricao_id,a.hora,a.data,a.descricao,b.nome, a.cuidados, a.componente1,a.componente2,a.componente3,a.administracao,a.tipo FROM prescricao_itens a
															left join pessoas b ON b.username = a.medico
															WHERE a.atendimento_id =' . $transacao . ' and a.sequencia is null order by a.tipo,a.prescricao_id ASC';
																$sth = pg_query($stmt) or die($stmt);

																while ($row = pg_fetch_object($sth)) {
																	echo '<tr>';
																	echo "<td><div><input type=\"checkbox\" class='marcar' name=\"cb_prescricao[]\"    value=\"" . $row->prescricao_id . '"><label></label></div></td>';
																	echo '<td>' . inverteData(substr($row->data, 0, 10)) . '<br>' . $row->hora . '</td>';

																	if ($row->tipo == 10) {
																		echo "<td class='small'>" . utf8_encode($row->cuidados) . '<br>' . ts_decodifica($row->nome) . '</td>';
																	}

																	if ($row->tipo == 1) {
																		echo "<td class='small'>" . utf8_encode($row->cuidados) . '<br>' . ts_decodifica($row->nome) . '</td>';
																	}

																	if ($row->tipo == 3) {
																		echo "<td class='small'>" .
																			utf8_encode($row->descricao) . '<br>' .
																			utf8_encode($row->componente1) . ' - ' . utf8_encode($row->componente2) . ' - ' . utf8_encode($row->componente3) . '<br>' .
																			utf8_encode($row->administracao) .
																			'<br>' .
																			ts_decodifica($row->nome) .
																			'</td>';
																	}

																	if ($row->tipo == 5) {
																		echo "<td class='small'>" . utf8_encode($row->descricao) . '<br>' . ts_decodifica($row->nome) . '</td>';
																	}

																	//echo "<td class='small'><a href=\"deletarprescricao.php?id=$row->prescricao_id&atendimento=$transacao\" data-toggle=\"tooltip\" data-original-title=\"Deletar Prescrição\"><i class=\"fa fa-times text-danger\" aria-hidden=\"true\"></i></a></td>";

																	echo '<tr>';
																}
																?>
                                                                <?php include 'conexao.php';
																$stmt = "select a.data, a.hora, c.nome, a.prescricao_id, d.nome as medico
																from prescricoes a
																left join atendimentos b on a.atendimento_id = b.transacao
																left join pessoas c on b.paciente_id = c.pessoa_id
																left join pessoas d on d.pessoa_id = a.profissional_id
																where a.atendimento_id = $transacao order by a.prescricao_id desc";
																$sth = pg_query($stmt) or die($stmt);

																while ($row = pg_fetch_object($sth)) {
																	$seq = $row->sequencia + 1;
																	echo '<tr>';
																	echo "<td class='small'>" . $seq . '</td>';
																	echo "<td class='small'>" . date('d/m/Y', strtotime($row->data)) . '</td>';
																	echo "<td class='small'>" . $row->hora . '</td>';
																	echo "<td class='small'>" . ts_decodifica($row->nome) . '</td>';
																	echo "<td class='small'>" . ts_decodifica($row->medico) . '</td>';
																	if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '07' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '10' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '03' or $destino == '' or $perfil == '06' or $perfil == '04') {
																		echo "<td class='small'>
															<a href=\"prescricaoenfermagemy.php?id=$row->prescricao_id&p=$transacao\" target=\"_blank\" 
															class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" 
															data-original-title=\"Prescrição\">
                                                            <i class=\"fas fa-print\"></i>
															</a>
															<input type='button' onClick=\"window.open('popprescricao.php?prioridade=$prioridade&pr=$row->prescricao_id&id=$transacao&nome=$nome&cns=$cns&idade=$idade&prontuario=$prontuario', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=980, height=550'); return false;\" target='_blank'
															class='btn btn-secondary btn-sm' data-toggle=\"tooltip\" data-original-title=\"Duplicar Prescrição\" value='+'>
															
                                                        </td>";
																	}

																	echo '<tr>';
																}
																?>
                                                            </tbody>
                                                            <tfoot>

                                                            </tfoot>
                                                        </table>
                                                        <div id="table">

                                                        </div>

                                                    </div>
                                                    <div class="col-sm-12 text-center">
                                                        <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '07' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '10' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '03' or $destino == '' or $perfil == '06' or $perfil == '04') { ?>
                                                        <!--<input type='button' value="Solicitar Prescrição" id="prescricaoSolicitaModal" onclick="solicitacaoprescricao('<?php echo $_GET['id'] ?>')"
                                                        class="btn btn-success margin-10" data-toggle="modal" >-->
                                                        <!--<input type='button' value="Solicitar Prescrição" id="prescricaoSolicitaModal" onclick="solicitacaoprescricao('<?php echo $_GET['id'] ?>')"
                                                        class="btn btn-success margin-10" data-toggle="modal" >-->
                                                        <input type='button' id="pres" value='Solicitar Prescricão'
                                                            class="btn btn-success"
                                                            onClick="window.open('popprescricao.php?prioridade=<?php echo $prioridade; ?>&id=<?php echo $transacao; ?>&nome=<?php echo $nome; ?>&cns=<?php echo $cns; ?>&idade=<?php echo $idade; ?>&prontuario=<?php echo $prontuario; ?>', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1030, height=550'); return false;">
                                                        <input type='button' id="prescricao" onclick="return validar()"
                                                            value='Imprimir Prescricão' class="btn btn-warning">
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="tab-pane" id="linkOpt" role="tabpanel" aria-labelledby="linkOpt-tab"
                                            aria-expanded="false">
                                            <div class="col-12 d-flex">

                                                <div class="col-6 col"><br>
                                                    <div class="col-12 text-center"><br>
                                                        <h4 class="form-section-center"><i
                                                                class="fas fa-hospital-alt"></i> Estadias Anteriores
                                                        </h4>
                                                        <hr style="margin: auto;width: 260px">
                                                    </div>

                                                    <div class="col-sm-12 scroll"><br>

                                                        <table class="table table-hover table-striped width-full">
                                                            <thead>
                                                                <tr>
                                                                    <th>Data</th>
                                                                    <th>Unidade</th>
                                                                    <th style="text-align: center">Laudo</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php
																include 'conexao.php';
																$stmt = 'select dat_cad, paciente_id, sigla, transacao from atendimentos a left join unidade_atendimento b on a.local=b.unidade_id where paciente_id=' . $prontuario . ' and a.transacao<>' . $transacao . ' order by dat_cad desc';
																$sth = pg_query($stmt) or die($stmt);
																//echo $stmt;
																while ($row = pg_fetch_object($sth)) {
																	echo '<tr>';
																	echo '<td>' . inverteData(substr($row->dat_cad, 0, 10)) . '</td>';
																	echo '<td>' . UNIDADE_CONFIG . '</td>';
																	echo "<td align=\"center\" ><a href='atendimentoclinico.php?id=" . $row->transacao . "&estadia=1' target='_blank' class=\"fas fa-search\"></a></td>";
																	echo '</tr>';
																}
																?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                                <div id="receit" class="col-6">
                                                    <div class="col-12"><br>
                                                        <div class="col-12 text-center"><br>
                                                            <h4 class="form-section-center"><i
                                                                    class="fas fa-clipboard"></i> Receituário</h4>
                                                            <hr style="margin: auto;width: 260px">
                                                        </div>

                                                        <div class="col-sm-12 scroll"><br>

                                                            <table class="table table-hover table-striped width-full">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Itens/Medicamentos</th>
                                                                        <th width="20%">Quantidade</th>
                                                                        <th>Modo de usar</th>
                                                                        <th>Ações</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    <?php
																	$stmt = "select medicamentos,quantidade,modo_usar 
																from receituario_remedio 
																where transacao = $transacao";
																	$sth = pg_query($stmt) or die($stmt);
																	//echo $stmt;
																	while ($row = pg_fetch_object($sth)) {
																		echo '<tr>';
																		echo '<td>' . $row->medicamentos . '</td>';
																		echo '<td>' . $row->quantidade . '</td>';
																		echo '<td>' . $row->modo_usar . '</td>';
																		echo '<td><button type="button" class="btn btn-success" onclick="edit_receituario(' . $transacao . ',\'' . $row->medicamentos . '\')"><i class="fas fa-file-medical"></i></button></td>';
																		echo '</tr>';
																	}
																	?>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="col-sm-12 text-center margin-top-10">
                                                            <?php
															include 'conexao.php';
															$stmt = 'select count(*) as qtd from receituario_remedio where transacao =' . $transacao;
															$sth = pg_query($stmt) or die($stmt);
															//echo $stmt;
															$row = pg_fetch_object($sth);
															echo '<input type="button" id="receituario" href="#" data-id="$_GET[\'id\']" data-target="#modalSolicitaReceituario" onclick="return validar()" value="Receituário" class="btn btn-success mr-2" data-toggle="modal">';
															echo '<a href="relReceituario.php?transacao=' . $transacao . '" target="_blank" class="btn btn-success">Imprimir Receituário</a>';
															?>
                                                        </div>
                                                    </div>

                                                </div>
                                                <input id="senha-autorizado" name="senha-autorizado" type="hidden" />
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="linkAtestados" role="tabpanel"
                                            aria-labelledby="linkOpt-tab" aria-expanded="false">
                                            <div class="col-12 d-flex">
                                                <div class="col-6 col"><br>
                                                    <div class="col-12 text-center"><br>
                                                        <h4 class="form-section-center"><i
                                                                class="fas fa-hospital-alt"></i>Historico de Atestados
                                                        </h4>
                                                        <hr style="margin: auto;width: 260px">
                                                    </div>

                                                    <div class="col-sm-12 scroll"><br>

                                                        <table class="table table-hover table-striped width-full">
                                                            <thead>
                                                                <tr>
                                                                    <th>Data</th>
                                                                    <th>Hora</th>
                                                                    <th>Dias</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php
																include 'conexao.php';
																$stmt = 'select distinct partir_dia, qtd_dias, max(hora_atendimento) hora from atestados where pessoa_id =' . $prontuario . ' group by 1,2 order by 1 desc';
																$sth = pg_query($stmt) or die($stmt);
																//echo $stmt;
																while ($row = pg_fetch_object($sth)) {
																	echo '<tr>';
																	echo '<td>' . inverteData(substr($row->partir_dia, 0, 10)) . '</td>';
																	echo "<td>$row->hora</td>";
																	echo "<td>$row->qtd_dias</td>";
																	echo '</tr>';
																}
																?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                    </div>
                </div> <!-- FIM DOS TABS -->

                <div class="col-sm-12"><br>
                    <div class="col-12 text-center"><br>
                        <h4 class="form-section-center"><i class="fas fa-share"></i> Destino/Diagnóstico</h4>
                        <hr style="margin: auto;width: 260px">
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label class="control-label">Destino dado ao Paciente</label>
                                <?php if ($destino == '19' and $med == 1) { ?>
                                <label class="control-label">guardando os resultados de exames</label>
                                <textarea name="evolucao" class="form-control" style="height: 404px"
                                    id="evolucao"></textarea> </br>
                                <?php } ?>
                                <select class="form-control" name="destino" id="destino" <?php if ($destino != '19' or $med != 1) {
																	echo $disable;
																} ?>>
                                    <option value=""></option>;
                                    <option value="01" <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '01') {
																	echo 'selected';
																} ?>>
                                        ALTA
                                    </option>;
                                    <option value="07" <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '07') {
																	echo 'selected';
																} ?>>
                                        EM
                                        OBSERVAÇÃO / MEDICAÇÃO
                                    </option>;
                                    <!-- <option value="19" <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '19') {
																	echo 'selected';
																} ?>>EXAMES
                                    LABORATORIAIS</option>; -->
                                    <option value="10" <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '10') {
																	echo 'selected';
																} ?>>
                                        EXAMES /
                                        REAVALIACAO
                                    </option>;
                                    <option value="03" <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '03') {
																	echo 'selected';
																} ?>>
                                        PERMANÊNCIA.
                                    </option>;
                                    <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '20') { ?>
                                    <option value="20" selected>ALTA VIA SISTEMA
                                    </option>;
                                    <?php } ?>
                                    <option value="09" <?php if ($destino == '09') {
																	echo 'selected';
																} ?>>NAO
                                        RESPONDEU CHAMADO
                                    </option>;
                                </select>
                            </div>
                        </div>
                        <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) <> '07' and str_pad($destino, 2, '0', STR_PAD_LEFT) <> '10' and str_pad($destino, 2, '0', STR_PAD_LEFT) <> '03' and str_pad($destino, 2, '0', STR_PAD_LEFT) <> '00' and str_pad($destino, 2, '0', STR_PAD_LEFT) <> '09') { ?>
                        <div class="col-md-2">
                            <button type="button" data-target="#modalFimEvolucao" data-toggle="modal"
                                class="btn btn-raised btn-danger square btn-min-width mr-1 mt-4">Extornar Alta</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>


                <div class="row">

                    <div class="col-3 ml-3">
                        <label class="control-label">CID</label>
                        <input type="text" name="CID" id="CID" class="form-control"
                            value="<?php echo $CID; ?>"
                            onkeyup="maiuscula(this),copiarCid(this)" maxlength='5' <?php echo $rdonly ?>>
                    </div>
                    <div class="col-8">
                        <label class="control-label">Diagnóstico Principal</label>
                        <input type="text" name="diag_pri" id="diag_pri" onkeyup="retornaCid(this)" class="form-control"
                            value="<?php echo $diag_pri; ?>" <?php echo $rdonly ?>>

                        <!-- Está parte do codigo é referente a busca do CID -->

                        <style>
                            table #cidTable {
                                border-collapse: collapse;
                                width: 100%;
                            }

                            #cidTable th,
                            #cidTable td {
                                text-align: left;
                                padding: 8px;
                            }

                            #cidTable tr:nth-child(even) {
                                background-color: #f0f0f0;
                            }

                            #lista_diagnostico {
                                height: 150px;
                                overflow: scroll;
                                display: none;
                                overflow-x: hidden;

                            }

                            #cidTable a {
                                text-decoration: none;
                            }

                            .linha {
                                padding: 10px;
                                border-top: 1px solid #999999;
                            }
                        </style>
                        <div id="lista_diagnostico">
                            <table id="cidTable" class="table table-hover table-striped width-full">

                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" align="center"><br><br>
                    <div class="form-group">
                        <?php if (str_pad($destino, 2, '0', STR_PAD_LEFT) == '07' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '10' or str_pad($destino, 2, '0', STR_PAD_LEFT) == '03') { ?>
                        <a href="evolucao_atendimento.php?id=<?= $transacao ?>"
                            target="_blank" name="faa" class="btn btn-primary" onclick="evoluir()">Evoluir</a>
                        <?php } elseif ($destino == '' or $destino == '09') { ?>
                        <input type='button' id="gravar" name='gravar' class="btn btn-primary" value='Gravar'
                            onclick="g()">
                        <?php } ?>
                        <?php if ($origem == 12) { ?>
                        <button type="button" id="brelatorio_pmmg" class="btn btn-success" data-toggle="modal"
                            data-target="#relatorio_pmmg">Relatorio PMMG</button>
                        <?php } ?>
                        <input type='button' id="atestado" href="#"
                            data-id="<?= $_GET['id'] ?>"
                            data-target="#exampleTabs" onclick="return validar()" value='Atestados'
                            class="btn btn-warning" data-toggle="modal">
                        <button type="button" id="breferencia_contra" class="btn btn-success" data-toggle="modal"
                            data-target="#referencia_contra">Referencia/Contra Referencia</button>


                        <button type="button" id="receituario" class="btn btn-success" href="#"
                            data-id="<?= $_GET['id']; ?>"
                            data-toggle="modal" data-target="#ExemploModalCentralizado" value='Receituário'>
                            Solicitação de Internação
                        </button>

                        <input type="hidden"
                            value="<?= $_GET['id'] ?>">
                        <input type='button' id="declaracao_comparecimento" data-toggle="modal"
                            data-target="#comparecimento" value='Declaração de Comparecimento' class="btn btn-warning">

                        <a href="relFAA.php?id=<?= $_GET['id'] ?>"
                            target="_blank" name="faa" class="btn btn-primary">FAA / Imprimir</a>
                        <a href="formapacant.php?paciente=<?php echo $paciente_id; ?>"
                            target="_blank" name="faa" class="btn btn-primary">Solicitar APAC</a>
                        <input type='hidden' readOnly class="form-control" name="origem" id="origem"
                            value='<?php echo $origem; ?>'>
                        <!--<input type='submit' name='imprimir'  class="btn btn-primary" value='Imprimir'>-->
                        <!--<input type='submit' name='xcancelar' class="btn btn-danger"  value='Cancelar'>-->

                    </div>
                </div>
            </div> <!-- FINALIZANDO TABS -->

        </div>

    </div>

    </div>

    </div>
    </div>


    <!-- <?php include 'footer.php'; ?> -->
    <!-- </div> -->

    <!-- <script src="app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script> -->
    <script src="app-assets/vendors/js/core/popper.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/prism.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/jquery.matchHeight-min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/screenfull.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/pace/pace.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/chartist.min.js" type="text/javascript"></script>
    <script src="app-assets/js/app-sidebar.js" type="text/javascript"></script>
    <script src="app-assets/js/notification-sidebar.js" type="text/javascript"></script>
    <script src="app-assets/js/customizer.js" type="text/javascript"></script>
    <script src="app-assets/js/dashboard1.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
    <script src="app-assets/js/scripts.js" type="text/javascript"></script>
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.jss"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <script>
    </script>
    <script>
        var slider = document.getElementById("dor");
        var output = document.getElementById("valor");
        output.innerHTML = slider.value;

        slider.oninput = function() {
            output.innerHTML = this.value;
        }

        var contador = 2;
        $("#novo_receituario").click(function(event) {
            $('#bloco_receituario').prepend('<div id="item-' + contador +
                '"><div class="row"><div class="col-4"><div class="form-group"><label class="control-label">Medicamento</label><input id="medicamento-' +
                contador +
                '" class="form-control" value="" maxlength="100" onkeyup="maiuscula(this)"></div></div><div class="col-2"><div class="form-group"><label class="control-label">Quantidade</label><input id="quantidade-' +
                contador +
                '" maxlength="50" class="form-control" value="" onkeyup="maiuscula(this)"></div></div><div class="col-5"><div class="form-group"><label class="control-label">Modo de usar</label><textarea id="usar-' +
                contador +
                '" class="form-control" value="" onkeyup="maiuscula(this)"></textarea></div></div><div class="col-1"><div class="form-group"><button onclick="apagar_item_receituario(this)" value="' +
                contador +
                '" class="btn mr-1 mb-1 btn-danger btn-sm" style="margin-top: 28px">X</button></div></div></div></div>'
            );
            $("#salvar_receituario").attr("value", contador);
            contador++;
        });

        function copiarCid(cid) {
            $("#cidAtestado").val(cid.value);
        }

        function copiarCidpermanencia(cid) {
            $("#cidAtestado").val(cid.value);
        }

        function marcardesmarcar() {
            $('.marcar').each(function() {
                if (this.checked) $(this).attr("checked", false);
                else $(this).prop("checked", true);
            });
        }

        function apagar_item_receituario(indice) {
            $("#item-" + indice.value).remove();
        }

        function salvar_prescricao(campo) {
            var i = 1;
            var x = 1;
            var total = campo.value;
            var transacao = $("#transacao").val();
            total++;

            if ($("#medicamento-1").val() == '') {
                swal("Informe o medicamento/cuidado", "", "warning")
            } else {
                while (i <= total) {
                    var medicamento = $("#medicamento-" + i).val();
                    var quantidade = $("#quantidade-" + i).val();
                    var usar = $("#usar-" + i).val();
                    if (medicamento != undefined && (medicamento != '')) {
                        $.post('salvar_receituario.php', {
                            medicamento: medicamento,
                            quantidade: quantidade,
                            usar: usar,
                            transacao: transacao
                        }, function(data, status) {
                            $('#receit').html(data);
                        })
                    }
                    i++;
                }

                while (x <= total) {
                    var medicamento = $("#medicamento-" + x).val('');
                    var quantidade = $("#quantidade-" + x).val('');
                    var usar = $("#usar-" + x).val('');
                    x++;
                }
                $("#modalSolicitaReceituario").modal('hide');
                window.open("relReceituario.php?transacao=" + transacao, '_blank');

            }

        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        function g() {
            var destino = document.getElementById("destino").value;
            if (destino) {

                swal({
                    title: "Solicite a assinatura do paciente na Ficha de Atendimento",
                    text: "",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: '#428bca',
                    confirmButtonText: 'OK',
                    closeOnConfirm: false,
                    closeOnCancel: false
                }).then((isConfirm) => {

                    if (isConfirm) {


                        if (document.getElementById('evolucao')) {
                            if (document.getElementById('evolucao').value == '') {
                                document.getElementById('evolucao').focus();
                                alert("Dados incompletos!", "Informe a evolucao", "error");
                            } else {
                                $("#pedido").submit();
                            }
                        } else {
                            $("#pedido").submit();
                        }

                    }
                });
            } else {
                alert("Destino deve ser informado!!!");
            }

        }

        function evoluir() {
            $("#pedido").submit();
        }



        //////////////////////////INICIO LOGICA MODULO DE PRESCRIÇÃO//////////////////////////
        function solicitacaoprescricao(
            valor) { //ABRE O MODAL ONDE É LISTADO OS MEDICAMENTOS QUE ESTÁ GRAVADO NA SESSION
            $('#modaSolictalPrecricao').modal('toggle');
            $.get('listaprescricao.php?atendimento=<?php echo $_GET['id'] ?>',
                function(dataReturn) {
                    $('#conteudoPrescricaoModal').html(dataReturn);
                });
        }

        function modal_prescricao(
            valor
        ) { //FUNÇÃO PARA ABRIR O MODAL ONDE É REALIZADO A ESCOLHA DO TIPO DE PRESCRIÇÃO: DIETA, HIDRATAÇÃO, MEDICAMENTOS E CUIDADOS
            $('#modalPrecricao').modal('toggle');
            $("#novo_prescricao").prop('disabled', true);
            $('#conteudoModal').html('');
            $('#conteudoModal').html('<h3 align="center">Aguarde...</h3>');
            $.get('prescricao.php', function(dataReturn,
                status) { //PRESCRICAO.PHP: ARQUIVO ONDE FICA A ESTRUTURA, CONTEUDO, DO MODAL
                $('#conteudoModal').html(dataReturn);
                if (status == 'success') {
                    $("#novo_prescricao").prop('disabled', false);
                } else {
                    $("#novo_prescricao").prop('disabled', true);
                }
            });
        }

        $("#novo_prescricao").click(function(
            event
        ) { //FUNÇÃO PARA SALVAR OS MEDICAMENTOS NA SESSION E POSTERIORMENTE LISTAR O QUE FOI SOLICITADO
            var atendimento = $('#transacao').val();
            var dosagem = $('#dosagem').val();
            var aprazamento = $('#aprazamento').val();
            var medicamento = $('#medicamento').val();
            var medico = $('#usuario_transacao').val();
            var via = $('#via').val();
            var descricao = $('#medicamento option:selected').text();
            var campoCuidados = $('#campoCuidados').val();
            var campoDieta = $('#campoDieta').val();
            var tipo_prescricao = $('#tipo_prescricao').val();

            var componente1 = $('#componente1').val();
            var componente2 = $('#componente2').val();
            var componente3 = $('#componente3').val();
            var hidratacao_text = $('#hidratacao_text').val();
            var descricao_hd = $('#descricao_hd').val();

            var url = '';

            if (tipo_prescricao == '5') {

                url = 'salvarprescricaosistema.php?atendimento=' + atendimento + '&dosagem=' +
                    dosagem + '&aprazamento=' + aprazamento + '&medicamento=' + medicamento + '&medico=' +
                    medico + '&descricao=' + descricao + '&via=' + via + '&tipo_prescricao=' + tipo_prescricao;

            } else if (tipo_prescricao == '10') {

                url = 'salvarprescricaosistema.php?atendimento=' + atendimento + '&cuidados=' +
                    campoCuidados + '&medico=+' + medico + '&tipo_prescricao=' + tipo_prescricao;

            } else if (tipo_prescricao == '1') {

                url = 'salvarprescricaosistema.php?atendimento=' + atendimento + '&dieta=' +
                    campoDieta + '&medico=' + medico + '&tipo_prescricao=' + tipo_prescricao;

            } else if (tipo_prescricao == '3') {


                url = 'salvarprescricaosistema.php?atendimento=' + atendimento + '&descricao_hd=' +
                    descricao_hd + '&componente1=' + componente1 + '&componente2=' + componente2 +
                    '&componente3=' +
                    componente3 + '&hidratacao_text=' + hidratacao_text + '&medico=' + medico +
                    '&tipo_prescricao=' + tipo_prescricao;

            } //SALVAPRESCRICAOSISTEMA.PHP: SALVA OS MEDICAMENTOS NA SESSION		

            $.get(url, function(dataReturn) {
                $.get('listaprescricao.php?atendimento=<?php echo $_GET['id'] ?>',
                    function(Return) {
                        $('#conteudoPrescricaoModal').html(Return);
                    });
            }); //LISTAPRESCRICAO.PHP: LISTA OS MEDICAMENTOS SALVOS NA SESSION

            event.preventDefault();

            var modal = $(this).closest('#modalPrecricao');
            $(modal).modal('hide');
        });


        $("#confirmar_prescricao").click(function(
            event) { //FUNÇÃO É CHAMANDA QUANDO O BOTAO CONFIRMAR PRESCRICAO É CLICADO

            var total_campos = $("#tamanhoArray").val();
            if (total_campos == 0) {
                swal("Lista de Prescrição vazia!", "", "warning");
            } else {

                var total_campos = $("#tamanhoArray").val();
                var i = 1;

                var via = new Array();
                var aprazamento = new Array();
                var medicamento = new Array();
                var dosagem = new Array();
                var cod_medicamento = new Array();

                var hidratacao_text = new Array();
                var componente1 = new Array();
                var componente2 = new Array();
                var componente3 = new Array();
                var descricao_hd = new Array();

                var cuidados = new Array();
                var tipo = new Array();
                var nova_sequencia = '';
                var data =
                    '<?php echo date('Y-m-d'); ?>';
                var hora =
                    '<?php echo date('H:i'); ?>';


                while (i <= total_campos) {

                    tipo[i] = $('#tipo_presc' + i + '').val();

                    if ($('#cuidados' + i + '').val() != undefined) {

                        cuidados[i] = $('#cuidados' + i + '').val();

                        url = 'gerarnovaprescricao_clinico.php?cuidados=' + cuidados[i] + '&atendimento=' +
                            <?php echo $_GET['id'] ?>
                            +'&flag=' + i + '&nova_sequencia=' + nova_sequencia +
                            '&data=' + data + '&hora=' + hora + '&tipo_prescricao=' + tipo[i];

                    } else if ($('#hidratacao_text' + i + '').val() != undefined) {



                        hidratacao_text[i] = $('#hidratacao_text' + i + '').val();
                        componente1[i] = $('#componente1' + i + '').val();
                        componente2[i] = $('#componente2' + i + '').val();
                        componente3[i] = $('#componente3' + i + '').val();
                        descricao_hd[i] = $('#descricao_hd' + i + '').val();
                        url = 'gerarnovaprescricao_clinico.php?hidratacao_text=' + hidratacao_text[i] +
                            '&componente1=' + componente1[i] + '&componente2=' + componente2[i] +
                            '&componente3=' +
                            componente3[i] + '&descricao_hd=' + descricao_hd[i] + '&atendimento=' +
                            <?php echo $_GET['id'] ?>
                            +'&flag=' + i + '&nova_sequencia=' + nova_sequencia +
                            '&data=' + data + '&hora=' + hora + '&tipo_prescricao=' + tipo[i];

                    } else {
                        cod_medicamento[i] = $('#medicamento' + i + '').val();
                        medicamento[i] = $('#descricao' + i + '').val();


                        if ($('#dosagem' + i + '').val() != undefined) {
                            dosagem[i] = $('#dosagem' + i + '').val();
                        } else {
                            dosagem[i] = '';
                        }

                        if ($('#via' + i + '').val() != undefined) {
                            via[i] = $('#via' + i + '').val();
                        } else {
                            via[i] = '';
                        }

                        if ($('#aprazamento' + i + '').val() != undefined) {
                            aprazamento[i] = $('#aprazamento' + i + '').val();
                        } else {
                            aprazamento[i] = '';
                        }

                        url = 'gerarnovaprescricao_clinico.php?dosagem=' + dosagem[i] + '&medicamento=' +
                            medicamento[i] + '&via=' + via[i] + '&aprazamento=' + aprazamento[i] +
                            '&atendimento=' +
                            <?php echo $_GET['id'] ?>
                            +'&flag=' + i + '&cod_medicamento=' + cod_medicamento[i] +
                            '&nova_sequencia=' + nova_sequencia + '&data=' + data + '&hora=' + hora +
                            '&tipo_prescricao=' + tipo[i];
                    } //GERANOVAPRESCRICAO_CLINICI.PHP: SALVA NO BANCO DE DADOS O QUE ESTA SALVO NA SESSION

                    $.get(url, function(dataReturn) {
                        $('#conteudoPrescricao').html(dataReturn);

                    });
                    i++;
                }

                //LISTA_PRESCRICAO_CLINICO.PHP: RETORNA PARA DENTRO DA DIV COM ID=modaSolictalPrecricao OS MEDICAMENTOS SALVOS NO BANCO
                urls =
                    'lista_prescricao_clinico.php?atendimento=<?php echo $_GET['id'] ?>';

                $('#modaSolictalPrecricao').modal('hide');
            }

        });

        function remover_prescricao(
            indice
        ) { //FUNCÃO É CHAMADA CLICA EM DELETAR UM MEDICAMENTO QUE ESTA LA LISTAGEM DE MEDICAMENTOS, ISTO PRESENTE NA SESSION
            $.get('exlcuirIndiceArray.php?indice=' + indice);

            $.get('listaprescricao.php?atendimento=<?php echo $_GET['id'] ?>&flag=1',
                function(Return) {
                    $('#conteudoPrescricaoModal').html(Return);
                });
        }


        $("#prescricao").click(
            function() { //FUNCÃO É CHAMADA QUANDO É SOLICTADO A GERAÇÃO DO PDF COM OS MEDICAMENTOS SALVOS NO BANCO

                var prescricoes = '';
                $("input[type=checkbox][name='cb_prescricao[]']:checked").each(function() {
                    prescricoes = prescricoes + ',' + $(this).val();
                });

                prescricoes = prescricoes.substr(1)

                if ($("[name='cb_prescricao[]']").is(":checked") ==
                    true) { //VALIDA SE FOI SELECIONADO ALGUM MEDICAMENTO, CHECKBOX.
                    window.open(
                        'relprescricao.php?id=<?php echo $_GET['id'] ?>&medicamentos=' +
                        prescricoes, '_blank');
                } else {
                    swal("Marque a caixa de selecao ao lado dos medicamentos que deseja imprimir", "", "warning")
                }
            });



        $("#prescricaoModal").click(
            function() { //FUNÇAO RETORNA PARA O MODAL COM ID=conteudoModal, A ESTRUTURA COM OS TIPOS DE PRESCRICAO

                $.get('prescricao.php', function(dataReturn) {
                    $('#conteudoModal').html(dataReturn);
                });

                event.preventDefault();
            });
        //////////////////////////FIM LOGICA MODULO DE PRESCRIÇÃO//////////////////////////


        function preencheCid(cid, descricao) {
            if (cid == 'B340') {
                $("#convenio").modal({
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                });
            }
            $("#CID").val(cid);
            $("#cidAtestado").val(cid);
            $("#diag_pri").val(descricao);
            $('#cidTable').empty();
            $("#lista_diagnostico").slideUp(100);
        }


        function retornaCid(valor) { //A fun褯 顰ara retorno do CID 10
            var cid = valor.value;
            $("#lista_diagnostico").css("display", "block");
            $.get('retorno_cid10.php?cid=' + cid, function(dataReturn) {
                $('#cidTable').html(dataReturn);
            });

            //Ocultar a caixa de sugest䯠do CID
            if (cid == "") {
                $("#lista_diagnostico").slideUp(100);
            }

        }



        $("#cancelarModal").click(function() {
            var modal = $(this).closest('#modalPrecricao');
            $(modal).modal('hide');
        });


        function formata(src, mask) {
            var i = src.value.length;
            var saida = mask.substring(0, 1);
            var texto = mask.substring(i)
            if (texto.substring(0, 1) != saida) {
                src.value += texto.substring(0, 1);
            }
        }



        $("#novo_exame").click(function() {
            var data_atendimento =
                '<?php echo date('d/m/Y'); ?>';
            var atendimento = $('#atendimento').val();
            var prioridade = $('#prioridade').val();
            var profissional = $('#profissional').val();
            var prontuario = $('#paciente').val();
            var procedimento = $('#procedimento').val();
            var origem = $('#origem').val();
            var id = $('#id').val();
            var retorno = 'N';

            if (procedimento != "" && procedimento != null) {
                $("#loading-hover").css("z-index", "1"); // Alterna loading ao clicar em "solicitar"
                jQuery.get('solicitapedido.php?paciente_id=' + id + '&prioridade=' + prioridade +
                    '&data_atendimento=' + data_atendimento + '&atendimento=' + atendimento +
                    '&profissional=' + profissional + '&prontuario=' + prontuario + '&procedimento=' +
                    procedimento + '&origem=' + origem,
                    function(dataReturn, status) {
                        $('#exames_atendimentos').html(dataReturn);
                        if (status == "success") {
                            $("#loading-hover").css("z-index", "-1"); // Alterna loading apos processamento
                            $('#procedimento').val('').trigger('chosen:updated');
                            swal("Sucesso,", "o procedimento foi solicitado.", "success");
                        }
                    });
            } else {
                swal("Selecione um procedimento.", "", "warning");
            }

            event.preventDefault();
        });

        $(document).ready(function() {
            $("#soclab").on('click', function(e) {
                e.preventDefault(); //cancel default action

                //Recuperate href value
                var data_atendimento =
                    '<?php echo date('d/m/Y'); ?>';
                var atendimento = $('#atendimento').val();
                var prioridade = $('#prioridade').val();
                var profissional = $('#profissional').val();
                var prontuario = $('#paciente').val();
                var procedimento = $('#procedimento_laboratorio').val();
                var origem = $('#origem').val();
                var id = $('#id').val();
                var retorno = 'N';

                //pop up
                swal({

                        title: "ATENÇÃO!!!",
                        text: "Deseja pedir somente esses exames? Se não por favor acrescente os demais desejaveis.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Sim, Desejo finalizar!",
                        closeOnConfirm: false,


                        // title: "Are you sure ??",
                        // text: message,
                        // icon: "warning",
                        buttons: true
                        // dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.get('solicitapedidoxj.php?paciente_id=' + id + '&prioridade=' +
                                prioridade +
                                '&data_atendimento=' + data_atendimento + '&atendimento=' +
                                atendimento +
                                '&profissional=' + profissional + '&prontuario=' + prontuario +
                                '&procedimento=' +
                                procedimento + '&origem=' + origem,
                                function(dataReturn, status) {
                                    $('#exames_laboratorio').html(dataReturn);
                                });
                            swal("Finalizado!", "Exames solicitados ao laboratorio.", "success");
                            $('#modalLaboratorio').modal('hide');
                            window.location.href = href;
                        } else {
                            swal("Your imaginary file is safe!");
                        }
                    });
            });
        });




        /*sweetAlert({
        title: "ATENÇÃO!!!",
				text: "Deseja pedir somente esses exames? Se não por favor acrescente os demais desejaveis.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
        })
            .then((willDelete) => {
            if (willDelete) {
							if(procedimento != "" && procedimento != null){
			$("#loading-hover").css("z-index", "1"); // Alterna loading ao clicar em "solicitar"
			$.get('solicitapedido.php?paciente_id='+id+'&prioridade='+prioridade+'&data_atendimento='+data_atendimento+'&atendimento='+atendimento+'&profissional='+profissional+'&prontuario='+prontuario+'&procedimento='+procedimento+'&origem='+origem, function(dataReturn, status) {
				$('#exames_atendimentos').html(dataReturn);	
				if(status == "success") {
					$("#loading-hover").css("z-index", "-1"); // Alterna loading apos processamento
					$('#procedimento').val('').trigger('chosen:updated');
					swal("Sucesso,", "o procedimento foi solicitado.", "success");
				}
			});
		} else {
			swal("Selecione um procedimento.", "", "warning");
		}
  
		event.preventDefault();
                swal("Exames solicitados com sucesso", {
                icon: "success",
                });
            } else {
                swal("Escolha os demais exames");
            }
        });*/



        $("#salvar").click(function() {
            var prontuario = $('#paciente').val();
            var data_atendimento = $('#data_atendimento').val();
            var hora_entrada = $('#hora_entrada').val();
            var hora_saida = $('#hora_saida').val();
            var finalidade = $('#finalidade').val();
            var descricao = $('#descricao').val();
            var atendimento = $('#atendimento').val();
            var profissional = $('#profissional').val();

            $.get('salvaratestado.php?prontuario=' + prontuario + '&data_atendimento=' + data_atendimento +
                '&hora_entrada=' + hora_entrada + '&hora_saida=' + hora_saida + '&finalidade=' +
                finalidade +
                '&descricao=' + descricao + '&atendimento=' + atendimento + '&profissional=' + profissional,
                function(dataReturn) {
                    $('#modalbody').html(dataReturn);
                });
            event.preventDefault();

            $("#pedido").submit();

        });


        // $("#procedimento").chosen({
        //     placeholder_text_single: "Selecione...",
        //     search_contains: true
        // });
        // $(document).ready(function() {
        //     $('#procedimento').chosen.change(function() {
        //         var cbo = document.getElementById("procedimento");
        //         var codigotabela = cbo.options[cbo.selectedIndex].text;
        //         document.getElementById("codigo").value = codigotabela.substring(0, 8);
        //         document.getElementById("qtde").value = '01';
        //     });
        // });

        // $("#medicamento").chosen({
        //     placeholder_text_single: "Selecione...",
        //     search_contains: true
        // });


        $("#CID").blur(function() {
            var codcid = $('#CID').val();
            var url = 'ajax_buscar_cid.php?cid=' + codcid;
            $.get(url, function(dataReturn) {
                $('#diag_pri').val(dataReturn);
                console.log(dataReturn);
                if (codcid == 'B340') {
                    $("#convenio").modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            });
        });

        function buscaCidpermanencia(a) {
            var url = 'ajax_buscar_cid_permanencia.php?cid=' + a.value;
            $.get(url, function(dataReturn) {
                $('#diag_pri_permanencia').val(dataReturn);
            });
        }

        // Autenticacao da pendÃªncia do paciente
        $("#autoriza-pendencia-bt").click(function() {

            var oUsuarioAutentica = $("#usuario-autentica").val();
            var oSenhaAutentica = $("#senha-autentica").val();

            $.post("autentica-usuario.php", {
                    usuario: oUsuarioAutentica,
                    senha: oSenhaAutentica
                }, function(data) {

                }).done(function(data) {

                    if (data == "1") {

                        var oNome = $("#nome");
                        var sHtmlI =
                            '<i title="PendÃªncia autorizada!" class="icon fa-check has-warning" aria-hidden="true" style="margin-left: 1em; cursor:pointer; font-size: 1.2em;"></i>';

                        sweetAlert("UsuÃ¡rio autenticado com sucesso!", "", "success");

                        oNome.closest("div").find("label").after(sHtmlI)

                        $("#pendencia-modal-i").off("click");

                        $("#usuario-autorizado").val(oUsuarioAutentica);
                        $("#senha-autorizado").val(oSenhaAutentica);

                    } else if (data == "0") {
                        sweetAlert("Falha na autenticacao!", "Tente novamente.", "error");
                    }
                })
                .fail(function() {
                    sweetAlert("Falha na autenticacao!", "Tente novamente.", "error");
                });
        });

        // Reseta os campos do paciente
        function resetaDadosPac() {

            $("#dados-paciente-div").find(":text, :hidden").prop("readonly", false);
            $("#dados-paciente-div").find(":text, :hidden").val("");
            $("#nome, #cpf").closest("div").removeClass("has-warning");
            $("#nome").closest("div").find("i").remove();
            $("#usuario-autorizado").val("");
            $("#senha-autorizado").val("");
        }

        //$("#dados-paciente-div").find(":text, :hidden").prop("readonly", true);

        // Desabilida a tecla enter em autorizar pendÃªncia (bug)
        $("#usuario-autentica, #senha-autentica").keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });

        function atualizat(a) {
            var nome = document.getElementById("nome").innerhtml;
            var cns = document.getElementById("cns").innerhtml;
            var idade = document.getElementById("idade").value;
            var prontuario = document.getElementById("paciente").value;
            var url = 'ajax_tabela_atendimento.php?transacao=' + a + '&nome=' + nome + '&cns=' + cns + '&idade=' +
                idade +
                '&prontuario=' + prontuario;
            $.get(url, function(dataReturn) {
                $('#conteudoPrescricao').html(dataReturn);
            });
        }

        function modal_obs(id) {
            var modal = document.getElementById('obs_modal').value;
            var cid = document.getElementById('CID_permanencia').value;
            var tipo_leito = document.getElementById('tipo_leito').value;
            var pa_sis = $("#pa_sis").val();
            var pa_dist = $("#pa_dis").val();
            var temperatura = $("#temp").val();
            var dor = $("#dor").val();
            var oxigenio = $("#oxigenio").val();
            var frequencia_respiratoria = $("#frequencia_respiratoria").val();
            var pulso = $("#pulso").val();
            var glicose = $("#glicose").val();
            var ecg = $("#ecg").val();
            // modal = modal.replace(/(?:\r\n|\r|\n)/g, '/p');
            // modal = modal.replace('#', '');
            // if (pa_sis == '' || pa_dist == '' || temperatura == '' || dor == '' || oxigenio == '' || pulso == '' || glicose == '' || ecg == '') 
            if (pa_sis == '' || pa_dist == '') {
                alert("Por favor preencha os sinais vitais!!!");
            } else if (cid == '') {
                alert("Por favor preencha o CID referente a internação!!!");
            } else if (tipo_leito == '') {
                alert("Por favor preencha o leito referente a internação!!!");
            } else {
                $.get("salvar_obs.php", {
                        modal: modal,
                        cid: cid,
                        id: id,
                        tipo_leito: tipo_leito,
                        pa_sis: pa_sis,
                        pa_dist: pa_dist,
                        temperatura: temperatura,
                        dor: dor,
                        oxigenio: oxigenio,
                        frequencia_respiratoria: frequencia_respiratoria,
                        pulso: pulso,
                        glicose: glicose,
                        ecg: ecg
                    },

                    function(dataReturn) {
                        $('#teste').html(dataReturn);
                    })
            }
        }

        $("select").chosen({
            width: "100%"
        });

        function validar() {
            var nome = pedido.nome.value;
            var dt_nascimento = pedido.dt_nascimento.value;
            var endereco = pedido.endereco.value;
            var numero = pedido.end_num.value;
            var bairro = pedido.end_bairro.value;
            var cidade = pedido.end_cidade.value;
            var estado = pedido.end_uf.value;
            var cep = pedido.end_cep.value;
            var cpf = pedido.cpf.value;
            var dt_solicitacao = pedido.dt_nsolicitacao.value;
            var solicitante = pedido.solicitante.value;
            var nomesolicitante = pedido.nomesolicitante.value;
            var convenio = pedido.conveniox.value;
            var tipoconv = convenio.substring(0, 1)
            var carteirinha = pedido.carteirinha.value;

            if (nome == "") {
                pedido.nome.focus();
                sweetAlert("Dados incompletos!", "Insira um Nome", "error");
                return false;
            }

            if (dt_nascimento == "") {
                pedido.dt_nascimento.focus();
                sweetAlert("Dados incompletos!", "Insira uma Data de Nascimento", "error");
                return false;
            }
            if (endereco == "") {
                pedido.endereco.focus();
                sweetAlert("Dados incompletos!", "Insira um Endereço", "error");
                return false;
            }
            if (numero == "") {
                pedido.end_num.focus();
                sweetAlert("Dados incompletos!", "Insira o número no endereço", "error");
                return false;
            }
            if (bairro == "") {
                pedido.end_bairro.focus();
                sweetAlert("Dados incompletos!", "Informe o bairro no endereço", "error");
                return false;
            }
            if (cidade == "") {
                pedido.end_cidade.focus();
                sweetAlert("Dados incompletos!", "Informe a Cidade no endereço", "error");
                return false;
            }
            if (estado == "") {
                pedido.end_uf.focus();
                sweetAlert("Dados incompletos!", "Informe a UF no endereço", "error");
                return false;
            }


            if (solicitante == "") {
                pedido.solicitante.focus();
                sweetAlert("Dados incompletos!", "Informe o Solicitante", "error");
                return false;
            }
            if (nomesolicitante == "") {
                pedido.nomesolicitante.focus();
                sweetAlert("Dados incompletos!", "Informe o Nome do Solicitante", "error");
                return false;
            }
            if (convenio == "") {
                pedido.carteirinha.focus();
                sweetAlert("Dados incompletos!", "Informe o Convênio da Solicitação", "error");
                return false;
            }
            if (tipoconv == 'C' && carteirinha == "") {
                pedido.carteirinha.focus();
                sweetAlert("Dados incompletos!", "Informe a Carteirinha do Conveniado", "error");
                return false;
            }
        };

        function laboratorio() {
            var data = document.getElementById("data").value;
            if (data) {
                window.open(
                    "<?= 'http://' . IP_CONFIG . '/desenvolvimento/laboratorio/gera_resultado.php?data='; ?>" +
                    data +
                    "<?= "&pessoa_id=$prontuario&origem=" . PORIGEM_CONFIG; ?>"
                );
            } else {
                alert("Informe da data!!!");
            }
        }

        function limitarTextArea(campo) {
            var string = campo.value;
            var novastring = "";
            var linhas = new Array();
            var trocarLinha = false;
            linhas = string.split("\n");
            var contador = linhas.length;

            for (x in linhas) {
                if (linhas[x].length > campo.cols - 2) {
                    linhas[x] = linhas[x].substring(0, campo.cols);
                    trocarLinha = true;
                }
                if (x < 25) {
                    novastring += linhas[x] + "\n";
                }
            }

            if (contador > 25 || trocarLinha) {
                campo.value = novastring.substring(0, novastring.length - 1);
            }
            return contador <= 25;
        }

        function limitLines(obj, limit) {
            var values = obj.value.replace(/\r\n/g, "\n").split("\n")
            if (values.length > limit) {
                obj.value = values.slice(0, limit).join("\n")
            }
        }
        $("#queixa_paciente_pmmg").bind('paste', function(e) {
            limitLines(this, 3);
        });
        $("#diagnostico_medico_pmmg").bind('paste', function(e) {
            limitLines(this, 2);
        });
        $("#orientacao_paciente_pmmg").bind('paste', function(e) {
            limitLines(this, 3);
        });
        // $("#justificativa_referencia").bind('paste', function(e) {
        //     limitLines(this, 15);
        // });
        // $("#diagnostico_referencia").bind('paste', function(e) {
        //     limitLines(this, 2);
        // });
        // $("#resultado_referencia").bind('paste', function(e) {
        //     limitLines(this, 5);
        // });

        function imprimir_laboratorio() {
            var checkeds = new Array();
            $("input[name='cb_exame[]']:checked").each(function() {
                checkeds.push($(this).val());
            });
            if (checkeds) window.open('impexamelab.php?id=' + checkeds);
        }

        function retornaCidpermanencia(valor) { //A fun褯 顰ara retorno do CID 10
            var cid = valor.value;
            $("#lista_diagnostico_permanencia").css("display", "block");
            $.get('retorno_cid_permanencia.php?cid=' + cid, function(dataReturn) {
                $('#cidTablepermanencia').html(dataReturn);
            });

            //Ocultar a caixa de sugest䯠do CID
            if (cid == "") {
                $("#lista_diagnostico_permanencia").slideUp(100);
            }

        }

        function preencheCidpermanencia(cid, descricao) {
            $("#CID_permanencia").val(cid);
            $("#diag_pri_permanencia").val(descricao);
            $('#cidTablepermanencia').empty();
            $("#lista_diagnostico_permanencia").slideUp(100);
        }

        $(document).ready(function() {

            $('#exame_fisico').keydown(function(e) {

                var linhasAtuais = $(this).val().split("\n").length;

                if (e.keyCode == 13 && linhasAtuais >= 20) {
                    return false;
                }
            });
        });

        function salvar_convenio(a) {
            var radios = document.getElementsByName("pconvenio");

            if (radios[0].checked) {
                $.get('sconvenio.php?transacao=' + a + '&resposta=1', function(dataReturn) {});
                $("#convenio").modal('hide');
            } else if (radios[1].checked) {
                $.get('sconvenio.php?transacao=' + a + '&resposta=0', function(dataReturn) {});
                $("#convenio").modal('hide');
            } else {
                swal("Esta informação é obrigatoria", "", "warning")
            }
        }

        function edit_receituario(a, b) {
            $.get('editar_receituario.php?transacao=' + a + '&medicamento=' + b, function(dataReturn) {
                $('#modal').html(dataReturn);
                $("#modalEditaReceituario").modal({
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                });
            });


        }

        function salvar_edicao_prescricao() {
            var medicamento = $("#medicamentoedit").val();
            var quantidade = $("#quantidadeedit").val();
            var usar = $("#usaredit").val();
            var transacao = $("#transacao").val();
            if (medicamento != undefined && (medicamento != '')) {
                $.post('salvar_edicao_receituario.php', {
                    medicamento: medicamento,
                    quantidade: quantidade,
                    usar: usar,
                    transacao: transacao
                }, function(data, status) {
                    $('#receit').html(data);
                })
            } else {
                swal("Informe o medicamento", "", "warning");
            }

            $("#modalEditaReceituario").modal('hide');
            window.open("relReceituario.php?transacao=" + transacao, '_blank');
        }
    </script>

</body>








</html>