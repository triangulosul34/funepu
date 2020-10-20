<?php

$controle = $_GET['controle'];
$categoria = $_GET['categoria'];

include('conexao.php');
$sql = "delete from controle_antimicrobiano where controle_id = $controle";
$result = pg_query($sql) or die($sql);
?>
<script>
    $("#tb<?= $categoria; ?>_antibiotico tbody").html(
        <?php include('conexao.php');
        $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = $categoria group by 1,2,3,4,5";
        $sth = pg_query($sql) or die($sql);
        while ($row = pg_fetch_object($sth)) { ?> "<tr>" +
            "<td><?= $row->ordem; ?></td>" +
            "<td><?= $row->descricao; ?></td>" +
            "<td><?= $row->via; ?></td>" +
            "<td><?= $row->aprazamento; ?></td>" +
            "<td style='display:none;'><?= $row->categoria; ?></td><td style='display:none;'><?= $row->controle_id; ?></td>" +
            "<td>" +
            "<button class='btn btn-sm btn-warning m-1' onclick='editar(this,<?= $row->controle_id; ?>)'>" +
            "<i class='far fa-edit'></i>" +
            "</button>" +
            "<button class='btn btn-sm btn-danger m-1' onclick='deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)'>" +
            "<i class='far fa-trash-alt'></i>" +
            "</button>" +
            "</td>" +
            "</tr>" +
        <?php } ?> "");
</script>