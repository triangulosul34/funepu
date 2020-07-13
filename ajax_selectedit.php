<?php
$optradio = $_GET['tipo'];
$id       = $_GET['id'];
$item_id  = $_GET['id_item'];

if ($item_id == '') {
    $item_id = $id;
}

include('conexao.php');
$sqla = "select * from prescricao_item where prescricao_item_id = $item_id";
$stha = pg_query($sqla) or die($sqla);
$rowa = pg_fetch_object($stha);

$complemento     = $rowa->complemento;
$dosagem         = $rowa->dosagem;
$escolha         = $rowa->descricao;
$via             = $rowa->via;
$aprazamento     = $rowa->aprazamento;
$diluente       = $rowa->diluente;
$bomba          = $rowa->bomba;
$obs            = $rowa->obs_med;

if ($optradio == '5') {
?>
    <link rel="stylesheet" href="assets/vendor/chosen/chosen.css">
    <div id="solucoes">
        <div class="col-12  mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == '1') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == '5') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == '6') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == '10') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == '11') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Escolha</label>
                    <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha" id="escolha" onchange="obs_medi(this)">
                        <option value=""></option>
                        <?php
                        include('conexao.php');
                        $stmt = "Select descricao, id, perigo from medicamentos";
                        $sth = pg_query($stmt) or die($stmt);
                        while ($row = pg_fetch_object($sth)) {
                            if ($row->perigo == 1) {
                                echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                if ($row->descricao == $escolha) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            } else if ($row->perigo == 2) {
                                echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                                if ($row->descricao == $escolha) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            } else {
                                echo "<option value=\"" . $row->id . "\"";
                                if ($row->descricao == $escolha) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Via</label>
                    <select class="form-control square selectnew" data-size="4" data-live-search="true" name="via" id="via" <?php if ($optradio == 'medicamentos') { ?>onchange="en()" <?php } ?>>
                        <option value=""></option>
                        <?php include('conexao.php');
                        $stmt = "Select descricao from vias_administracao";
                        $sth = pg_query($stmt) or die($stmt);
                        while ($row = pg_fetch_object($sth)) {
                            echo "<option value=\"" . $row->descricao . "\"";
                            if ($row->descricao == $via) {
                                echo "selected";
                            }
                            echo ">" . $row->descricao . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Aprazamento</label>
                    <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento" id="aprazamento">
                        <option value=""></option>
                        <?php include('conexao.php');
                        $stmt = "Select * from aprazamento";
                        $sth = pg_query($stmt) or die($stmt);
                        while ($row = pg_fetch_object($sth)) {
                            echo "<option value=\"" . $row->quantidade . "\"";
                            if ($row->descricao == $aprazamento) {
                                echo "selected";
                            }
                            echo ">" . $row->descricao . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="text" class="form-control square" id="dosagem" name="dosagem" value="<?php echo $dosagem; ?>">
                </div>
            </div>
            <div class="col-8">
                <div class="form-group">
                    <label>Informações Adicionais</label>
                    <input type="text" class="form-control square" id="complemento" name="complemento" value="<?php echo $complemento; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" align="center" id="teste" name="teste" style="display:none;">
                <label>Hipótese Diagnostica</label>
                <textarea class="form-control square" id="obs_text" name="obs_text"></textarea>
                <br>
            </div>
        </div>
        <?php
        if ($obs != "") {
            $esc =  $rowa->codigo_medicamento;
            $arrayId = ['105139', '105452', '106577', '105377', '105384', '105352', '105034', '105361', '105354', '105091', '105088', '105457', '106576', '105427', '105426'];
            if (in_array($esc, $arrayId)) {
        ?>
                <script>
                    document.getElementById("teste").style.display = "block"
                    document.getElementById('obs_text').value = '<?php echo $obs; ?>'
                </script>
        <?php
            }
        }
        ?>


        <div class="col-md-2"><br>
            <input type="button" name='atualizar' id='atualizar' onclick='editar("<?php echo $item_id; ?>")' class="btn btn-primary" value="Atualizar">
        </div>
    </div>
    <!-- CUIDADOS -->
<?php } else if ($optradio == '10') { ?>
    <link rel="stylesheet" href="assets/vendor/chosen/chosen.css">
    <div id="solucoes">
        <div class="col-12  mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == '1') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == '5') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == '6') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == '10') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == '11') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <form id="apagar">
            <div class="row">
                <div class="col-9">
                    <div class="form-group">
                        <label>Escolha</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha" id="escolha">
                            <option value=""></option>
                            <?php
                            include('conexao.php');
                            $stmt = "Select descricao from prescricao_cuidados";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $tipo . "\"";
                                if ($row->descricao == $escolha) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Aprazamento</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento" id="aprazamento">
                            <option value=""></option>
                            <?php include('conexao.php');
                            $stmt = "Select * from aprazamento";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $row->quantidade . "\"";
                                if ($row->descricao == $aprazamento) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="form-group">
                        <label>Informações Adicionais</label>
                        <input type="text" class="form-control" id="complemento" name="complemento" value="<?php echo $complemento; ?>">
                    </div>
                </div>
            </div>
        </form>
        <div class="col-md-2"><br>
            <input type="button" name='atualizar' id='atualizar' onclick='editar(<?php echo $item_id; ?>)' class="btn btn-primary" value="Atualizar">
        </div>
    </div>

    <!-- DIETA -->
<?php } else if ($optradio == '1') { ?>
    <link rel="stylesheet" href="assets/vendor/chosen/chosen.css">
    <div id="solucoes">
        <div class="col-12  mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == '1') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == '5') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == '6') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == '10') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == '11') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <form id="apagar">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Escolha</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha" id="escolha">
                            <option value=""></option>
                            <?php
                            include('conexao.php');
                            $stmt = "Select descricao from dietas";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $tipo . "\"";
                                if ($row->descricao == $escolha) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input class="form-check-input" type="checkbox" value="" id="bomba" <?php if ($bomba == '1') {
                                                                                                echo "checked";
                                                                                            } ?>>
                        <label class="control-label">Bomba de Infusao</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label>Via</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="via" id="via" <?php if ($optradio == 'medicamentos') { ?>onchange="en()" <?php } ?>>
                            <option value=""></option>
                            <?php include('conexao.php');
                            $stmt = "Select descricao from vias_administracao";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $row->descricao . "\"";
                                if ($row->descricao == $via) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Aprazamento</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento" id="aprazamento">
                            <option value=""></option>
                            <?php include('conexao.php');
                            $stmt = "Select * from aprazamento";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $row->quantidade . "\"";
                                if ($row->descricao == $aprazamento) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="form-group">
                        <label>Quantidade</label>
                        <input type="text" class="form-control square" id="dosagem" name="dosagem" value="<?php echo $dosagem; ?>">
                    </div>
                </div>
                <div class="col-8">
                    <div class="form-group">
                        <label>Informações Adicionais</label>
                        <input type="text" class="form-control square" id="complemento" name="complemento" value="<?php echo $complemento; ?>">
                    </div>
                </div>
            </div>
        </form>
        <div class="col-md-2"><br>
            <input type="button" name='atualizar' id='atualizar' onclick='editar(<?php echo $item_id; ?>)' class="btn btn-primary" value="Atualizar">
        </div>
    </div>
    <!-- SOLUÇÕES -->
<?php } else if ($optradio == '6') {
    $dil = "105472,105471,105340,105203,105204,105941,105942,105334,105335,105336,105337,104942,104941,107578";
?>
    <div id="solucoes">
        <div class="col-12  mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == '1') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == '5') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == '6') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == '10') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == '11') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <form id="apagar">
            <table class="table" id="editar_esc">
                <thead>
                    <tr>
                        <th>Escolha</th>
                        <th>Quantidade</th>
                        <th></th>
                    </tr>
                    <tr>
                </thead>
                <td>
                    <select class="form-control square selectnew teste" data-size="4" data-live-search="true" name="escolha" id="escolha[]" onchange="obs_medi(this)">
                        <option value=""></option>

                        <?php
                        include('conexao.php');
                        $stmt = "Select descricao, id from medicamentos";
                        $sth = pg_query($stmt) or die($stmt);
                        while ($row = pg_fetch_object($sth)) {
                            include('conexao.php');
                            $stmt = "Select descricao, id, perigo from medicamentos";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                if ($row->perigo == 1) {
                                    echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                    if ($row->descricao == $escolha) {
                                        echo "selected";
                                    }
                                    echo ">" . $row->descricao . "</option>";
                                } else if ($row->perigo == 2) {
                                    echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                                    if ($row->descricao == $escolha) {
                                        echo "selected";
                                    }
                                    echo ">" . $row->descricao . "</option>";
                                } else {
                                    echo "<option value=\"" . $row->id . "\"";
                                    if ($row->descricao == $escolha) {
                                        echo "selected";
                                    }
                                    echo ">" . $row->descricao . "</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control dosagem" id="dosagem" name="dosagem" onkeyup="numero(this);" value="">
                </td>
                <td>
                    <input type="button" name='add' id='add' class="btn btn-primary" value="+">
                </td>
                </tr>
            </table>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <input class="form-check-input" type="checkbox" value="" id="bomba" <?php if ($bomba == '1') {
                                                                                                echo "checked";
                                                                                            } ?>>
                        <label class="control-label">Bomba de Infusao</label>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Diluente</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="diluente" id="diluente">
                            <option value=""></option>
                            <option value="9999">EM BOLUS</option>
                            <?php
                            include('conexao.php');
                            $stmt = "Select descricao, id from medicamentos where id in ($dil)";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $row->id . "\"";
                                if ($row->descricao == $escolha) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Via</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="via" id="via">
                            <option value=""></option>
                            <?php include('conexao.php');
                            $stmt = "Select descricao from vias_administracao";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $row->descricao . "\"";
                                if ($row->descricao == $via) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>Aprazamento</label>
                        <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento" id="aprazamento">
                            <option value=""></option>
                            <?php include('conexao.php');
                            $stmt = "Select * from aprazamento";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                echo "<option value=\"" . $row->quantidade . "\"";
                                if ($row->descricao == $aprazamento) {
                                    echo "selected";
                                }
                                echo ">" . $row->descricao . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Informações Adicionais</label>
                    <input type="text" class="form-control" id="complemento" name="complemento" value="<?php echo $complemento; ?>">
                </div>
            </div>
            <div class="col-md-12" align="center" id="teste" name="teste" style="display:none;">
                <label>Hipótese Diagnostica</label>
                <textarea class="form-control" id="obs_text" name="obs_text"></textarea>
                <br>
            </div>
            <?php
            if ($obs != "") {
                $esc =  $rowa->codigo_medicamento;
                $arrayId = ['105139', '105452', '106577', '105377', '105384', '105352', '105034', '105361', '105354', '105091', '105088', '105457', '106576', '105427', '105426'];
                if (in_array($esc, $arrayId)) {
            ?>
                    <script>
                        document.getElementById("teste").style.display = "block"
                        document.getElementById('obs_text').value = '<?php echo $obs; ?>'
                    </script>
            <?php
                }
            }
            ?>

        </form>
        <div class="col-md-2"><br>
            <input type="button" name='atualizar' id='atualizar' onclick='editar(<?php echo $item_id; ?>)' class="btn btn-primary" value="Atualizar">
        </div>
    </div>
    <!-- MEDICAMENTO PARTICULAR -->
<?php } else { ?>
    <div id="solucoes">
        <div class="col-12  mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == '1') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == '5') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == '6') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == '10') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == '11') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <form id="apagar">
            <div class="col-8">
                <div class="form-group">
                    <label>Medicamento paciente</label>
                    <input type="text" class="form-control square" id="paciente" name="paciente" value="<?php echo $escolha; ?>">
                </div>
            </div>
        </form>
        <div class="col-2"><br>
            <input type="button" name='atualizar' id='atualizar' onclick='editar(<?php echo $item_id; ?>)' class="btn btn-primary" value="Atualizar">
        </div>
    </div>
<?php } ?>
<script src="assets/vendor/chosen/chosen.jquery.js"></script>
<script>
    //$("#escolha").chosen({width: "100%"})
    //$("#via").chosen({width: "100%"})
    //$("#aprazamento").chosen({width: "100%"})
    // $("select").chosen({
    //     width: "100%"
    // })

    $('.selectnew').selectpicker();

    $("#add").click(function() {
        var campos_novos = "<tr id='remove'><td><select class='form-control square teste selectnew' data-size='4' data-live-search='true' name='escolha' id='escolha[]' ><option value=''></option><?php include('conexaovalim.php');
                                                                                                                                                                                                    $sql = 'select * from QtdeProdutoUPA WHERE produtoquantidade > 0 and subgrupoprodutodescricao=\'MEDICAMENTOS\' order by produtodescricao';
                                                                                                                                                                                                    $sth = pg_query($sql) or die($sql);
                                                                                                                                                                                                    while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                                                        echo '<option value=\"' . $row->produtoid . '\">' . $row->produtodescricao . '</option>';
                                                                                                                                                                                                    } ?></select></td><td><input type='text' class='form-control dosagem' onkeyup='numero(this);' id='dosagem' name='dosagem'></input></td><td><button class='btn btn-primary remove' type='button' >-</button></td></tr>";
        $("#editar_esc").append(campos_novos);
        $('.selectnew').selectpicker();
    });

    $(document).on('click', 'button.remove', function() {
        $(this).closest('#remove').remove();
    });
</script>