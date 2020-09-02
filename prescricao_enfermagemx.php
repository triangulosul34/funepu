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
    $cpf = ereg_replace('[^0-9]', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

    // Verifica se o numero de digitos informados é igual a 11
    if (strlen($cpf) != 11) {
        return false;
    } // Verifica se nenhuma das sequências invalidas abaixo
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
        $stmt = "select a.transacao,a.hora_cad, a.cid_principal, a.destino_paciente, a.data_destino, a.queixa, a.exame_fisico, a.diagnostico_principal,a.prioridade,
		a.paciente_id, a.status, a.tipo, a.dat_cad as cadastro, c.nome, c.dt_nasc, c.sexo, c.telefone, c.celular, c.endereco, a.oque_faz, a.com_oqfaz, 
		a.tempo_faz, a.como_faz, c.numero, c.complemento, c.bairro, c.num_carteira_convenio, c.cep, c.cpf, c.cidade, c.estado, a.observacao, k.origem,  
		x.peso, x.pressaodiastolica, x.pressaosistolica, x.queixa as relato, x.pulso, x.temperatura,x.discriminador, x.prioridade as atendprioridade
		from atendimentos a 
		left join pessoas c on a.paciente_id=c.pessoa_id 
		left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
		left join classificacao x ON ltrim(x.atendimento_id, '0')= '$transacao' 
		where a.transacao=$transacao";
        $sth = pg_query($stmt) or die($stmt);
        $row = pg_fetch_object($sth);
        $data_transacao = substr($row->cadastro, 0, 10);
        $hora_transacao = $row->hora_cad;
        $prontuario = $row->paciente_id;

        $status = $row->status;
        $data_destino = $row->data_destino;
        $prioridade = $row->prioridade;
        $sexo = $row->sexo;
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
        $atendprioridade = $row->atendprioridade;
        $peso = $row->peso;
        $pressaodiastolica = $row->pressaodiastolica;
        $pressaosistolica = $row->pressaosistolica;
        $relato = $row->relato;
        $pulso = $row->pulso;
        $temperatura = $row->pressaodiastolica;
        $cns = $row->num_carteira_convenio;
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
        $observacao = $row->relato . PHP_EOL;
        if ($pressaodiastolica != '') {
            $observacao = $observacao . 'PA DIAST:' . $pressaodiastolica . ' PA SIST.:' . $pressaosistolica . PHP_EOL;;
        }
        if ($peso != '') {
            $observacao = $observacao . 'PESO:' . $peso . ' Temperatura:' . $temperatura . PHP_EOL;;
        }

        $oque_faz = $row->oque_faz;
        $com_oqfaz = $row->com_oqfaz;
        $tempo_faz = $row->tempo_faz;
        $como_faz = $row->como_faz;
        $enfermaria = $row->enfermaria;
        $leito = $row->leito;
        $imagem = $row->imagem;
        $origem = $row->tipo;
        $destino = $row->destino_paciente;
        $alta = inverteData($row->data_destino);
        $CID = $row->cid_principal;
        $diag_pri = $row->diagnostico_principal;
        $queixa = $row->queixa;
        $exame_fisico = $row->exame_fisico;
        $hora_dest = $row->hora_destino;
    } else {
        $data_transacao = date('Y-m-d');
        $hora_transacao = date('H:i');
        $usuario_transacao = $usuario;
    }
}


include('conexao.php');
$stmtCns = "
    select *
    from controle_epidemiologico
    where cns = '$cns' order by notificacao_id desc limit 1
    ";
$sthCns = pg_query($stmtCns) or die(stmtCns);
$rowcns = pg_fetch_object($sthCns);
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
    <title>FUNEPU | Prescricao Enfermagem</title>
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
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/pickadate/pickadate.css">
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

    .invisivel {
        display: none;
    }

    .visivel {
        visibility: visible;
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
                                                        » </p>Prescricao Enfermagem
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
                                                <li class="active">Prescricao Enfermagem</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <input type="hidden" name="transacao" id="transacao" class="form-control" value="<?php echo $transacao; ?>" readonly>
                                    <input type="hidden" name="data_transacao" class="form-control" value="<?php echo date('d/m/Y', strtotime($data_transacao)); ?>" readonly>
                                    <input type="hidden" name="hora_transacao" class="form-control" value="<?php

                                                                                                            if (empty($transacao)) {
                                                                                                                echo date('H:i');
                                                                                                            } else {
                                                                                                                echo $hora_transacao;
                                                                                                            }
                                                                                                            ?>" readonly>
                                    <input type="hidden" name="usuario_transacao" id="usuario_transacao" class="form-control" value="<?php echo $usuario; ?>" readonly>
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Nome </label> <input type="text" name="nome" id="nome" class="form-control square" style="font-weight: bold;" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)" readOnly>
                                        </div>
                                        <div class="col-2">
                                            <label>Sexo</label> <input type="text" name="sexo" id="sexo" class="form-control square" value="<?php echo $sexo; ?>" readonly>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Nascimento</label> <input type="text" name="dt_nascimento" id="dt_nascimento" class="form-control square" value="<?php echo $dt_nascimento; ?>" OnKeyPress="formatar('##/##/####', this)" readOnly>
                                        </div>
                                        <div class="col-sm-2">
                                            <label>Idade</label> <input type="text" name="idade" id="idade" class="form-control square" value="<?php echo $idade; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>CNS</label> <input type="text" name="cns" id="cns" class="form-control square" value="<?php echo $cns; ?>" onkeypress='return SomenteNumero(event)' readOnly>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Origem</label>
                                            <input type="text" name="origem" id="origem" class="form-control" value="<?php echo $origem; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <h3 align="center">Historico de Prescrições</h3>
                                            <hr style="width: 100%;" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">Nº</th>
                                                        <th>Data</th>
                                                        <th>Hora</th>
                                                        <th>Nome</th>
                                                        <th>Medico</th>
                                                        <th width="10%">Ação</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php include('conexao.php');
                                                    $stmt = "select a.data, a.hora, c.nome, a.prescricao_id, d.nome as medico
												from prescricoes a
												left join atendimentos b on a.atendimento_id = b.transacao
												left join pessoas c on b.paciente_id = c.pessoa_id
												left join pessoas d on d.pessoa_id = a.profissional_id
												where a.atendimento_id = $transacao order by a.data desc";
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
											class=\"btn btn-sm btn-default\" data-toggle=\"tooltip\" 
											data-original-title=\"Prescrição\">
                                            <i class=\"fas fa-print\"> </i>
											</a>
											<a href='prescricaoj.php?prioridade=$prioridade&pr=$row->prescricao_id&id=$transacao&nome=$nome&cns=$cns&idade=$idade&prontuario=$prontuario' target='_blank'
											class='btn btn-sm btn-default' data-toggle=\"tooltip\" data-original-title=\"Duplicar Prescrição\">
                                            <i class=\"fas fa-copy\"></i>
											</a>
										</td>";

                                                        echo "<tr>";
                                                    }
                                                    ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <a class="btn btn-success" href="prescricaoj.php?prioridade=<?php echo $prioridade; ?>&id=<?php echo $transacao; ?>&nome=<?php echo $nome; ?>&cns=<?php echo $cns; ?>&idade=<?php echo $idade; ?>&prontuario=<?php echo $prontuario; ?>" role="button" target="_blank">Solicitar Prescricao</a>
                                        </div>
                                    </div>
                                </div>
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
    <script src="app-assets/js/popover.js" type="text/javascript"></script>
    <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
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

        function solicitacaoprescricao(valor) {
            $('#modaSolictalPrecricao').modal('toggle');
            $.get('listaprescricaoevolucao.php?atendimento=<?php echo $_GET['id'] ?>', function(dataReturn) {
                $('#conteudoPrescricaoModal').html(dataReturn);
            });
        }


        $("#novo_prescricao").click(function(event) {
            var atendimento = $('#transacao').val();
            var dosagem = $('#dosagem').val();
            var aprazamento = $('#aprazamento').val();
            var medicamento = $('#medicamento').val();
            var medico = $('#usuario_transacao').val();
            var via = $('#via').val();
            var descricao = $('#medicamento option:selected').text();
            var campoCuidados = $('#campoCuidados').val();
            var campoCuidados = campoCuidados.replace("+", "MMAISS");
            var hidratacao_text = $('#hidratacao_text').val();
            var componente1 = $('#componente1').val();
            var componente2 = $('#componente2').val();
            var componente3 = $('#componente3').val();
            var descricao_hd = $('#descricao_hd').val();

            var tipo_prescricao = $('#tipo_prescricao').val();

            var campoDieta = $('#campoDieta').val();
            var campoDieta = campoDieta.replace("+", "MMAISS");

            var url = '';

            if (tipo_prescricao == '5') {
                url = 'salvarprescricaoevolucao.php?atendimento=' + atendimento + '&dosagem=' + dosagem + '&aprazamento=' + aprazamento + '&medicamento=' + medicamento + '&medico=' + medico + '&descricao=' + descricao + '&via=' + via + '&tipo_prescricao=' + tipo_prescricao;
            } else if (tipo_prescricao == '10') {
                url = 'salvarprescricaoevolucao.php?atendimento=' + atendimento + '&cuidados=' + campoCuidados + '&medico=+' + medico + '&tipo_prescricao=' + tipo_prescricao;
            } else if (tipo_prescricao == '1') {
                url = 'salvarprescricaoevolucao.php?atendimento=' + atendimento + '&dieta=' + campoDieta + '&medico=' + medico + '&tipo_prescricao=' + tipo_prescricao;
            } else if (tipo_prescricao == '3') {
                url = 'salvarprescricaoevolucao.php?atendimento=' + atendimento + '&hidratacao_text=' + hidratacao_text + '&componente1=' + componente1 + '&componente2=' + componente2 + '&componente3=' + componente3 + '&descricao_hd=' + descricao_hd + '&medico=' + medico + '&tipo_prescricao=' + tipo_prescricao;
            }


            $.get(url, function(dataReturn) {
                $.get('listaprescricaoevolucao.php?atendimento=<?php echo $_GET['id'] ?>', function(Return) {
                    $('#conteudoPrescricaoModal').html(Return);
                });
            });

            event.preventDefault();

            var modal = $(this).closest('#modalPrecricao');
            $(modal).modal('hide');

        });


        $("#confirmar_prescricao").click(function(event) {

            var total_campos = $("#tamanhoArray").val();
            if (total_campos == 0) {
                swal("Lista de Prescrição vazia!", "", "warning");
            } else {
                $.get('salvar_lista_prescricao.php?atendimento=<?php echo $_GET['id'] ?>', function(dataReturn) {
                    var total_campos = $("#tamanhoArray").val();
                    var i = 0;
                    var data = '<?php echo date('Y-m-d'); ?>';
                    var hora = '<?php echo date('H:i'); ?>';

                    var prescricao = new Array();

                    while (i <= total_campos) {
                        if ($('#cuidados' + i + '').val() != undefined) {
                            prescricao[i] = ["cuidados", "<?php echo $_GET['id'] ?>", $('#tipo_presc' + i + '').val(), $('#cuidados' + i + '').val(), data, hora];
                        }

                        if ($('#medicamento' + i + '').val() != undefined) {
                            prescricao[i] = ["medicamentos", "<?php echo $_GET['id'] ?>", $('#tipo_presc' + i + '').val(), $('#medicamento' + i + '').val(), $('#descricao' + i + '').val(), $('#dosagem' + i + '').val(), $('#via' + i + '').val(), $('#aprazamento' + i + '').val(), data, hora];
                        }

                        if ($('#hidratacao_text' + i + '').val() != undefined) {
                            prescricao[i] = ["hidratacao", "<?php echo $_GET['id'] ?>", $('#tipo_presc' + i + '').val(), $('#hidratacao_text' + i + '').val(), $('#componente1' + i + '').val(), $('#componente2' + i + '').val(), $('#componente3' + i + '').val(), $('#descricao_hd' + i + '').val(), data, hora];
                        }

                        i++;
                    }
                    $.post("geradorPrescricaoEvolucao.php", {
                        prescricao: prescricao
                    }, function(data, status) {
                        $("#retorno_prescricao").html(data);
                    });


                    $('#modaSolictalPrecricao').modal('hide');
                    window.location.assign("prescricao_enfermagem.php?id=<?php echo $_GET['id'] ?>");

                });

            }

        });

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





        function modal_prescricao(valor) {
            $('#modalPrecricao').modal('toggle');

            $.get('prescricao.php', function(dataReturn) {
                $('#conteudoModal').html(dataReturn);
            });
        }


        function remover_prescricao(indice) {
            $.get('exlcuirIndiceArray.php?indice=' + indice);

            $.get('listaprescricaoevolucao.php?atendimento=<?php echo $_GET['id'] ?>&flag=1', function(Return) {
                $('#conteudoPrescricaoModal').html(Return);
            });
        }


        $("#prescricao").click(function() {
            window.open('relprescricao.php?id=<?php echo $_GET['id'] ?>', '_blank');
        });


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

        function valida() {

            if (document.pedido.destino.value == '') {
                sweetAlert("Informe o destino do paciente!", "", "warning");
                return false;
            }

            if ((document.pedido.destino.value != '09' && document.pedido.destino.value != '10') && document.pedido.CID.value == '') {
                sweetAlert("Informe o CID!", "", "warning");
                return false;
            }

        }

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
        }

        function copiarCid(cid) {
            $("#cidAtestado").val(cid.value);
        }


        $("#medicamento").chosen({
            placeholder_text_single: "Selecione...",
            search_contains: true
        });
    </script>
</body>

</html>