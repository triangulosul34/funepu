<?php
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

$start = $_GET['start'];
$end = $_GET['end'];
$nome = $_GET['nome'];
$situacao = $_GET['situacao'];

if ($start != "") {
    $data = inverteData($start);
    if ($where != "") {
        $where = $where . " and (a.dat_cad >= '$data')";
    } else {
        $where = $where . " (a.dat_cad >= '$data')";
    }
}

if ($end != "") {
    $data = inverteData($end);
    if ($where != "") {
        $where = $where . " and (a.dat_cad <= '$data')";
    } else {
        $where = $where . " (a.dat_cad <= '$data')";
    }
}

if ($nome != "") {
    $where = $where . " c.nome like '%" . $nome . "%' ";
}

if ($situacao != "") {
    if ($situacao != "Pendentes") {
        if ($where != "") {
            $where = $where . " and (a.status = '$situacao')";
        } else {
            $where = $where . " (a.status = '$situacao')";
        }
    } else {
        if ($where != "" and $status == "Pendentes") {
            $where = $where . " and (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
        } else {
            $where = $where . " (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )";
        }
    }
}
?>
<thead>
    <tr>
        <th width="5%">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="cb_cm">
                <label class="custom-control-label" for="cb_cm"></label>
            </div>
        </th>
        <th width="5%">Solicitação</th>
        <th width="20%">Paciente</th>
        <th width="15%">Origem</th>
        <th width="8%">Chegada</th>
        <th width="8%">Triagem</th>
        <th width="8%">Atendimento</th>
        <th width="14%">Situação</th>
        <th width="7%">Ação</th>
    </tr>
</thead>
<tfoot>
    <tr>
        <th width="5%">#</th>
        <th width="5%">Solicitação</th>
        <th width="20%">Paciente</th>
        <th width="15%">Origem</th>
        <th width="8%">Chegada</th>
        <th width="8%">Triagem</th>
        <th width="8%">Atendimento</th>
        <th width="14%">Situação</th>
        <th width="7%">Ação</th>

    </tr>
</tfoot>
<tbody>
    <?php
    include('conexao.php');
    $stmt = "select a.transacao, a.paciente_id, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, a.dat_cad as cadastro, 	c.nome, k.origem, f.descricao as clinica,a.hora_destino, 
									CASE prioridade WHEN 'VERMELHO' THEN '0' WHEN 'LARANJA' THEN '1' WHEN 'AMARELO' THEN '2' WHEN 'VERDE' THEN '3'  WHEN 'AZUL' THEN '4' ELSE '5'
									END as ORDEM 
									from atendimentos a 
									left join pessoas c on a.paciente_id=c.pessoa_id  left join especialidade f on a.especialidade = f.descricao 
									left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) ";
    if ($where != "") {
        $stmt = $stmt . " where " . $where;
    } else {
        $stmt = $stmt . " where dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' ";
    }
    if ($_GET['situacao'] != '') {
        $stmt = $stmt . " and (a.status = '" . $_GET['situacao'] . "')";
    }
    $stmt = $stmt . " AND a.especialidade = 'Ortopedia' order by 5 ";
    $sth = pg_query($stmt) or die($stmt);
    //echo $stmt; 
    while ($row = pg_fetch_object($sth)) {

        $x = $x + 1;
        if ($row->prioridade   == 'AMARELO') {
            $classe = "style=\"background-color:gold\"";
        }
        if ($row->prioridade   == 'VERMELHO') {
            $classe = "class='bg-danger'";
        }
        if ($row->prioridade   == 'VERDE') {
            $classe = "class='bg-success'";
        }
        if ($row->prioridade   == 'AZUL') {
            $classe = "class='bg-primary'";
        }
        if ($row->prioridade   == 'LARANJA') {
            $classe = "class='bg-warning'";
        }
        if ($row->prioridade   == '') {
            $classe = "style=\"background-color:Gainsboro\"";
        }


        $ip = getenv("REMOTE_ADDR");
        echo "<tr $classe";
        /*if($x % 2 == 0){
											 echo "style=\"background-color:#CDC5BF\"";
										} else {
											 echo "style=\"background-color:#EEE5DE\"";
										}*/
        echo ">";
        echo "<td align='center'><div class=\"checkbox-custom checkbox-primary\"><input type=\"checkbox\" class='marcar' name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
        echo "<td>" . inverteData(substr($row->cadastro, 0, 10)) . "</td>";
        echo "<td>" . $row->nome . "</td>";
        echo "<td>" . $row->origem . "</td>";
        echo "<td>" . $row->hora_cad . "</td>";
        //echo "<td>".utf8_encode($row->convenio)."</td>";							
        echo "<td>" . $row->hora_triagem . "</td>";
        echo "<td>" . $row->hora_destino . "</td>";
        echo "<td>" . $row->status . "</td>";
        echo "<td>";
        echo "<a href=\"relFAA.php?id=$row->transacao\" target=\"_blank\" type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"FAA\"><i class=\"fas fa-print\"></i></a>";
        echo "</tr>";
    }
    ?>
</tbody>