<?php
$medicamento = pg_escape_string(stripslashes($_POST['medicamento']));
$quantidade = pg_escape_string(stripslashes($_POST['quantidade']));
$usar = pg_escape_string(stripslashes($_POST['usar']));
$cuidados = pg_escape_string(stripslashes($_POST['cuidados']));
$transacao = pg_escape_string(stripslashes($_POST['transacao']));

include('conexao.php');
$stmt = "insert into receituario_remedio (medicamentos, quantidade, modo_usar, transacao) 
							values ( '$medicamento', '$quantidade','$usar', '$transacao')";
$sth = pg_query($stmt) or die($stmt);
