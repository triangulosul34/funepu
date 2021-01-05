<script>
    $('#dor').on('keyup', function(event) {
        var valorMaximo = 10;
        if (event.target.value > valorMaximo) {
            return event.target.value = valorMaximo;
        }
    })
</script>
<?php
require 'tsul_ssl.php';
$transacao = $_GET['transacao'];
include 'conexao.php';

$stmtRetorno = "Select * from classificacao c where cast(atendimento_id as integer) = '$transacao'";
$sthRetorno = pg_query($stmtRetorno) or die($stmtRetorno);
$rowRetorno = pg_fetch_object($sthRetorno);

include 'conexao.php';
$stmtNome = "Select nome,idade from atendimentos a
					left join pessoas p on p.pessoa_id=a.paciente_id
					where transacao = '$transacao'";
$sthNome = pg_query($stmtNome) or die($stmtNome);
$rowNome = pg_fetch_object($sthNome);

?>
<div class="row">
    <div class="col-md-8">
        <h1 style="font-size: 100%; padding:0;margin:0; margin-bottom: 10px;">
            Nome: <span style="font-weight: bold;"><?php echo ts_decodifica($rowNome->nome); ?></span>
        </h1>
    </div>

    <div class="col-md-4">
        <h1 style="font-size: 100%; padding:0;margin:0; margin-bottom: 10px;">
            Idade: <span style="font-weight: bold;"><?php echo $rowNome->idade; ?></span>
        </h1>
    </div>
</div>


<?php
include 'verifica.php';
if ($perfil == '06') {
	$situacao = [
		'Aguardando Triagem' => 'Aguardando Triagem',
		'Aguardando Atendimento' => 'Aguardando Atendimento',
		'Atendimento Finalizado' => 'Atendimento Finalizado'
	];
} else {
	$situacao = [
		'Aguardando Triagem' => 'Aguardando Triagem',
		'Aguardando Atendimento' => 'Aguardando Atendimento'
	];
}

?>
<div class="row">
    <div class="col-md-12">
        <label class="control-label"><?php echo utf8_encode('Situacao'); ?></label>
        <select class="form-control" style='font-size:small;' name="situacaoMod" id="situacaoMod">
            <option value=""><?php echo utf8_encode('Selecione a Situacao'); ?>
            </option>
            <?php
			foreach ($situacao as $key => $value) {
				if ($rowRetorno->prioridade == $key) {
					echo '<option value="' . $key . '" selected>' . utf8_encode($value) . '</option>';
				} else {
					echo '<option value="' . $key . '">' . utf8_encode($value) . '</option>';
				}
			}
			?>
        </select>
    </div>

    <input type="hidden" name="transacaoMod" id="transacaoMod"
        value="<?php echo $_GET['transacao']; ?>">
</div>