<?php
error_reporting(0);

include("conexao.php");

$myusername = $_POST['myusername'];
$mypassword = md5($_POST['mypassword']);
$box        = $_POST['box'];

$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);

$unidade    = '';


$ip = getenv("REMOTE_ADDR");
if ($ip = '201.48.4.1') {
    $unidade == '1';
    $unid_desc = 'Mirante';
} elseif ($ip = '201.48.4.2') {
    $unidade == '2';
    $unid_desc = 'São Benedito';
}


$myusername = pg_escape_string($myusername);
$mypassword = pg_escape_string($mypassword);

$sql = "SELECT * FROM pessoas WHERE username='$myusername' and password='$mypassword'";
$result = pg_query($con, $sql) or die($sql);
$row = pg_fetch_array($result);

if ($row['username'] != "") {
    session_start();
    $_SESSION['myusername'] = $myusername;
    $_SESSION['mypassword'] = $mypassword;
    $_SESSION['id']        = $row['pessoa_id'];
    $_SESSION['perfil']    = $row['perfil'];
    $_SESSION['imagem']    = $row['imagem'];
    $_SESSION['box']       = $box;
    $id = $row['pessoa_id'];
    $_SESSION['unidade']   = $unidade;

    if (($_SESSION['perfil'] == '03' and $_SESSION['box'] == '8') or ($_SESSION['perfil'] == '03' and $_SESSION['box'] == '9')) {
        $_SESSION['perfil'] = '08';
        $aux = 1;
    }

    include("conexao.php");
    $data_hora = date('Y-m-d H:i:s');
    $sql = "insert into logons (data_hora, usuario, consultorio) values ('$data_hora', '$myusername', '$box') ";
    $result = pg_query($con, $sql) or die($sql);
    $row = pg_fetch_array($result);

    //validaçao de acesso
    if ($_SESSION['perfil'] == '03') {
        if ($box >= 1 and $box <= 5) {
            header("location:monitor_medico.php");
        } elseif ($box == 6) {
            header("location:painel_at_ortopedia.php");
        } elseif ($box == 7) {
            header("location:painel_at_ala_vermelha.php");
        } else {
            header("location:monitor_medico.php");
        }
    } else if ($_SESSION['perfil'] == '08') {
        if ($aux == 1) {
            header("location:triagemRecepcao.php");
        } else {
            header("location:monitor_triagem.php");
        }
    } else if ($_SESSION['perfil'] == '09') {
        header("location:painel_at_ortopedia.php");
    } else if ($_SESSION['perfil'] == '05') {
        header("location:painel_rx.php");
    } else {
        header("location:index.php");
    }
} else {
    if ($box == "") {
        header("location:login.html");
    } else {
        header("location:loginbox.php");
    }
}
