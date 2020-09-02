<?php

$id = $_POST['id'];
$atendimento = $_POST['atendimento'];
$observacao = $_POST['observacao'];

include('conexao.php');
$sql = "update assistente_social set relatorio = '$observacao' where assistente_social_id = $id";
$result = pg_query($sql) or die($sql);
header("location: evolucao_atendimentomp.php?id=" . $atendimento);
