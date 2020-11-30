<?php

function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $atendimento_id = $_POST['atendimento_sumario'];
    $nome = $_POST['nome_sumario'];
    $cns = $_POST['cns_sumario'];
    $paciente_id = $_POST['prontuario_sumario'];
    $idade = $_POST['idade_sumario'];
    $data_nascimento = $_POST['data_nascimento_sumario'];
    $sexo = $_POST['sexo_sumario'];
    $unidade = $_POST['unidade_sumario'];
    $nome_mae = $_POST['nome_mae_sumario'];
    $especialidade = $_POST['especialidade_sumario'];
    $modalidade_assistencial = $_POST['modalidade_assistencial_sumario'];
    $procedencia = $_POST['procedencia_sumario'];
    $internacao = $_POST['internacao_sumario'];
    $alta = $_POST['alta_sumario'];
    $carater_internacao = $_POST['carater_internacao_sumario'];
    $responsavel = $_POST['responsavel_sumario'];
    $permanencia = $_POST['permanencia_sumario'];
    $crm = $_POST['crm_sumario'];
    $diagnostico = $_POST['diagnostico'];
    $procedimento_terapeutico = $_POST['procedimento_terapeutico'];
    $evolucap = $_POST['evolucap'];
    $pos_alta = $_POST['pos_alta'];
    $segmento_atendimento = $_POST['segmento_atendimento'];
    $estado_paciente = $_POST['estado_paciente'];

    include('conexao.php');
    $sql="SELECT * FROM sumario_alta WHERE atendimento_id = $atendimento_id";
    $result = pg_query($sql) or die($sql);
    $row = pg_fetch_object($result);
    
    if ($row->atendimento_id == '') {
        include('conexao.php');
        $sql = "INSERT INTO sumario_alta(atendimento_id,nome_sumario,cns_sumario,prontuario_sumario,idade_sumario,data_nascimento_sumario,sexo_sumario,unidade_sumario,nome_mae_sumario,especialidade_sumario,modalidade_assistencial_sumario,procedencia_sumario,internacao_sumario,alta_sumario,carater_internacao_sumario,responsavel_sumario,crm_sumario,diagnostico,procedimento_terapeutico,evolucap,pos_alta,segmento_atendimento,estado_paciente, permanencia_sumario) VALUES($atendimento_id,'$nome','$cns',$paciente_id,'$idade','$data_nascimento','$sexo','$unidade','$nome_mae','$especialidade','$modalidade_assistencial','$procedencia','$internacao','$alta','$carater_internacao','$responsavel','$crm','$diagnostico','$procedimento_terapeutico','$evolucap','$pos_alta','$segmento_atendimento','$estado_paciente','$permanencia')";
        $result = pg_query($sql) or die($sql);
    } else {
        include('conexao.php');
        $sql = "UPDATE sumario_alta SET atendimento_id=$atendimento_id,nome_sumario='$nome',cns_sumario='$cns',prontuario_sumario=$paciente_id,idade_sumario='$idade',data_nascimento_sumario='$data_nascimento',sexo_sumario='$sexo',unidade_sumario='$unidade',nome_mae_sumario='$nome_mae',especialidade_sumario='$especialidade',modalidade_assistencial_sumario='$modalidade_assistencial',procedencia_sumario='$procedencia',internacao_sumario='$internacao',alta_sumario='$alta',carater_internacao_sumario='$carater_internacao',responsavel_sumario='$responsavel',permanencia_sumario='$permanencia',crm_sumario='$crm',diagnostico='$diagnostico',procedimento_terapeutico='$procedimento_terapeutico',evolucap='$evolucap',pos_alta='$pos_alta',segmento_atendimento='$segmento_atendimento',estado_paciente='$estado_paciente' WHERE atendimento_id=$atendimento_id";
        $result = pg_query($sql) or die($sql);
    }

    header("Location: sumario_alta_pdf.php?atendimento_id=".$atendimento_id);
}
