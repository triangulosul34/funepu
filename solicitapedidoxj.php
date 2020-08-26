<?php
$procedimentos = $_GET['procedimento'];
$procedi = $_GET['procedimento'];
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
$df = 1;
$aux2 = '';


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
$n = 0;


include('conexao.php');
$dt_transacao = date('Y-m-d');
$dt_solicitacao = date('Y-m-d');
$horacad = date('H:i');
$stmt = "insert into pedidos (transacao, cad_user, dat_cad, convenio_id, dt_solicitacao, paciente_id, solicitante_id,  tipo, 
		num_socio, status,  box, hora_cad, local, atendimento_id)
	values ($transacao, '$usuario', '$dt_transacao', '1', '$dt_solicitacao', $prontuario, $profissional, '$origem', '00', 'Cadastrado',  '1',  
		'$horacad', '" . ORIGEM_CONFIG . "', $atendimento  );";
$sth = pg_query($stmt) or die($stmt);

$data = date('Y-m-d');
$hora = date('H:i');
$ip = $_SERVER['REMOTE_ADDR'];
include('conexao.php');
$stmtLogs = "insert into logs (usuario,tipo_acao,atendimento_id,data,hora, ip) 
			values ('$usuario','SOLICITOU EXAME','$atendimento','$data','$hora', '$ip')";
$sthLogs = pg_query($stmtLogs) or die($stmtLogs);

//echo $stmt;

if (in_array("82", $procedimentos) and in_array("81", $procedimentos)) {
    $n = 1;
}

include('conexao_laboratorio.php');
$sql = "select count(*), exame_id from pedido_item where " . ORIGEM_CONFIG  . str_pad($atendimento, 7, '0', STR_PAD_LEFT) . " = substring(ordem_servico, 5,9)::integer and exame_id in ($procedi) group by 2 order by count";
$result = pg_query($sql) or die($sql);
$row = pg_fetch_object($result);

if ($row->count != '') {
    $df = $row->count + 1;
}


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
        //$row = pg_fetch_object ( $result );
        //$laboratorio = $row->exames_laboratoriais;
        $laboratorio = 1;

        if ($laboratorio == 1) {
            include('conexao_laboratorio.php');
            $sql = "select * from pessoas where pessoa_id_origem = $paciente_id and origem = " . PORIGEM_CONFIG;
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
						VALUES (3, '$row->nome', '', '$row->nome_mae', '$row->pai', '$row->identidade', '$row->org_expeditor', '$row->cpf', '$row->dt_nasc', $sexo, 1, '$row->celular', '$row->telefone', '', '', '$row->cep', '$row->endereco', '$row->bairro', '$row->cidade', '$row->estado', 'NA', 'NA', '', $paciente_id, " . PORIGEM_CONFIG . ")";
                    $resultl = pg_query($sqll) or die($sqll);
                }
            }
            if ($procedimento != 741 and $procedimento != 779 and $procedimento != 740 and $procedimento != 800 and $procedimento != 772 and $procedimento != 763 and $prioridade != 'VERMELHO' and $prioridade != 'LARANJA' and $prioridade != 'AMARELO') {
                include('conexao.php');
                $sqlt = "select * from itenspedidos where exame_id = $procedimento and pessoa_id = $paciente_id and atendimento_id = '$atendimento' and now() - data_solicitacao < '18:00:00'";
                //$resultt = pg_query($sqlt) or die($sqlt);
                if ($rowt = pg_fetch_object($resultt) != '') {
                    $nao = 1;
                    $sqlt = "select * from procedimentos where procedimento_id = $procedimento";
                    $resultt = pg_query($sqlt) or die($sqlt);
                    $rowt = pg_fetch_object($resultt);
                    echo '<script>alert("Não pode solicitar outro ' . $rowt->descricao . ' dentro de 18horas")</script>';
                }
            }
            include('conexao_laboratorio.php');
            $sqll = "select * from pessoas where pessoa_id_origem = $paciente_id and origem = " . PORIGEM_CONFIG;
            $resultl = pg_query($sqll) or die($sqll);
            $rowl = pg_fetch_object($resultl);

            include('conexao_laboratorio.php');
            $sqlt = "select * from pedidos a inner join pedido_guia b on a.pedido_id = b.pedido_id inner join pedido_item c on b.pedido_guia_id = c.pedido_guia_id where a.pessoa_id = $rowl->pessoa_id and atendimento_id = '$atendimento' and now() - (a.data || ' ' || a.horario)::timestamp  < '01:00:00' and exame_id = $procedimento";
            $resultt = pg_query($sqlt) or die($sqlt);
            if ($rowt = pg_fetch_object($resultt) != '') {
                $nao = 1;
                $sqlt = "select * from procedimentos where procedimentos_id = $procedimento";
                $resultt = pg_query($sqlt) or die($sqlt);
                $rowt = pg_fetch_object($resultt);
                echo '<script>alert("Não pode solicitar outro ' . $rowt->descricao . ' dentro de 1horas")</script>';
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

                $cod = str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);
                $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = $procedimento and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                $result = pg_query($sql) or die($sql);
                $row = pg_fetch_object($result);
                if ($aux2 == '') {
                    $aux2 = $row->pedido_item_id;
                } else {
                    $aux2 = $aux2 . "," . $row->pedido_item_id;
                }
                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 740, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);
                if (($auxso == 1 and $procedimento == 772)) {
                    include('conexao_laboratorio.php');
                    $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 772, 0, 0, 0, 0, '$cod', '$prioridade')";
                    $result = pg_query($sql) or die($sql);
                    $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 772 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                    $result = pg_query($sql) or die($sql);
                    $row = pg_fetch_object($result);
                    if ($aux2 == '') {
                        $aux2 = $row->pedido_item_id;
                    } else {
                        $aux2 = $aux2 . "," . $row->pedido_item_id;
                    }

                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 772, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                    $sth = pg_query($stmt) or die($stmt);
                }
                if ($auxpo = 1 and $procedimento == 763) {
                    include('conexao_laboratorio.php');
                    $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 763, 0, 0, 0, 0, '$cod', '$prioridade')";
                    $result = pg_query($sql) or die($sql);
                    $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 763 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                    $result = pg_query($sql) or die($sql);
                    $row = pg_fetch_object($result);
                    if ($aux2 == '') {
                        $aux2 = $row->pedido_item_id;
                    } else {
                        $aux2 = $aux2 . "," . $row->pedido_item_id;
                    }

                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 763, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                    $sth = pg_query($stmt) or die($stmt);
                }

                include('conexao_laboratorio.php');
                $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 272, 0, 0, 0, 0, '$cod', '$prioridade')";
                $result = pg_query($sql) or die($sql);
                $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 272 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                $result = pg_query($sql) or die($sql);
                $row = pg_fetch_object($result);
                if ($aux2 == '') {
                    $aux2 = $row->pedido_item_id;
                } else {
                    $aux2 = $aux2 . "," . $row->pedido_item_id;
                }

                include('conexao.php');
                $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 761, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

                $sth = pg_query($stmt) or die($stmt);


                /*	include('conexao_laboratorio.php');
					$sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 741, 0, 0, 0, 0, '$cod', '$prioridade')";					
					$result = pg_query($sql) or die($sql);
					$sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 741 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
					$result = pg_query($sql) or die($sql);
					$row = pg_fetch_object($result);
					if($aux2 == ''){
						$aux2 = $row->pedido_item_id;
					}else{
						$aux2 = $aux2.",".$row->pedido_item_id;
					}
					
					include ('conexao.php');
					$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 741, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

					//$sth = pg_query ( $stmt ) or die ( $stmt );
					
					
					include('conexao_laboratorio.php');
					$sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
					VALUES ($guia_id, 800, 0, 0, 0, 0, '$cod', '$prioridade')";					
					$result = pg_query($sql) or die($sql);
					$sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 800 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
					$result = pg_query($sql) or die($sql);
					$row = pg_fetch_object($result);
					if($aux2 == ''){
						$aux2 = $row->pedido_item_id;
					}else{
						$aux2 = $aux2.",".$row->pedido_item_id;
					}
					
					include ('conexao.php');
					$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, 800, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

					//$sth = pg_query ( $stmt ) or die ( $stmt );
					/*$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

					$sth = pg_query ( $stmt ) or die ( $stmt );
					$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";

					$sth = pg_query ( $stmt ) or die ( $stmt );*/
                //$aux++;
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
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT) . "1";
                        $pedido_tp1 = $pedido_id . "1";
                        $guia_tp1 = $guia_id . "1";
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_tp1, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_tp1 and exame_id = $procedimento and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                    } else if ($procedimento == 821) {
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT) . "2";
                        $pedido_tp3 = $pedido_id . "2";
                        $guia_tp3 = $guia_id . "2";
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_tp3, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_tp3 and exame_id = $procedimento and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                    } else if ($procedimento == 822) {
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT) . "3";
                        $pedido_tp6 = $pedido_id . "3";
                        $guia_tp6 = $guia_id . "3";
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_tp6, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_tp6 and exame_id = $procedimento and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                        $result = pg_query($sql) or die($sql);
                    } else if ($procedimento == 825) {
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_id, 771, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 771 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_id, 754, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 754 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_id, 756, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 756 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                        $result = pg_query($sql) or die($sql);
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_id, 18, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 18 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                        $result = pg_query($sql) or die($sql);
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
							VALUES ($guia_id, 310, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 310 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                    } else if ($procedimento == 82) {
                        if ($n == 0) {
                            $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                            $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
								VALUES ($guia_id, 82, 0, 0, 0, 0, '$cod', '$prioridade')";
                            $result = pg_query($sql) or die($sql);
                            $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 82 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                            $result = pg_query($sql) or die($sql);
                            $row = pg_fetch_object($result);
                            if ($aux2 == '') {
                                $aux2 = $row->pedido_item_id;
                            } else {
                                $aux2 = $aux2 . "," . $row->pedido_item_id;
                            }
                            $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                            $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
								VALUES ($guia_id, 81, 0, 0, 0, 0, '$cod', '$prioridade')";
                            $result = pg_query($sql) or die($sql);
                            $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 81 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                            $result = pg_query($sql) or die($sql);
                            $row = pg_fetch_object($result);
                            if ($aux2 == '') {
                                $aux2 = $row->pedido_item_id;
                            } else {
                                $aux2 = $aux2 . "," . $row->pedido_item_id;
                            }
                        } else {
                            $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                            $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
								VALUES ($guia_id, 82, 0, 0, 0, 0, '$cod', '$prioridade')";
                            $result = pg_query($sql) or die($sql);
                            $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 82 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                            $result = pg_query($sql) or die($sql);
                            $row = pg_fetch_object($result);
                            if ($aux2 == '') {
                                $aux2 = $row->pedido_item_id;
                            } else {
                                $aux2 = $aux2 . "," . $row->pedido_item_id;
                            }
                        }
                    } else if ($procedimento == 81) {
                        if ($n == 0) {
                            $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                            $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
								VALUES ($guia_id, 82, 0, 0, 0, 0, '$cod', '$prioridade')";
                            $result = pg_query($sql) or die($sql);
                            $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 82 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                            $result = pg_query($sql) or die($sql);
                            $row = pg_fetch_object($result);
                            if ($aux2 == '') {
                                $aux2 = $row->pedido_item_id;
                            } else {
                                $aux2 = $aux2 . "," . $row->pedido_item_id;
                            }
                            $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                            $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
								VALUES ($guia_id, 81, 0, 0, 0, 0, '$cod', '$prioridade')";
                            $result = pg_query($sql) or die($sql);
                            $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 81 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                            $result = pg_query($sql) or die($sql);
                            $row = pg_fetch_object($result);
                            if ($aux2 == '') {
                                $aux2 = $row->pedido_item_id;
                            } else {
                                $aux2 = $aux2 . "," . $row->pedido_item_id;
                            }
                        } else {
                            $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                            $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
								VALUES ($guia_id, 81, 0, 0, 0, 0, '$cod', '$prioridade')";
                            $result = pg_query($sql) or die($sql);
                            $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = 81 and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                            $result = pg_query($sql) or die($sql);
                            $row = pg_fetch_object($result);
                            if ($aux2 == '') {
                                $aux2 = $row->pedido_item_id;
                            } else {
                                $aux2 = $aux2 . "," . $row->pedido_item_id;
                            }
                        }
                    } else {
                        $cod = str_pad($df, 2, '0', STR_PAD_LEFT) . str_pad($row->setor, 2, '0', STR_PAD_LEFT) . ORIGEM_CONFIG . str_pad($atendimento, 7, '0', STR_PAD_LEFT);
                        $sql = "INSERT INTO pedido_item(pedido_guia_id, exame_id, pendente, nao_autorizado, coletado, recoleta, ordem_servico, prioridade)
								VALUES ($guia_id, $procedimento, 0, 0, 0, 0, '$cod', '$prioridade')";
                        $result = pg_query($sql) or die($sql);
                        $sql = "SELECT pedido_item_id FROM pedido_item where pedido_guia_id = $guia_id and exame_id = $procedimento and pendente = 0 and nao_autorizado = 0 and coletado = 0 and recoleta = 0 and ordem_servico = '$cod' and prioridade = '$prioridade'";
                        $result = pg_query($sql) or die($sql);
                        $row = pg_fetch_object($result);
                        if ($aux2 == '') {
                            $aux2 = $row->pedido_item_id;
                        } else {
                            $aux2 = $aux2 . "," . $row->pedido_item_id;
                        }
                    }

                    /*if($procedimento == 825){
							include ('conexao.php');
							$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id, data_solicitacao) values ($transacao, $prontuario, 771, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento', '".date('Y-m-d H:m:s')."');";
							$sth = pg_query ( $stmt ) or die ( $stmt );
							include ('conexao.php');
							$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id, data_solicitacao) values ($transacao, $prontuario, 754, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento', '".date('Y-m-d H:m:s')."');";
							$sth = pg_query ( $stmt ) or die ( $stmt );
							include ('conexao.php');
							$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id, data_solicitacao) values ($transacao, $prontuario, 756, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento', '".date('Y-m-d H:m:s')."');";
							$sth = pg_query ( $stmt ) or die ( $stmt );
							include ('conexao.php');
							$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id, data_solicitacao) values ($transacao, $prontuario, 18, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento', '".date('Y-m-d H:m:s')."');";
							$sth = pg_query ( $stmt ) or die ( $stmt );
							include ('conexao.php');
							$stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id, data_solicitacao) values ($transacao, $prontuario, 310, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento', '".date('Y-m-d H:m:s')."');";
							$sth = pg_query ( $stmt ) or die ( $stmt );
						}else{*/
                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id, data_solicitacao) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento', '" . date('Y-m-d H:m:s') . "')";
                    $sth = pg_query($stmt) or die($stmt);
                    //}
                } else {
                    include('conexao.php');
                    $stmt = "insert into itenspedidos (transacao, pessoa_id, exame_id, qtde, guia, valor,  situacao, autorizador, atendimento_id) values ($transacao, $prontuario, $procedimento, 1, '$ex_autorizacao', 0,  '$situacao', 'Automática', '$atendimento');";
                    $sth = pg_query($stmt) or die($stmt);
                }
            }
        }
        if ($procedimento == 772 or $procedimento == 740 or $procedimento == 800) {
            $auxso = 1;
        }
        if ($procedimento == 763 or $procedimento == 740 or $procedimento == 800) {
            $auxpo = 1;
        }
        $nao = 0;
        $laboratorio = 0;
    }
}
if ($pedido_id != 0) {

    include('conexao_laboratorio.php');
    $sqll = "select * from pessoas where pessoa_id_origem = $paciente_id and origem = " . PORIGEM_CONFIG;
    $resultl = pg_query($sqll) or die($sqll);
    $rowl = pg_fetch_object($resultl);

    $data = date('Y-m-d');
    $hora = date('H:i:s');

    $cod = "1" . date('d-m-y') . $transacao;
    $cod = str_replace("-", "", "$cod");

    if ($pedido_tp1 != '') {
        include('conexao_laboratorio.php');
        $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id, medico_solicitante) 
			VALUES ($pedido_tp1, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento, '$nome_med')";
        $resultls = pg_query($sqlls) or die($sqlls);

        include('conexao_laboratorio.php');
        $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_tp1, 1, 0, 0, 0, '" . ORIGEM_CONFIG . "', '$data', $profissional, $pedido_tp1)";
        $result = pg_query($sql) or die($sql);
    }
    if ($pedido_tp3 != '') {
        include('conexao_laboratorio.php');
        $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id, medico_solicitante) 
			VALUES ($pedido_tp3, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento, '$nome_med')";
        $resultls = pg_query($sqlls) or die($sqlls);

        include('conexao_laboratorio.php');
        $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_tp3, 1, 0, 0, 0, '" . ORIGEM_CONFIG . "', '$data', $profissional, $pedido_tp3)";
        $result = pg_query($sql) or die($sql);
    }
    if ($pedido_tp6 != '') {
        include('conexao_laboratorio.php');
        $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id, medico_solicitante) 
			VALUES ($pedido_tp6, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento, '$nome_med')";
        $resultls = pg_query($sqlls) or die($sqlls);

        include('conexao_laboratorio.php');
        $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_tp6, 1, 0, 0, 0, '" . ORIGEM_CONFIG . "', '$data', $profissional, $pedido_tp6)";
        $result = pg_query($sql) or die($sql);
    }
    include('conexao_laboratorio.php');
    $sqlls = "INSERT INTO pedidos(pedido_id, pessoa_id, data, horario, cod_pedidos, atendimento_id, medico_solicitante) 
			VALUES ($pedido_id, $rowl->pessoa_id, '$data', '$hora', $cod, $atendimento, '$nome_med')";
    $resultls = pg_query($sqlls) or die($sqlls);

    include('conexao_laboratorio.php');
    $sql = "INSERT INTO pedido_guia(pedido_guia_id, convenio_id, guia, numero_carterinha, autorizacao, origem, data_solicitacao, medico_solicitante_id, pedido_id)
			VALUES ($guia_id, 1, 0, 0, 0, '" . ORIGEM_CONFIG . "', '$data', $profissional, $pedido_id)";
    $result = pg_query($sql) or die($sql);
}
echo "<script>window.open('impexamelab.php?id=$aux2');</script>";
echo $aux;
?>
<table class="table condensed width-full">
    <thead>
        <tr>
            <th></th>
            <th width='25%'>Data / Situação</th>
            <th width='65%'>Descrição</th>
            <th width='10%'>Laudo</th>
            <!--<th width='10%'>Ação</th>-->
        </tr>
    </thead>

    <body>
        <?php
        include('conexao_laboratorio.php');
        $sql = "SELECT distinct d.exame_id, a.pedido_id, b.nome, a.data, a.horario, d.coletado, d.recoleta, d.pendente, b.celular, d.liberado, d.situacao, e.descricao, d.pedido_item_id 
                                                                FROM pedidos a
                                                                INNER JOIN pessoas b ON a.pessoa_id = b.pessoa_id
                                                                INNER JOIN pedido_guia c ON a.pedido_id = c.pedido_id
                                                                INNER JOIN pedido_item d ON d.pedido_guia_id = c.pedido_guia_id
                                                                LEFT JOIN procedimentos e ON e.procedimentos_id = d.exame_id
                                                                LEFT JOIN modalidades f ON f.modalidade_id = e.setor where c.origem = '" . ORIGEM_CONFIG . "' and b.origem = '" . PORIGEM_CONFIG . "' and pessoa_id_origem = $prontuario order by a.data desc, a.horario";
        $result = pg_query($sql) or die($sql);
        while ($rows = pg_fetch_object($result)) {
            echo "<tr>";
            if ($rows->situacao == '' or $rows->situacao == 'Coletado') {
                echo "<td><div><input type=\"checkbox\" name=\"cb_exame[]\" value=\"" . $rows->pedido_item_id . "\"><label></label></div></td>";
            } else {
                echo "<td></td>";
            }
            echo "<td>" . inverteData($rows->data) . "</td>";
            echo "<td>$rows->descricao</td>";
            if ($rows->situacao == 'Liberado') {
                echo "<td><a href='http://" . IP_CONFIG . "/desenvolvimento/laboratorio/gera_resultado.php?gera=$rows->pedido_id&exame=$rows->exame_id' target='_blank' class=\"fas fa-search\"></a></td>";
            } else if ($rows->situacao != 'Liberado') {
                echo "<td>" . $rows->situacao . "</td>";
            }
            echo "</tr>";
        }
        ?>
    </body>
</table>

</div>