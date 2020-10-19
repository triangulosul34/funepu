<?php
$categoria = $_GET['categoria'];
?>
<script>
    $("#tb<?= $categoria; ?>_antibiotico tbody").append(
        "<tr>" +
        "<td><input type='text' class='form-control' onkeypress='return event.charCode >= 48 && event.charCode <= 57' /></td>" +
        "<td><select class='form-control selectnew' data-size='4' data-live-search='true' name='antibiotico' id='antibiotico' ><option value=''></option><?php
                                                                                                                                                            include('conexao.php');
                                                                                                                                                            $stmt = 'Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where b.antibiotico = 1';
                                                                                                                                                            $sth = pg_query($stmt) or die($stmt);
                                                                                                                                                            while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                echo '<option value=\'' . $row->id . '\'';
                                                                                                                                                                echo '>' . $row->descricao . '</option>';
                                                                                                                                                            } ?></select></td>" +
        "<td><select class='form-control selectnew' data-size='4' data-live-search='true' name='via' id='via'><option value=" +
        "></option>" +
        <?php include('conexao.php');
        $stmt = "Select descricao from vias_administracao";
        $sth = pg_query($stmt) or die($stmt);
        while ($row = pg_fetch_object($sth)) { ?> "<option value='<?= $row->descricao; ?>'><?= $row->descricao; ?></option>" + <?php } ?> "</select></td > " +
        "<td><select class='form-control selectnew' data-size='4' data-live-search='true' name='aprazamento' id='aprazamento'><option value=''></option>" +
        <?php include('conexao.php');
        $stmt = "Select * from aprazamento";
        $sth = pg_query($stmt) or die($stmt);
        while ($row = pg_fetch_object($sth)) { ?> "<option value='<?= $row->descricao; ?>'><?= $row->descricao; ?></option>" +
        <?php }
        ?> "</select></td>" +
        "<td style='display:none;'><?= $categoria; ?></td>" +
        "<td><button class='btn btn-success' onclick='salvar(this)'>Salvar</button></td>" +
        "</tr>");

    $('.selectnew').selectpicker();
</script>