<?php

require 'tsul_ssl.php';

error_reporting(0);
include 'conexao.php';

$myusername = $_POST['myusername'];
$mypassword = md5($_POST['mypassword']);
$box = $_POST['box'];
$unidade = '';

$ip = getenv('REMOTE_ADDR');
if ($ip = '201.48.4.1') {
	$unidade == '1';
	$unid_desc = 'Mirante';
} elseif ($ip = '201.48.4.2') {
	$unidade == '2';
	$unid_desc = 'São Benedito';
}

$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);

$myusername = pg_escape_string($myusername);
$mypassword = pg_escape_string($mypassword);

$sql = "SELECT * FROM pessoas WHERE username='$myusername' and password='$mypassword'";
$result = pg_query($con, $sql) or die($sql);
$row = pg_fetch_array($result);

if ($row['username'] != '' and $row['perfil'] != '08' and $row['perfil'] != '03') {
	session_start();
	$_SESSION['myusername'] = $myusername;
	$_SESSION['nome'] = ts_decodifica($row['nome']);
	$_SESSION['mypassword'] = $mypassword;
	$_SESSION['id'] = $row['pessoa_id'];
	$_SESSION['perfil'] = $row['perfil'];
	$_SESSION['imagem'] = $row['imagem'];
	$_SESSION['box'] = $box;
	$_SESSION['tipopessoa'] = $row['tipo_pessoa'];
	$_SESSION['grupo'] = $row['sgrupo_user_id'];
	$_SESSION['unidade'] = $unidade;
	$data = date('Y-m-d');
	$id = $row['pessoa_id'];
	header('location:index.php');
} elseif ($row['perfil'] == '08' or $row['perfil'] == '03') {
	header('location: loginbox.php');
} else {
	if ($box == '') {
		header('location:login.html');
	} else {
		header('location:loginbox.html');
	}
}
