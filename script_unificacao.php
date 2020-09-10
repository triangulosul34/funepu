<?php

set_time_limit(3600);

function validaCPF($cpf = null)
{

    // Verifica se um número foi informado
    if (empty($cpf)) {
        return false;
    }

    // Elimina possivel mascara
    $cpf = preg_replace("/[^0-9]/", "", $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

    // Verifica se o numero de digitos informados é igual a 11 
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequências invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if (
        $cpf == '00000000000' ||
        $cpf == '11111111111' ||
        $cpf == '22222222222' ||
        $cpf == '33333333333' ||
        $cpf == '44444444444' ||
        $cpf == '55555555555' ||
        $cpf == '66666666666' ||
        $cpf == '77777777777' ||
        $cpf == '88888888888' ||
        $cpf == '99999999999'
    ) {
        return false;
        // Calcula os digitos verificadores para verificar se o
        // CPF é válido
    } else {

        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{
                    $c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{
                $c} != $d) {
                return false;
            }
        }

        return true;
    }
}

include('conexao.php');

$sql_replace = "update pessoas set cpf = replace(replace(cpf, '.',''), '-','')";
$result_replace = pg_query($sql_replace) or die($sql_replace);
$sql_trim = "update pessoas set cpf = trim(cpf)";
$result_trim = pg_query($sql_trim) or die($sql_trim);
$sql_all = "select cpf,pessoa_id from pessoas";
$result_all = pg_query($sql_all) or die($sql_all);
while ($row_all = pg_fetch_object($result_all)) {
    if (!validaCPF($row_all->cpf)) {
        $sql_valida = "update pessoas set cpf = '' WHERE pessoa_id = $row_all->pessoa_id";
        $result_valida = pg_query($sql_valida) or die($sql_valida);
    }
}
$sql_unifica = "select cpf, max(pessoa_id) pessoa_id from pessoas where cpf is not null and cpf <> '' group by 1 having count(*) > 1";
$result_unifica = pg_query($sql_unifica) or die($sql_unifica);
while ($row_unifica = pg_fetch_object($result_unifica)) {
    $sql_pessoa = "select * from pessoas where cpf = '$row_unifica->cpf' and pessoa_id <> $row_unifica->pessoa_id";
    $result_pessoa = pg_query($sql_pessoa) or die($sql_pessoa);
    while ($row_pessoa = pg_fetch_object($result_pessoa)) {
        $sql_apacs_solicitadas = "update apacs_solicitadas set pessoa_id=$row_unifica->pessoa_id where pessoa_id=$row_pessoa->pessoa_id";
        $result_apacs_solicitadas = pg_query($sql_apacs_solicitadas) or die($sql_apacs_solicitadas);
        $sql_atendimentos = "update atendimentos set paciente_id=$row_unifica->pessoa_id where paciente_id=$row_pessoa->pessoa_id";
        $result_atendimentos = pg_query($sql_atendimentos) or die($sql_atendimentos);
        $sql_exames_resultados = "update exames_resultados set cd_paciente='$row_unifica->pessoa_id' where cd_paciente='$row_pessoa->pessoa_id'";
        $result_exames_resultados = pg_query($sql_exames_resultados) or die($sql_exames_resultados);
        $sql_itenspedidos = "update itenspedidos set pessoa_id=$row_unifica->pessoa_id where pessoa_id=$row_pessoa->pessoa_id";
        $result_itenspedidos = pg_query($sql_itenspedidos) or die($sql_itenspedidos);
        $sql_pedidos = "update pedidos set paciente_id=$row_unifica->pessoa_id where paciente_id=$row_pessoa->pessoa_id";
        $result_pedidos = pg_query($sql_pedidos) or die($sql_pedidos);
        $sql_sindrome_gripal = "update sindrome_gripal set pessoa_id=$row_unifica->pessoa_id where pessoa_id=$row_pessoa->pessoa_id";
        $result_sindrome_gripal = pg_query($sql_sindrome_gripal) or die($sql_sindrome_gripal);
        $sql_delete = "delete from pessoas where pessoa_id=$row_pessoa->pessoa_id";
        $result_delete = pg_query($sql_delete) or die($sql_delete);
    }
}

$sql_unifica = "select nome, dt_nasc, nome_mae, max(pessoa_id) pessoa_id from pessoas where cpf is not null and cpf <> '' group by 1,2,3 having count(*) > 1";
$result_unifica = pg_query($sql_unifica) or die($sql_unifica);
while ($row_unifica = pg_fetch_object($result_unifica)) {
    $sql_pessoa = "select * from pessoas where cpf = '$row_unifica->cpf' and pessoa_id <> $row_unifica->pessoa_id";
    $result_pessoa = pg_query($sql_pessoa) or die($sql_pessoa);
    while ($row_pessoa = pg_fetch_object($result_pessoa)) {
        $sql_apacs_solicitadas = "update apacs_solicitadas set pessoa_id=$row_unifica->pessoa_id where pessoa_id=$row_pessoa->pessoa_id";
        $result_apacs_solicitadas = pg_query($sql_apacs_solicitadas) or die($sql_apacs_solicitadas);
        $sql_atendimentos = "update atendimentos set paciente_id=$row_unifica->pessoa_id where paciente_id=$row_pessoa->pessoa_id";
        $result_atendimentos = pg_query($sql_atendimentos) or die($sql_atendimentos);
        $sql_exames_resultados = "update exames_resultados set cd_paciente='$row_unifica->pessoa_id' where cd_paciente='$row_pessoa->pessoa_id'";
        $result_exames_resultados = pg_query($sql_exames_resultados) or die($sql_exames_resultados);
        $sql_itenspedidos = "update itenspedidos set pessoa_id=$row_unifica->pessoa_id where pessoa_id=$row_pessoa->pessoa_id";
        $result_itenspedidos = pg_query($sql_itenspedidos) or die($sql_itenspedidos);
        $sql_pedidos = "update pedidos set paciente_id=$row_unifica->pessoa_id where paciente_id=$row_pessoa->pessoa_id";
        $result_pedidos = pg_query($sql_pedidos) or die($sql_pedidos);
        $sql_sindrome_gripal = "update sindrome_gripal set pessoa_id=$row_unifica->pessoa_id where pessoa_id=$row_pessoa->pessoa_id";
        $result_sindrome_gripal = pg_query($sql_sindrome_gripal) or die($sql_sindrome_gripal);
        $sql_delete = "delete from pessoas where pessoa_id=$row_pessoa->pessoa_id";
        $result_delete = pg_query($sql_delete) or die($sql_delete);
    }
}

echo "concluido";
