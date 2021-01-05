<?php

require 'tsul_ssl.php';

$evolucao = $_GET['evolucao'];
include 'verifica.php';
include 'conexao.php';

$stmt = 'SELECT a.evolucao_id,a.atendimento_id,a.tipo,a.data,a.hora,b.nome,a.evolucao, d.nome as paciente,a.pressao_arterial,a.frequencia_cardiaca,
				a.frequencia_respiratoria,a.saturacao_ox,a.glicemia,a.diurese,a.temperatura
			FROM evolucoes a
				left join pessoas b ON b.username = a.usuario
				left join atendimentos c on c.transacao = a.atendimento_id
				left join pessoas d on d.pessoa_id = c.paciente_id
			WHERE a.evolucao_id =' . $evolucao . ' order by 1 desc';
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);

?>
<table class="table condensed width-full padding-0" id="conteudoPrescicao">

    <tr>
        <td>

            <div class="col-sm-3 margin-top-10" id="cuidados">
                <label class="control-label">Nª da Evolução</label>
                <input style="background-color: #FFFFFF; border-color: #dddddd"
                    value="<?php echo $evolucao; ?>"
                    class="form-control" readonly>
            </div>


            <div class="col-sm-9 margin-top-10" id="cuidados">
                <label class="control-label">Paciente</label>
                <input style="background-color: #FFFFFF; border-color: #dddddd"
                    value="<?php echo ts_decodifica($row->paciente); ?>"
                    class="form-control" readonly>
            </div>

            <?php if ($row->tipo == 8 or $row->tipo == 6) { ?>

            <div class="col-md-4">
                <label class="control-label">Temperatura</label>
                <input type="text" style="background-color: #FFFFFF; border-color: #dddddd" class="form-control"
                    value="<?php echo $row->temperatura; ?>"
                    name="temp" readonly>
            </div>

            <div class="col-md-4">
                <label class="control-label">Pressão Arterial</label>
                <input type="text" style="background-color: #FFFFFF; border-color: #dddddd" class="form-control"
                    value="<?php echo $row->pressao_arterial; ?>"
                    name="pa" readonly>
            </div>

            <div class="col-md-4">
                <label class="control-label">Freq. Respiratória</label>
                <input type="text" style="background-color: #FFFFFF; border-color: #dddddd" name="fr"
                    class="form-control"
                    value="<?php echo $row->frequencia_respiratoria; ?>"
                    readonly>
            </div>

            <div class="col-md-3">
                <label class="control-label">Freq. Cardíaca</label>
                <input type="text" style="background-color: #FFFFFF; border-color: #dddddd" name="fc"
                    class="form-control"
                    value="<?php echo $row->frequencia_cardiaca; ?>"
                    readonly>
            </div>

            <div class="col-md-3">
                <label class="control-label">Sat O²</label>
                <input type="text" style="background-color: #FFFFFF; border-color: #dddddd" class="form-control"
                    value="<?php echo $row->saturacao_ox; ?>"
                    name="sat" readonly>
            </div>

            <div class="col-md-3">
                <label class="control-label">Glicemia</label>
                <input type="text" style="background-color: #FFFFFF; border-color: #dddddd" name="glicemia"
                    value="<?php echo $row->glicemia; ?>"
                    class="form-control" readonly>
            </div>

            <div class="col-md-3">
                <label class="control-label">Diurese</label>
                <input type="text" style="background-color: #FFFFFF; border-color: #dddddd"
                    value="<?php echo $row->diurese; ?>"
                    class="form-control" name="diurese" readonly>
            </div>

            <?php } ?>

            <div class="col-sm-12 margin-top-20" id="cuidados">
                <label class="control-label">Evolução</label>
                <textarea style="background-color: #FFFFFF; border-color: #dddddd" name="observacao" rows="12"
                    class="form-control"
                    readonly><?php echo $row->evolucao; ?></textarea>
            </div>


        </td>
    </tr>
</table>