<?php
$con = pg_Connect("host=200.170.151.5 dbname=laboratorio user=postgres password=tsul2018##");
if (!$con) {
    die('connection failed');
}
