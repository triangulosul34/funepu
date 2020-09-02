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
        $stmt = "select b.convenio_id, b.paciente_id, b.profissional_id,  c.imagem, b.dat_cad, b.DUM, b.peso, c.nome, c.dt_nasc, c.sexo, c.telefone,
		c.celular, c.endereco, c.numero, c.email, b.num_senha, b.hora_cad, b.idade,b.dt_solicitacao, b.solicitante_id, b.enfermaria, b.leito, b.tipo as tipopac, 
		e.nome as nome_solicitante, e.telefone as tel_solicitante, e.celular as cel_solicitante, e.email as email_solicitante, b.nec_especiais, b.num_socio, 
		c.complemento, c.bairro, c.cep, c.cpf, c.cidade, c.estado, d.tipo from pedidos b left join pessoas c on b.paciente_id=c.pessoa_id 
		left join convenios d on b.convenio_id=d.convenio_id left join solicitantes e on b.solicitante_id=e.solicitante_id 
		where b.transacao=$transacao";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $data_transacao = substr($row->dat_cad, 0, 10);
        $prontuario = $row->paciente_id;
        $sexo = $row->sexo;
        $dum = $row->DUM;
        $peso = $row->peso;
        $convenio = $row->convenio_id;
        $nome = $row->nome;
        $email = $row->email;
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
        $guia = $row->convenio_guia;
        $pe = $row->celular;
        $dvd = $row->dvd;
        $d4  = $row->d4;
        if ($row->tipo == '1') {
            $op = "P";
        } else {
            $op = "C";
        }
        $convenio = $row->convenio_id;
        $dt_nasc = $row->dt_nasc;
        $date = new DateTime($dt_nasc); // data de nascimento
        $interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
        $idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
        $procedimento = $row->procedimento_id;
        $senha = $row->num_senha;
        $dt_nsolicitacao = inverteData(substr($row->dt_solicitacao, 0, 10));
        $solicitante = $row->solicitante_id;
        $nomesolicitante = $row->nome_solicitante;
        $telsolicitante = $row->tel_solicitante;
        $celsolicitante = $row->cel_solicitante;
        $emailsolicitante = $row->email_solicitante;
        $deficiencia = $_POST['deficiencia'];
        $num_carteirinha = $row->num_socio;
        $num_autorizacao = $row->num_guia;
        $origem = $row->tipopac;
        $deficiencia = $row->nec_especiais;

        $enfermaria = $row->enfermaria;
        $leito = $row->leito;
        $imagem = $row->imagem;
        $hr_agenda = $row->hora_agenda;
    } else {
        $data_transacao = date('Y-m-d');
        $hora_transacao = date('h:i');
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
            $convenio = $row->convenio_id;
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

            $convenio = $row->convenio_id;
            $dt_nasc = $row->dt_nasc;
            $date = new DateTime($dt_nasc); // data de nascimento
            $interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
            $idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
            $procedimento = $row->procedimento_id;
            $pendencia = $row->qtde;
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
            $convenio = $row->convenio_id;
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

            $pe = $row->celular;
            if ($row->tipo == '1') {
                $op = "P";
            } else {
                $op = "C";
            }
            $convenio = $row->convenio_id;
            $dt_nasc = $row->dt_nasc;
            $date = new DateTime($dt_nasc); // data de nascimento
            $interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
            $idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias
            $procedimento = $row->procedimento_id;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transacao = $_POST['transacao'];
    $senha = $_POST['senha'];
    $data_transacao = $_POST['data_transacao'];
    $usuario_transacao = $_POST['usuario_transacao'];
    $acao = $_POST['acao'];
    $medico = $_POST['medico'];
    $convenio = $_POST['conveniox'];
    $procedimento = $_POST['procedimento'];
    $desproced = $_post['desc_proc'];
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
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $celular = $_POST['celular'];
    $convenio = $_POST['conveniox'];
    $deficiencia = $_POST['deficiencia'];
    $num_carteirinha = $_POST['carteirinha'];
    $num_autorizacao = $_POST['nroguia'];
    $origem = $_POST['origem'];
    $enfermaria = $_POST['enfermaria'];
    $leito = $_POST['leito'];
    $dt_nsolicitacao = $_POST['dt_nsolicitacao'];
    $solicitante = $_POST['solicitante'];
    $telsolicitante = $_POST['telsolicitante'];
    $celsolicitante = $_POST['celsolicitante'];
    $emailsolicitante = $_POST['emailsolicitante'];
    $situacao = $_POST['situacao'];
    $convenio = $_POST['conveniox'];
    $dum = $_POST['dum'];
    $peso = $_POST['peso'];
    $qtde = $_POST['qtde'];
    $ex_autorizacao = $_POST['ex_autorizacao'];
    $forma_pagto = $_POST['forma_pagto'];
    $valor = $_POST['valor'];
    $banco = $_POST['banco'];
    $cartao = $_POST['cartao'];
    $email = $_POST['email'];
    $cheque_num = $_POST['cheque_num'];
    $idade = $_POST['idade'];
    $observacao = $_POST['observacao'];
    $pendencia = $_POST['pendencia'];
    $sexo = $_POST['sexo'];
    $usuario_aut = $_POST['usuario-autorizado'];
    $pass_aut = $_POST['senha_autorizado'];
    $guia     = $_POST['guia'];
    $hr_agenda = $_POST['hr_agenda'];

    $dvd = $_POST['dvd'];
    $d4  = $_POST['d4'];

    if ($pendencia > 0) {
        if ($usuario_aut == "" or $pass_aut == "") {
            $erro = "Pendencia não Autorizada";
        } else {
            include('conexao.php');
            $sql = "SELECT * FROM pessoas WHERE username='$usuario_aut' and password=md5('$pass_aut')";
            $result = pg_query($con, $sql) or die($sql);
            $row = pg_fetch_array($result);
            if ($row['username'] == "") {
                $erro = $sql;
                $usuario_aut = "";
                $pass_aut = "";
            }
        }
    }
    if ($origem != "") {
        $origem = substr($origem, 1, strlen($origem) - 1);
    } else {
        $erro = "Origem deve ser Informado";
    }
    /*if (! validaCPF ( $cpf )) {
		$erro = "O CPF Informado não é válido";
	}*/
    if ($convenio != "") {
        include('conexao.php');
        $stmt = "select * from convenios where convenio_id=$convenio ;";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $tabela = $row->tabela_id;
    } else {
        $erro = "Convênio deve ser Informado";
    }

    if ($prontuario == "") {
        $erro = 'Paciente deve ser Informado';
    } else {
        if ($enderecox == "" or $end_numero == "" or $bairro == "" or $cidade == "" or $estado == "") {
            $erro = 'O endereço completo deve ser Informado';
        }
        if ($erro == "") {
            include('conexao.php');
            $stmt = "Update pessoas set nome='$nome', sexo='$sexo', dt_nasc='" . inverteData($dt_nascimento) . "', endereco='$enderecox', numero='$end_numero', complemento='$complemento', bairro='$bairro', cidade='$cidade',
			estado='$estado', cep='$cep', telefone='$telefone', celular='$celular', cpf='$cpf', email='$email', num_carteira_convenio='$num_carteirinha' where pessoa_id=$prontuario";
            $sth = pg_query($stmt) or die($stmt);
        }
    }

    if ($dt_nsolicitacao == "") {
        $erro = 'A Data de Solicitação deve ser Informada';
    }
    if ($solicitante == "") {
        $erro = 'Solicitante deve ser Informado';
    } else {
        include('conexao.php');
        $stmt = "select * from solicitantes where solicitante_id=$solicitante;";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $nomesolicitante = $row->nome;
        $emailsol = $row->email;
        $telsol = $row->telefone;
        $celsol = $row->celular;

        if ($emailsol != $emailsolicitante or $telsol != $telsolicitante or $celsol != $celsolicitante) {
            include('conexao.php');
            $stmt = "Update solicitantes set telefone='$telsolicitante', celular='$celsolicitante', email='$emailsolicitante' where solicitante_id=$solicitante";
            $sth = pg_query($stmt) or die($stmt);
        }
    }

    if ($erro == "") {
        if (isset($_POST['gravar'])) {
            if ($transacao == "") {
                $erro = 'Complete os dados antes de gravar';
            } else {

                include('conexao.php');
                $stmt = "select count(*) as qtde from itenspedidos where transacao=$transacao;";
                $sth = pg_query($stmt) or die($stmt);
                $row = pg_fetch_object($sth);
                $qtde_exa = $row->qtde;
                if ($qtde_exa < 1) {
                    $erro = 'Nenhum exame solicitado';
                }

                if ($tipoConv == 'P') {
                    include('conexao.php');
                    $stmt = "select sum(valor) as valor_devido from itenspedidos where transacao = $transacao;";
                    $sth = pg_query($stmt) or die($stmt);
                    $row = pg_fetch_object($sth);
                    $valor_devido = $row->valor_devido;

                    include('conexao.php');
                    $stmt = "select count(*) as qtde from pedidos_pagamentos where forma_pgto in ('FH', 'AD') and transacao= $transacao;";
                    $sth = pg_query($stmt) or die($stmt);
                    $row = pg_fetch_object($sth);
                    $qtde = $row->qtde;

                    if ($qtde < 1) {
                        include('conexao.php');
                        $stmt = "select sum(valor) as valor_pago from pedidos_pagamentos  where transacao = $transacao;";
                        $sth = pg_query($stmt) or die($stmt);
                        $row = pg_fetch_object($sth);
                        $valor_pago = $row->valor_pago;


                        if ($valor_devido > $valor_pago) {
                            $erro = 'O valor recebido é menor que o valor pago';
                        }
                    }
                }
                if ($erro == "") {
                    include('conexao.php');
                    $dt_transacao = inverteData(substr($data_transacao, 0, 10));
                    $dt_solicitacao = inverteData($dt_nsolicitacao);
                    $horacad = date('H:i');
                    $stmt = "update pedidos set transacao=$transacao, cad_user='$usuario_transacao', dat_cad='$dt_transacao', convenio_id=$convenio, dt_solicitacao='$dt_solicitacao', paciente_id=$prontuario, solicitante_id=$solicitante,
					tipo='$origem',  num_socio='$num_carteirinha', status='Cadastrado',  observacao='$observacao', box='1', hora_cad='$horacad', local='DC', sexo='$sexo', enfermaria='$enfermaria', leito='$leito',
					peso='0', tel_solicitante='$telsolicitante', email_solicitante='$emailsolicitante' where transacao=$transacao ";
                    $sth = pg_query($stmt) or die($stmt);
                    echo "<script type=\"text/javascript\" language=\"Javascript\">window.open('relpedido.php?transacao=$transacao');</script>";
                    header("location: pedidos.php");
                }
            }
        } else {
            if ($procedimento != "") {
                if ((int) $qtde < 1) {
                    $erro = 'A quantidade de Procedimento deve ser Informada';
                }
            }
        }
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
                $stmt = "select nextval('pedidos_transacao');";
                $sth = pg_query($stmt) or die($stmt);
                $row = pg_fetch_object($sth);
                $transacao = str_pad($row->nextval, 7, "0", STR_PAD_LEFT);

                include('conexao.php');
                $dt_transacao = inverteData($data_transacao);
                $dt_solicitacao = inverteData($dt_nsolicitacao);
                $horacad = date('H:i');
                $stmt = "insert into pedidos (transacao, cad_user, dat_cad, convenio_id, dt_solicitacao, paciente_id, solicitante_id,  tipo,  num_socio, status,  box, hora_cad, local, sexo, enfermaria, leito,  nec_especiais, tel_solicitante, email_solicitante, idade, observacao, apurado_ocorrencia, usu_aut_ocorrencia)
				values ($transacao, '$usuario_transacao', '$dt_transacao', $convenio, '$dt_solicitacao', $prontuario, $solicitante, '$origem', '$num_carteirinha', 'Cadastrado',  '1',  '$horacad', 'DC', '$sexo', '$enfermaria', '$leito',";
                $stmt = $stmt . "  '$deficiencia', '$telsolicitante', '$emailsolicitante', '$idade', '$observacao', '$pendencia', '$usuario_aut'  );";
                $sth = pg_query($stmt) or die($stmt);

                if ($procedimento != '') {

                    include('conexao.php');
                    $stmt = "select * from tabela_itens where tabela_id=$tabela and procedimento_id=$procedimento;";
                    $sth = pg_query($stmt) or die($stmt);
                    $row = pg_fetch_object($sth);
                    $vr_proced = $row->valor;
                    if ($vr_proced == "") {
                        $vr_proced = 0;
                    }

                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor, dvd, d4, situacao) values ($transacao, $prontuario, $procedimento, $qtde, '$ex_autorizacao', $vr_proced, '$dvd', '$d4', 'Cadastrado');";
                    $sth = pg_query($stmt) or die($stmt);
                }
                if ($forma_pagto != '') {
                    if ($valor != "") {
                        $valor = str_replace('.', '', $valor);
                        $valor = str_replace(',', '.', $valor);
                        include('conexao.php');
                        $stmt = "INSERT INTO pedidos_pagamentos( transacao, forma_pgto, banco, numero, valor) values ($transacao, '$forma_pagto', '$banco', '$cheque_num', $valor);";
                        $sth = pg_query($stmt) or die($stmt);
                        $forma_pagto = "";
                        $valor = "";
                        $banco = "";
                        $cartao = "";
                        $cheque_num = "";
                    }
                }
            } else {
                include('conexao.php');
                $dt_transacao = inverteData($data_transacao);
                $dt_transacao = inverteData($data_transacao);
                $dt_solicitacao = inverteData($dt_nsolicitacao);
                $horacad = date('H:i');
                $stmt = "update pedidos set  transacao=$transacao, cad_user='$usuario_transacao', dat_cad='$dt_transacao', convenio_id=$convenio, dt_solicitacao='$dt_solicitacao', paciente_id=$prontuario, solicitante_id=$solicitante,
				tipo='$origem',  num_socio='$num_carteirinha', status='Cadastrado',  observacao='$observacao', box='1', hora_cad='$horacad', local='DC', sexo='$sexo', enfermaria='$enfermaria', leito='$leito',
				peso='0', nec_especiais='$deficiencia', tel_solicitante='$telsolicitante', email_solicitante='$emailsolicitante' where transacao=$transacao ";
                $sth = pg_query($stmt) or die($stmt);

                if ($procedimento != '') {
                    include('conexao.php');
                    $stmt = "select * from tabela_itens where tabela_id=$tabela and procedimento_id=$procedimento;";
                    $sth = pg_query($stmt) or die($stmt);
                    $row = pg_fetch_object($sth);
                    $vr_proced = $row->valor;
                    if ($vr_proced == "") {
                        $vr_proced = 0;
                    }

                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor, dvd, d4) values ($transacao, $prontuario, $procedimento, $qtde, '$ex_autorizacao', $vr_proced, '$dvd', '$d4');";
                    $sth = pg_query($stmt) or die($stmt);
                    $procedimento = "";
                    $desc_proc = "";
                    $qtde = "";
                }
                if ($forma_pagto != '') {
                    if ($valor != "") {
                        $valor = str_replace('.', '', $valor);
                        $valor = str_replace(',', '.', $valor);
                        include('conexao.php');
                        $stmt = "INSERT INTO pedidos_pagamentos( transacao, forma_pgto, banco, numero, valor) values ($transacao, '$forma_pagto', '$banco', '$cheque_num', $valor);";
                        $sth = pg_query($stmt) or die($stmt);
                        $forma_pagto = "";
                        $valor = "";
                        $banco = "";
                        $cartao = "";
                        $cheque_num = "";
                    }
                }
            }
            $procedimento = "";
            $desc_proc = "";
            $qtde = "";
        }
    }

    if (isset($_POST['xcancelar']) == 'Cancelar') {
        include('conexao.php');
        $stmt = "delete from temp_agenda where transacao=$transacao ";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);

        include('conexao.php');
        $stmt = "delete from temp_agenda_itens where transacao=$transacao ";
        $sth = pg_query($stmt) or die($stmt);
        $texto = "cancelou";
        $dia = date('Y-m-d');
        header("location: agendaexame.php?data=$dia");
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



        header("location: pedidosbox.php");
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
    <title>FUNEPU | Solicitar Exames</title>
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
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <!--load all styles -->



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

<body class="  pace-done" cz-shortcut-listen="true">
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>

    <div class="wrapper">
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
                                                            » </p>Solicitar Exames
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
                                                    <li><a href="#">Solicitar Exame</a></li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CORPO DA PAGINA -->
                                <div class="card-content">
                                    <div class="card-body">
                                        <?php
                                        if ($erro != "") {
                                            echo '<div class="row">
		        <div class="col-sm-12">
								<strong>Erro:!</strong><br><li>' . $erro . '</li>
				</div>		
		  </div>';
                                        } ?>
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
                                                                <label class="control-label">Sexo</label> <input type="text" name="sexo" id="sexo" class="form-control" value="<?php echo $sexo; ?>" onkeyup="maiuscula(this)"> <input type="hidden" name="pendencia" id="pendencia" class="form-control" value="<?php echo $pendencia; ?>" readonly>
                                                            </div>

                                                            <div class="col-sm-2">
                                                                <label class="control-label">CPF</label> <input type="text" name="cpf" OnKeyPress="formatar('###.###.###-##', this)" maxlength="14" id="cpf" class="form-control" value="<?php echo $cpf; ?>">
                                                            </div>

                                                            <div class="col-sm-2">
                                                                <label class="control-label">RG</label> <input type="text" name="rg" id="rg" class="form-control" value="<?php echo $identidade; ?>">
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <label class="control-label">Expedição</label> <input type="text" name="org_expeditor" id="org_expeditor" class="form-control" value="<?php echo $org_expeditor; ?>"><br>
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
                                                        </div>
                                                    </div>
                                                </div>
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
                                                <div class="row">
                                                    <div class="col-4">
                                                        <label class="control-label">Convenio</label>
                                                        <select class="form-control" name="conveniox" id="conveniox">

                                                            <?php
                                                            include('conexao.php');
                                                            $stmt = "Select * from convenios order by sigla";
                                                            $sth = pg_query($stmt) or die($stmt);
                                                            while ($row = pg_fetch_object($sth)) {
                                                                if ($row->tipo == '1') {
                                                                    $op = "P";
                                                                } else {
                                                                    $op = "C";
                                                                }
                                                                $vconvenio = $row->convenio_id;
                                                                echo "<option value=\"" . $vconvenio . "\"";
                                                                if ($vconvenio == $convenio) {
                                                                    echo "selected";
                                                                }
                                                                echo ">" . $row->sigla . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="control-label">
                                                            <font color='red'>Origem</font>
                                                        </label> <select class="form-control" name="origem" id="origem" onChange="showenfermaria(this.value)">
                                                            <option value=""></option>;
                                                            <?php
                                                            include('conexao.php');
                                                            $stmt = "Select * from tipo_origem where situacao='0' order by atendimento";
                                                            $sth = pg_query($stmt) or die($stmt);
                                                            while ($row = pg_fetch_object($sth)) {
                                                                if ($row->atendimento == 'Interno') {
                                                                    $op = 'I';
                                                                } else {
                                                                    $op = 'E';
                                                                }
                                                                echo "<option value=\"" . $op . $row->tipo_id . "\"";
                                                                if ($row->tipo_id == $origem) {
                                                                    echo "selected";
                                                                }
                                                                echo ">" . $row->origem . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-2">
                                                        <label class="control-label">Enfermaria</label> <input type="text" name="enfermaria" id="enfermaria" class="form-control" value="<?php echo $enfermaria; ?>" maxlength='3'>
                                                    </div>
                                                    <div class="col-2">
                                                        <label class="control-label">Leito</label> <input type="text" name="leito" id="leito" class="form-control" maxlength='4' value="<?php echo $leito; ?>"><br>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-2">
                                                        <label class="control-label">Data Solicit.</label> <input type="text" name="dt_nsolicitacao" class="form-control" value="<?php if ($dt_nsolicitacao != "") {
                                                                                                                                                                                        echo $dt_nsolicitacao;
                                                                                                                                                                                    } else {
                                                                                                                                                                                        echo date('d/m/Y');
                                                                                                                                                                                    } ?>" OnKeyPress="formatar('##/##/####', this)">
                                                    </div>
                                                    <div class="col-2">
                                                        <label class="control-label">
                                                            <font color='red'>Solicitante</font>
                                                        </label>
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <input type="text" class="form-control" name="solicitante" id="solicitante" placeholder="Solicitante..." value='<?php echo $solicitante; ?>' readonly>
                                                            </div>
                                                            <div class="col-4">
                                                                <button type="button" class="btn btn-primary" onClick="window.open('popsol.php', 'Janela', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=700, height=500'); return false;">
                                                                    <i class="fas fa-search"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-4">
                                                        <label class="control-label">Nome Solicitante</label> <input type="text" name="nomesolicitante" class="form-control" value="<?php echo $nomesolicitante; ?>" OnKeyPress="formatar('##-#########', this)">
                                                    </div>
                                                    <div class="col-2">
                                                        <label class="control-label">Telef.Solicitante</label> <input type="text" name="telsolicitante" class="form-control" value="<?php echo $telsolicitante; ?>" OnKeyPress="formatar('##-#########', this)" maxlength="11">
                                                    </div>
                                                    <div class="col-2">
                                                        <label class="control-label">Celular Solic.</label> <input type="text" name="celsolicitante" class="form-control" value="<?php echo $celsolicitante; ?>" OnKeyPress="formatar('##-#########', this)" maxlength="12">
                                                    </div>
                                                </div>
                                                <div class="row"></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
</body>

</html>