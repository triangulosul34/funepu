<?php

$relatorio = $_GET['evolucao'];
include('verifica.php');
include('conexao.php');

$stmt = "SELECT a.assistente_social_id,a.atendimento_id,a.data,a.hora,b.nome,a.relatorio, d.nome as paciente
			FROM assistente_social a
				left join pessoas b ON b.username = a.usuario
				left join atendimentos c on c.transacao = a.atendimento_id
				left join pessoas d on d.pessoa_id = c.paciente_id
			WHERE a.assistente_social_id =" . $relatorio . " order by 1 desc";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);

?>
<table class="table condensed width-full padding-0" id="conteudoPrescicao">

    <tr>
        <td>

            <div class="col-sm-3 margin-top-10" id="cuidados">
                <label class="control-label">Nª do Relatorio</label>
                <input style="background-color: #FFFFFF; border-color: #dddddd" value="<?php echo $relatorio; ?>" class="form-control" readonly>
            </div>


            <div class="col-sm-9 margin-top-10" id="cuidados">
                <label class="control-label">Paciente</label>
                <input style="background-color: #FFFFFF; border-color: #dddddd" value="<?php echo $row->paciente; ?>" class="form-control" readonly>
            </div>
            <div class="col-sm-12 margin-top-20" id="cuidados">
                <label class="control-label">Relatorio</label>
                <textarea style="background-color: #FFFFFF; border-color: #dddddd" name="observacao" rows="12" class="form-control" readonly><?php echo $row->relatorio; ?></textarea>
            </div>


        </td>
    </tr>
</table>