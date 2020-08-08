<?php
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}



function validaCPF($cpf = null)
{

    // Verifica se um número foi informado
    if (empty($cpf)) {
        return false;
    }

    // Elimina possivel mascara
    $cpf = ereg_replace('[^0-9]', '', $cpf);
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
error_reporting(0);
include('verifica.php');
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
    $texto = "";

    if ($transacao != "") {
        include('conexao.php');
        $stmt = "select a.transacao, a.paciente_id, a.status, a.tipo, a.dat_cad as cadastro, c.nome, c.nome_social, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco, a.acompanhante,
		a.oque_faz, a.com_oqfaz, a.tempo_faz, a.como_faz, c.nome_mae, c.numero, c.complemento, c.bairro, c.cep, c.num_carteira_convenio as cns, c.cidade, c.estado, a.observacao, k.origem,
		c.identidade, c.org_expeditor,c.cpf, a.coronavirus
		from atendimentos a
		left join pessoas c on a.paciente_id=c.pessoa_id
		left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) where a.transacao=$transacao";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $data_transacao = substr($row->cadastro, 0, 10);
        $hora_transacao = $row->hora_transacao;
        $prontuario = $row->paciente_id;
        $sexo = $row->sexo;
        $nome = $row->nome;
        $nome_social = $row->nome_social;
        $nomeMae = $row->nome_mae;
        $dt_nascimento = inverteData($row->dt_nasc);
        $sexo = $row->sexo;
        $enderecox = $row->endereco;
        $end_numero = $row->numero;
        $complemento = $row->complemento;
        $bairro = $row->bairro;
        $cidade = $row->cidade;
        $estado = $row->estado;
        $cep = $row->cep;
        $cns = $row->cns;
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
        $observacao  = $row->observacao;
        $oque_faz  = $row->oque_faz;
        $com_oqfaz = $row->com_oqfaz;
        $tempo_faz = $row->tempo_faz;
        $como_faz  = $row->como_faz;
        $enfermaria = $row->enfermaria;
        $leito = $row->leito;
        $imagem = $row->imagem;
        $origem = $row->tipo;
        $status = $row->status;
        $nome_acompanhante = $row->acompanhante;
        $identidade = $row->identidade;
        $org_expeditor = $row->org_expeditor;
        $cpf = $row->cpf;
        $coronavirus = $row->coronavirus;
    } else {
        $data_transacao = date('Y-m-d');
        $hora_transacao = date('H:i');
        $usuario_transacao = $usuario;

        if ($senha != "") {
            include('conexao.php');
            $stmt = "select a.senha, b.convenio_id, b.pessoa_id,  b.profissional_id, c.nome, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco,
			c.numero, c.complemento, c.bairro, c.cep, c.cpf, c.cidade, c.estado, b.procedimento_id, d.tipo, (select count(*) from ocorrencias e where
			b.pessoa_id=e.pessoa_id and situacao='Pendente') as qtde from painel_senhas a left join agendamentos b on a.agendamento_id=b.agendamento_id
			left join pessoas c on b.pessoa_id=c.pessoa_id left join convenios d on b.convenio_id=d.convenio_id where senha='$senha'";
            $sth = pg_query($stmt) or die($stmt);
            $row = pg_fetch_object($sth);

            $prontuario = $row->pessoa_id;
            $sexo = $row->sexo;
            $dum = $row->DUM;
            $peso = $row->peso;
            $nome = $row->nome;
            $dt_nascimento = inverteData($row->dt_nasc);
            $sexo = $row->sexo;
            $enderecox = $row->endereco;
            $end_numero = $row->numero;
            $complemento = $row->complemento;
            $bairro = $row->bairro;
            $cidade = $row->cidade;
            $estado = $row->estado;
            $cep = $row->cep;
            $cpf = $row->cpf;
            $telefone = $row->telefone;
            $celular = $row->celular;
            $pe = $row->celular;
            $observacao  = $row->observacao;
            $oque_faz  = $row->oque_faz;
            $com_oqfaz = $row->com_oqfaz;
            $tempo_faz = $row->tempo_faz;
            $como_faz  = $row->como_faz;
            $dt_nasc   = $row->dt_nasc;
            $date = new DateTime($dt_nasc); // data de nascimento
            $interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
            $idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias

        }
        if ($agendamento != "") {
            include('conexao.php');
            $stmt = "select a.agendamento_id, a.convenio_id, e.tipo, a.situacao, a.horario, a.data, a.usuario_agendamento, a.sala_id, a.procedimento_id, a.pessoa_id, a.profissional_id, b.nome, b.sexo, b.dt_nasc,
			b.endereco, b.numero, b.num_carteira_convenio, b.complemento, b.bairro, b.cidade, b.cpf, b.estado, b.cep, b.telefone, b.celular, c.descricao as procedimento, d.nome as profissional 
			from agendamentos a left join pessoas b on a.pessoa_id=b.pessoa_id left join procedimentos c on a.procedimento_id=c.procedimento_id	
			left join pessoas d on a.profissional_id=d.pessoa_id left join convenios e on a.convenio_id=e.convenio_id where a.agendamento_id = $agendamento";
            $sth = pg_query($stmt) or die($stmt);
            $row = pg_fetch_object($sth);

            $prontuario = $row->pessoa_id;
            $sexo = $row->sexo;
            $dum = $row->DUM;
            $peso = $row->peso;
            $nome = $row->nome;
            $dt_nascimento = inverteData($row->dt_nasc);
            $sexo = $row->sexo;
            $enderecox = $row->endereco;
            $end_numero = $row->numero;
            $complemento = $row->complemento;
            $bairro = $row->bairro;
            $cidade = $row->cidade;
            $estado = $row->estado;
            $cep = $row->cep;
            $cpf = $row->cpf;
            $telefone = $row->telefone;
            $celular = $row->celular;
            $num_carteirinha = $row->num_carteira_convenio;
            $hr_agenda = $row->horario;
            $med_executante = $row->profissional_id;
            $pe = $row->celular;
            $dt_nasc = $row->dt_nasc;
            $date = new DateTime($dt_nasc); // data de nascimento
            $interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
            $idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias

        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transacao = $_POST['transacao'];
    $senha = $_POST['senha'];
    $data_transacao = $_POST['data_transacao'];
    $hora_transacao = $_POST['hora_transacao'];
    $usuario_transacao = $_POST['usuario_transacao'];
    $acao = $_POST['acao'];
    $idade = $_POST['idade'];
    $sexo  = $_POST['sexo'];
    $prontuario = $_POST['prontuario'];
    $nome = $_POST['nome'];
    $dt_nascimento = $_POST['dt_nascimento'];
    $enderecox = $_POST['endereco'];
    $end_numero = $_POST['end_num'];
    $complemento = $_POST['end_comp'];
    $bairro = $_POST['end_bairro'];
    $cidade = $_POST['end_cidade'];
    $estado = $_POST['end_uf'];
    $cep = $_POST['end_cep'];
    $cns = $_POST['cns'];
    $telefone = $_POST['telefone'];
    $celular = $_POST['celular'];
    $deficiencia = $_POST['deficiencia'];
    $observacao = $_POST['observacao'];
    $origem = $_POST['origem'];
    $enfermaria = $_POST['enfermaria'];
    $leito = $_POST['leito'];
    $oque_faz = $_POST['oque_faz'];
    $com_oqfaz = $_POST['com_oqfaz'];
    $tempo_faz = $_POST['tempo_faz'];
    $como_faz = $_POST['como_faz'];
    $nomeMae  = $_POST['nomeMae'];
    $situacao = $_POST['situacao'];
    $imagem = $_POST['imagem'];
    $nome_social = $_POST['nome_social'];
    $nome_acompanhante = $_POST['nome_acompanhante'];
    $org_expeditor = $_POST['org_expeditor'];
    $cpf = $_POST['cpf'];
    $identidade = $_POST['rg'];
    //$validaCPF = validaCPF($cpf);
    // $coronavirus = $_POST['coronavirus'];

    if (isset($_POST['coronavirus'])) {
        $coronavirus = 1;
    } else {
        $coronavirus = 0;
    }

    if ($origem == "") {
        $erro = "Origem deve ser Informado";
    }
    if ($dt_nascimento == "") {

        $erro = "Data Nascimento deve ser Informado";
    }
    $dt_nasc = substr($dt_nascimento, 6, 4) . "-" . substr($dt_nascimento, 3, 2) . "-" . substr($dt_nascimento, 0, 2);
    if ($dt_nasc > date('Y-m-d')) {
        $erro = "Data Nascimento Incorreta";
    }

    if ($cns == "") {
        $erro = "Catao SUS deve ser Informado";
    }

    if ($sexo == "") {
        $erro = "Sexo deve ser Informado";
    }

    // if ($validaCPF == false && $cpf != '') {
    //     $erro = "CPF inválido";
    // }

    if ($prontuario == "") {
        $erro = 'Paciente deve ser Informado';
    } else {
        if ($enderecox == "" or $end_numero == "" or $bairro == "" or $cidade == "" or $estado == "") {
            $erro = 'O endereço completo deve ser Informado';
        }
        if ($erro == "") {
            include('conexao.php');
            $stmt = "Update pessoas set nome='$nome',cpf='$cpf',identidade='$identidade',org_expeditor='$org_expeditor', sexo='$sexo', dt_nasc='" . inverteData($dt_nascimento) . "', endereco='$enderecox', numero='$end_numero', complemento='$complemento', bairro='$bairro', cidade='$cidade',
			estado='$estado', cep='$cep', telefone='$telefone', celular='$celular', num_carteira_convenio='$cns', nome_mae='$nomeMae', email='$email', imagem='$imagem', nome_social='$nome_social' where pessoa_id=$prontuario";
            $sth = pg_query($stmt);
        }
    }

    if (isset($_POST['gravar']) != '') {
        if ($erro == "") {
            $xdum = "";
            if ($transacao == "") {
                if ($peso == "") {
                    $peso = "0";
                } else {
                    $peso = str_replace(",", ".", $peso);
                }
                if ($dum != "") {
                    $xdum = inverteData($dum);
                }


                include('conexao.php');
                $stmt = "select nextval('atendimentos_transacao_seq');";
                $sth = pg_query($stmt) or die($stmt);
                $row = pg_fetch_object($sth);
                $transacao = str_pad($row->nextval, 7, "0", STR_PAD_LEFT);

                include('conexao.php');
                $dt_transacao = inverteData($data_transacao);
                $dt_solicitacao = inverteData($dt_nsolicitacao);
                $horacad = date('H:i');
                include('conexao.php');
                $stmtt = "SELECT * FROM atendimentos where dat_cad = '$dt_transacao' and hora_cad= '$horacad' and paciente_id = $prontuario";
                $stht = pg_query($stmtt) or die($stmtt);
                $rowt = pg_fetch_object($stht);
                if ($rowt->paciente_id == '') {
                    $stmt = "insert into atendimentos ( transacao, cad_user, dat_cad, paciente_id, tipo,  status,  box, hora_cad, local,   nec_especiais, idade, observacao, 
				oque_faz, tempo_faz, com_oqfaz, como_faz,acompanhante, coronavirus)
				values ($transacao, '$usuario_transacao', '$dt_transacao', $prontuario, '$origem', 'Aguardando Triagem',  '1',  '$horacad', '01',";
                    $stmt = $stmt . "  '$deficiencia', '$idade', '$observacao', '$oque_faz', '$tempo_faz', '$com_oqfaz', '$como_faz','$nome_acompanhante', $coronavirus);";
                    $sth = pg_query($stmt);
                }


                $data = date('Y-m-d');
                $hora = date('H:i');
                $ip = $_SERVER['REMOTE_ADDR'];
                include('conexao.php');
                $stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora, ip) 
						values ('$usuario','CADASTROU UM NOVO ATENDIMENTO','$transacao','$data','$hora', '$ip')";
                $sthLogs = pg_query($stmtLogs) or die($stmtLogs);

                /*
				if ($origem<>'6' && $origem<>'9' && $origem<>'10' && $origem<>'11')
				{	
					$dta_atendimento = date('Y-m-d H:i:s');
					$dt_nasc = substr($dt_nascimento,6,4)."-".substr($dt_nascimento,3,2)."-".substr($dt_nascimento,0,2);
					
					$client = new SoapClient('http://172.18.52.181:8085/WebServiceEmerges/IntegracaoWS?wsdl');
					$function = 'admissaoPaciente';
					$arguments= array('admissaoPaciente' => array(
											'nome'            => $nome,
											'dataNascimento'  => $dt_nasc,
											'sexo'            => $sexo,
											'nomeMae'         => $nomeMae,										
											'CARTAO_SUS'      => $cns,
											'PRONTUARIO'  	  => $prontuario,
											'ATENDIMENTO'	  => $transacao,									
											'DATA_ATENDIMENTO'=> $dta_atendimento			
									));
					$options = array('');
					$result = $client->__soapCall($function, $arguments, $option);
					$retorno = 'Mensagem ToLife:'.$result->return.$dta_atendimento.$dt_nasc;
				}
				*/
                header("location: atendimentos.php");
            } else {
                include('conexao.php');
                $dt_transacao = inverteData($data_transacao);
                $dt_transacao = inverteData($data_transacao);
                $dt_solicitacao = inverteData($dt_nsolicitacao);
                $horacad = date('H:i');
                $stmt = "update atendimentos set  transacao=$transacao, cad_user='$usuario_transacao',  paciente_id=$prontuario, tipo='$origem',  observacao='$observacao', box='1', hora_cad='$horacad', local='01',
				peso='0', nec_especiais='$deficiencia', oque_faz='$oque_faz', como_faz='$como_faz', tempo_faz='$tempo_faz', acompanhante = '$nome_acompanhante', com_oqfaz='$com_oqfaz', coronavirus = $coronavirus where transacao=$transacao ";
                $sth = pg_query($stmt) or die($stmt);

                $data = date('Y-m-d');
                $hora = date('H:i');
                include('conexao.php');
                $stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora) 
						values ('$usuario','ALTEROU INFORMAÇÃO DO CADASTRO DO ATENDIMENTO','$transacao','$data','$hora')";
                $sthLogs = pg_query($stmtLogs) or die($stmtLogs);
            }
        }
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
    <script language="Javascript">
        function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('endereco').value = ("");
            document.getElementById('bairro').value = ("");
            document.getElementById('cidade').value = ("");
            document.getElementById('uf').value = ("");
        }

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


        function mascaratel(campo) {
            campo.value = campo.value.replace(/[^\d]/g, '')
                .replace(/^(\d\d)(\d)/, '($1) $2')
                .replace(/(\d{4})(\d)/, '$1-$2');
            if (campo.value.length > 14) campo.value = stop;
            else stop = campo.value;
        }


        function mascara(str) {
            // Caso passe de 14 caracteres será formatado como CNPJ 
            if (str.value.length > 14)
                str.value = cnpj(str.value);
            // Caso contrário como CPF
            else
                str.value = cpf(str.value);
        }

        function maiuscula(z) {
            v = z.value.toUpperCase();
            z.value = v;
        }

        // Funcao de formatacao CPF
        function cpf(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valoralor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o terceiro e o quarto digito
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um ponto entre o terceiro e o quarto dígitos 
            // desta vez para o segundo bloco      
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

            // Adiciona um hifen entre o terceiro e o quarto dígitos
            valor = valor.replace(/(\d{3})(\d)$/, "$1-$2");
            return valor;
        }

        // Funcao de formatacao CNPJ
        function cnpj(valor) {
            // Remove qualquer caracter digitado que não seja numero
            valor = valor.replace(/\D/g, "");

            // Adiciona um ponto entre o segundo e o terceiro dígitos
            valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");

            // Adiciona um ponto entre o quinto e o sexto dígitos
            valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");

            // Adiciona uma barra entre o oitavaloro e o nono dígitos
            valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");

            // Adiciona um hífen depois do bloco de quatro dígitos
            valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
            return valor;
        }

        function formatar(mascara, documento) {
            var i = documento.value.length;
            var saida = mascara.substring(0, 1);
            var texto = mascara.substring(i)

            if (texto.substring(0, 1) != saida) {
                documento.value += texto.substring(0, 1);
            }
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
</style>

<body class="pace-done" cz-shortcut-listen="true">
    <!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

    <!-- <div class="wrapper"> -->
    <?php include('menu.php'); ?>
    <?php include('header.php'); ?>
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
                                                <li><a href="index.php">Home</a></li>
                                                <li><a href="atendimentos.php">Atendimentos</a></li>
                                                <li class="active">Novo Atendimento</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ($erro != "") {
                                echo '<div class="row">
		        <div class="col-sm-12">
								<strong>Erro:!</strong><br><li>' . $erro . '</li>
				</div>		
		  </div>';
                            } ?>
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="post" name='pedido' id='pedido' autocomplete="off" action="#">
                                        <div id="dados-paciente-div">
                                            <div class="col-12 text-center">
                                                <h4 class="form-section-center"><i class="ft-user"></i> Identificação do Paciente</h4>
                                                <!-- <h3 class="title" align="center">Identificação do Paciente</h3> -->
                                                <hr style="margin: auto;width: 350px">
                                            </div>

                                            <!-- DADOS PACIENTE -->
                                            <div class="row mt-4">
                                                <div class="col-2">
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div>
                                                                <?php
                                                                if ($imagem == "") {
                                                                    echo "<img id=\"blah\" src=\"app-assets/img/gallery/user-transp.png\"  alt=\"\" height=\"120\" width=\"130\" ondblclick=\" window.open('popcam/index.html', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=700'); return false;\">";
                                                                } else {
                                                                    echo "<img id=\"blah\" src=\"app-assets/img/gallery/user-transp.png\"         alt=\"\" height=\"120\" width=\"130\" ondblclick=\" window.open('popcam/index.html', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=700'); return false;\">";
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                                        <div class="col-2">
                                                            <!-- <label class="control-label">
                                                            <font color='red'>Paciente</font>
                                                        </label> -->
                                                            <div class="input-group ">
                                                                <span class="input-group-btn">
                                                                    <button type="button" class="btn btn-primary" onClick="window.open('poppac.php', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;" style="margin-left: 50px">
                                                                        <i class="fa fa-search" aria-hidden="true"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-10">
                                                    <div class="row"><input type="hidden" name="transacao" class="form-control" value="<?php echo $transacao; ?>" readonly><input type="hidden" name="data_transacao" class="form-control" value="<?php echo inverteData($data_transacao); ?>" readonly><input type="hidden" name="hora_transacao" class="form-control" value="<?php echo $hora_transacao; ?>" readonly><input type="hidden" name="senhac" class="form-control" value="<?php echo senhal; ?>" readonly><input type="hidden" name="usuario_transacao" class="form-control" value="<?php echo $usuario; ?>" readonly><input type="hidden" name="situacao" class="form-control" value="<?php echo $situacao; ?>" readonly>
                                                        <input type="hidden" class="form-control" name="prontuario" id="prontuario" placeholder="Paciente..." value='<?php echo $prontuario; ?>' readonly>
                                                        <input type="hidden" class="form-control" name="imagem" id="imagem" value="<?php echo $imagem; ?>" value='<?php echo $imagem; ?>' readonly>

                                                        <div class="col-sm-6">
                                                            <label class="control-label">Nome </label> <input type="text" name="nome" id="nome" class="form-control" style="font-weight: bold;" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)">
                                                        </div>

                                                        <div class="col-sm-6">
                                                            <label class="control-label">Nome Social</label> <input type="text" name="nome_social" id="nome_social" class="form-control" value="<?php echo $nome_social; ?>" style="font-weight: bold;" onkeyup="maiuscula(this)">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-9">
                                                            <label class="control-label">Nome do Acompanhante</label> <input type="text" name="nome_acompanhante" id="nome_acompanhante" class="form-control" value="<?php echo $nome_acompanhante; ?>" style="font-weight: bold;" onkeyup="maiuscula(this)">
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label class="control-label">
                                                                <font color='red'>CNS</font>
                                                            </label> <input type="text" name="cns" id="cns" class="form-control" value="<?php echo $cns; ?>" onkeypress='return SomenteNumero(event)'>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Nascimento</label> <input type="text" name="dt_nascimento" id="dt_nascimento" class="form-control" value="<?php echo $dt_nascimento; ?>" OnKeyPress="formatar('##/##/####', this)" OnBlur="calcularIdade(this.value)">
                                                        </div>

                                                        <div class="col-sm-1">
                                                            <label class="control-label">Idade</label> <input type="text" name="idade" id="idade" class="form-control" value="<?php echo $idade; ?>" readonly>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <label class="control-label">Sexo</label> <select name="sexo" id="sexo" class="form-control">
                                                                <option></option>
                                                                <option value="F" <?php if ($sexo == 'F') {
                                                                                        echo 'selected';
                                                                                    } ?>>Feminino</option>
                                                                <option value="M" <?php if ($sexo == 'M') {
                                                                                        echo 'selected';
                                                                                    } ?>>Masculino</option>
                                                            </select> <input type="hidden" name="pendencia" id="pendencia" class="form-control" value="<?php echo $pendencia; ?>" readonly> <input type="hidden" name="pendencia" id="pendencia" class="form-control" value="<?php echo $pendencia; ?>" readonly>
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <label class="control-label">CPF</label> <input type="text" name="cpf" maxlength="14" id="cpf" class="form-control" value="<?php echo $cpf; ?>">
                                                        </div>

                                                        <div class="col-sm-2">
                                                            <label class="control-label">RG</label> <input type="text" name="rg" id="rg" class="form-control" value="<?php echo $identidade; ?>">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Expedição</label> <input type="text" name="org_expeditor" id="org_expeditor" class="form-control" OnKeyPress="formatar('##/##/####', this)" value="<?php echo $org_expeditor; ?>"><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label class="control-label">
                                                        <font color='red'>Telefone</font>
                                                    </label> <input type="text" name="telefone" class="form-control" value="<?php echo $telefone; ?>" OnKeyPress="formatar('##-########', this)" maxlength="11">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">Celular</label> <input type="text" name="celular" id="celular" class="form-control" value="<?php echo $celular; ?>" OnKeyPress="formatar('##-#########', this)" maxlength="12">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label class="control-label">
                                                        <font color='red'>Nome da Mae</font>
                                                    </label> <input type="text" name="nomeMae" id="nome_mae" class="form-control" value="<?php echo $nomeMae; ?>" onkeyup="maiuscula(this)">
                                                </div>


                                                <div class="col-sm-4">
                                                    <label class="control-label">
                                                        <font color='red'>Origem</font>
                                                    </label>
                                                    <select class="form-control" name="origem" id="origem" onChange="showenfermaria(this.value)">
                                                        <option value=""></option>;
                                                        <?php
                                                        include('conexao.php');
                                                        $stmt = "Select * from tipo_origem where situacao='0' order by atendimento";
                                                        $sth = pg_query($stmt) or die($stmt);
                                                        while ($row = pg_fetch_object($sth)) {
                                                            echo "<option value=\"" . $row->tipo_id . "\"";
                                                            if ($row->tipo_id == $origem) {
                                                                echo "selected";
                                                            }
                                                            echo ">" . $row->origem . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mt-3 mb-3 align-items-center">
                                                <div class="col-6">
                                                    <label class="control-label">Necessidades Especiais</label> <select name="deficiencia" class="form-control">
                                                        <option value="Nenhuma">Nenhuma</option>
                                                        <option value="Visual">Visual</option>
                                                        <option value="Motora">Motora</option>
                                                        <option value="Mental">Mental</option>
                                                        <option value="Auditiva">Auditiva</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-checkbox ">
                                                        <input type="checkbox" class="custom-control-input" name="coronavirus" id="coronavirus" value='CM' <?php if ($coronavirus == 1) echo "checked"; ?>>
                                                        <label class="custom-control-label" style="font-size: 10pt" for="coronavirus">Problema Respirátorio</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- <div class="col-11 text-center mt-3">
                                                        <h4 class="form-section-center"><i class="far fa-map"></i> Endereço do Paciente</h4>
                                                        <hr style="margin: auto;width: 350px">
                                                    </div> -->

                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label class="control-label">CEP</label> <input type="text" name="end_cep" id="end_cep" class="form-control" maxlength="9" value="<?php echo $cep; ?>" OnKeyPress="formatar('#####-###', this)" onblur="pesquisacep(this.value);">
                                                </div>
                                                <div class="col-sm-5">
                                                    <label class="control-label">Endereço</label> <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo $enderecox; ?>">
                                                </div>

                                                <div class="col-sm-1">
                                                    <label class="control-label">Numero</label> <input type="text" name="end_num" id="end_num" class="form-control" value="<?php echo $end_numero; ?>">
                                                </div>

                                                <div class="col-sm-4">
                                                    <label class="control-label">Complemento</label> <input type="text" name="end_comp" id="end_comp" class="form-control" value="<?php echo $complemento; ?>">
                                                </div>
                                            </div>

                                            <!-- <hr> -->

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label class="control-label">Bairro</label> <input type="text" name="end_bairro" id="end_bairro" class="form-control" value="<?php echo $bairro; ?>">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="control-label">Cidade</label> <input type="text" name="end_cidade" id="end_cidade" class="form-control" value="<?php echo $cidade; ?>">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">UF</label>
                                                    <input type="text" name="end_uf" id="end_uf" class="form-control" value="<?php echo $estado; ?>" maxlength="2" onkeyup="maiuscula(this)">
                                                </div>

                                            </div>

                                        </div>
                                        <!-- <hr style="width: 100%; color: gray; height: 1px; background-color: gray;" /> -->
                                        <input id="usuario-autorizado" name="usuario-autorizado" type="hidden" />
                                        <input id="senha-autorizado" name="senha-autorizado" type="hidden" />

                                        <div class="col-md-12 mt-3" align="center">
                                            <div class="form-group">
                                                <input type='submit' name='gravar' onclick="return valida()" id='gravar' class="btn btn-primary" value='Gravar'>
                                                <input type='button' name='xcancelar' class="btn btn-danger" value='Cancelar' onclick="javascript:location.href='http://mr.midaspa.com.br/mr/funepu/atendimentos.php'">
                                            </div>
                                        </div>


                                        <!-- FINAL DADOS PACIENTE -->
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
                                                $stmt = "SELECT a.tipo_doc_id, a.descricao, a.data_arquivo, a.usuario, a.arquivo, b.descricao as tipo 
                                                            FROM arquivos_documentos a, tipo_documentos b where a.tipo_doc_id=b.tipo_doc_id and transacao=$transacao and arquivo is not null 
                                                            order by data_arquivo ";
                                                $sth = pg_query($stmt) or die($stmt);
                                                while ($row = pg_fetch_object($sth)) {
                                                    $x = $x + 1;
                                                    echo "<tr>";
                                                    echo "<td>" . inverteData($row->data_arquivo) . "</td>";
                                                    echo "<td>" . $row->tipo . "</td>";
                                                    echo "<td>" . $row->descricao . "</td>";
                                                    echo "<td>" . $row->usuario . "</td>";
                                                    echo "<td><a href='imagens/documentos/" . $row->arquivo . "' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a></td>";
                                                    echo "</tr>";
                                                    $total_recebido = $total_recebido + $row->valor;
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
                                    </form>
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
            $(document).ready(function() {
                $('#pedido').on('focus', ':input', function() {
                    $(this).attr('autocomplete', 'off');
                });
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
                            var sHtmlI = '<i title="PendÃªncia autorizada!" class="icon fa-check has-warning" aria-hidden="true" style="margin-left: 1em; cursor:pointer; font-size: 1.2em;"></i>';

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

            function valida() {

                var nome = $("#nome").val();
                var dt_nascimento = $("#dt_nascimento").val();
                var endereco = $("#endereco").val();
                var numero = $("#numero").val();
                var bairro = $("#bairro").val();
                var cidade = $("#cidade").val();
                var estado = $("#estado").val();
                var cep = $("#cep").val();
                var cns = $("#cns").val();
                var dt_solicitacao = $("#dt_solicitacao").val();
                var solicitante = $("#solicitante").val();
                var nomesolicitante = $("#nomesolicitante").val();
                var convenio = $("#convenio").val();
                //var tipoconv      = convenio.substr(0, 1);
                var carteirinha = $("#carteirinha").val();
                var nomeMae = $("#nomeMae").val();

                if (nome == "") {
                    pedido.nome.focus();
                    sweetAlert("Dados incompletos!", "Insira um Nome", "error");
                    return false;
                } else if (nomeMae == "") {
                    pedido.nomeMae.focus();
                    sweetAlert("Dados incompletos!", "Insira o Nome da Mãe do Paciente", "error");
                    return false;
                } else if (dt_nascimento == "") {
                    pedido.dt_nascimento.focus();
                    sweetAlert("Dados incompletos!", "Insira uma Data de Nascimento", "error");
                    return false;
                } else if (endereco == "") {
                    pedido.endereco.focus();
                    sweetAlert("Dados incompletos!", "Insira um Endereço", "error");
                    return false;
                } else if (numero == "") {
                    pedido.end_num.focus();
                    sweetAlert("Dados incompletos!", "Insira o número no endereço", "error");
                    return false;
                } else if (bairro == "") {
                    pedido.end_bairro.focus();
                    sweetAlert("Dados incompletos!", "Informe o bairro no endereço", "error");
                    return false;
                } else if (cidade == "") {
                    pedido.end_cidade.focus();
                    sweetAlert("Dados incompletos!", "Informe a Cidade no endereço", "error");
                    return false;
                } else if (estado == "") {
                    pedido.end_uf.focus();
                    sweetAlert("Dados incompletos!", "Informe a UF no endereço", "error");
                    return false;
                } else if (solicitante == "") {
                    pedido.solicitante.focus();
                    sweetAlert("Dados incompletos!", "Informe o Solicitante", "error");
                    return false;
                } else if (nomesolicitante == "") {
                    pedido.nomesolicitante.focus();
                    sweetAlert("Dados incompletos!", "Informe o Nome do Solicitante", "error");
                    return false;
                } else if (convenio == "") {
                    pedido.carteirinha.focus();
                    sweetAlert("Dados incompletos!", "Informe o Convênio da Solicitação", "error");
                    return false;
                } else {
                    $("#loading-hover").css("z-index", "1");
                    return true;
                }

            }
        </script>
</body>

</html>