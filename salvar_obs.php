<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $obs_modal = pg_escape_string($_GET['modal']);
    $cid = $_GET['cid'];
    $id        = $_GET['id'];
    $tipo_leito        = $_GET['tipo_leito'];
    $pa_sis = $_GET['pa_sis'];
    $pa_dist = $_GET['pa_dist'];
    $temperatura = $_GET['temperatura'];
    $dor = $_GET['dor'];
    $oxigenio = $_GET['oxigenio'];
    $frequencia_respiratoria = $_GET['frequencia_respiratoria'];
    $pulso = $_GET['pulso'];
    $glicose = $_GET['glicose'];
    $ecg = $_GET['ecg'];
}
// echo "<script>alert('UPDATE atendimentos SET obs_modal = '$obs_modal', cid = '$cid' WHERE transacao=$id')</script>";
$stmt = "UPDATE atendimentos SET obs_modal = '$obs_modal', cid_internacao = '$cid', tipo_leito = '$tipo_leito', pa_sis_internacao = '$pa_sis', pa_dist_internacao = '$pa_dist', temperatura_internacao = '$temperatura', dor_internacao = '$dor', oxigenio_internacao = '$oxigenio', pulso_internacao = '$pulso', glicose_internacao = '$glicose', ecg_internacao = '$ecg', frequencia_respiratoria = '$frequencia_respiratoria', data_internacao = '".date('Y-m-d')."', hora_internacao = '".date('H:i')."' WHERE transacao=$id";
$sth = pg_query($stmt) or die($stmt);

echo '<script> window.open("relSUSFacil.php?id=' . $id . '") </script>';
