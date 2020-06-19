<?php
function soNumero($str)
{
    return preg_replace("/[^0-9]/", "", $str);
}

$max = 35;
$prescricao      = $_GET['prescricao'];
$b                 = $_GET['bomba'];
$atendimento     = $_GET['atendimento'];
$nome               = $_GET['nome'];
$paciente         = $_GET['paciente'];
$medico           = $_GET['medico'];
$escolha         = $_GET['escolha'];
$via             = $_GET['via'];
$aprazamento     = $_GET['aprazamento'];
$complemento     = $_GET['complemento'];
$dosagem         = $_GET['dosagem'];
$dosagemvetor     = explode(',', $_GET['dosagemvetor']);
$escolhav         = explode(',,', substr($_GET['escolhavetor'], 2));
$dosagemv         =  $_GET['dosagemvetor'];
$radio           = $_GET['radio'];
$value           = $_GET['value'];
$qtd            = $_GET['qtd'];
$diluente         = $_GET['diluente'];
$data            = date('Y-m-d');
$hora            = date('H:i');
$escolhavetor     = str_replace(",,", ",", substr($_GET['escolhavetor'], 2));
$nome             = substr_replace($nome, (strlen($nome) > $max ? '...' : ''), $max);
$obs            = $_GET['obs'];
$complemento = stripslashes($complemento);
$complemento = pg_escape_string($complemento);

if ($radio == 'dietas') {
    $tipo = 1;
} else if ($radio == 'medicamentos') {
    $tipo = 5;
} else {
    $tipo = 10;
}

$solucoes = array("105195", "105194", "105019", "105283", "105159", "105041", "105242", "105129", "105338", "105164", "105165", "105000", "105131", "105130");


if ($radio == 'solucoes') {

    $r = 1;
    $c = 0;
    $i = 0;
    foreach ($escolhav as $vesc) {
        if (in_array($vesc, $solucoes)) {
        } else {
            $filtro = soNumero($dosagemvetor[$c]);
            if ($valim == '') {
                $valim = "(" . $r . "," . $vesc . "," . $filtro . ",'$complemento')";
            } else {
                $valim .= ",(" . $r . "," . $vesc . "," . $filtro . ",'$complemento')";
            }
            $c++;
            $r++;

            if ($vesc == $diluente) {
                $diluente = '8888';
            }
        }

        include('conexao.php');
        $sql = "select * from medicamentos WHERE id in ($vesc)";
        $sth = pg_query($sql) or die($sql);
        while ($row = pg_fetch_object($sth)) {
            if ($descmedicamento == '') {
                $descmedicamento = $row->descricao;
                $descmedicamento .= ' - ' . $dosagemvetor[$i];
            } else {
                $descmedicamento .= ' ++' . $row->descricao;
                $descmedicamento .= ' - ' . $dosagemvetor[$i];
            }
            $i++;
        }
    }

    $valim .= ",(" . $r . "," . $diluente . ",1,'$complemento')";




    if ($diluente == '9999') {
        $diluente = "Em BOLUS";
    } else if ($diluente == '8888') {
        $diluente = "PURO";
    } else {
        include('conexao.php');
        $sqla = "select * from medicamentos WHERE id = $diluente";
        $stha = pg_query($sqla) or die($sqla);
        $rowa = pg_fetch_object($stha);
        $diluente = $rowa->descricao;
    }

    include('conexao.php');
    $stmt = "insert into prescricao_item (bomba,tipo, prescricao_id, aprazamento, via, obs_med, dosagem, complemento, descricao, codigo_medicamento, diluente) 
			values ($b, 6, $prescricao, '$aprazamento', '$via', '$obs',  ";
    $stmt = $stmt . " '$dosagemv',";
    if ($complemento == '') {
        $stmt = $stmt . " null,";
    } else {
        $stmt = $stmt . " '$complemento',";
    }
    $stmt = $stmt . "'$descmedicamento', '$escolhavetor', '$diluente')";

    $sth = pg_query($stmt) or die($stmt);
} else if ($radio == 'paciente') {
    include('conexao.php');
    $stmt = "insert into prescricao_item(tipo, prescricao_id, descricao) 
			values (11, $prescricao, '$paciente')";
    $sth = pg_query($stmt) or die($stmt);
} else if ($radio == 'dietas') {
    include('conexao.php');
    $stmt = "insert into prescricao_item (bomba, tipo, prescricao_id, aprazamento, via, obs_med, dosagem, complemento, descricao, codigo_medicamento) 
			values ($b, $tipo, $prescricao, '$aprazamento', '$via', '$obs', ";
    if ($dosagem == '') {
        $stmt = $stmt . " null,";
    } else {
        $stmt = $stmt . " '$dosagem',";
    }
    if ($complemento == '') {
        $stmt = $stmt . " null,";
    } else {
        $stmt = $stmt . " '$complemento',";
    }

    if ($radio == 'medicamentos') {
        $stmt = $stmt . "'$escolha', '$value')";
    } else {
        $stmt = $stmt . " '$escolha', null)";
    }
    $sth = pg_query($stmt) or die($stmt);
} else {
    include('conexao.php');
    $stmt = "insert into prescricao_item (tipo, prescricao_id, aprazamento, via, obs_med, dosagem, complemento, descricao, codigo_medicamento) 
			values ($tipo, $prescricao, '$aprazamento', '$via', '$obs',  ";
    if ($dosagem == '') {
        $stmt = $stmt . " null,";
    } else {
        $stmt = $stmt . " '$dosagem',";
    }
    if ($complemento == '') {
        $stmt = $stmt . " null,";
    } else {
        $stmt = $stmt . " '$complemento',";
    }

    if ($radio == 'medicamentos') {
        $stmt = $stmt . "'$escolha', '$value')";
    } else {
        $stmt = $stmt . " '$escolha', null)";
    }
    $sth = pg_query($stmt) or die($stmt);
}
?>
<table id="tabela" class="table table-striped width-full">
    <thead>
        <tr>
            <th width="30">Procedimento</th>
            <th width="20">Via</th>
            <th width="10%">Aprazamento</th>
            <th width="10%">Quantidade</th>
            <th width="10%">Diluente</th>
            <th width="10%">Complemento</th>
            <th width="10%">Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include('conexao.php');
        $stmt = "select *
							from prescricao_item
							where prescricao_id = $prescricao";
        $sth = pg_query($stmt) or die($stmt);
        while ($row = pg_fetch_object($sth)) {
            echo "<tr>";
            echo "<td>" . $row->descricao . "</td>";
            echo "<td>" . $row->via . "</td>";
            echo "<td>" . $row->aprazamento . "</td>";
            echo "<td>" . $row->dosagem . "</td>";
            echo "<td>" . $row->diluente . "</td>";
            echo "<td>" . $row->complemento . "</td>";
            echo "<td><button type=\"button\" name=\"editalinha\" onclick=\"editaconta(" . $prescricao . "," . $row->tipo . "," . $row->prescricao_item_id . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-edit\"></i></button>";
            echo "<button type=\"button\" name=\"apagalinha\" onclick=\"deletaconta(" . $row->prescricao_item_id . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-trash-alt\"></i></button></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>