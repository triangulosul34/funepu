<?php

//iniciar a sessao

session_start();

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}

//verifica se existe uma sessao com esse nome

$dir_fat = "http://200.170.151.138/ftp/";

if (isset($_SESSION['myusername'])) {

    $usuario = $_SESSION['myusername'];
    $id     = $_SESSION['id'];
    $nome_med     = $_SESSION['nome'];
    $usr_imagem = $_SESSION['imagem'];
    $tipopessoa = $_SESSION['tipopessoa'];
    $grupo      = $_SESSION['grupo'];
    $perfil     = $_SESSION['perfil'];
    $box        = $_SESSION['box'];
    $unidade    = $_SESSION['unidade'];
    if (isset($_SESSION['box'])) {
        $box = $_SESSION['box'];
    }
    $_SESSION['LAST_ACTIVITY'] = time();
} else {
    header("location:login.html");
    $usuario = "naopermitido";
}

date_default_timezone_set('America/Sao_Paulo');
