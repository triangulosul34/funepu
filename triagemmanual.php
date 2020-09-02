<link rel="stylesheet" href="assets/vendor/sweetalert/dist/sweetalert.css">
<script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $('#dor').on('keyup', function(event) {
        var valorMaximo = 10;
        if (event.target.value > valorMaximo) {
            return event.target.value = valorMaximo;
        }
    });
</script>
<?php
$transacao = $_GET['transacao'];
include('conexao.php');
$stmtRetorno = "Select * from classificacao c where cast(atendimento_id as integer) = '$transacao'";
$sthRetorno = pg_query($stmtRetorno) or die($stmtRetorno);
$rowRetorno = pg_fetch_object($sthRetorno);

$hoje = date('Y-m-d');
include('conexao.php');
$stmtNome = "select nome,idade,dt_nasc,extract(year from age(dt_nasc)) as idadenormal,num_carteira_convenio,p.nome_social, paciente_id, a.nec_especiais
	from atendimentos a
					left join pessoas p on p.pessoa_id=a.paciente_id
					where transacao = '$transacao'";
$sthNome = pg_query($stmtNome) or die($stmtNome);
$rowNome = pg_fetch_object($sthNome);
$cns = $rowNome->num_carteira_convenio;


include('conexao.php');
$stmtCns = "
	select *
		from controle_epidemiologico
		where cns = '$cns' order by notificacao_id desc limit 1
	";
$sthCns = pg_query($stmtCns) or die($stmtCns);
$rowcns = pg_fetch_object($sthCns);

$date = new DateTime($rowNome->dt_nasc); // data de nascimento
$interval = $date->diff(new DateTime(date('Y-m-d'))); // data definida
$idade = $interval->format('%YA%mM%dD'); // 110 Anos, 2 Meses e 2 Dias

if ($rowcns->descricao != '') {
?>
    <script>
        sweetAlert("<?php echo utf8_encode('Aten��o, paciente com notifica��o epidemiologica'); ?>", "<?php echo $rowcns->descricao; ?>", "warning");
    </script>
<?php } ?>
<style>
    .slidecontainer {
        width: 100%;
    }

    .slider {
        -webkit-appearance: none;
        width: 90%;
        height: 10px;
        border-radius: 5px;
        background: #e4e9f2;
        outline: none;
        opacity: 0.7;
        -webkit-transition: .2s;
        transition: opacity .2s;
    }

    .slider:hover {
        opacity: 1;
    }

    .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #12A1A6;
        cursor: pointer;
    }

    .slider::-moz-range-thumb {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #12A1A6;
        cursor: pointer;
    }
</style>
<div class="col-md-12 pl-0 pr-0">
    <div class="row">
        <div class="col-9">
            <input type="hidden" class="form-control" value="<?php echo $rowNome->paciente_id ?>" name="paciente" id="paciente">
            <h4 style="font-size: 100%; padding:0;margin:0; margin-bottom: 10px;">
                Nome: <span style="font-weight: bold;">
                    <?php if ($rowNome->nome_social == '') { ?>
                        <?php echo $rowNome->nome; ?>
                    <?php } else { ?>
                        <?php echo $rowNome->nome_social; ?> (<?php echo $rowNome->nome; ?>)
                    <?php } ?>
                    <?php if ($rowNome->nec_especiais != 'Nenhuma') {
                    ?>
                        <br>Paciente com deficiência <?= $rowNome->nec_especiais; ?>
                    <?php } ?>
                </span>
            </h4>
        </div>

        <div class="col-3">
            <h3 style="font-size: 100%; padding:0;margin:0; margin-bottom: 10px;">
                <span style="font-weight: bold;">
                    <?php echo $idade; ?>
                </span>
            </h3>
        </div>

        <hr style="margin: auto;width: 95%; height: 2px;">
    </div>
    <div class="row mt-2">
        <div class="col-6">
            <label>Fluxograma</label>
            <select class="form-control square" style='font-size:small;' name="fluxograma" id="fluxograma" onchange='carrega_discriminador()'>
                <option value="">Selecione o Fluxograma</option>
                <?php
                include('conexao.php');
                $stmt = 'Select * from fluxo_class_risco order by fluxograma_id';
                $sth = pg_query($stmt) or die($stmt);
                while ($row = pg_fetch_object($sth)) {
                    if ($rowRetorno->fluxograma == $row->descricao) {
                        echo '<option value="' . $row->fluxograma_id . '" selected>' . $row->descricao . '</option>';
                    } else {
                        echo '<option value="' . $row->fluxograma_id . '">' . $row->descricao . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-6" id='load_discriminador'>
            <label>Discriminador</label>
            <select class="form-control square" style='font-size:small;' name="discriminador" id="discriminador">
                <option value="">Selecione o Discriminador</option>
            </select>
        </div>
    </div>
    <!-- display: flex; justify-content: center;  align-items: center -->
    <form class="form">
        <div class="form-body">

            <!-- LINHA 1 -->
            <div class='row mt-2'>

                <!-- PA Sistolica -->
                <div class="col-4 form-group">
                    <label>PA Sistolica</label>
                    <div class="position-relative has-icon-left">
                        <input type="text" class="form-control square" name="pa_sis" value="<?php echo $rowRetorno->pressaosistolica ?>" id="pa_sis">
                        <div class="form-control-position" style="top: 0px">
                            <img src="app-assets/img/svg/nano.png" alt="\" height="20" width="20">
                        </div>
                    </div>
                </div>

                <!-- PA Distolica -->
                <div class="col-4 form-group">
                    <label>PA Distolica</label>
                    <div class="position-relative has-icon-left">
                        <input type="text" class="form-control square" name="pa_dis" value="<?php echo $rowRetorno->pressaodiastolica ?>" id="pa_dis">
                        <div class="form-control-position" style="top: 0px">
                            <img src="app-assets/img/svg/nano.png" alt="\" height="20" width="20">
                        </div>
                    </div>
                </div>

                <!-- Oxigênio -->
                <div class="col-4 form-group">
                    <label>Oxigênio</label>
                    <div class="position-relative has-icon-left">
                        <input type="text" id="oxigenio" class="form-control" name="oxigenio" value="<?php echo $rowRetorno->oxigenio ?>" id="oxigenio">
                        <div class="form-control-position" style="top: 0px">
                            <img src="app-assets/img/svg/o2.png" alt="\" height="20" width="20">
                        </div>
                    </div>
                </div>
            </div>

            <!-- LINHA 2 -->
            <div class='row'>
                <div class="col-3 form-group">
                    <!-- Pulso -->
                    <label>Pulso</label>
                    <div class="position-relative has-icon-left">
                        <input type="text" id="pulso" class="form-control square" name="pulso" value="<?php echo $rowRetorno->pulso ?>" id="pulso">
                        <div class="form-control-position" style="top: 0px">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                    </div>
                </div>
                <div class="col-3 form-group">
                    <!-- Temperatura -->
                    <label>Temperatura</label>
                    <div class="position-relative has-icon-left">
                        <input type="text" class="form-control square" name="temp" value="<?php echo $rowRetorno->temperatura ?>" id="temp">
                        <div class="form-control-position" style="top: 0px">
                            <i class="fas fa-thermometer" style="font-size: 15pt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-3 form-group">
                    <!-- Glicemia -->
                    <label>Glicemia</label>
                    <div class="position-relative has-icon-left">
                        <input type="text" id="glicose" class="form-control square" value="<?php echo $rowRetorno->glicose ?>" name="glicose" id="glicose">
                        <div class="form-control-position" style="top: 0px">
                            <img src="app-assets/img/svg/glicose.png" alt="\" height="25" width="18">
                        </div>
                    </div>
                </div>
                <div class="col-3 form-group">
                    <!-- Peso -->
                    <label>Peso</label>
                    <div class="position-relative has-icon-left">
                        <input type="text" id="peso" class="form-control square" value="<?php echo $rowRetorno->peso ?>" name="peso" id="peso">
                        <div class="form-control-position" style="top: 0px">
                            <i class="fas fa-weight" aria-hidden="true" style="font-size: 15pt;"></i></h1>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>


<div class="row mt-2">
    <div class="col-12">
        <p class="text-center"><img src="app-assets/img/svg/dor.png" alt="\" height="25" width="25"> Dor</p>
        <input type="range" id="dor" class="slider mr-3" name="dor" min="0" max="9" value="<?php echo $rowRetorno->dor ?>">
        <strong id="valor" style="font-size: 20pt;"></strong>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label class="control-label">Queixa</label>
        <textarea class="form-control" name="queixa" id="queixa" rows="5"><?php echo $rowRetorno->queixa ?></textarea>
    </div>
</div>
<?php
$prioridades = array(
    'VERMELHO' => utf8_decode('EMERGÊNCIA - VERMELHO'),
    'LARANJA' => utf8_decode('MUITO URGENTE - LARANJA'),
    'AMARELO' => utf8_decode('URGENTE - AMARELO'),
    'VERDE'  => utf8_decode('POUCO URGENTE - VERDE'),
    'AZUL'   => utf8_decode('NÃO URGENTE - AZUL'),
    'BRANCO' => utf8_decode('NÃO RESPONDEU'),
    'ORIENTACOESVACINAS' => utf8_decode('ORIENTACOES/VACINAS'),
);
?>
<div class="row">
    <div class="col-md-6">
        <label class="control-label">Prioridade</label>
        <select class="form-control" style='font-size:small;' name="prioridadeModal" id="prioridadeModal">
            <option value="">Selecione a Prioridade</option>
            <?php
            foreach ($prioridades as $key => $value) {

                if ($rowRetorno->prioridade == $key) {
                    echo '<option value="' . $key . '" selected>' . utf8_encode($value) . '</option>';
                } else {
                    echo '<option value="' . $key . '">' . utf8_encode($value) . '</option>';
                }
            } ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="control-label">Consultorio</label>
        <select class="form-control" style='font-size:small;' name="consultorioModal" id="consultorioModal">
            <option value="">Selecione o Consultorio</option>
            <?php


            if ($rowRetorno->encaminhamentos == utf8_encode('Ortopedia')) {

                echo '<option value="' . utf8_encode('Ortopedia') . '" selected >' . utf8_encode('Ortopedia') . '</option>';
                echo '<option selected value="' . utf8_encode('Consultorio Adulto') . '">' . utf8_encode('Consultorio Adulto') . '</option>';
                echo '<option value="' . utf8_encode('Ala Vermelha') . '">' . utf8_encode('Ala Vermelha') . '</option>';
                echo '<option value="' . utf8_encode('Troca de Sonda') . '">' . utf8_encode('Troca de Sonda') . '</option>';
            } else if ($rowRetorno->encaminhamentos == utf8_encode('Consultorio Adulto')) {

                echo '<option value="' . utf8_encode('Ortopedia') . '">' . utf8_encode('Ortopedia') . '</option>';
                echo '<option selected value="' . utf8_encode('Consultorio Adulto') . '" selected >' . utf8_encode('Consultorio Adulto') . '</option>';
                echo '<option value="' . utf8_encode('Ala Vermelha') . '">' . utf8_encode('Ala Vermelha') . '</option>';
                echo '<option value="' . utf8_encode('Troca de Sonda') . '">' . utf8_encode('Troca de Sonda') . '</option>';
            } else if ($rowRetorno->encaminhamentos == utf8_encode('Ala Vermelha')) {

                echo '<option value="' . utf8_encode('Ortopedia') . '">' . utf8_encode('Ortopedia') . '</option>';
                echo '<option selected value="' . utf8_encode('Consultorio Adulto') . '">' . utf8_encode('Consultorio Adulto') . '</option>';
                echo '<option value="' . utf8_encode('Ala Vermelha') . '" selected>' . utf8_encode('Ala Vermelha') . '</option>';
                echo '<option value="' . utf8_encode('Troca de Sonda') . '">' . utf8_encode('Troca de Sonda') . '</option>';
            } else if ($rowRetorno->encaminhamentos == utf8_encode('Troca de Sonda')) {

                echo '<option value="' . utf8_encode('Ortopedia') . '">' . utf8_encode('Ortopedia') . '</option>';
                echo '<option selected value="' . utf8_encode('Consultorio Adulto') . '">' . utf8_encode('Consultorio Adulto') . '</option>';
                echo '<option value="' . utf8_encode('Ala Vermelha') . '">' . utf8_encode('Ala Vermelha') . '</option>';
                echo '<option value="' . utf8_encode('Troca de Sonda') . '" selected>' . utf8_encode('Troca de Sonda') . '</option>';
            } else {

                echo '<option value="' . utf8_encode('Ortopedia') . '">' . utf8_encode('Ortopedia') . '</option>';
                echo '<option selected value="' . utf8_encode('Consultorio Adulto') . '">' . utf8_encode('Consultorio Adulto') . '</option>';
                echo '<option value="' . utf8_encode('Ala Vermelha') . '">' . utf8_encode('Ala Vermelha') . '</option>';
                echo '<option value="' . utf8_encode('Troca de Sonda') . '">' . utf8_encode('Troca de Sonda') . '</option>';
            }
            ?>
            <option value="Realizado/Encaminhado">Realizado/Encaminhado</option>
        </select>
    </div>
</div>
<input type="hidden" name="transacaoModal" id="transacaoModal" value="<?php echo $_GET['transacao']; ?>">
</div>
<script>
    var slider = document.getElementById("dor");
    var output = document.getElementById("valor");
    output.innerHTML = slider.value;

    slider.oninput = function() {
        output.innerHTML = this.value;
    }

    function limitarTextArea(campo) {
        var string = campo.value;
        var novastring = "";
        var linhas = new Array();
        var trocarLinha = false;
        linhas = string.split("\n");
        var contador = linhas.length;

        for (x in linhas) {
            if (linhas[x].length > campo.cols - 2) {
                linhas[x] = linhas[x].substring(0, campo.cols);
                trocarLinha = true;
            }
            if (x < campo.rows) {
                novastring += linhas[x] + "\n";
            }
        }

        if (contador > campo.rows || trocarLinha) {
            campo.value = novastring.substring(0, novastring.length - 1);
        }
        return contador <= campo.rows;
    }
</script>