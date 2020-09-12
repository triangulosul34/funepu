<?php
include('verifica.php');
include('conexao.php');

$stmt = "select a.transacao, a.paciente_id, case when EXTRACT(year from AGE(CURRENT_DATE, c.dt_nasc)) >= 60 then 0 else 1 end pidade, a.nec_especiais, a.status, a.prioridade, a.hora_cad,a.hora_triagem,a.hora_atendimento, 
	a.dat_cad, c.nome, c.nome_social, k.origem, a.tipo, a.coronavirus 
	from atendimentos a 
	left join pessoas c on a.paciente_id=c.pessoa_id 
	left join tipo_origem k on k.tipo_id=cast(a.tipo as integer) 
	WHERE status = 'Aguardando Triagem' and dat_cad between '" . date('Y-m-d', strtotime("-1 days")) . "' and '" . date('Y-m-d') . "' and 
	cast(tipo as integer) != '6' and tipo_at is null
	ORDER by 3, 1";
$sth = pg_query($stmt) or die($stmt);
//echo $stmt; 
while ($row = pg_fetch_object($sth)) {

    if ($row->prioridade   == 'AMARELO') {
        $classe = "style=\"background-color:gold\"";
    }
    if ($row->prioridade   == 'VERMELHO') {
        $classe = "class='bg-danger'";
    }
    if ($row->prioridade   == 'VERDE') {
        $classe = "class='bg-success'";
    }
    if ($row->prioridade   == 'AZUL') {
        $classe = "class='bg-primary'";
    }
    if ($row->prioridade   == 'LARANJA') {
        $classe = "class='bg-warning'";
    }
    if ($row->prioridade   == '') {
        $classe = "style=\"background-color:Gainsboro\"";
    }

    $ip = getenv("REMOTE_ADDR");
    echo "<tr " . $classe . ">";
    if ($row->coronavirus == 1) {
        echo "<td class='blink'>" . $row->transacao . "</td>";
        echo "<td class='blink'>" . date('d/m/Y',  strtotime($row->dat_cad)) . " - " . $row->hora_cad . "</td>";
        if ($row->nome_social == '') {
            echo "<td class='blink'>" . $row->nome;
        } else {
            echo "<td class='blink'>" . $row->nome_social . " (" . $row->nome . ")";
        }
        if ($row->nec_especiais != 'Nenhuma') {
            echo "<br>Paciente com deficiencia $row->nec_especiais";
        }
        echo "</td>";
        echo "<td class='blink'>" . $row->origem . "</td>";
        echo "<td class='blink'>" . $row->status . "</td>";
    } else {
        echo "<td>" . $row->transacao . "</td>";
        echo "<td>" . date('d/m/Y',  strtotime($row->dat_cad)) . " - " . $row->hora_cad . "</td>";
        if ($row->nome_social == '') {
            echo "<td>" . $row->nome;
        } else {
            echo "<td>" . $row->nome_social . " (" . $row->nome . ")";
        }
        if ($row->nec_especiais != 'Nenhuma') {
            echo "<br>Paciente com deficiencia $row->nec_especiais";
        }
        echo "</td>";
        echo "<td>" . $row->origem . "</td>";
        echo "<td>" . $row->status . "</td>";
    }
    echo "<td>";
    //if (($perfil == '06' or $perfil == '04' or $perfil == '08' or $perfil == '15') and isset($_SESSION['box'])) {
    echo "<a id=\"triagemmanual\" data-id=\"$row->transacao\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn triagemmanual\" data-target=\"#modalConteudo\" data-toggle=\"modal\" data-original-title=\"Triagem Manual\" onClick=\"valorTriagem(this);\">
        <i class=\"fas fa-hand-holding-medical\"></i></a>";
    //}
    echo "</td>";

    echo "</tr>";
    $i++;
}
echo "
	<tr align=\"center\">
		<td colspan=\"5\">
			<h3>Pacientes aguardando triagem: $i</h3>
		</td>
	</tr>
    ";

?>
<script>
    $(".triagemmanual").click(function() {
        $("#confTriagem").attr("disabled", true);
        $('#conteudoModal').html("");
        $('#conteudoModal').append("<h2>CARREGANDO...</h2>");

        setTimeout(function() {
            $("#confTriagem").attr("disabled", false);
            // alert("Handler for .click() called.");
        }, 3000);

    });
</script>