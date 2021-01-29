<?php
$con = pg_Connect("host=localhost dbname=upa-002 user=postgres password=tsul2020##");
if (!$con) {

    die('Falha de comunicaoção com o servidor. Favor entrar em contato com o suporte da TI!');
}
