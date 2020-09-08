<?php
// include("conexao.php");
// $sql = "SELECT a.transacao FROM atendimentos a 
// LEFT JOIN destino_paciente b ON a.transacao = b.atendimento_id
// WHERE (a.destino_paciente in ('07','10','03', '', '0') or a.destino_paciente is null) and (destino_encaminhamento is null) and a.dat_cad < '2020-08-30' and a.transacao not in(358527,358115,358395,358119,358382,358826,357020,358240,358173,358198,357870,358067,358267,357933,357488)";
// $result = pg_query($sql) or die($sql);
// while ($row = pg_fetch_object($result)) {
//     $sql2 = "insert into destino_paciente (atendimento_id, destino_encaminhamento, motivo,data, hora) 
//     values ($row->transacao, 20, 'Alta automatizado via sistema', '" . date('Y-m-d') . "', '" . date('H:i') . "')";
//     $result2 = pg_query($sql2) or die($sql2);
// }

// include("conexao.php");
// $sql = "SELECT a.transacao FROM atendimentos a 
// LEFT JOIN destino_paciente b ON a.transacao = b.atendimento_id
// WHERE destino_encaminhamento = 20";
// $result = pg_query($sql) or die($sql);
// while ($row = pg_fetch_object($result)) {
//     $sql2 = "update destino_paciente set destino_encaminhamento = 20, motivo= 'Alta automatizado via sistema', data = '" . date('Y-m-d') . "', hora = '" . date('H:i') . "' where atendimento_id = $row->transacao";
//     $result2 = pg_query($sql2) or die($sql2);
// }

include("conexao.php");
$sql = "SELECT a.transacao, max((c.data || ' ' || c.hora)::timestamp) as data FROM atendimentos a 
LEFT JOIN destino_paciente b ON a.transacao = b.atendimento_id
INNER JOIN logs c ON a.transacao = c.atendimento_id
WHERE destino_encaminhamento = 20 group by 1";
$result = pg_query($sql) or die($sql);
while ($row = pg_fetch_object($result)) {
    $sql2 = "update destino_paciente set destino_encaminhamento = 20, motivo= 'Alta automatizado via sistema', data = '" . substr($row->data, 0, 10) . "', hora = '" . substr($row->data, 11, 5) . "' where atendimento_id = $row->transacao";
    $result2 = pg_query($sql2) or die($sql2);
}
