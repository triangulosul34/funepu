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
		left join tipo_origem k on cast(k.tipo_id as varchar) = a.tipo
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
        $destino     = $row->destino_paciente;
        $alta         = inverteData($row->data_destino);
        $CID         = $row->cid_principal;
        $diag_pri     = $row->diagnostico_principal;
        $queixa       = $row->queixa;
        $exame_fisico   = $row->exame_fisico;
        $hora_dest    = $row->hora_destino;
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
$sthCns = pg_query($stmtCns) or die($stmtCns);
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
    <title>FUNEPU | Exames do Paciente</title>
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

    .scroll {
        height: 425px;
        overflow-y: auto;
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
                                                        » </p>Exames
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
                                                <li class="active">Exames Paciente</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
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
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <h3 class="title" align="center" style="margin-bottom: -15px;">Exames Radiologicos</h3><br>
                                            <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" /><br>
                                            <div class="scroll">
                                                <table class="table">
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
                                                        $stmt = "SELECT b.dat_cad,c.formulario, a.exame_nro,a.arquivo_upload, a.pessoa_id,a.transacao, c.descricao, a.situacao, a.versao, c.exames_laboratoriais, c.procedimento_id from itenspedidos a 
													left join pedidos b on a.transacao=b.transacao 
													left join procedimentos c on a.exame_id=c.procedimento_id where exames_laboratoriais is null and a.pessoa_id=" . $prontuario . " order by dat_cad desc";
                                                        $sth = pg_query($stmt) or die($stmt);
                                                        //echo $stmt;
                                                        $x = 0;
                                                        while ($row = pg_fetch_object($sth)) {
                                                            if ($row->exames_laboratoriais == 1 and substr($row->dat_cad, 0, 10) >= '2019-01-08') {
                                                                include('conexao_laboratorio.php');
                                                                $sql = "SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, e.descricao 
                                                            	FROM pedidos a
                                                            	INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
                                                            	INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
                                                            	INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
                                                            	LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
                                                            	LEFT JOIN modalidades f ON f.modalidade_id = e.setor where a.data = '" . substr($row->dat_cad, 0, 10) . "' and c.origem = '" . ORIGEM_CONFIG . "' AND b.origem = '" . PORIGEM_CONFIG . "' AND pessoa_id_origem = $prontuario order by a.data, a.horario";
                                                                $result = pg_query($sql) or die($sql);
                                                                while ($rows = pg_fetch_object($result)) {
                                                                    if ($rows->exame_id == $row->procedimento_id) {
                                                                        echo "<tr>";
                                                                        echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\" value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                        echo "<td>" . inverteData($rows->data) . "</td>";
                                                                        echo "<td>$row->descricao</td>";
                                                                        if ($rows->liberado == 1) {
                                                                            echo "<td><a href='http://" . IP_CONFIG . "/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a></td>";
                                                                        }
                                                                        echo "</tr>";
                                                                    }
                                                                }
                                                                $x = $x + 1;
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
                                                                    echo "<a href='imagens/documentos/$rowDetalhe->arquivo' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                }

                                                                if ($row->arquivo_upload != '') {
                                                                    echo "<a href='arquivos/exames/$row->arquivo_upload' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                }



                                                                if ($row->formulario == 'A') {
                                                                    echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\"><i class=\"icon wb-print\" aria-hidden=\"true\" onclick=\"openInNewTab('relApac.php?id=$row->exame_nro')\"></i></button>";
                                                                }
                                                                if ($row->situacao == 'Finalizado') {
                                                                    if ($row->versao != '2') {
                                                                        echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                    } else {
                                                                        echo "<a href='http://" . IP_CONFIG . "/laboratorio/html/relExamemr.php?local=MR&transacao=$row->exame_nro'' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                    }
                                                                }

                                                                if ($row->situacao == 'Impresso') {
                                                                    echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
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
                                        <div class="col-6">
                                            <h3 class="title" align="center" style="margin-bottom: -15px;">Exames Laboratoriais</h3><br>
                                            <hr style="width: 100%; color: #FF0000; height: 1px; background-color: #FF0000;" /><br>
                                            <div class="scroll">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th width='25%'>Data / Situação</th>
                                                            <th width='65%'>Descrição</th>
                                                            <th width='10%'>Situação</th>
                                                            <!--<th width='10%'>Ação</th>-->
                                                        </tr>
                                                    </thead>

                                                    <body>
                                                        <?php
                                                        $stmt = "SELECT b.dat_cad,c.formulario, a.exame_nro,a.arquivo_upload, a.pessoa_id,a.transacao, c.descricao, a.situacao, a.versao, c.exames_laboratoriais, c.procedimento_id from itenspedidos a 
													left join pedidos b on a.transacao=b.transacao 
													left join procedimentos c on a.exame_id=c.procedimento_id where exames_laboratoriais = 1 and a.pessoa_id=" . $prontuario . " order by dat_cad desc";
                                                        $sth = pg_query($stmt) or die($stmt);
                                                        //echo $stmt;
                                                        $x = 0;
                                                        while ($row = pg_fetch_object($sth)) {
                                                            if ($row->exames_laboratoriais == 1 and substr($row->dat_cad, 0, 10) >= '2019-01-08') {
                                                                include('conexao_laboratorio.php');
                                                                $sql = "SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, d.situacao, e.descricao, d.pedido_item_id 
                                                                FROM pedidos a
                                                                INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
                                                                INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
                                                                INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
                                                                LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
                                                                LEFT JOIN modalidades f ON f.modalidade_id = e.setor where a.data = '" . substr($row->dat_cad, 0, 10) . "' AND c.origem = '" . ORIGEM_CONFIG . "' AND b.origem = '" . PORIGEM_CONFIG . "' AND pessoa_id_origem = $prontuario order by a.data, a.horario";
                                                                $result = pg_query($sql) or die($sql);
                                                                while ($rows = pg_fetch_object($result)) {
                                                                    echo "<tr>";
                                                                    echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\" value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                                                                    echo "<td>" . inverteData($rows->data) . "</td>";
                                                                    echo "<td>$row->descricao</td>";
                                                                    // if ($rows->liberado == 1) {
                                                                    //     echo "<td><a href='http://" . IP_CONFIG . "/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a></td>";
                                                                    // }
                                                                    echo "<td>" . $rows->situacao . "</td>";
                                                                    echo "</tr>";
                                                                }
                                                                $x = $x + 1;
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
                                                                    echo "<a href='imagens/documentos/$rowDetalhe->arquivo' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                }

                                                                if ($row->arquivo_upload != '') {
                                                                    echo "<a href='arquivos/exames/$row->arquivo_upload' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                }



                                                                if ($row->formulario == 'A') {
                                                                    echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\"><i class=\"icon wb-print\" aria-hidden=\"true\" onclick=\"openInNewTab('relApac.php?id=$row->exame_nro')\"></i></button>";
                                                                }
                                                                if ($row->situacao == 'Finalizado') {
                                                                    if ($row->versao != '2') {
                                                                        echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                    } else {
                                                                        echo "<a href='http://" . IP_CONFIG . "/laboratorio/html/relExamemr.php?local=MR&transacao=$row->exame_nro'' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
                                                                    }
                                                                }

                                                                if ($row->situacao == 'Impresso') {
                                                                    echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon \"><i class=\"fas fa-search\"></i></a>";
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
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <input type="date" name="data" id="data">
                                                    <input type='button' name='imprimir_exames' id="imprimir_exames" class="btn btn-primary" value='Imprimir Exames' onclick="laboratorio()">
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
        <script>
            function laboratorio() {
                var data = document.getElementById("data").value;
                if (data) {
                    window.open("<?= "http://" . IP_CONFIG . "/desenvolvimento/laboratorio/gera_resultado.php?data="; ?>" + data + "<?= "&pessoa_id=$prontuario&origem=" . PORIGEM_CONFIG; ?>");
                } else {
                    alert("Informe da data!!!");
                }
            }
        </script>
</body>

</html>