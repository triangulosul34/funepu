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
    $transacao         = $_GET['id'];
    $senha             = $_GET['senha'];
    $agendamento     = $_GET['ag'];
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
        if ($pressaodiastolica != '') {
            $observacao = $observacao . 'PA DIAST:' . $pressaodiastolica . ' PA SIST.:' . $pressaosistolica . PHP_EOL;;
        }
        if ($peso != '') {
            $observacao = $observacao . 'PESO:' . $peso . ' Temperatura:' . $temperatura . PHP_EOL;;
        }

        $oque_faz      = $row->oque_faz;
        $com_oqfaz     = $row->com_oqfaz;
        $tempo_faz     = $row->tempo_faz;
        $como_faz      = $row->como_faz;
        $enfermaria = $row->enfermaria;
        $leito         = $row->leito;
        $imagem     = $row->imagem;
        $origem     = $row->tipo;
        $destino     = $row->destino_paciente;
        $alta         = inverteData($row->data_destino);
        $CID         = $row->cid_principal;
        $diag_pri     = $row->diagnostico_principal;
        $queixa       = $row->queixa;
        $exame_fisico   = $row->exame_fisico;
        $hora_dest    = $row->hora_destino;
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transacao = stripslashes(pg_escape_string($_GET['id']));
    $evolucao = stripslashes(pg_escape_string($_POST['evolucao']));
    $temp = stripslashes(pg_escape_string($_POST['temp']));
    $pa = stripslashes(pg_escape_string($_POST['pa']));
    $fc = stripslashes(pg_escape_string($_POST['fc']));
    $fr = stripslashes(pg_escape_string($_POST['fr']));
    $sat = stripslashes(pg_escape_string($_POST['sat']));
    $glicemia = stripslashes(pg_escape_string($_POST['glicemia']));
    $diurese = stripslashes(pg_escape_string($_POST['diurese']));

    if ($evolucao == "") {
        $erro = "Evolução deve ser Informado";
    }

    if ($erro == "") {
        include('conexao.php');
        $horacad = date('H:i');
        $datacad = date('Y-m-d');

        $stmt = "insert into evolucoes  (atendimento_id,tipo,data,hora,usuario,evolucao,temperatura,pressao_arterial,frequencia_cardiaca,
												frequencia_respiratoria,saturacao_ox,glicemia,diurese)
						values ($transacao,$perfil,'$datacad','$horacad','$usuario','$evolucao','$temp','$pa','$fc','$fr','$sat','$glicemia','$diurese')";
        $sth = pg_query($stmt) or die($stmt);

        header("location: evolucao_atendimento.php?id=" . $transacao);
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
    <title>FUNEPU | Nova Evolução</title>
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
                                                        » </p>Nova Evolução
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
                                                <li class="active">Nova Evolução</li>
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
                                    <form method="post" id="pedido" name='pedido'>
                                        <div class="row mb-2">
                                            <div class="col-sm-12">
                                                <h3 align="center">Identificação do Paciente</h3>
                                                <hr style="width: 100%">
                                            </div>
                                        </div>
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
                                        <div class="row">
                                            <div class="col-12">
                                                <label>Observações</label>
                                                <textarea rows="5" name="observacao" class="form-control square" readonly><?php echo $observacao; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mt-2  mb-2">
                                            <div class="col-12">
                                                <h3 align="center">Nova Evolução</h3>
                                                <hr style="width: 100%" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php if ($perfil == '08' or $perfil == '06') { ?>
                                                <div class="col-3">
                                                    <label>Temperatura</label>
                                                    <input type="text" class="form-control square" name="temp">
                                                </div>
                                                <div class="col-3">
                                                    <label>Pressão Arterial</label>
                                                    <input type="text" class="form-control square" name="pa">
                                                </div>
                                                <div class="col-3">
                                                    <label>Frequência Cardíaca</label>
                                                    <input type="text" name="fc" class="form-control square">
                                                </div>
                                                <div class="col-3">
                                                    <label>Frequência Respiratória</label>
                                                    <input type="text" name="fr" class="form-control square">
                                                </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <label>Sat O²</label>
                                                <input type="text" class="form-control square" name="sat">
                                            </div>
                                            <div class="col-4">
                                                <label>Glicemia</label>
                                                <input type="text" name="glicemia" class="form-control square">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Diurese</label>
                                                <input type="text" class="form-control square" name="diurese">
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <label>Evolução</label>
                                            <textarea name="evolucao" class="form-control square" rows="50" cols="10" style="resize: none"></textarea> </br>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12" align="center">
                                            <?php if ($perfil == '06' or $perfil == '03' or $perfil == '08') { ?>
                                                <input type='submit' name='gravar' class="btn btn-primary" value='Gravar' onclick="return valida()">
                                            <?php } ?>
                                            <a href="evolucao_atendimento.php?id=<?php echo $_GET['id'] ?>" class="btn btn-danger">Cancelar</a>
                                        </div>
                                    </div>
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
        <script src="app-assets/js/popover.js" type="text/javascript"></script>
        <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
        <script defer src="/your-path-to-fontawesome/js/all.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</body>

</html>