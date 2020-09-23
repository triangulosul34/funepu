<?php
$con = pg_Connect("host=200.170.151.4 dbname=valim user=medbox password=medbox123");
if (!$con) {
    die('connection failed');
}


// $stmtvalim = "select * from insereprescricaoupasao('FUNEPU','leonardohumberto','LUCILTON VIEIRA FARIA', '000.000.000.00',214310,'2020-06-01','12:44',array[(1,105488,1,'')]::prodprescricao[])";
// $sthvalim = pg_query($stmtvalim) or die($stmtvalim);
