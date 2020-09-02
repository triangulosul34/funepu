<?php
error_reporting ( 0 );

if ($_SERVER ['REQUEST_METHOD'] == 'GET') {

	$id = $_GET ['exame'];
	include ('conexao.php');
	$stmt = "select resultado from itenspedidos where exame_nro=" . $id;
	$sth = pg_query ( $stmt ) or die ( $stmt );
	$row = pg_fetch_object ( $sth );
	$laudo = $row->resultado;
	echo $laudo; 
}
