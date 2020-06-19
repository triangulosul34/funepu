<?php

require_once('phpqrcode/qrlib.php');
require("../vendor/autoload.php");

$a = $_GET['a'];
$c = $_GET['c'];

QRcode::png('http://' . UNIDADEABV_CONFIG . '.midaspa.com.br/' . UNIDADEABV_CONFIG . '/funepu/qratestado.php?a=' . $a . '&c=' . $c);
