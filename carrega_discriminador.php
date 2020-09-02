<label class="control-label">Discriminador</label>
<?php

$fluxo = $_GET['fluxo'];
$paciente = $_GET['paciente'];
include('conexao.php');
$stmt = 'Select * from fluxograma_item where fluxograma_id=' . $fluxo . 'order by fluxograma_id';

$sth = pg_query($stmt) or die($stmt);
echo '<label class="control-label"></label>
   		  <select class="form-control" style="font-size:small;"  name="discriminador" id="discriminador" onchange="carrega_notificacao(this,' . $paciente . ')">
		  <option value="">Selecione o Discriminador</option>		
		  ';
while ($row = pg_fetch_object($sth)) {
    echo "<option value=\"" . $row->discriminador_id . "\">" . $row->descricao . "</option>";
}
echo '</select>';

?>