<?php
$procedimentos = $_GET['procedimento'];
$procedimentos = explode(",", $procedimentos);






function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}

include('verifica.php');
include('Config.php');
$prontuario  = $_GET['prontuario'];
$prioridade  = $_GET['prioridade'];
$atendimento = $_GET['atendimento'];
$data        = $_GET['data_atendimento'];
$profissional = $_GET['profissional'];
$origem      = $_GET['origem'];
$paciente_id = $_GET['paciente_id'];
$pedido_id = 0;
$guia_id = 0;
$nao = 0;
$aux = 0;


include('conexao.php');
$stmt = "select * from pessoas where username       = '$profissional';";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$profissional = $row->pessoa_id;

include('conexao.php');
$stmt = "select * from atendimentos where transacao = '$atendimento';";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$data = inverteData($row->dat_cad);

include('conexao.php');
$stmt = "select nextval('pedidos_transacao');";
$sth = pg_query($stmt) or die($stmt);
$row = pg_fetch_object($sth);
$transacao = str_pad($row->nextval, 7, "0", STR_PAD_LEFT);


include('conexao.php');
$dt_transacao = date('Y-m-d');
$dt_solicitacao = date('Y-m-d');
$horacad = date('H:i');
$stmt = "insert into pedidos (transacao, cad_user, dat_cad, convenio_id, dt_solicitacao, paciente_id, solicitante_id,  tipo, 
		num_socio, status,  box, hora_cad, local)
	values ($transacao, '$usuario', '$dt_transacao', '1', '$dt_solicitacao', $prontuario, $profissional, '$origem', '00', 'Cadastrado',  '1',  
		'$horacad', '01'  );";
$sth = pg_query($stmt) or die($stmt);

$data = date('Y-m-d');
$hora = date('H:i');
$ip = $_SERVER['REMOTE_ADDR'];
include('conexao.php');
$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora, ip) 
			values ('$usuario','SOLICITOU EXAME','$atendimento','$data','$hora', '$ip')";
$sthLogs = pg_query($stmtLogs) or die($stmtLogs);

//echo $stmt;


foreach ($procedimentos as $procedimento) {
    if ($procedimento != '') {
        $inicio = date('Y-m-01');
        $parcelas = 1;
        $data_termino = new DateTime($inicio);
        $data_termino->add(new DateInterval('P' . $parcelas . 'M'));
        $termino_pagamento = $data_termino->format('Y-m-d');
        $parcelas = 1;
        $data_termino = new DateTime($termino_pagamento);
        $data_termino->sub(new DateInterval('P' . $parcelas . 'D'));
        $dt_fim_mes = $data_termino->format('Y-m-d');


        include('conexao.php');
        $stmt = "select coalesce(qtde_mes,0), necessita_autorizacao, coalesce(qtde_mes,0)-coalesce(sum(i.qtde),0) as saldo
			from procedimentos p left join itenspedidos i on p.procedimento_id=i.exame_id
			left join pedidos e on i.transacao=e.transacao
			where dat_cad between '$inicio' and '$dt_fim_mes' and procedimento_id=" . $procedimento . " group by 1,2";
        $sth = pg_query($stmt) or die($stmt);
        //echo $stmt;
        $row = pg_fetch_object($sth);
        $qtde_mes = $row->saldo;
        $autorizacao = $row->necessita_autorizacao;
        $situacao = 'Autorizado';

        include('conexao.php');
        $sql = "select exames_laboratoriais from procedimentos_laboratoriais where procedimentos_id=" . $procedimento . "
			union
			select exames_laboratoriais from procedimentos where procedimento_id=" . $procedimento . "";
        $result = pg_query($sql) or die($sql);
        $row = pg_fetch_object($result);
        $laboratorio = $row->exames_laboratoriais;

        if ($laboratorio == 1) {
            include('conexao_laboratorio.php');
            $sql = "select * from pessoas where pessoa_id_origem = $paciente_id and origem = '" . PORIGEM_CONFIG . "'";
            $result = pg_query($sql) or die($sql);
            if ($row = pg_fetch_object($result) == "") {
                include('conexao.php');
                $sql = "select * from pessoas where pessoa_id = $paciente_id";
                $result = pg_query($sql) or die($sql);
                while ($row = pg_fetch_object($result)) {
                    if ($row->sexo == 'M') {
                        $sexo = 1;
                    } else {
                        $sexo = 2;
                    }
                    include('conexao_laboratorio.php');
                    $sqll = "INSERT INTO pessoas(tipo_pessoa, nome, nome_social, nome_mae, nome_pai, rg, uf_identidade, cpf, data_nascimento, sexo, necessidades_especiais, celular, telefone, email, foto, cep, rua, bairro, cidade, estado, usuario, senha, crbm, pessoa_id_origem, origem) 
						VALUES (3, '$row->nome', '', '$row->nome_mae', '$row->pai', '$row->identidade', '$row->org_expeditor', '$row->cpf', '$row->dt_nasc', $sexo, 1, '$row->celular', '$row->telefone', '', '', '$row->cep', '$row->endereco', '$row->bairro', '$row->cidade', '$row->estado', 'NA', 'NA', '', $paciente_id, 2)";
                    $resultl = pg_query($sqll) or die($sqll);
                }
            }
            if ($procedimento != 741 and $procedimento != 779 and $procedimento != 740 and $procedimento != 800 and $procedimento != 772 and $procedimento != 763 and $prioridade != 'VERMELHO' and $prioridade != 'LARANJA' and $prioridade != 'AMARELO') {
                include('conexao.php');
                $sqlt = "select * from itenspedidos where exame_id = $procedimento and pessoa_id = $paciente_id and atendimento_id = '$atendimento' and now() - data_solicitacao < '18:00:00'";
                $resultt = pg_query($sqlt) or die($sqlt);
                if ($rowt = pg_fetch_object($resultt) != '') {
                    $nao = 1;
                    $sqlt = "select * from procedimentos where procedimento_id = $procedimento";
                    $resultt = pg_query($sqlt) or die($sqlt);
                    $rowt = pg_fetch_object($resultt);
                    echo '<script>alert("Não pode solicitar outro ' . $rowt->descricao . ' dentro de 18horas")</script>';
                }
            }
        }

        if ($autorizacao == 'N') {
            //$situacao='Autorizado';
            /*
				echo "<script>";
				echo "sweetAlert('A solicitacao foi realizada e Autorizada com Sucesso!','','success')";
				echo "</script>	";
				*/
        }
        if ($autorizacao == 'S') {
            //$situacao='Aut.Pendente';
            /*
				echo "<script>";
				echo "sweetAlert('A solicitacao foi realizada e  aguarda Autorização!','','error')";
				echo "</script>	";
				*/
        }
        if ($autorizacao == 'Q') {
            if ($qtde_mes > 0) {
                //$situacao='Autorizado';
                /*
					echo "<script>";
					echo "sweetAlert('A solicitacao foi realizada e Autorizada com Sucesso!','','success')";
					echo "</script>	";	
					*/
            } else {
                //$situacao='Aut.Pendente';
                /*
					echo "<script>";
					echo "sweetAlert('A solicitacao foi realizada e  aguarda Autorização!','','error')";
					echo "</script>	";	
					*/
            }
        }

        //if($procedimento == 740 or $procedimento == 800){
        if ($nao != 1) {
            if ($procedimento == 740 or $procedimento == 800) {
                include('conexao_laboratorio.php');
                if ($pedido_id == 0) {
                    $sql = "select nextval('pedidos_pedido_id_seq')";
                    $result = pg_query($sql) or die($sql);
                    $row = pg_fetch_object($result);
                    $pedido_id = $row->nextval;

                    $sql = "select nextval('pedido_guia_pedido_guia_id_seq')";
                    $result = pg_query($sql) or die($sql);
                    $row = pg_fetch_object($result);
                    $guia_id = $row->nextval;
                }
                $sql = "SELECT * FROM procedimentos WHERE procedimentos_id = $procedimento";
                $result = pg_query($sql) or die($sql);
                $row = pg_fetch_object($result);

                $cod = str_pad($row->setor, 2, '0', STR_PAD_LEFT) . str_pad($pedido_id, 7, '0', STR_PAD_LEFT);
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 740, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);
                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 740, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);

                include('conexao_laboratorio.php');
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 772, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);

                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 772, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);

                include('conexao_laboratorio.php');
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 763, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);

                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 763, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);

                include('conexao_laboratorio.php');
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 761, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);

                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 761, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);

                include('conexao_laboratorio.php');
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 741, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);

                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 741, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);


                include('conexao_laboratorio.php');
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 800, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);

                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 800, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);
                /*$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

					$sth = pg_query ( $stmt ) or die ( $stmt );
					$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

					$sth = pg_query ( $stmt ) or die ( $stmt );*/
                $aux++;
            } else {
                if ($laboratorio == 1) {
                    include('conexao_laboratorio.php');
                    if ($pedido_id == 0) {
                        $sql = "select nextval('pedidos_pedido_id_seq')";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        $pedido_id = $row->nextval;

                        $sql = "select nextval('pedido_guia_pedido_guia_id_seq')";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        $guia_id = $row->nextval;
                    }
                    $sql = "SELECT * FROM procedimentos WHERE procedimentos_id = $procedimento";
                    $result = pg_query($sql) or die($sql);
                    $row = pg_fetch_object($result);

                    if ($procedimento == 779) {
                        $cod = str_pad($row->setor, 2, '0', STR_PAD_LEFT) . str_pad($pedido_id, 6, '0', STR_PAD_LEFT) . "1";
                        $pedido_tp1 = $pedido_id . "1";
                        $guia_tp1 = $guia_id . "1";
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_tp1, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                    } else if ($procedimento == 821) {
                        $cod = str_pad($row->setor, 2, '0', STR_PAD_LEFT) . str_pad($pedido_id, 6, '0', STR_PAD_LEFT) . "2";
                        $pedido_tp3 = $pedido_id . "2";
                        $guia_tp3 = $guia_id . "2";
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_tp3, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                    } else if ($procedimento == 822) {
                        $cod = str_pad($row->setor, 2, '0', STR_PAD_LEFT) . str_pad($pedido_id, 6, '0', STR_PAD_LEFT) . "3";
                        $pedido_tp6 = $pedido_id . "3";
                        $guia_tp6 = $guia_id . "3";
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_tp6, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                    } else {
                        $cod = str_pad($row->setor, 2, '0', STR_PAD_LEFT) . str_pad($pedido_id, 7, '0', STR_PAD_LEFT);
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_id, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                    }


                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id, data_solicitacao) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento', '" . date('Y-m-d H:m:s') . "')";
                    $sth = pg_query($stmt) or die($stmt);
                } else {
                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";
                    $sth = pg_query($stmt) or die($stmt);
                }
            }
        }
        $nao = 0;
        $laboratorio = 0;
    }
}
if ($pedido_id != 0) {

    include('conexao_laboratorio.php');
    $sqll = "select * from pessoas where pessoa_id_origem = $paciente_id and origem = '" . PORIGEM_CONFIG . "'";
    $resultl = pg_query($sqll) or die($sqll);
    $rowl = pg_fetch_object($resultl);

    $data = date('Y-m-d');
    $hora = date('H:i:s');

    $cod = "2" . date('d-m-y') . $pedido_id;
    $cod = str_replace("-", "", "$cod");

    if ($pedido_tp1 != '') {
        include('conexao_laboratorio.php');
        $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id) 
			VALUES ($pedido_tp1, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento)";
        $resultls = pg_query($sqlls) or die($sqlls);

        include('conexao_laboratorio.php');
        $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_tp1, 1, 0, 0, 0, '02', '$data', $profissional, $pedido_tp1)";
        $result = pg_query($sql) or die($sql);
    } else if ($pedido_tp3 != '') {
        include('conexao_laboratorio.php');
        $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id) 
			VALUES ($pedido_tp3, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento)";
        $resultls = pg_query($sqlls) or die($sqlls);

        include('conexao_laboratorio.php');
        $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_tp3, 1, 0, 0, 0, '02', '$data', $profissional, $pedido_tp3)";
        $result = pg_query($sql) or die($sql);
    } else if ($pedido_tp6 != '') {
        include('conexao_laboratorio.php');
        $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id) 
			VALUES ($pedido_tp6, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento)";
        $resultls = pg_query($sqlls) or die($sqlls);

        include('conexao_laboratorio.php');
        $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_tp6, 1, 0, 0, 0, '02', '$data', $profissional, $pedido_tp6)";
        $result = pg_query($sql) or die($sql);
    } else {
        include('conexao_laboratorio.php');
        $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id) 
			VALUES ($pedido_id, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento)";
        $resultls = pg_query($sqlls) or die($sqlls);

        include('conexao_laboratorio.php');
        $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_id, 1, 0, 0, 0, '02', '$data', $profissional, $pedido_id)";
        $result = pg_query($sql) or die($sql);
    }
}

echo $aux;
?>

<thead>
    <tr>
        <th width='5%'></th>
        <th width='30%'>Data / Situação</th>
        <th width='60%'>Descrição</th>
        <th width='10%'>Laudo</th>
        <!--<th width='10%'>Ação</th>-->
    </tr>
</thead>

<tbody>
    <?php
    include('conexao.php');
    $stmt = "SELECT b.dat_cad,c.formulario, a.exame_nro,a.arquivo_upload, a.pessoa_id,a.transacao, c.descricao, a.situacao, a.versao, c.exames_laboratoriais, c.procedimento_id FROM itenspedidos a 
                                                                LEFT JOIN pedidos b ON a.transacao=b.transacao 
                                                                LEFT JOIN procedimentos c ON a.exame_id=c.procedimento_id WHERE a.pessoa_id=" . $paciente_id . " and c.exames_laboratoriais is null order by dat_cad desc, a.exame_id";
    $sth = pg_query($stmt) or die($stmt);
    //echo $stmt;
    $x = 0;
    $data = '';
    $procedimento_lab = '';
    while ($row = pg_fetch_object($sth)) {
        if ($row->exames_laboratoriais == 1 and substr($row->dat_cad, 0, 10) >= '2019-01-08') {
            //if(($data != substr($row->dat_cad, 0, 10) or $data != date('Y-m-d', strtotime("+1 days",strtotime(substr($row->dat_cad, 0, 10))))) and $procedimento_lab != $row->procedimento_id){
            include('conexao_laboratorio.php');
            $sql = "SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, e.descricao 
																FROM pedidos a
																INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
																INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
																INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
																LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
																LEFT JOIN modalidades f ON f.modalidade_id = e.setor where d.exame_id = " . $row->procedimento_id . " and a.data = '" . substr($row->dat_cad, 0, 10) . "' and c.origem = '" . ORIGEM_CONFIG . "' and b.origem = '" . PORIGEM_CONFIG . "' and pessoa_id_origem = $prontuario order by a.data, a.horario";
            $result = pg_query($sql) or die($sql);
            while ($rows = pg_fetch_object($result)) {

                if ($rows->exame_id == $row->procedimento_id) {
                    echo "<tr>";
                    echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\" value=\"" . $row->exame_nro . "\"><label></label></div></td>";
                    echo "<td>" . inverteData($rows->data) . "</td>";
                    echo "<td>$row->descricao</td>";
                    if ($rows->liberado == 1) {
                        echo "<td><a href='http://" . IP_CONFIG . "/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a></td>";
                    }
                    echo "</tr>";
                }
            }
            $data = $row->dat_cad;
            $procedimento_lab = $row->procedimento_id;
            $x = $x + 1;
            //}
        } else {
            $x = $x + 1;
            if ($row->situacao == 'Aut.Pendente') {
                echo "<tr class='bg-danger' >";
            } else {
                echo "<tr>";
            }
            echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\"    value=\"" . $row->exame_nro . "\"><label></label></div></td>";
            echo "<td>" . inverteData(substr($row->dat_cad, 0, 10)) . ' ' . $row->situacao . $row->versao . "</td>";
            echo "<td>" . $row->descricao . "</td>";
            include('conexao.php');
            $sqlDetalhe = "SELECT * FROM arquivos_documentos WHERE transacao = " . $row->exame_nro;
            $sthDet = pg_query($sqlDetalhe) or die($sqlDetalhe);
            $rowDetalhe = pg_fetch_object($sthDet);
            echo "<td>";
            if ($rowDetalhe->arquivo != '') {
                echo "<a href='imagens/documentos/$rowDetalhe->arquivo' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
            }

            if ($row->arquivo_upload != '') {
                echo "<a href='arquivos/exames/$row->arquivo_upload' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
            }

            if ($row->formulario == 'A') {
                echo "<button type=\"button\" class=\"btn btn-sm btn-icon btn-pure btn-default delete-row-btn\" data-toggle=\"tooltip\" data-original-title=\"Visualizar\"><i class=\"icon wb-print\" aria-hidden=\"true\" onclick=\"openInNewTab('relApac.php?id=$row->exame_nro')\"></i></button>";
            }
            if ($row->situacao == 'Finalizado') {
                if ($row->versao != '2') {
                    echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
                } else {
                    echo "<a href='http://" . IP_CONFIG . "/laboratorio/html/relExamesb.php?local=SB&transacao=$row->exame_nro'' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
                }
            }

            if ($row->situacao == 'Impresso') {
                echo "<a href='rellaudo.php?id=$row->exame_nro' target='_blank' class=\"btn btn-pure btn-danger icon wb-search\"></a>";
            }
            echo "</td>";

            //echo"<td class='small'><a href=\"deletarexames.php?id=$row->transacao&atendimento=$transacao\" data-toggle=\"tooltip\" data-original-title=\"Deletar Pedido de Exame\"><i class=\"fa fa-times text-danger\" aria-hidden=\"true\"></i></a></td>";

            echo "</tr>";
        }
    }

    ?>
</tbody>