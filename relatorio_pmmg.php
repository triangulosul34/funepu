<?php
/*$atendimento_id = $_POST['atendimento_id_pmmg'];
$queixa_paciente = $_POST['queixa_paciente_pmmg'];
$diagnostico_medico = $_POST['diagnostico_medico_pmmg'];
$orientacao_paciente = $_POST['orientacao_paciente_pmmg'];
*/
echo $orientacao_paciente;

/*include('conexao.php');
$sqlcon = "SELECT * FROM relatorio_pmmg WHERE atendimento_id = $atendimento_id";
$resultcon = pg_query($sqlcon) or die($sqlcon);
$rowcon = pg_fetch_object($resultcon);
if ($rowcon->atendimento_id) {
    include('conexao.php');
    $sql = "UPDATE relatorio_pmmg SET queixa_paciente='$queixa_paciente', diagnostico_medico='$diagnostico_medico', orientacao_paciente='$orientacao_paciente' WHERE atendimento_id = $rowcon->atendimento_id";
    //$result = pg_query($sql) or die($sql);
} else {
    include('conexao.php');
    $sql = "INSERT INTO relatorio_pmmg(atendimento_id, queixa_paciente, diagnostico_medico, orientacao_paciente) values($atendimento_id, '$queixa_paciente', '$diagnostico_medico', '$orientacao_paciente')";
    //$result = pg_query($sql) or die($sql);
}

header("Location: relpmmg.php?atendimento_id=" . $atendimento_id);
*/
?>