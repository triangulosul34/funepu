<?php
 $con=pg_Connect("host=200.170.151.4 dbname=valim user=medbox password=medbox123");
if (!$con){
 
 	$con2=pg_Connect("host=200.170.151.138 dbname=produtos_estoque user=medboxdb password=medbox@upa");
	if (!$con2){
 		die('connection failed');
	}
 }
