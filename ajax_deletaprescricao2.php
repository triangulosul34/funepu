<?php
$id  = $_GET['id'];
$prescricao  = $_GET['prescricao'];

include('conexao.php');
$stmt =    "delete from prescricao_item where prescricao_item_id = $id  and prescricao_id = $prescricao";
$sth = pg_query($stmt) or die($stmt);
?>
<table id="tabela" class="table table-striped width-full">
    <thead>
        <tr>
            <th width="30">Procedimento</th>
            <th width="20">Via</th>
            <th width="20%">Aprazamento</th>
            <th width="10%">Dosagem</th>
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
            if ($row->descricao == '') {
                echo "<td>" . $row->codigo_medicamento . "</td>";
            } else {
                echo "<td>" . $row->descricao . "</td>";
            }
            echo "<td>" . $row->via . "</td>";
            echo "<td>" . $row->aprazamento . "</td>";
            echo "<td>" . $row->dosagem . "</td>";
            echo "<td>" . $row->complemento . "</td>";
            echo "<td><button type=\"button\" name=\"editalinha\" onclick=\"editaconta(" . $row->prescricao_item_id . "," . $row->tipo . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-edit\"></i></button>";
            echo "<button type=\"button\" name=\"apagalinha\" onclick=\"deletaconta(" . $row->prescricao_item_id . ")\" class=\"btn btn-pure btn-danger\"><i class=\"fas fa-trash-alt\"></i></button></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>