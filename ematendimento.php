<?php
include('conexao.php');
$stmt = "	select transacao, p.nome as medico,a.dat_cad, q.nome as paciente, prioridade, a.status, a.prioridade, a.hora_cad,a.hora_triagem, a.especialidade
									from atendimentos a
									left join pessoas p on p.username = a.med_atendimento
									left join pessoas q on q.pessoa_id = a.paciente_id
									where dat_cad = '" . date('Y-m-d') . "' and status = 'Em Atendimento'
									order by a.especialidade desc, hora_cad asc";
$sth = pg_query($stmt) or die($stmt);

while ($row = pg_fetch_object($sth)) {
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

    echo "<tr " . $classe . ">";
    echo "<td>" . date('d/m/Y',  strtotime($row->dat_cad)) . "<br>" . $row->hora_cad . "</td>";
    echo "<td>" . $row->hora_triagem . "</td>";
    echo "<td>" . $row->paciente . "</td>";
    echo "<td>" . $row->medico . "</td>";
    echo "<td>" . $row->especialidade . "</td>";
    echo "</tr>";
}
