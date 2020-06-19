<?php

require("../vendor/autoload.php");

function calculatempo($dataenvio)
{
    $dataAtual = date('Y-m-d H:i:s');

    $date_time  = new DateTime($dataenvio);
    $diff       = $date_time->diff(new DateTime());
    $segundos   = $diff->format('%s');
    $minutos    = $diff->format('%i');
    $horas      = $diff->format('%h');
    $dias       = $diff->format('%d');
    if ($segundos > 0) {
        $resultado  = $diff->format('%s segundo(s)');
    }
    if ($minutos > 0) {
        $resultado  = $diff->format('%i minuto(s)');
    }
    if ($horas > 0) {
        $resultado  = $diff->format('%h hora(s)');
    }
    if ($dias > 0) {
        $resultado  = $diff->format('%d dia(s)');
    }

    return $resultado;
}
include('verifica.php');

$nome_user = $usuario;
$nomeUser = '';

if ($unidade == '1') {
    $unid_desc = 'Mirante';
}
if ($unidade == '2') {
    $unid_desc = 'São Benedito';
}

$ip = getenv("REMOTE_ADDR");
include('conexao.php');
$stmt = "SELECT ip_estacao, descricao from boxes where ip_estacao='$ip' order by descricao";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$box = $row->descricao;
if ($box == "") {
    $box = '999';
}

?>
<?php
$data = date('Y-m-d');
include('conexao.php');
$stmt = "select * from pessoas where username='$nome_user'";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$codigo = $row->pessoa_id;
$uimagem = $row->imagem;
$nomeUser = explode(" ", $row->nome);
$sgrupo_user = $row->sgrupo_user_id;
if ($sgrupo_user == '2') {
    header("location:pedidosbox.php?data=$data");
}
if ($sgrupo_user == '5') {
    header("location:painel_atendimento.php?data=$data");
}
if ($usuario == 'hsm') {
    header("location:pedidossm.php?data=$data");
}

?>
<style>
    .teste {
        height: 55px;
        /* width: 435px;
        min-width: 735px; */
        padding-right: 15px;
        color: white;
        font-weight: bold;
        display: -webkit-flex;
        display: flex;
        -webkit-align-items: center;
        align-items: center;
        -webkit-justify-content: center;
        justify-content: center;
        padding-left: 20px !important;
    }
</style>
<nav class="navbar navbar-expand-lg navbar-light bg-faded header-navbar" style="padding-top: 0px;">
    <div class="container-fluid" style="height: 75px;">
        <div onclick="javascript:location.href='http://mr.midaspa.com.br/mr/funepu/index.php'" class="navbar-header teste" style="background-color: #12A1A6; border-top-right-radius: 30px;border-bottom-right-radius:30px;">
            <h3 style="margin: 0; padding: 0"><strong>UNIDADE <?php echo UNIDADE_CONFIG; ?></strong></h3>
        </div>
        <div class="navbar-container">
            <div id="navbarSupportedContent" class="collapse navbar-collapse">
                <p><?php echo $nome_med; ?></p>
                <div class="fonticon-container">
                    <div class="fonticon-wrap" style=" margin-left: 0px;">
                        <i class="icon-user-following" style="font-size: 18pt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>