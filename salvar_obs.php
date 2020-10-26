<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $obs_modal = pg_escape_string($_GET['modal']);
    $cid = $_GET['cid'];
    $id        = $_GET['id'];
}
// echo "<script>alert('UPDATE atendimentos SET obs_modal = '$obs_modal', cid = '$cid' WHERE transacao=$id')</script>";
$stmt = "UPDATE atendimentos SET obs_modal = '$obs_modal', cid_internacao = '$cid' WHERE transacao=$id";
$sth = pg_query($stmt) or die($stmt);

echo '<script> window.open("relSUSFacil.php?id=' . $id . '") </script>';
