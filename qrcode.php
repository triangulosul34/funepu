<?php

require_once('phpqrcode/qrlib.php');
require("../vendor/autoload.php");

$a = $_GET['a'];
$c = $_GET['c'];

//::png('http://201.48.4.90/' . UNIDADEABV_CONFIG . '/funepu/qratestado.php?a=' . $a . '&c=' . $c);
?>
<img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php echo 'http://201.48.4.90/' . UNIDADEABV_CONFIG . '/funepu/qratestado.php?a=' . $a . '&c=' . $c; ?>&choe=UTF-8" title="Link to Google.com" />