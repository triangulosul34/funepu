<?php
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
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
    else if ($cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
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
    $transacao         = $_GET['id'];
    $senha             = $_GET['senha'];
    $agendamento     = $_GET['ag'];
    $med = $_GET['med'];
    $texto = "";


    $data = date('Y-m-d');
    $hora = date('H:i');
    include('conexao.php');
    $stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
			values ('$usuario','FEZ ACESSO AO ATENDIMENTO','$transacao','$data','$hora')";
    $sthLogs = pg_query($stmtLogs) or die($stmtLogs);


    include('conexao.php');
    $bloqueiaAt = "select transacao,status, count(*) as total,nome
					from atendimentos a
					left join pessoas p on p.pessoa_id = a.paciente_id
						where med_atendimento = '$usuario' and transacao != $transacao and status = 'Em Atendimento' and dat_cad = '" . date('Y-m-d') . "'
					group by 1,2,4";
    $sthBlock = pg_query($bloqueiaAt) or die($bloqueiaAt);
    $valBlock = pg_fetch_object($sthBlock);
    if ($valBlock->total > 0 && isset($_GET['continue'])) {
        if (!isset($_GET['estadia'])) {
            echo "
			<script>
				alert('Finalize o atendimento de " . $valBlock->nome . " que esta em aberto. Você será redirecionado para o paciente em questão.');
				location.href='atendimentoclinico.php?id=" . $valBlock->transacao . "&continue=1';
			</script>";
        }
    } else {
        include('conexao.php');
        $validaAtendimento = "select a.status from atendimentos a where a.transacao=$transacao";
        $sthAt = pg_query($validaAtendimento) or die($validaAtendimento);
        $valAt = pg_fetch_object($sthAt);
        if ($valAt->status == 'Aguardando Atendimento' && $perfil == '03') {
            $stmt = "update atendimentos set status='Em Atendimento', med_atendimento='$usuario' WHERE transacao = $transacao";
            $sth = pg_query($stmt) or die($stmt);
        }
    }




    if ($transacao != "") {
        include('conexao.php');
        $stmt = "select a.transacao,a.hora_cad, a.cid_principal, a.destino_paciente, a.data_destino, a.queixa, a.exame_fisico, a.diagnostico_principal,a.prioridade,
		a.paciente_id, a.status, a.tipo, a.dat_cad as cadastro, a.obs_modal, c.nome, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco, a.oque_faz, a.com_oqfaz, 
		a.tempo_faz, a.como_faz, c.numero, c.complemento, c.bairro, c.num_carteira_convenio, c.cep, c.cpf, c.cidade, c.estado, a.observacao, k.origem,  
		x.peso, x.pressaodiastolica, x.pressaosistolica, x.queixa as relato, x.pulso, x.temperatura,x.discriminador, x.prioridade as atendprioridade, x.glicose, x.dor, a.coronavirus
		from atendimentos a 
		left join pessoas c on a.paciente_id=c.pessoa_id 
		left join tipo_origem k on cast(k.tipo_id as varchar)=a.tipo 
		left join classificacao x ON ltrim(x.atendimento_id, '0')= '$transacao' 
		where a.transacao=$transacao";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $data_transacao = substr($row->cadastro, 0, 10);
        $hora_transacao = $row->hora_cad;
        $prontuario = $row->paciente_id;

        $destino     = $row->destino_paciente;
        $destinox     = $row->destino_paciente;
        $status = $row->status;

        if (($status == "Atendimento Finalizado") && ($destino != "07" && $destino != "10")
        ) {
            $liberacao = "readonly";
            $liberacaox = "disabled";
        } else {
            $liberacao = "";
            $liberacaox = "";
        }

        $data_destino = $row->data_destino;
        $prioridade = $row->prioridade;
        $sexo = $row->sexo;
        $nome = $row->nome;
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
        $dor     = $row->dor;
        $glicose = $row->glicose;
        $pressaodiastolica = $row->pressaodiastolica;
        $pressaosistolica = $row->pressaosistolica;
        $relato = $row->relato;
        $pulso = $row->pulso;
        $temperatura = $row->temperatura;
        $cns    = $row->num_carteira_convenio;
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
        $observacao  = $row->relato . PHP_EOL;
        $observacao = $observacao . 'PA DIAST:' . $pressaodiastolica . PHP_EOL . 'PA SIST.:' . $pressaosistolica . PHP_EOL;
        $observacao = $observacao . 'PESO:' . $peso . PHP_EOL . 'Temperatura:' . $temperatura . PHP_EOL;
        $observacao = $observacao . 'GLICEMIA:' . $glicose . PHP_EOL . 'Dor:' . $dor . PHP_EOL;

        $oque_faz      = $row->oque_faz;
        $com_oqfaz     = $row->com_oqfaz;
        $tempo_faz     = $row->tempo_faz;
        $como_faz      = $row->como_faz;
        $enfermaria = $row->enfermaria;
        $leito         = $row->leito;
        $imagem     = $row->imagem;
        $origem     = $row->tipo;
        $alta         = inverteData($row->data_destino);
        $CID         = $row->cid_principal;
        $diag_pri     = $row->diagnostico_principal;
        $queixa       = $row->queixa;
        $exame_fisico   = $row->exame_fisico;
        $hora_dest    = $row->hora_destino;
        $obs_modal  = $row->obs_modal;
        $coronavirus  = $row->coronavirus;
    } else {
        $data_transacao = date('Y-m-d');
        $hora_transacao = date('H:i');
        $usuario_transacao = $usuario;
    }

    $sql = "select * from atendimentos where paciente_id = $paciente_id and destino_paciente in ('01','02','11','12','14','15') and now() - data_destino < '8'";
    $res = pg_query($sql) or die($sql);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transacao = stripslashes(pg_escape_string($_POST['transacao']));
    $senha = stripslashes(pg_escape_string($_POST['senha']));
    $data_transacao = stripslashes(pg_escape_string($_POST['data_transacao']));
    $hora_transacao = stripslashes(pg_escape_string($_POST['hora_transacao']));
    $usuario_transacao = stripslashes(pg_escape_string($_POST['usuario_transacao']));
    $acao = stripslashes(pg_escape_string($_POST['acao']));
    $idade = stripslashes(pg_escape_string($_POST['idade']));
    $prontuario = stripslashes(pg_escape_string($_POST['prontuario']));
    $nome = stripslashes(pg_escape_string($_POST['nome']));
    $dt_nascimento = stripslashes(pg_escape_string($_POST['dt_nascimento']));
    $enderecox = stripslashes(pg_escape_string($_POST['endereco']));
    $end_numero = stripslashes(pg_escape_string($_POST['end_num']));
    $complemento = stripslashes(pg_escape_string($_POST['end_comp']));
    $bairro = stripslashes(pg_escape_string($_POST['end_bairro']));
    $cidade = stripslashes(pg_escape_string($_POST['end_cidade']));
    $estado = stripslashes(pg_escape_string($_POST['end_uf']));
    $cep = stripslashes(pg_escape_string($_POST['end_cep']));
    $cpf = stripslashes(pg_escape_string($_POST['cpf']));
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

    if ($erro == "") {
        $xdum = "";

        include('conexao.php');
        $dt_transacao = inverteData($data_transacao);
        $alta = inverteData($alta);
        $dt_solicitacao = inverteData($dt_nsolicitacao);
        $horacad = date('H:i');
        $stmt = "update atendimentos set  transacao=$transacao,   observacao='$observacao', box='1',  local='1', 
				oque_faz='$oque_faz', como_faz='$como_faz', tempo_faz='$tempo_faz', com_oqfaz='$com_oqfaz', queixa='$queixa', exame_fisico='$exame_fisico', 
				diagnostico_principal='$diag_pri', cid_principal='$CID',";
        if (str_replace(' ', '', $destino) != '') {
            $stmt = $stmt . " destino_paciente='$destino',";
            if ($destino == '01' or $destino == '02' or $destino == '11' or $destino == '12' or $destino == '14' or $destino == '15') {
                $stmt = $stmt . " coronavirus=5,";
            }
        }
        if ($alta != '') {
            $stmt = $stmt . " data_destino='$alta', hora_destino='$hora_destino', status='Atendimento Finalizado' ";
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

            $stmt = "insert into evolucoes  (evolucao_id, atendimento_id,tipo,data,hora,usuario,evolucao)
						values ($row->nextval, $transacao,'$perfil','$datacad','$horacad','$usuario','$evolucao')";
            $sth = pg_query($stmt) or die($stmt);
            header("location: relevolucao.php?id=" . $row->nextval);
        }

        $df = '';
        if ($destino == '01') {
            $df = 'ALTA';
        } else if ($destino == '02') {
            $df = 'ALTA / ENCAM. AMBUL.';
        } else if ($destino == '07') {
            $df = 'EM OBSERVAÇÃO / MEDICAÇÃO';
        } else if ($destino == '10') {
            $df = 'EXAMES / REAVALIACAO';
        } else if ($destino == '03') {
            $df = 'PERMANÊCIA.';
        } else if ($destino == '04') {
            $df = 'TRANSF. OUTRA UPA';
        } else if ($destino == '05') {
            $df = 'TRANSF. INTERN. HOSPITALAR';
        } else if ($destino == '06') {
            $df = 'ÓBITO';
        } else if ($destino == '09') {
            $df = 'NAO RESPONDEU CHAMADO';
        } else if ($destino == '11') {
            $df = 'ALTA EVASÃO';
        } else if ($destino == '12') {
            $df = 'ALTA PEDIDO';
        } else if ($destino == '14') {
            $df = 'ALTA / POLICIA';
        } else if ($destino == '15') {
            $df = 'ALTA / PENITENCIÁRIA';
        } else if ($destino == '16') {
            $df = 'ALTA / PÓS MEDICAMENTO';
        }





        $data = date('Y-m-d');
        $hora = date('H:i');
        include('conexao.php');
        $stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
						values ('$usuario','FINALIZOU O ATENDIMENTO - DESTINO $df','$transacao','$data','$hora')";
        $sthLogs = pg_query($stmtLogs) or die($stmtLogs);
    }



    if (isset($_POST['encerrar']) != '') {

        include('conexao.php');
        $dt_transacao = inverteData(substr($data_transacao, 0, 10));
        $dt_solicitacao = inverteData($dt_nsolicitacao);
        $horacad = date('H:i');
        $stmt = "update pedidos set status='Cadastrado' where transacao=$transacao ";
        $sth = pg_query($stmt) or die($stmt);

        include('conexao.php');
        $dt_transacao = inverteData(substr($data_transacao, 0, 10));
        $dt_solicitacao = inverteData($dt_nsolicitacao);
        $horacad = date('H:i');
        $stmt = "update itenspedidos set situacao='Cadastrado' where transacao=$transacao ";
        $sth = pg_query($stmt) or die($stmt);


        header("location: atendimentos.php");
    }

    if (isset($_POST['novo_exame']) != '') {


        include('conexao.php');
        $dataTransacao = date('Y-m-d');
        $horacad = date('H:i');
        $stmt = "INSERT INTO pedidos ";
        $sth = pg_query($stmt) or die($stmt);

        include('conexao.php');
        $dt_transacao = inverteData(substr($data_transacao, 0, 10));
        $dt_solicitacao = inverteData($dt_nsolicitacao);
        $horacad = date('H:i');
        $stmt = "update itenspedidos set situacao='Cadastrado' where transacao=$transacao ";
        $sth = pg_query($stmt) or die($stmt);


        header("location: atendimentos.php");
    }

    if (isset($_POST["req_exame"])) {


        $exames = '';
        foreach ($transfere as $item) {
            $exames = $exames . $item . ',';
        }
        $exames =  rtrim($exames, ',');

        if ($exames != '') {
            echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('impexame.php?id=$exames');</script>";
        }
    }

    if (isset($_POST["req_exame_lab"])) {


        $exames = '';
        foreach ($transfere as $item) {
            $exames = $exames . $item . ',';
        }
        $exames =  rtrim($exames, ',');

        if ($exames != '') {
            echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('impexamelab.php?id=$exames');</script>";
        }
    }

    if (isset($_POST['imprimir']) != '') {
        echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('relFAA.php?id=$transacao');</script>";
    }

    echo "<script type=\"text/javascript\" language=\"Javascript\">location.href = 'atendimentoclinico.php?id=$transacao';</script>";
}


include('conexao.php');
$stmtCns = "SELECT * from controle_epidemiologico
		WHERE cns = '$cns' ORDER BY notificacao_id DESC LIMIT 1";
$sthCns = pg_query($stmtCns) or die($stmtCns);
$rowcns = pg_fetch_object($sthCns);

$rdonly = "";
$disable = "";
$hidden = false;
if ($destino != '') {
    $rdonly = " readonly ";
    $disable = " disabled ";
    $hidden = "hidden";
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
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <!--load all styles -->

    <script type="text/javascript">
        /*
	window.onbeforeunload = function (evt) {
	  var message = 'Você está saindo do sistema sem fazer logout. Deseja realmente sair?';
	  if (typeof evt == 'undefined') {
		evt = window.event;
	  }
	  if (evt) {
		evt.returnValue = message;
	  }
	  return message;
	}
*/
        function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('endereco').value = ("");
            document.getElementById('bairro').value = ("");
            document.getElementById('cidade').value = ("");
            document.getElementById('uf').value = ("");
        }

        <?php if ($destino == '19' and $med == 1) echo 'alert("Paciente sobre reavaliação. Exames prontos!!!");'; ?>

        function meu_callback(conteudo) {
            if (!("erro" in conteudo)) {
                //Atualiza os campos com os valores.
                document.getElementById('endereco').value = (conteudo.logradouro);
                document.getElementById('end_bairro').value = (conteudo.bairro);
                document.getElementById('end_cidade').value = (conteudo.localidade);
                document.getElementById('end_uf').value = (conteudo.uf);
            } //end if.
            else {
                //CEP não Encontrado.
                limpa_formulário_cep();
                alert("CEP não encontrado.");
            }
        }

        function pesquisacep(valor) {

            //Nova variável "cep" somente com dígitos.
            var cep = valor.replace(/\D/g, '');

            //Verifica se campo cep possui valor informado.
            if (cep != "") {

                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;

                //Valida o formato do CEP.
                if (validacep.test(cep)) {

                    //Preenche os campos com "..." enquanto consulta webservice.
                    document.getElementById('endereco').value = "...";
                    document.getElementById('end_bairro').value = "...";
                    document.getElementById('end_cidade').value = "...";
                    document.getElementById('end_uf').value = "...";

                    //Cria um elemento javascript.
                    var script = document.createElement('script');

                    //Sincroniza com o callback.
                    script.src = '//viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

                    //Insere script no documento e carrega o conteúdo.
                    document.body.appendChild(script);

                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulário_cep();
                    alert("Formato de CEP inválido.");
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
        };

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

        function calcularIdade() {

            dataNasc = document.getElementById("dt_nascimento").value;


            var dataAtual = new Date();

            //alert(" teste " + dataAtual);
            //Ex: Tue Mar 27 2012 09:43:24

            var anoAtual = dataAtual.getFullYear();
            //alert(" teste " + anoAtual);
            //teste 2012

            var anoNascParts = dataNasc.split('/');


            var diaNasc = anoNascParts[0];
            //alert("dia é..:" + diaNasc);

            var mesNasc = anoNascParts[1];
            var anoNasc = anoNascParts[2];


            var idade = anoAtual - anoNasc;


            var mesAtual = dataAtual.getMonth() + 1;


            //se mês atual for menor que o nascimento,não faz aniversario ainda.
            if (mesAtual < mesNasc) {
                idade--;
            } else {
                //se tiver no mes do nasc,verificar o dia
                if (mesAtual <= mesNasc) {
                    if (dataAtual.getDay() < diaNasc) {
                        //se a data atual for menor que o dia de nascimento,quer dizer que ele ainda não fez aniversario
                        idade--;
                    }

                }

            }

            document.getElementById("idade").value = idade;





        }

        function showpagtos() {
            conv = document.getElementById("conveniox").value;
            dvd = 'S';
            d4 = 'S';
            abre = 'N';
            if (document.getElementById("dvd").checked) {
                dvd = 'S';
            } else {
                dvd = 'N';
            }
            if (document.getElementById("d4").checked) {
                d4 = 'S';
            } else {
                d4 = 'N';
            }

            div = conv.substring(0, 1);

            if ((dvd == "S")) {
                abre = 'S';
            }
            if ((d4 == "S")) {
                abre = 'S';
            }
            if ((div == "P")) {
                abre = 'S';
            }
            if ((abre == "S")) {
                document.getElementById("pagamentos").style.display = 'block';
                document.getElementById("carteirinha").value = '';
                document.getElementById("carteirinha").readOnly = true;
                document.getElementById("nroguia").value = '';
                document.getElementById("nroguia").readOnly = true;
            }
            if (abre == "N") {
                document.getElementById("pagamentos").style.display = 'none';
                document.getElementById("carteirinha").readOnly = false;
                document.getElementById("nroguia").readOnly = false;
            }
        }

        function showenfermaria(div) {
            div = div.substring(0, 1);
            if (div == "E") {
                document.getElementById("enfermaria").value = '';
                document.getElementById("enfermaria").readOnly = true;
                document.getElementById("leito").value = '';
                document.getElementById("leito").readOnly = true;
            }
            if (div == "I") {
                document.getElementById("enfermaria").readOnly = false;
                document.getElementById("leito").readOnly = false;
            }
        }

        function SomenteNumero(e) {
            var tecla = (window.event) ? event.keyCode : e.which;
            if ((tecla > 47 && tecla < 58)) return true;
            else {
                if (tecla == 8 || tecla == 0) return true;
                else return false;
            }
        }

        function showDiv(div) {
            if (div == "DD") {
                document.getElementById("bandeira").style.display = 'none';
                document.getElementById("banco_num").style.display = 'none';
                document.getElementById("num_cheque").style.display = 'none';
            }
            if (div == "CD") {
                document.getElementById("banco_num").style.display = 'none';
                document.getElementById("bandeira").style.display = 'block';
                document.getElementById("num_cheque").style.display = 'block';
            }
            if (div == "CC") {
                document.getElementById("banco_num").style.display = 'none';
                document.getElementById("bandeira").style.display = 'block';
                document.getElementById("num_cheque").style.display = 'block';
            }
            if (div == "CH") {
                document.getElementById("bandeira").style.display = 'none';
                document.getElementById("banco_num").style.display = 'block';
                document.getElementById("num_cheque").style.display = 'block';
            }
        }

        function openInNewTab(url) {
            var win = window.open(url, '_blank');
            win.focus();
        }
    </script>
    <style>
        .invisivel {
            display: none;
        }

        .visivel {
            visibility: visible;
        }
    </style>
    <script>
        function buscar_agenda(data) {
            var estado = $('#modalidade').val();
            if (estado) {
                var url = 'ajax_buscar_agenda.php?data=' + data + '&modalidade=' + estado;
                $.get(url, function(dataReturn) {
                    $('#load_agenda').html(dataReturn);
                });
            }
        }

        function buscar_sgrupos() {
            var estado = $('#cod_id').val();
            var conv = $('#conveniox').val();
            if (estado) {
                var url = 'ajax_buscar_proc.php?estado=' + estado + '&convenio=' + conv;
                $.get(url, function(dataReturn) {
                    $('#load_cidades').html(dataReturn);
                });
            }
        }


        function doConfirm(id) {

            var ok = confirm("Confirma a exclusao?<?php echo $transacao; ?>")
            if (ok) {

                if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else { // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }

                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        window.location = "agendaexame.php?data=<?php echo date('Y-m-d'); ?>";
                        window.location.reload()
                    }
                }

                xmlhttp.open("GET", "apagaagendatemp.php?id=<?php echo $transacao; ?>");
                xmlhttp.send();
            }
        }

        function aConf(mes) {
            alertify.confirm(mes, function(e) {
                return e;
            });
        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
        }

        function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e) {
            var sep = 0;
            var key = '';
            var i = j = 0;
            var len = len2 = 0;
            var strCheck = '0123456789';
            var aux = aux2 = '';
            var whichCode = (window.Event) ? e.which : e.keyCode;
            if (whichCode == 13) return true;
            key = String.fromCharCode(whichCode); // Valor para o código da Chave
            if (strCheck.indexOf(key) == -1) return false; // Chave inválida
            len = objTextBox.value.length;
            for (i = 0; i < len; i++)
                if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;
            aux = '';
            for (; i < len; i++)
                if (strCheck.indexOf(objTextBox.value.charAt(i)) != -1) aux += objTextBox.value.charAt(i);
            aux += key;
            len = aux.length;
            if (len == 0) objTextBox.value = '';
            if (len == 1) objTextBox.value = '0' + SeparadorDecimal + '0' + aux;
            if (len == 2) objTextBox.value = '0' + SeparadorDecimal + aux;
            if (len > 2) {
                aux2 = '';
                for (j = 0, i = len - 3; i >= 0; i--) {
                    if (j == 3) {
                        aux2 += SeparadorMilesimo;
                        j = 0;
                    }
                    aux2 += aux.charAt(i);
                    j++;
                }
                objTextBox.value = '';
                len2 = aux2.length;
                for (i = len2 - 1; i >= 0; i--)
                    objTextBox.value += aux2.charAt(i);
                objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);
            }
            return false;
        }

        function busca_tabela() {
            var conv = $('#conveniox').val();
            if (conv) {
                var url = 'tabelas.php?convenio=' + conv;
                $.get(url, function(dataReturn) {
                    $('#procedimento').load(dataReturn);
                });
            }
        }

        function openInNewTab(url) {
            var win = window.open(url, '_blank');
            win.focus();
        }


        function preencheCid(cid, descricao) {
            $("#CID").val(cid);
            $("#cidAtestado").val(cid);
            $("#diag_pri").val(descricao);
            $('#cidTable').empty();
            $("#lista_diagnostico").slideUp(100);
        }

        function copiarCid(cid) {
            $("#cidAtestado").val(cid.value);
        }

        function modal_obs(id) {
            var modal = document.getElementById('obs_modal').value;
            modal = modal.replace(/(?:\r\n|\r|\n)/g, '/p');
            modal = modal.replace('#', '');
            $.get("salvar_obs.php?modal=" + modal + "&id=" + id, function(dataReturn) {
                $('#teste').html(dataReturn);
            })
        }
    </script>

</head>
<style>
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

    .blink {
        animation-duration: 0.85s;
        animation-name: blink;
        animation-iteration-count: infinite;
        animation-direction: alternate;
        animation-timing-function: ease-in-out;
    }

    @keyframes blink {
        from {
            color: white;
        }

        to {
            color: darkred;
        }
    }

    #loading-hover {
        position: fixed;
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.4);
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    #loading-hover img {
        position: relative;
        top: 40%;
        left: 41%;
    }

    .page-content {
        padding: 0;
    }

    .site-footer {
        background-color: #FFF;
    }
</style>

<!-- <div id="loading-hover">
    <img src="imgs/loading.gif" alt="Carregando..." />
</div> -->

<body class="pace-done" cz-shortcut-listen="true">
    <!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

    <!-- <div class="wrapper"> -->
    <?php
    include('menu.php');
    include('header.php');
    $stmt = "Select status, med_atendimento from atendimentos where transacao='$transacao'";
    $sth  = pg_query($stmt) or die($stmt);
    $row  = pg_fetch_object($sth);

    if ($origem != 6) {
        if ($row->status == 'Aguardando Triagem' || $row->status == 'Em Atendimento' && $row->med_atendimento != $usuario) { ?>

            <script>
                swal({
                        title: "Paciente não disponivel",
                        text: "Paciente foi chamado por outro profissional!",
                        type: "warning",
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "OK",
                        closeOnConfirm: false
                    },
                    function() {
                        window.close();
                    });
            </script>

        <?php
        }
    }


    if ($rowcns->descricao != '') {
        ?>
        <script>
            sweetAlert("<?php echo 'Atenção, paciente com notificação epidemiologica'; ?>", "<?php echo $rowcns->descricao; ?>",
                "warning");
        </script>
    <?php }
    ?>
    <div class="main-panel">
        <div class="main-content">
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
                                                    <p style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                        » </p>Página Padrão
                                                </h4>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <ol class="breadcrumb">
                                                <li><a href="../index.html">Home</a></li>
                                                <li><a href="#">Atendimentos</a></li>
                                                <li class="active">Atendimentos Cadastrados</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CORPO DA PAGINA -->
                        <div class="card-content">
                            <div class="col-12">
                                <div class="card-content">
                                    <?php
                                    if ($erro != "") {
                                        echo "<div class=\"alert dark alert-danger alert-dismissible\" role=\"alert\">";
                                        echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">";
                                        echo "<span aria-hidden=\"true\">Ã—</span>";
                                        echo "</button>";
                                        echo $erro;
                                        echo "</div>";
                                    }
                                    ?>
                                    <form method="post" id="pedido" name='pedido' autocomplete="off">

                                        <div class="card-body">
                                            <input type="hidden" name="transacao" id="transacao" class="form-control" value="<?php echo $transacao; ?>" readonly>
                                            <input type="hidden" value="<?php echo $prioridade; ?>" id="prioridade">
                                            <input type="hidden" name="data_transacao" class="form-control" value="<?php echo date('d/m/Y', strtotime($data_transacao)); ?>" readonly>
                                            <input type="hidden" name="hora_transacao" class="form-control" value="<?php echo (empty($transacao)) ? date('H:i') : $hora_transacao;  ?>" readonly>
                                            <input type="hidden" name="senhac" class="form-control" value="<?php echo $senhal; ?>" readonly>
                                            <input type="hidden" name="usuario_transacao" id="usuario_transacao" class="form-control" value="<?php echo $usuario; ?>" readonly>
                                            <input type="hidden" name="situacao" class="form-control" value="<?php echo $situacao; ?>" readonly>

                                            <!-- <br> <br> <br> -->
                                            <div id="teste"></div>

                                            <div class="col-sm-12">
                                            <div class="col-12 text-center">
                                                <h4 class="form-section-center"><i class="ft-user"></i> Identificação do Paciente</h4>
                                                <!-- <h3 class="title" align="center">Identificação do Paciente</h3> -->
                                                <hr style="margin: auto;width: 350px">
                                            </div>
                                                <div class="col-lg-2">
                                                    <div>
                                                        <?php
                                                        if ($imagem == "") {
                                                            echo "<img id=\"blah\" src=\"app-assets/img/gallery/user-transp.png\"         alt=\"\" height=\"120\" width=\"130\" ondblclick=\" window.open('webcam/index.php', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=700'); return false;\">";
                                                        } else {
                                                            echo "<img id=\"blah\" src=\"imagens/clientes/" . $imagem . "\" alt=\"\" height=\"120\" width=\"130\" ondblclick=\" window.open('webcam/index.php', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350'); return false;\">";
                                                        }

                                                        ?>
                                                    </div>
                                                </div>
                                                <div id="dados-paciente-div">
                                                    <input type="hidden" class="form-control" name="prontuario" id="prontuario" placeholder="Paciente..." value='<?php echo $prontuario; ?>' readonly>
                                                    <input type="hidden" class="form-control" name="imagem" id="imagem" value="<?php echo $imagem; ?>" value='<?php echo $imagem; ?>' readonly>
                                                    <div class="col-sm-5">
                                                        <label class="control-label">Nome </label> <input type="text" name="nome" id="nome" class="form-control <?php if ($coronavirus == 1) {
                                                                                                                                                                    echo 'blink';
                                                                                                                                                                } ?>" style="font-weight: bold;" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)" <?php echo $rdonly ?>>
                                                        <input type="hidden" id="id" value="<?php echo $paciente_id; ?>">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label class="control-label">CNS</label> <input type="text" name="cns" id="cns" class="form-control" value="<?php echo $cns; ?>" onkeypress='return SomenteNumero(event)' <?php echo $rdonly ?>>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Nascimento</label> <input type="text" name="dt_nascimento" id="dt_nascimento" class="form-control" value="<?php echo $dt_nascimento; ?>" OnKeyPress="formatar('##/##/####', this)" OnBlur="calcularIdade(this.value)" <?php echo $rdonly ?>>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Idade</label> <input type="text" name="idade" id="idade" class="form-control" value="<?php echo $idade; ?>" readonly>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <label class="control-label">Sexo</label> <input type="text" name="sexo" id="sexo" class="form-control" value="<?php echo $sexo; ?>" readonly> <input type="hidden" name="pendencia" id="pendencia" class="form-control" value="<?php echo $pendencia; ?>">
                                                    </div>


                                                    <div class="col-sm-6">
                                                        <label class="control-label">
                                                            <font color='red'>Origem</font>
                                                        </label>
                                                        <input type='text' readOnly class="form-control" name="origem" id="origem" value='<?php echo $origem; ?>'>
                                                    </div>

                                                    <div class="col-sm-12">
                                                        <label class="control-label">Observações</label>
                                                        <textarea rows="14" name="observacao" class="form-control" <?php echo $rdonly ?>><?php echo $observacao; ?></textarea> </br>
                                                    </div><br>
                                                    <ul class="nav nav-tabs nav-justified">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" aria-controls="active" aria-expanded="true">Informaçoes do Atendimento Realizado</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="link-tab" data-toggle="tab" href="#link" aria-controls="link" aria-expanded="false">Link</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="click-tab" data-toggle="tab" href="#click" aria-controls="click" aria-expanded="false">Click</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="linkOpt-tab" data-toggle="tab" href="#linkOpt" aria-controls="linkOpt">Another
                                                                Link</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content px-1 pt-1">
                                                        <div class="col-sm-12"><br>
                                                            <h3 class="title" align="center">Informaçoes do Atendimento Realizado</h3>
                                                            <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label class="control-label">Queixa</label>
                                                            <input type="text" name="queixa" id="queixa" class="form-control" value="<?php echo $queixa; ?>" maxlength="80" onkeyup="maiuscula(this)" <?php echo $rdonly ?>>
                                                        </div>
                                                        <div class="col-sm-12 margin-top-10">
                                                            <label class="control-label">Exame Físico</label>
                                                            <textarea name="exame_fisico" class="form-control" rows="15" <?php echo $rdonly ?>><?php echo $exame_fisico; ?></textarea> </br>
                                                        </div>

                                                        <input id="usuario-autorizado" name="usuario-autorizado" type="hidden" />

                                                        <div class="tab-pane" id="link" role="tabpanel" aria-labelledby="link-tab" aria-expanded="false">
                                                            <p>Chocolate bar gummies sesame snaps. Liquorice cake sesame snaps cotton candy cake sweet brownie.
                                                                Cotton candy candy canes brownie. Biscuit pudding sesame snaps pudding pudding sesame snaps biscuit
                                                                tiramisu.</p>
                                                        </div>
                                                        <div class="tab-pane" id="click" role="tabpanel" aria-labelledby="click-tab" aria-expanded="false">
                                                            <p>Fruitcake marshmallow donut wafer pastry chocolate topping cake. Powder powder gummi bears jelly
                                                                beans. Gingerbread cake chocolate lollipop. Jelly oat cake pastry marshmallow sesame snaps.</p>
                                                        </div>
                                                        <div class="tab-pane" id="linkOpt" role="tabpanel" aria-labelledby="linkOpt-tab" aria-expanded="false">
                                                            <p>Cookie icing tootsie roll cupcake jelly-o sesame snaps. Gummies cookie dragée cake jelly marzipan
                                                                donut pie macaroon. Gingerbread powder chocolate cake icing. Cheesecake gummi bears ice cream
                                                                marzipan.</p>
                                                        </div>
                                                    </div>







                                                    <?php
                                                    if ($transacao != "") {
                                                        include('conexao.php');
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
                                                            include('conexao.php');
                                                            $stmt = "select a.pessoa_id, b.dat_cad, a.situacao, a.exame_id, c.descricao from itenspedidos a left join pedidos b on a.transacao=b.transacao left join procedimentos c on a.exame_id=c.procedimento_id where a.pessoa_id=" . $prontuario;
                                                            $sth = pg_query($stmt) or die($stmt);
                                                            while ($row = pg_fetch_object($sth)) {
                                                                echo "<tr>";
                                                                echo "<td>" . inverteData(substr($row->dat_cad, 0, 10)) . "</td>";
                                                                echo "<td>UPA ". utf8_decode(UNIDADE_CONFIG). "</td>";
                                                                echo "<td><a href='atendimentoclinico.php?id=" . $row->transacao . "' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a></td>";
                                                                echo "</tr>";
                                                            }

                                                            echo "</tbody></table>";
                                                            echo "</div>";
                                                            echo "</div>";
                                                            echo "<br>";
                                                            echo "</div>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                    ?>
                                                    <div class="col-sm-12"><br>
                                                        <h3 class="title" align="center">Destino/Diagnóstico</h3>
                                                        <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />
                                                        <div class="col-sm-12">
                                                            <label class="control-label">Destino dado ao Paciente</label>
                                                            <?php if ($destino == '19' and $med == 1) { ?>
                                                                <label class="control-label">guardando os resultados de exames</label>
                                                                <textarea name="evolucao" class="form-control" style="height: 404px" id="evolucao"></textarea> </br>
                                                            <?php } ?>
                                                            <select class="form-control" name="destino" id="destino" <?php if ($destino != '19'  or $med != 1) echo $disable ?>>
                                                                <option value=""></option>;
                                                                <option value="01" <?php if ($destino == '01') echo "selected"; ?>>ALTA
                                                                </option>;
                                                                <option value="02" <?php if ($destino == '02') echo "selected"; ?>>ALTA /
                                                                    ENCAM. AMBUL.</option>;
                                                                <option value="11" <?php if ($destino == '11') echo "selected"; ?>>ALTA EVASÃO
                                                                </option>;
                                                                <option value="12" <?php if ($destino == '12') echo "selected"; ?>>ALTA PEDIDO
                                                                </option>;
                                                                <option value="14" <?php if ($destino == '14') echo "selected"; ?>>ALTA / PM
                                                                </option>;
                                                                <option value="15" <?php if ($destino == '15') echo "selected"; ?>>ALTA /
                                                                    PENITENCIÁRIA</option>;
                                                                <option value="07" <?php if ($destino == '07') echo "selected"; ?>>EM
                                                                    OBSERVAÇÃO / MEDICAÇÃO</option>;
                                                                <!-- <option value="19" <?php if ($destino == '19') echo "selected"; ?>>EXAMES LABORATORIAIS</option>; -->
                                                                <option value="10" <?php if ($destino == '10') echo "selected"; ?>>EXAMES /
                                                                    REAVALIACAO</option>;
                                                                <option value="03" <?php if ($destino == '03') echo "selected"; ?>>PERMANÊNCIA.
                                                                </option>;
                                                                <option value="04" <?php if ($destino == '04') echo "selected"; ?>>TRANSF.
                                                                    OUTRA UPA</option>;
                                                                <option value="09" <?php if ($destino == '09') echo "selected"; ?>>NAO
                                                                    RESPONDEU CHAMADO</option>;
                                                            </select>
                                                        </div>



                                                        <div class="col-sm-4">
                                                            <label class="control-label">CID</label>
                                                            <input type="text" name="CID" id="CID" class="form-control" value="<?php echo $CID; ?>" onkeyup="maiuscula(this),copiarCid(this)" maxlength='5' <?php echo $rdonly ?>>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <label class="control-label">Diagnóstico Principal</label>
                                                            <input type="text" name="diag_pri" id="diag_pri" onkeyup="retornaCid(this)" class="form-control" value="<?php echo $diag_pri; ?>" <?php echo $rdonly ?>>

                                                            <!-- Está parte do codigo é referente a busca do CID -->

                                                            <style>
                                                                table #cidTable {
                                                                    border-collapse: collapse;
                                                                    width: 100%;
                                                                }

                                                                #cidTable th,
                                                                #cidTable td {
                                                                    text-aling: left;
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
                                                        <div class="col-md-12" align="center"><br><br>
                                                            <div class="form-group">
                                                                <input type='button' id="gravar" name='gravar' class="btn btn-primary" value='Gravar' onclick="return valida()">
                                                                <input type='button' id="atestado" href="#" data-id="<?= $_GET['id'] ?>" data-target="#exampleTabs" onclick="return validar()" value='Atestados' class="btn btn-warning" data-toggle="modal">
                                                                <input type='button' id="receituario" href="#" data-id="<?= $_GET['id'] ?>" data-target="#modalSolicitaReceituario" onclick="return validar()" value='Receituário' class="btn btn-success" data-toggle="modal">


                                                                <button type="button" id="receituario" class="btn btn-success" href="#" data-id="<?= $_GET['id']; ?>" data-toggle="modal" data-target="#ExemploModalCentralizado" value='Receituário'>
                                                                    Solicitação de Internação
                                                                </button>

                                                                <div class="modal fade" id="ExemploModalCentralizado" tabindex="-1" role="dialog" aria-labelledby="TituloModalCentralizado" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <!-- <h5 class="modal-title" id="TituloModalCentralizado">Título do modal</h5> -->
                                                                                <div class="modal-body">

                                                                                    <label for="message-text" class="col-form-label">Observações:</label>
                                                                                    <!-- <textarea class="form-control" name="obs_modal" id="obs_modal" style="resize: none" rows="10" cols="60" form="usrform" static><?php echo $obs_modal; ?></textarea> -->
                                                                                    <textarea name="obs_modal" id="obs_modal" class="form-control" rows="15" style="resize: none" rows="10" cols="60" form="usrform" static><?php echo str_replace('/p', '&#10', $obs_modal); ?></textarea>
                                                                                    <br>

                                                                                </div>
                                                                            </div>

                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                                                <button type="button" data-dismiss="modal" onclick="modal_obs(<?php echo $transacao; ?> );" class="btn btn-primary">Salvar mudanças</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <a href="relComparecimento.php" target="_blank" name="faa" class="btn btn-primary">Declaração de Comparecimento</a>
                                                                <a href="relFAA.php?id=<?= $_GET['id'] ?>" target="_blank" name="faa" class="btn btn-primary">FAA / Imprimir</a>
                                                                <a href="formapacant.php?paciente=<?php echo $paciente_id; ?>" target="_blank" name="faa" class="btn btn-primary">Solicitar APAC</a>
                                                                <!--<input type='submit' name='imprimir'  class="btn btn-primary" value='Imprimir'>-->
                                                                <!--<input type='submit' name='xcancelar' class="btn btn-danger"  value='Cancelar'>-->

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

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
                                            <div class="col-sm-5">
                                                <div id="exames_atendimentos" class="col-sm-12"><br>
                                                    <h3 class="title" align="center" style="margin-bottom: -15px;">Exames de Imagem</h3>
                                                    <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />
                                                    <div class="col-sm-12" style="height: 255px; overflow-y: auto; overflow-x: hidden; padding:0"><br>
                                                        <table class="table condensed width-full">
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
                                                                include('conexao.php');
                                                                $stmt = "select b.dat_cad,c.formulario, a.exame_nro,a.arquivo_upload, a.pessoa_id,a.transacao, c.descricao, a.situacao, a.versao, c.exames_laboratoriais, c.procedimento_id from itenspedidos a 
													left join pedidos b on a.transacao=b.transacao 
													left join procedimentos c on a.exame_id=c.procedimento_id where a.pessoa_id=" . $paciente_id . " and c.exames_laboratoriais is null order by dat_cad desc, a.exame_id";
                                                                $sth = pg_query($stmt) or die($stmt);
                                                                //echo $stmt;
                                                                $x = 0;
                                                                $data = '';
                                                                $procedimento_lab = '';
                                                                while ($row = pg_fetch_object($sth)) {
                                                                    if ($row->exames_laboratoriais == 1 and substr($row->dat_cad, 0, 10) >= '2019-01-08') {
                                                                        //if(($data != substr($row->dat_cad, 0, 10) or $data != date('Y-m-d', strtotime("+1 days",strtotime(substr($row->dat_cad, 0, 10))))) and $procedimento_lab != $row->procedimento_id){
                                                                        include('conexao_laboratorio.php');
                                                                        $sql = "SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, e.descricao 
																FROM pedidos a
																INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
																INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
																INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
																LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
																LEFT JOIN modalidades f ON f.modalidade_id = e.setor where d.exame_id = " . $row->procedimento_id . " and a.data = '" . substr($row->dat_cad, 0, 10) . "' and c.origem = '02' and b.origem = 2 and pessoa_id_origem = $prontuario order by a.data, a.horario";
                                                                        $result = pg_query($sql) or die($sql);
                                                                        while ($rows = pg_fetch_object($result)) {

                                                                            if ($rows->exame_id == $row->procedimento_id) {
                                                                                echo "<tr>";
                                                                                echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\" value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                                echo "<td>" . inverteData($rows->data) . "</td>";
                                                                                echo "<td>$row->descricao</td>";
                                                                                if ($rows->liberado == 1) {
                                                                                    echo "<td><a href='http://".IP_CONFIG."/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a></td>";
                                                                                }
                                                                                echo "</tr>";
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
                                                                            echo "<tr>";
                                                                        }
                                                                        echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                        echo "<td>" . inverteData(substr($row->dat_cad, 0, 10)) . ' ' . $row->situacao . $row->versao . "</td>";
                                                                        echo "<td>" . $row->descricao . "</td>";
                                                                        include('conexao.php');
                                                                        $sqlDetalhe = "SELECT * FROM arquivos_documentos WHERE transacao = " . $row->exame_nro;
                                                                        $sthDet = pg_query($sqlDetalhe) or die($sqlDetalhe);
                                                                        $rowDetalhe = pg_fetch_object($sthDet);
                                                                        echo "<td>";
                                                                        if ($rowDetalhe->arquivo != '') {
                                                                            echo "<a href='imagens/documentos/$rowDetalhe->arquivo' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
                                                                        }

                                                                        if ($row->arquivo_upload != '') {
                                                                            echo "<a href='arquivos/exames/$row->arquivo_upload' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
                                                                        }



                                                                        if ($row->formulario == 'A') {
                                                                            echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\"><i class=\"icon wb-print\" aria-hidden=\"true\" onclick=\"openInNewTab('relApac.php?id=$row->exame_nro')\"></i></button>";
                                                                        }
                                                                        if ($row->situacao == 'Finalizado') {
                                                                            if ($row->versao != '2') {
                                                                                echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
                                                                            } else {
                                                                                echo "<a href='http://".IP_CONFIG."/laboratorio/html/relExamesb.php?local=SB&transacao=$row->exame_nro'' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
                                                                            }
                                                                        }

                                                                        if ($row->situacao == 'Impresso') {
                                                                            echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
                                                                        }
                                                                        echo "</td>";

                                                                        //echo"<td class='small'><a href=\"deletarexames.php?id=$row->transacao&atendimento=$transacao\" data-toggle=\"tooltip\" data-original-title=\"Deletar Pedido de Exame\"><i class=\"fa fa-times text-danger\" aria-hidden=\"true\"></i></a></td>";

                                                                        echo "</tr>";
                                                                    }
                                                                }

                                                                ?>
                                                            </body>
                                                        </table>

                                                    </div>
                                                </div>
                                                <?php if ($perfil == '06' or $perfil == '03') { ?>
                                                    <div class="col-md-12">

                                                        <table class="table table-hover table-striped condensed width-full">
                                                            <tr>
                                                                <td><label class="control-label">
                                                                        <font color='red'>Exame/Procedimento</font>
                                                                    </label></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>

                                                                <td>
                                                                    <select style='width: 220px; font-size:small;' multiple name="procedimento" id="procedimento">
                                                                        <?php

                                                                        include('conexao.php');
                                                                        $sql = "SELECT procedimento_id, descricao, sigtap from procedimentos a 
													                            WHERE descricao <> '%EXCLUIDO%' and procedimento_id not in (729,730,822,821,779) and descricao not in ('DOSAGEM DE FOLATO',
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
                                                                        echo "<option value=\"\">Selecione o Procedimento</option>";
                                                                        while ($row = pg_fetch_object($sth)) {
                                                                            echo "<option value=\"" . $row->procedimento_id . "\"";
                                                                            echo ">" . $row->descricao . "</option>";
                                                                        }

                                                                        ?>
                                                                    </select>

                                                                </td>
                                                                <td><input type='button' name='novo_exame' id="novo_exame" class="btn btn-primary" value='Solicitar'>
                                                                    <input type='submit' name='req_exame' id="req_exame" class="btn btn-success" value='Imprimir'>
                                                                </td>

                                                            </tr>
                                                        </table>

                                                    </div>
                                                <?php } ?>

                                                <div class="col-sm-12">
                                                    <div id="exames_laboratorio" class="col-sm-12"><br>
                                                        <h3 class="title" align="center" style="margin-bottom: -15px;">Exames Laboratoriais
                                                        </h3>
                                                        <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />
                                                        <div class="col-sm-12" style="height: 255px; overflow-y: auto; overflow-x: hidden; padding:0"><br>
                                                            <table class="table condensed width-full">
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
                                                                    include('conexao_laboratorio.php');
                                                                    $sql = "SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, d.situacao, e.descricao, d.pedido_item_id 
                                                                            FROM pedidos a
                                                                            INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
                                                                            INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
                                                                            INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
                                                                            LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
                                                                            LEFT JOIN modalidades f ON f.modalidade_id = e.setor where c.origem = '02' and b.origem = 2 and pessoa_id_origem = $prontuario order by a.data desc, a.horario";
                                                                    $result = pg_query($sql) or die($sql);
                                                                    while ($rows = pg_fetch_object($result)) {
                                                                        echo "<tr>";
                                                                        if ($rows->situacao == '') {
                                                                            echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\" value=\"" . $rows->pedido_item_id . "\"><label></label></div></td>";
                                                                        } else {
                                                                            echo "<td></td>";
                                                                        }
                                                                        echo "<td>" . inverteData($rows->data) . "</td>";
                                                                        echo "<td>$rows->descricao</td>";
                                                                        if ($rows->liberado == 1) {
                                                                            echo "<td><a href='http://".IP_CONFIG."/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a></td>";
                                                                        } else {
                                                                            echo "<td>" . $rows->situacao . "</td>";
                                                                        }
                                                                        echo "</tr>";
                                                                    }
                                                                    ?>
                                                                </body>
                                                            </table>

                                                        </div>
                                                    </div>
                                                    <?php if ($perfil == '06' or $perfil == '03') { ?>
                                                        <div class="col-md-12">

                                                            <input type='button' id="solicita_laboratorio" href="#" data-target="#modalLaboratorio" value='Solicitar Laboratorio' class="btn btn-success" data-toggle="modal">
                                                            <input type='submit' name='req_exame_lab' id="req_exame_lab" class="btn btn-warning" value='Imprimir Solicitados'>

                                                        </div>
                                                    <?php } ?>

                                                    <br>
                                                    <div class="col-sm-12"><br>
                                                        <div id="retorno_prescricao">
                                                            <h3 class="title" align="center" style="margin-bottom: -15px;">Prescrições</h3>
                                                            <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />
                                                            <div class="col-sm-12" style="height: 295px; overflow-y: auto; overflow-x: hidden;"><br>

                                                                <table class="table table-hover table-striped condensed width-full">
                                                                    <thead>
                                                                        <tr>
                                                                            <th><input type="checkbox" name="todos" id='todos' onclick='marcardesmarcar();' value="T"></th>
                                                                            <th width="10%">Data</th>
                                                                            <th>Prescrição/Profissional</th>
                                                                            <th width="40%"><input type="button" name="atualizart" id="atualizart" onclick="atualizat(<?php echo $transacao; ?>)" class="form-control" value="Atualizar"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="conteudoPrescricao">
                                                                        <?php
                                                                        include('conexao.php');
                                                                        $stmt = "SELECT a.prescricao_id,a.hora,a.data,a.descricao,b.nome, a.cuidados, a.componente1,a.componente2,a.componente3,a.administracao,a.tipo FROM prescricao_itens a
                                                                                left join pessoas b ON b.username = a.medico
                                                                                WHERE a.atendimento_id =" . $transacao . " and a.sequencia is null order by a.tipo,a.prescricao_id ASC";
                                                                        $sth = pg_query($stmt) or die($stmt);

                                                                        while ($row = pg_fetch_object($sth)) {
                                                                            echo "<tr>";
                                                                            echo "<td><div><input type=\"checkbox\" class='marcar' name=\"cb_prescricao[]\"    value=\"" . $row->prescricao_id . "\"><label></label></div></td>";
                                                                            echo "<td>" . inverteData(substr($row->data, 0, 10)) . '<br>' . $row->hora . "</td>";

                                                                            if ($row->tipo == 10) {
                                                                                echo "<td class='small'>" .  utf8_encode($row->cuidados) . "<br>" . $row->nome . "</td>";
                                                                            }

                                                                            if ($row->tipo == 1) {
                                                                                echo "<td class='small'>" .  utf8_encode($row->cuidados) . "<br>" . $row->nome . "</td>";
                                                                            }

                                                                            if ($row->tipo == 3) {
                                                                                echo "<td class='small'>" .
                                                                                    utf8_encode($row->descricao) . "<br>" .
                                                                                    utf8_encode($row->componente1) . ' - ' . utf8_encode($row->componente2) . ' - ' . utf8_encode($row->componente3) . '<br>' .
                                                                                    utf8_encode($row->administracao) .
                                                                                    '<br>' .
                                                                                    $row->nome .
                                                                                    "</td>";
                                                                            }

                                                                            if ($row->tipo == 5) {
                                                                                echo "<td class='small'>" . utf8_encode($row->descricao) . "<br>" . $row->nome . "</td>";
                                                                            }

                                                                            //echo "<td class='small'><a href=\"deletarprescricao.php?id=$row->prescricao_id&atendimento=$transacao\" data-toggle=\"tooltip\" data-original-title=\"Deletar Prescrição\"><i class=\"fa fa-times text-danger\" aria-hidden=\"true\"></i></a></td>";	

                                                                            echo "<tr>";
                                                                        }
                                                                        ?>
                                                                        <?php include('conexao.php');
                                                                        $stmt = "select a.data, a.hora, c.nome, a.prescricao_id, d.nome as medico
                                                                                from prescricoes a
                                                                                left join atendimentos b on a.atendimento_id = b.transacao
                                                                                left join pessoas c on b.paciente_id = c.pessoa_id
                                                                                left join pessoas d on d.pessoa_id = a.profissional_id
                                                                                where a.atendimento_id = $transacao order by a.hora desc";
                                                                        $sth = pg_query($stmt) or die($stmt);

                                                                        while ($row = pg_fetch_object($sth)) {
                                                                            $seq = $row->sequencia + 1;
                                                                            echo "<tr>";
                                                                            echo "<td class='small'>" . $seq . "</td>";
                                                                            echo "<td class='small'>" . date('d/m/Y', strtotime($row->data)) . "</td>";
                                                                            echo "<td class='small'>" . $row->hora . "</td>";
                                                                            echo "<td class='small'>" . $row->nome . "</td>";
                                                                            echo "<td class='small'>" . $row->medico . "</td>";
                                                                            echo "<td class='small'>
															<a href=\"prescricaoenfermagemy.php?id=$row->prescricao_id&p=$transacao\" target=\"_blank\" 
															class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" 
															data-original-title=\"Prescrição\">
																<i class=\"icon wb-print\" aria-hidden=\"true\" >
																</i>
															</a>
															<input type='button' onClick=\"window.open('popprescricao.php?prioridade=$prioridade&pr=$row->prescricao_id&id=$transacao&nome=$nome&cns=$cns&idade=$idade&prontuario=$prontuario', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=980, height=550'); return false;\" target='_blank'
															class='btn btn-secondary btn-sm' data-toggle=\"tooltip\" data-original-title=\"Duplicar Prescrição\" value='+'>
															
														</td>";

                                                                            echo "<tr>";
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
                                                                <?php if ($perfil == '06' or $perfil == '03') { ?>
                                                                    <!--<input type='button' value="Solicitar Prescrição" id="prescricaoSolicitaModal" onclick="solicitacaoprescricao('<?php echo $_GET['id'] ?>')" class="btn btn-success margin-10" data-toggle="modal" >-->
                                                                    <!--<input type='button' value="Solicitar Prescrição" id="prescricaoSolicitaModal" onclick="solicitacaoprescricao('<?php echo $_GET['id'] ?>')" class="btn btn-success margin-10" data-toggle="modal" >-->
                                                                    <input type='button' id="pres" value='Solicitar Prescricão' class="btn btn-success" onClick="window.open('popprescricao.php?prioridade=<?php echo $prioridade; ?>&id=<?php echo $transacao; ?>&nome=<?php echo $nome; ?>&cns=<?php echo $cns; ?>&idade=<?php echo $idade; ?>&prontuario=<?php echo $prontuario; ?>', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1030, height=550'); return false;">
                                                                    <input type='button' id="prescricao" onclick="return validar()" value='Imprimir Prescricão' class="btn btn-warning">
                                                                <?php } ?>
                                                            </div>
                                                        </div>




                                                        <div class="col-sm-12"><br>
                                                            <h3 class="title" align="center" style="margin-bottom: -15px;">Estadias
                                                                Anteriores</h3>
                                                            <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />
                                                            <div class="col-sm-12" style="height: 155px; overflow-y: auto; overflow-x: hidden;"><br>

                                                                <table class="table table-hover table-striped width-full">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Data</th>
                                                                            <th>Descrição</th>
                                                                            <th>Laudo</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <body>
                                                                        <?php
                                                                        include('conexao.php');
                                                                        $stmt = "select dat_cad, paciente_id, sigla, transacao from atendimentos a left join unidade_atendimento b on a.local=b.unidade_id where paciente_id=" . $prontuario . " and a.transacao<>" . $transacao;
                                                                        $sth = pg_query($stmt) or die($stmt);
                                                                        //echo $stmt;
                                                                        while ($row = pg_fetch_object($sth)) {
                                                                            echo "<tr>";
                                                                            echo "<td>" . inverteData(substr($row->dat_cad, 0, 10)) . "</td>";
                                                                            echo "<td>UPA SÃO BENEDITO</td>";
                                                                            echo "<td><a href='atendimentoclinico.php?id=" . $row->transacao . "&estadia=1' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a></td>";
                                                                            echo "</tr>";
                                                                        }
                                                                        ?>
                                                                    </body>
                                                                </table>
                                                            </div>

                                                        </div>
                                                        <div id="receit">
                                                            <div class="col-sm-12"><br>
                                                                <h3 class="title" align="center" style="margin-bottom: -15px;">Receituário
                                                                </h3>
                                                                <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" />
                                                                <div class="col-sm-12" style="height: 155px; overflow-y: auto; overflow-x: hidden;"><br>

                                                                    <table class="table table-hover table-striped width-full">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Itens/Medicamentos</th>
                                                                                <th width="20%">Quantidade</th>
                                                                                <th>Modo de usar</th>
                                                                            </tr>
                                                                        </thead>

                                                                        <body>
                                                                            <?php
                                                                            $stmt = "select medicamentos,quantidade,modo_usar 
																from receituario_remedio 
																where transacao = $transacao";
                                                                            $sth = pg_query($stmt) or die($stmt);
                                                                            //echo $stmt;
                                                                            while ($row = pg_fetch_object($sth)) {
                                                                                echo "<tr>";
                                                                                echo "<td>" . $row->medicamentos . "</td>";
                                                                                echo "<td>" . $row->quantidade . "</td>";
                                                                                echo "<td>" . $row->modo_usar . "</td>";
                                                                                echo "</tr>";
                                                                            }
                                                                            ?>
                                                                        </body>
                                                                    </table>
                                                                </div>

                                                                <div class="col-sm-12 text-center margin-top-10">
                                                                    <?php
                                                                    include('conexao.php');
                                                                    $stmt = "select count(*) as qtd from receituario_remedio where transacao =" . $transacao;
                                                                    $sth = pg_query($stmt) or die($stmt);
                                                                    //echo $stmt;
                                                                    $row = pg_fetch_object($sth);
                                                                    echo '<a href="relReceituario.php?transacao=' . $transacao . '" target="_blank" class="btn btn-success">Imprimir Receituário</a>';
                                                                    ?>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <input id="senha-autorizado" name="senha-autorizado" type="hidden" />
                                                </div>

                                    </form>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="modalEvolucao" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="exampleModalTabs">Evolução</h4>
                                                <div class="col-md-12" id="conteudoEvolucao">
                                                    dgbuyhdfbuyhbdfuygbdfub
                                                </div>
                                                <div class="col-md-12 margin-top-10 padding-0">
                                                    <div class="col-md-6">
                                                        <input type='button' name='novo_evolucao' id="novo_evolucao" class="btn btn-success width-full" value='Salvar'>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type='button' name='cancelarModal' id="cancelarModal" data-dismiss="modal" class="btn btn-danger width-full" value='Cancelar'>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->

                                <!-- Modal  laboratorio-->
                                <div class="modal fade" id="modalLaboratorio" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="exampleModalTabs">Exames Laboratoriais</h4>
                                                <div class="col-md-12" id="conteudoEvolucao">
                                                    <select data-placeholder="Escolha todos os exames" class="chosen-select" id="procedimento_laboratorio" multiple tabindex="4">
                                                        <?php

                                                        include('conexao_laboratorio.php');
                                                        if ($prioridade == 'AZUL' or $prioridade == 'VERDE') {
                                                            $sql = "select a.procedimentos_id as procedimento_id, a.descricao, a.codigo from procedimentos a
										inner join modalidades b on a.setor = b.modalidade_id
										inner join tabela_precos c on a.procedimentos_id = c.procedimento_id where modalidade_id not in (32 , 22) and a.procedimentos_id in (746, 769) and c.convenio_id = 1";
                                                        } else {
                                                            $sql = "select a.procedimentos_id as procedimento_id, a.descricao, a.codigo from procedimentos a
										inner join modalidades b on a.setor = b.modalidade_id where modalidade_id not in (32 , 22) and a.descricao not in('GLICOSE', 'PROTEINA C REATIVA - [ULTRA-SENSIVEL]')";
                                                        }
                                                        $sth = pg_query($sql) or die($sql);
                                                        echo "<option value=\"\">Selecione o Procedimento</option>";
                                                        while ($row = pg_fetch_object($sth)) {
                                                            echo "<option value=\"" . $row->procedimento_id . "\"";
                                                            echo ">" . $row->descricao . "</option>";
                                                        }

                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 margin-top-10 padding-0">
                                                    <div class="col-md-6">
                                                        <input type='button' name='soclab' id="soclab" class="btn btn-success width-full" value='Salvar'>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type='button' name='cancelarModal' id="cancelarModal" data-dismiss="modal" class="btn btn-danger width-full" value='Cancelar'>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->



                                <!-- Modal -->
                                <div class="modal fade" id="modaSolictalPrecricao" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="exampleModalTabs">Solictação de Prescrição</h4>
                                                <div class="col-md-12" id="conteudoSModal">
                                                    <div class="row">
                                                        <div class="col-sm-12" align="center">
                                                            <h4>Itens Solicitados</h4>
                                                        </div>


                                                        <div class="col-sm-12" align="center">

                                                            <table class="scroll table table-hover table-striped condensed width-full">
                                                                <thead>
                                                                    <tr class="width-full">
                                                                        <th><?php echo utf8_encode('Item'); ?></th>
                                                                        <th>Dosagem</th>
                                                                        <th>Via</th>
                                                                        <th>Aprazamento</th>
                                                                        <th></th>
                                                                    </tr>
                                                                </thead>


                                                                <tbody id="conteudoPrescricaoModal">

                                                                </tbody>

                                                            </table>


                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-12 margin-top-10 padding-0">

                                                    <div class="col-md-6">
                                                        <input type='button' onclick="modal_prescricao('<?= $_GET['id'] ?>')" id="prescricaoModal" value='Novo Item' class="btn btn-success width-full">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <input type='button' name='confirmar_prescricao' id="confirmar_prescricao" class="btn btn-success width-full" value='Confirmar Prescrição'>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->




                                <!-- Modal -->
                                <div class="modal fade" id="modalPrecricao" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="exampleModalTabs">Prescrição</h4>
                                                <div class="col-md-12" id="conteudoModal"></div>
                                                <div class="col-md-12 margin-top-10 padding-0">
                                                    <div class="col-md-6">
                                                        <input type='button' name='novo_prescricao' id="novo_prescricao" class="btn btn-success width-full" value='Solicitar'>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type='button' name='cancelarModal' id="cancelarModal" data-dismiss="modal" class="btn btn-danger width-full" value='Cancelar'>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->




                                <!-- Modal -->
                                <div class="modal fade" id="modalSolicitaReceituario" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                                <h4 class="modal-title" id="exampleModalTabs">Receituário</h4>
                                            </div>
                                            <div class="modal-body" id='modalbody'>
                                                <div class="row">
                                                    <div id="bloco_receituario">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">Item/Medicamento</label>
                                                                <input id="medicamento-1" maxlength="100" name="medicamento-1" class="form-control" value="" onkeyup="maiuscula(this)">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label class="control-label">Quantidade</label>
                                                                <input id="quantidade-1" maxlength="50" name="quantidade-1" class="form-control" value="" onkeyup="maiuscula(this)">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label class="control-label">Modo de usar</label>
                                                                <input id="usar-1" name="usar-1" maxlength="50" class="form-control" value="" onkeyup="maiuscula(this)">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div id="botao">
                                                        <div class="col-md-12" style="text-aling: center">
                                                            <input type='button' style="margin: 0 auto;" id="novo_receituario" class="btn btn-success" value="Adicionar Item">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" id="salvar_receituario" value="" class="btn btn-default" onclick="salvar_prescricao(this)">Salvar</button>
                                                <button type="button" id="closemodal" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                            </div>



                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->







                                <!-- Modal -->
                                <div class="modal fade" id="exampleTabs" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog" tabindex="-1">
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
                                                                <input type="text" name="data_atendimento" id="data_atendimento" class="form-control" value="<?php echo date('d/m/Y'); ?>" onKeyPress="formata(this,'##/##/####')" maxlength="10" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Hora do atendimento</label>
                                                                <input type="text" name="hora_atendimento" id="hora_atendimento" class="form-control" value="<?php echo $hora_transacao; ?>" onKeyPress="formata(this,'##:##')" maxlength="5" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Dias de atestado</label>
                                                                <input type="text" name="dias_atestado" id="dias_atestado" class="form-control" value="">
                                                            </div>
                                                        </div>


                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">CID</label>
                                                                <input type="text" name="cidAtestado" id="cidAtestado" class="form-control" value="<?php echo $CID; ?>" onkeyup="maiuscula(this)" readonly>
                                                            </div>
                                                        </div>

                                                    </div>


                                                </div>
                                                <div class="modal-footer">
                                                    <input type="hidden" name="atendimento" id="atendimento" value="<?= $_GET['id'] ?>">
                                                    <input type="hidden" name="profissional" id="profissional" value="<?php echo $usuario ?>">
                                                    <input type="hidden" name="paciente" id="paciente" value="<?php echo $prontuario ?>">

                                                    <button type="submit" name="enviar" class="btn btn-default">Imprimir</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>

                                                </div>
                                            </form>


                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
    <!-- </div> -->

    <script src="app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script>
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
    <script>
        var contador = 2;
        $("#novo_receituario").click(function(event) {
            $('#bloco_receituario').prepend('<div id="item-' + contador +
                '"><div class="col-md-4"><div class="form-group"><label class="control-label">Medicamento</label><input id="medicamento-' +
                contador +
                '" class="form-control" value="" maxlength="100" onkeyup="maiuscula(this)"></div></div><div class="col-md-2"><div class="form-group"><label class="control-label">Quantidade</label><input id="quantidade-' +
                contador +
                '" maxlength="50" class="form-control" value="" onkeyup="maiuscula(this)"></div></div><div class="col-md-5"><div class="form-group"><label class="control-label">Modo de usar</label><input id="usar-' +
                contador +
                '" maxlength="50" class="form-control" value="" onkeyup="maiuscula(this)"></div></div><div class="col-md-1"><div class="form-group"><button onclick="apagar_item_receituario(this)" value="' +
                contador + '" class="btn" style="margin-top: 28px">X</button></div></div></div>');
            $("#salvar_receituario").attr("value", contador);
            contador++;
        });

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
            }

        }

        $("#gravar").click(function() {

            swal({
                    title: "Solicite a assinatura do paciente na Ficha de Atendimento",
                    text: "",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: '#428bca',
                    confirmButtonText: 'OK',
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {

                    if (isConfirm) {


                        if (document.getElementById('evolucao')) {
                            if (document.getElementById('evolucao').value == '') {
                                document.getElementById('evolucao').focus();
                                sweetAlert("Dados incompletos!", "Informe a evolucao", "error");
                            } else {
                                $("#pedido").submit();
                            }
                        } else {
                            $("#pedido").submit();
                        }

                    }
                });

        })



        //////////////////////////INICIO LOGICA MODULO DE PRESCRIÇÃO//////////////////////////
        function solicitacaoprescricao(valor) { //ABRE O MODAL ONDE É LISTADO OS MEDICAMENTOS QUE ESTÁ GRAVADO NA SESSION
            $('#modaSolictalPrecricao').modal('toggle');
            $.get('listaprescricao.php?atendimento=<?php echo $_GET['id'] ?>', function(dataReturn) {
                $('#conteudoPrescricaoModal').html(dataReturn);
            });
        }

        function modal_prescricao(
            valor) { //FUNÇÃO PARA ABRIR O MODAL ONDE É REALIZADO A ESCOLHA DO TIPO DE PRESCRIÇÃO: DIETA, HIDRATAÇÃO, MEDICAMENTOS E CUIDADOS
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
            event) { //FUNÇÃO PARA SALVAR OS MEDICAMENTOS NA SESSION E POSTERIORMENTE LISTAR O QUE FOI SOLICITADO
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
                    descricao_hd + '&componente1=' + componente1 + '&componente2=' + componente2 + '&componente3=' +
                    componente3 + '&hidratacao_text=' + hidratacao_text + '&medico=' + medico +
                    '&tipo_prescricao=' + tipo_prescricao;

            } //SALVAPRESCRICAOSISTEMA.PHP: SALVA OS MEDICAMENTOS NA SESSION		

            $.get(url, function(dataReturn) {
                $.get('listaprescricao.php?atendimento=<?php echo $_GET['id'] ?>', function(Return) {
                    $('#conteudoPrescricaoModal').html(Return);
                });
            }); //LISTAPRESCRICAO.PHP: LISTA OS MEDICAMENTOS SALVOS NA SESSION

            event.preventDefault();

            var modal = $(this).closest('#modalPrecricao');
            $(modal).modal('hide');
        });


        $("#confirmar_prescricao").click(function(event) { //FUNÇÃO É CHAMANDA QUANDO O BOTAO CONFIRMAR PRESCRICAO É CLICADO

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
                var data = '<?php echo date('Y-m-d'); ?>';
                var hora = '<?php echo date('H:i'); ?>';


                while (i <= total_campos) {

                    tipo[i] = $('#tipo_presc' + i + '').val();

                    if ($('#cuidados' + i + '').val() != undefined) {

                        cuidados[i] = $('#cuidados' + i + '').val();

                        url = 'gerarnovaprescricao_clinico.php?cuidados=' + cuidados[i] + '&atendimento=' +
                            <?php echo $_GET['id'] ?> + '&flag=' + i + '&nova_sequencia=' + nova_sequencia +
                            '&data=' + data + '&hora=' + hora + '&tipo_prescricao=' + tipo[i];

                    } else if ($('#hidratacao_text' + i + '').val() != undefined) {



                        hidratacao_text[i] = $('#hidratacao_text' + i + '').val();
                        componente1[i] = $('#componente1' + i + '').val();
                        componente2[i] = $('#componente2' + i + '').val();
                        componente3[i] = $('#componente3' + i + '').val();
                        descricao_hd[i] = $('#descricao_hd' + i + '').val();
                        url = 'gerarnovaprescricao_clinico.php?hidratacao_text=' + hidratacao_text[i] +
                            '&componente1=' + componente1[i] + '&componente2=' + componente2[i] + '&componente3=' +
                            componente3[i] + '&descricao_hd=' + descricao_hd[i] + '&atendimento=' +
                            <?php echo $_GET['id'] ?> + '&flag=' + i + '&nova_sequencia=' + nova_sequencia +
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
                            medicamento[i] + '&via=' + via[i] + '&aprazamento=' + aprazamento[i] + '&atendimento=' +
                            <?php echo $_GET['id'] ?> + '&flag=' + i + '&cod_medicamento=' + cod_medicamento[i] +
                            '&nova_sequencia=' + nova_sequencia + '&data=' + data + '&hora=' + hora +
                            '&tipo_prescricao=' + tipo[i];
                    } //GERANOVAPRESCRICAO_CLINICI.PHP: SALVA NO BANCO DE DADOS O QUE ESTA SALVO NA SESSION

                    $.get(url, function(dataReturn) {
                        $('#conteudoPrescricao').html(dataReturn);

                    });
                    i++;
                }

                //LISTA_PRESCRICAO_CLINICO.PHP: RETORNA PARA DENTRO DA DIV COM ID=modaSolictalPrecricao OS MEDICAMENTOS SALVOS NO BANCO
                urls = 'lista_prescricao_clinico.php?atendimento=<?php echo $_GET['id'] ?>';

                $('#modaSolictalPrecricao').modal('hide');
            }

        });

        function remover_prescricao(
            indice) { //FUNCÃO É CHAMADA CLICA EM DELETAR UM MEDICAMENTO QUE ESTA LA LISTAGEM DE MEDICAMENTOS, ISTO PRESENTE NA SESSION
            $.get('exlcuirIndiceArray.php?indice=' + indice);

            $.get('listaprescricao.php?atendimento=<?php echo $_GET['id'] ?>&flag=1', function(Return) {
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
                    window.open('relprescricao.php?id=<?php echo $_GET['id'] ?>&medicamentos=' + prescricoes, '_blank');
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
            var data_atendimento = '<?php echo date('d/m/Y'); ?>';
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
                $.get('solicitapedido.php?paciente_id=' + id + '&prioridade=' + prioridade + '&data_atendimento=' +
                    data_atendimento + '&atendimento=' + atendimento + '&profissional=' + profissional +
                    '&prontuario=' + prontuario + '&procedimento=' + procedimento + '&origem=' + origem,
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

        $("#soclab").click(function() {
            var data_atendimento = '<?php echo date('d/m/Y'); ?>';
            var atendimento = $('#atendimento').val();
            var prioridade = $('#prioridade').val();
            var profissional = $('#profissional').val();
            var prontuario = $('#paciente').val();
            var procedimento = $('#procedimento_laboratorio').val();
            var origem = $('#origem').val();
            var id = $('#id').val();
            var retorno = 'N';

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

            swal({
                    title: "ATENÇÃO!!!",
                    text: "Deseja pedir somente esses exames? Se não por favor acrescente os demais desejaveis.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Sim, Desejo finalizar!",
                    closeOnConfirm: false
                },
                function() {
                    $.get('solicitapedidoxj.php?paciente_id=' + id + '&prioridade=' + prioridade +
                        '&data_atendimento=' + data_atendimento + '&atendimento=' + atendimento +
                        '&profissional=' + profissional + '&prontuario=' + prontuario + '&procedimento=' +
                        procedimento + '&origem=' + origem,
                        function(dataReturn, status) {
                            $('#exames_laboratorio').html(dataReturn);
                        });
                    swal("Finalizado!", "Exames solicitados ao laboratorio.", "success");
                    $('#modalLaboratorio').modal('hide');
                });


        });


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
                '&hora_entrada=' + hora_entrada + '&hora_saida=' + hora_saida + '&finalidade=' + finalidade +
                '&descricao=' + descricao + '&atendimento=' + atendimento + '&profissional=' + profissional,
                function(dataReturn) {
                    $('#modalbody').html(dataReturn);
                });
            event.preventDefault();

            $("#pedido").submit();

        });


        $("#procedimento").chosen({
            placeholder_text_single: "Selecione...",
            search_contains: true
        });
        $(document).ready(function() {
            $('#procedimento').chosen.change(function() {
                var cbo = document.getElementById("procedimento");
                var codigotabela = cbo.options[cbo.selectedIndex].text;
                document.getElementById("codigo").value = codigotabela.substring(0, 8);
                document.getElementById("qtde").value = '01';


            });
        });

        $("#medicamento").chosen({
            placeholder_text_single: "Selecione...",
            search_contains: true
        });


        $("#CID").blur(function() {
            var codcid = $('#CID').val();
            var url = 'ajax_buscar_cid.php?cid=' + codcid;
            $.get(url, function(dataReturn) {
                $('#diag_pri').val(dataReturn);
            });
        });
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
            var nome = document.getElementById("nome").value;
            var cns = document.getElementById("cns").value;
            var idade = document.getElementById("idade").value;
            var prontuario = document.getElementById("prontuario").value;
            var url = 'ajax_tabela_atendimento.php?transacao=' + a + '&nome=' + nome + '&cns=' + cns + '&idade=' + idade +
                '&prontuario=' + prontuario;
            $.get(url, function(dataReturn) {
                $('#table').html(dataReturn);
            });
        }

        $("select").chosen({
            width: "100%"
        });
    </script>
</body>

</html>