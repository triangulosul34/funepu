<?php

// include("conexao.php");
// $sql = "SELECT a.transacao, max((c.data || ' ' || c.hora)::timestamp) as data FROM atendimentos a
// LEFT JOIN destino_paciente b ON a.transacao = b.atendimento_id
// INNER JOIN logs c ON a.transacao = c.atendimento_id
// WHERE (a.destino_paciente in ('07','10','03', '', '0') or a.destino_paciente is null) and (destino_encaminhamento is null) and EXTRACT(epoch FROM (CURRENT_TIMESTAMP - (c.data || ' ' || c.hora)::timestamp))/3600 > 48 group by 1";
// $result = pg_query($sql) or die($sql);
// while ($row = pg_fetch_object($result)) {
//     $sql2 = "insert into destino_paciente (atendimento_id, destino_encaminhamento, motivo,data, hora)
//     values ($row->transacao, 20, 'Alta automatizado via sistema', '" . substr($row->data, 0, 10) . "', '" . substr($row->data, 11, 5) . "')";
//     $result2 = pg_query($sql2) or die($sql2);
// }

// include("conexao.php");
// $sql = "SELECT a.transacao, MAX(c.data || ' ' || c.hora) as data FROM atendimentos a
// LEFT JOIN destino_paciente b ON a.transacao = b.atendimento_id
// INNER JOIN evolucoes c ON a.transacao = c.atendimento_id
// WHERE (a.destino_paciente in ('07','10','03', '', '0') or a.destino_paciente is null) and (destino_encaminhamento = 3) and EXTRACT(epoch FROM (CURRENT_TIMESTAMP - (c.data || ' ' || c.hora)::timestamp))/3600 > 72 group by 1";
// $result = pg_query($sql) or die($sql);
// while ($row = pg_fetch_object($result)) {
//     $sql2 = "update destino_paciente set destino_encaminhamento = 20, motivo= 'Alta automatizado via sistema', data = '" . substr($row->data, 0, 10) . "', hora = '" . substr($row->data, 11, 5) . "' where atendimento_id = $row->transacao";
//     $result2 = pg_query($sql2) or die($sql2);
// }

// include("conexao.php");
// $sql = "SELECT a.transacao, max((c.data || ' ' || c.hora)::timestamp) as data FROM atendimentos a
// LEFT JOIN destino_paciente b ON a.transacao = b.atendimento_id
// INNER JOIN logs c ON a.transacao = c.atendimento_id
// WHERE destino_encaminhamento = 20 group by 1";
// $result = pg_query($sql) or die($sql);
// while ($row = pg_fetch_object($result)) {
//     $sql2 = "update destino_paciente set destino_encaminhamento = 20, motivo= 'Alta automatizado via sistema', data = '" . substr($row->data, 0, 10) . "', hora = '" . substr($row->data, 11, 5) . "' where atendimento_id = $row->transacao";
//     $result2 = pg_query($sql2) or die($sql2);
// }

include 'conexao.php';
$sql = "select a.transacao, data_destino, hora_destino, z.data data_enc, z.hora hora_enc, max((c.data || ' ' || c.hora)::timestamp) as data 
from atendimentos a 
left join destino_paciente z on a.transacao = z.atendimento_id 
left join evolucoes c on a.transacao = c.atendimento_id 
where case when c.data is null 
then 
case when z.data is null 
then data_destino < NOW() - interval '2 day' 
else z.data < NOW() - interval '2 day' end 
else c.data < NOW() - interval '2 day' end 
and case when z.destino_encaminhamento::varchar is null then a.destino_paciente in ('03','07','10') else z.destino_encaminhamento::varchar in ('03','07','10') end
group by 1,2,3,4,5";
$result = pg_query($sql) or die($sql);
while ($row = pg_fetch_object($result)) {
	if ($row->data_evolucao == null) {
		$data = $row->data_enc == null ? $row->data_destino : $row->data_enc;
		$hora = $row->hora_enc == null ? $row->hora_destino : $row->hora_enc;
	} else {
		$data = substr($row->data, 0, 9);
		$hora = substr($row->hora, 11);
	}

	if ($row->data_enc == null) {
		$sql2 = "insert into destino_paciente (atendimento_id, destino_encaminhamento, motivo,data, hora)
        values ($row->transacao, 20, 'Alta automatizado via sistema', '" . $data . "', '" . $hora . "')";
		$result2 = pg_query($sql2) or die($sql2);
	} else {
		$sql2 = "update destino_paciente set destino_encaminhamento = 20, motivo= 'Alta automatizado via sistema', data = '" . $data . "', hora = '" . $hora . "' where atendimento_id = $row->transacao";
		$result2 = pg_query($sql2) or die($sql2);
	}
}
