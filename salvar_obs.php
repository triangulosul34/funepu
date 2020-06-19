<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $obs_modal = pg_escape_string($_GET['modal']);
    $id        = $_GET['id'];
}
// echo "<script>alert('UPDATEx atendimentos SET obs_modal = $obs_modal WHERE transacao=$id')</script>";
$stmt = "UPDATE atendimentos SET obs_modal = '$obs_modal' WHERE transacao=$id";
$sth = pg_query($stmt) or die($stmt);

echo '<script> window.open("relSUSFacil.php?id=' . $id . '") </script>';
