<?php

$con = pg_Connect('host=localhost dbname=laboratorio user=postgres password=tsul2020##');
if (!$con) {
	die('connection failed');
}
