<?php

$ordem = $_GET['ordem'];
$antibiotico = $_GET['antibiotico'];
$via = $_GET['via'];
$aprazamento = $_GET['aprazamento'];
$categoria = $_GET['categoria'];
$quantidade = $_GET['quantidade'];
$id = $_GET['id'];

include('conexao.php');
$sql = "update controle_antimicrobiano set ordem=$ordem, medicamento='$antibiotico', via='$via', aprazamento='$aprazamento', categoria=$categoria, quantidade=$quantidade where controle_id = $id";
$result = pg_query($sql) or die($sql);
?>
<script>
    $("#tb<?= $categoria; ?>_antibiotico tbody").html(
        <?php include('conexao.php');
        $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = $categoria group by 1,2,3,4,5,6 order by ordem";
        $sth = pg_query($sql) or die($sql);
        while ($row = pg_fetch_object($sth)) { ?> "<tr>" +
            "<td><?= $row->ordem; ?></td>" +
            "<td><?= $row->descricao; ?></td>" +
            "<td><?= $row->via; ?></td>" +
            "<td><?= $row->aprazamento; ?></td>" +
            "<td><?= $row->quantidade; ?></td>" +
            "<td style='display:none;'><?= $row->categoria; ?></td><td style='display:none;'><?= $row->controle_id; ?></td>" +
            "<td>" +
            "<button class='btn btn-sm btn-warning m-1' onclick='editar(this)'>" +
            "<i class='far fa-edit'></i>" +
            "</button>" +
            "<button class='btn btn-sm btn-danger m-1' onclick='deletar_antibiotico(<?= $row->controle_id; ?>,<?= $row->categoria; ?>)'>" +
            "<i class='far fa-trash-alt'></i>" +
            "</button>" +
            "</td>" +
            "</tr>" +
        <?php } ?> "");
</script>