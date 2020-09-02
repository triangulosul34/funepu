<?php
function soNumero($str)
{
    return preg_replace("/[^0-9]/", "", $str);
}

$max = 35;
$id             = $_GET['id'];
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
$obs             = $_GET['obs'];
$nome             = substr_replace($nome, (strlen($nome) > $max ? '...' : ''), $max);

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
        }
    }

    $valim .= ",(" . $r . "," . $diluente . ",1,'$complemento')";

    include('conexaovalim.php');
    $sql = "select * from qtdeprodutoupa WHERE produtoid in ($escolhavetor)";
    $sth = pg_query($sql) or die($sql);
    $i = 0;
    while ($row = pg_fetch_object($sth)) {
        if ($descmedicamento == '') {
            $descmedicamento = $row->produtodescricao;
            $descmedicamento .= ' - ' . $dosagemvetor[$i];
        } else {
            $descmedicamento .= ' ++' . $row->produtodescricao;
            $descmedicamento .= ' - ' . $dosagemvetor[$i];
        }
        $i++;
    }

    if ($diluente == '9999') {
        $diluente = "Em BOLUS";
    } else {
        include('conexaovalim.php');
        $sqla = "select * from qtdeprodutoupa WHERE produtoid = $diluente";
        $stha = pg_query($sqla) or die($sqla);
        $rowa = pg_fetch_object($stha);
        $diluente = $rowa->produtodescricao;
    }

    include('conexao.php');
    $stmt = "update prescricao_item 
		set bomba = $b, tipo = 6, aprazamento = '$aprazamento', via = '$via', obs_med = '$obs', dosagem = '$dosagemv', ";
    if ($complemento == '') {
        $stmt = $stmt . " complemento = null,";
    } else {
        $stmt = $stmt . " complemento = '$complemento',";
    }
    $stmt = $stmt . "descricao = '$descmedicamento', codigo_medicamento = '$escolhavetor', diluente = '$diluente' where prescricao_item_id = $id";

    $sth = pg_query($stmt) or die($stmt);
} else if ($radio == 'paciente') {
    include('conexao.php');
    $stmt = "update prescricao_item 
		set tipo = 11, descricao = '$paciente' where prescricao_item_id = $id";
    $sth = pg_query($stmt) or die($stmt);
} else if ($radio == 'dietas') {
    include('conexao.php');
    $stmt = "update prescricao_item
		set bomba = $b, tipo = 1, aprazamento = '$aprazamento',";
    if ($via == '') {
        $stmt = $stmt . " via = null,";
    } else {
        $stmt = $stmt . " via = '$via',";
    }
    if ($dosagem == '') {
        $stmt = $stmt . "dosagem = null,";
    } else {
        $stmt = $stmt . " dosagem = '$dosagem',";
    }
    if ($complemento == '') {
        $stmt = $stmt . "complemento = null,";
    } else {
        $stmt = $stmt . " complemento = '$complemento',";
    }

    if ($radio == 'medicamentos') {
        $stmt = $stmt . " descricao = '$escolha', codigo_medicamento = '$value' where prescricao_item_id = $id";
    } else {
        $stmt = $stmt . " descricao = '$escolha', codigo_medicamento = null where prescricao_item_id = $id";
    }
    $sth = pg_query($stmt) or die($stmt);
} else {
    include('conexao.php');
    $stmt = "update prescricao_item
		set  tipo = $tipo, aprazamento = '$aprazamento', obs_med = '$obs', ";
    if ($via == '') {
        $stmt = $stmt . " via = null,";
    } else {
        $stmt = $stmt . " via = '$via',";
    }
    if ($dosagem == '') {
        $stmt = $stmt . " dosagem = null,";
    } else {
        $stmt = $stmt . " dosagem = '$dosagem',";
    }
    if ($complemento == '') {
        $stmt = $stmt . " complemento = null,";
    } else {
        $stmt = $stmt . " complemento = '$complemento',";
    }

    if ($radio == 'medicamentos') {
        $stmt = $stmt . " descricao = '$escolha', codigo_medicamento = '$value' where prescricao_item_id = $id";
    } else {
        $stmt = $stmt . " descricao = '$escolha', codigo_medicamento = null where prescricao_item_id = $id";
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
            <th width="10%">Dosagem</th>
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
            echo "<td><button type=\"button\" name=\"editalinha\" onclick=\"editaconta(" . $row->prescricao_item_id . "," . $row->tipo . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-edit\"></i></button>";
            echo "<button type=\"button\" name=\"apagalinha\" onclick=\"deletaconta(" . $row->prescricao_item_id . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-trash-alt\"></i></button></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>