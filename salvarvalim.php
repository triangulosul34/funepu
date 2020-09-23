<?php
include("Config.php");
function soNumero($str)
{
    return preg_replace("/[^0-9,.]/", "", $str);
}

$max = 35;
$prescricao  = $_GET['prescricao'];
$prioridade  = $_GET['prioridade'];
$idade  = substr($_GET['idade'], 0, 2);
$medico       = $_GET['medico'];
$data            = date('Y-m-d');
$hora            = date('H:i');
$atendimento     = $_GET['atendimento'];
$nome               = $_GET['nome'];
$nome             = substr_replace($nome, (strlen($nome) > $max ? '...' : ''), $max);

$solucoes = array("105194", "105019", "105283", "105159", "105041", "105242", "105129", "105338", "105164", "105165", "105000", "105131", "105130");
$antibioticos = array("105352", "105213", "106625", "105088", "105383", "105364", "107221", "105377", "105457", "105034", "104928", "105285", "105371", "105072", "105013", "104900", "104898", "105037");
$r = 1;
include('conexao.php');
$stmt = "select *
				from prescricao_item
				where prescricao_id = $prescricao";
$sth = pg_query($stmt) or die($stmt);
while ($row = pg_fetch_object($sth)) {
    if ($row->tipo == '6') {
        $escolhav = str_replace(',', '++', $row->codigo_medicamento);
        $escolhav = explode('++', $escolhav);
        $dosagem = explode(',', $row->dosagem);


        $f = 0;
        $v = 0;

        foreach ($escolhav as $vesc) {
            $medicamento = $vesc;
            if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL')) {
                if (in_array($medicamento, $antibioticos)) {
                    $nao = 1;
                }
            }
            if (in_array($medicamento, $solucoes)) {
            } else {
                if ($valim == '') {
                    $valim = "(" . $r . "," . $medicamento . ",";
                } else {
                    $valim .= ",(" . $r . "," . $medicamento . ",";
                }
                $r++;

                $filtro = soNumero($dosagem[$v]);
                $filtro = intval($filtro);
                if ($filtro == 0) {
                    $filtro = 1;
                }
                if ($filtro != '') {
                    $valim .= $filtro . ",'$row->complemento')";
                } else {
                    $valim .= "1,'$row->complemento')";
                }
                $v++;
            }
        }
        if ($row->diluente != 'Em BOLUS' and $row->diluente != 'PURO') {
            include('conexaovalimh.php');
            $sqla = "select * from qtdeprodutoupa WHERE produtodescricao = '$row->diluente'";
            $stha = pg_query($sqla) or die($sqla);
            $rowa = pg_fetch_object($stha);
            $diluente = $rowa->produtoid;
            if ($valim == '') {
                $valim .= "(1," . $diluente . ",1,'')";
            } else {
                $valim .= ",(" . $r . "," . $diluente . ",1,'')";
            }
        }
    }
    if ($row->tipo == '5') {
        $d = soNumero($row->dosagem);
        $d = intval($d);
        if ($d == 0) {
            $d = 1;
        }
        $value = $row->codigo_medicamento;
        if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL')) {
            if (in_array($value, $antibioticos)) {
                $nao = 1;
            }
        }
        if (in_array($value, $solucoes)) {
        } else {
            if ($valim == '') {
                $valim = "(" . $r . "," . $row->codigo_medicamento . ",$d,'$row->complemento')";
            } else {
                $valim .= ",(" . $r . "," . $row->codigo_medicamento . ",$d,'$row->complemento')";
            }
        }
        $r++;
    }
}
if ($nao == 1) {
    echo '<script>alert("Alguns medicamento nao podem ser prescritos");</script>';
} else {
    include("conexaovalimh.php");
    $stmtvalim = "select * from insereprescricaoupa" . FUNCTIONBD_FARMACIA . "('FUNEPU','$medico','$nome',
					'000.000.000.00',$prescricao,'$data','$hora',array[$valim]::prodprescricao[])";
    $sthvalim = pg_query($stmtvalim) or die($stmtvalim);
    echo '<script>document.getElementById("formp").submit();</script>';
}

echo $stmtvalim;
