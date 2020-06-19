<?php
$optradio = $_GET['radio'];
$prioridade = $_GET['prioridade'];
$idade = substr($_GET['idade'], 0, 2);
$complemento     = $_GET['complemento'];
$dosagem         = $_GET['dosagem'];
$escolha         = $_GET['escolha'];
$via             = $_GET['via'];
$aprazamento     = $_GET['aprazamento'];
?>
<?php
if ($optradio == 'medicamentos') {
?>
    <div id="solucoes">
        <div class="row">
            <div class="col-12  mt-2">
                <div class="form-group">
                    <label class="radio-inline">
                        <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                                                                                                                            echo "checked";
                                                                                                                        } ?>>Dieta
                    </label>
                    <label class="radio-inline">
                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                                                                                                                        echo "checked";
                                                                                                                                    } ?>>Medicamento
                    </label>
                    <label class="radio-inline">
                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == 'solucoes') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Soluções
                    </label>
                    <label class="radio-inline">
                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                                                                                                                                                echo "checked";
                                                                                                                                            } ?>>Cuidado
                    </label>
                    <label class="radio-inline">
                        <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == 'paciente') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>medicamento particular do paciente
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Escolha</label>
                    <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha" id="escolha" onchange="obs_medi(this)">
                        <option value=""></option>
                        <?php
                        if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL' or $prioridade == '')) {
                            include('conexao.php');
                            $stmt = "Select descricao,id,perigo from $optradio where id not in(105352,105213,106625,105088,105383,105364,107221,105377,105457,105034,104928,105285,105371,105072,105013,104900,104898,105037)";
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
                        } else {
                            include('conexao.php');
                            $stmt = "Select descricao,id, perigo from $optradio";
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
                    <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true" id="dosagem" name="dosagem" onkeyup="somenteNumeros(this);" value="<?php echo $dosagem; ?>">
                </div>
            </div>
            <div class="col-8">
                <div class="form-group">
                    <label>Informações Adicionais</label>
                    <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true" id="complemento" name="complemento" value="<?php echo $complemento; ?>">
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
        <div class="row">
            <div class="col-12" align="center">
                <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary" value="ADICIONAR">
                <br>
            </div>
        </div>
    </div>
<?php } else if ($optradio == 'prescricao_cuidados') { ?>
    <div id="solucoes">
        <div class="col-md-12 mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == 'solucoes') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == 'paciente') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="form-group">
                    <label>Escolha</label>
                    <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha" id="escolha" onchange="obs_medi(this)">
                        <option value=""></option>
                        <?php
                        include('conexao.php');
                        $stmt = "Select descricao from $optradio";
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
            <div class="col-4">
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
            <div class="col-12">
                <div class="form-group">
                    <label>Informações Adicionais</label>
                    <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true" id="complemento" name="complemento" value="<?php echo $complemento; ?>">
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
        <div class="row">
            <div class="col-12" align="center">
                <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary" value="ADICIONAR">
                <br>
            </div>
        </div>
    </div>
<?php } else if ($optradio == 'dietas') { ?>
    <div id="solucoes">
        <div class="col-12 mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == 'solucoes') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == 'paciente') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="form-group">
                    <label>Escolha</label>
                    <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha" id="escolha" onchange="obs_medi(this)">
                        <option value=""></option>
                        <?php
                        include('conexao.php');
                        $stmt = "Select descricao from $optradio";
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
            <div class="col-1"></div>
            <div class="col-3">
                <div class="form-group">
                    <input class="form-check-input" type="checkbox" value="" id="bomba">
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
            <div class="col-2">
                <div class="form-group">
                    <label>Quantidade</label>
                    <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true" id="dosagem" name="dosagem" value="<?php echo $dosagem; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
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
        <div class="row">
            <div class="col-md-12" align="center">
                <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary" value="ADICIONAR">
                <br>
            </div>
        </div>
    </div>
<?php } else if ($optradio == 'solucoes') {
    $dil = "105472,105471,105340,105203,105204,105941,105942,105334,105335,105336,105337,104942,104941,107578";
?>
    <div id="solucoes">
        <div class="col-12 mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == 'solucoes') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == 'paciente') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-12">

                <table class="table" id="adc_esc">
                    <thead>
                        <tr>
                            <th>Escolha</th>
                            <th>Quantidade</th>
                            <th></th>
                        </tr>
                        <tr>
                    </thead>
                    <td>
                        <select class="form-control square teste selectnew" data-size="4" data-live-search="true" name="escolha" id="escolha[]" onchange="obs_medi(this)">
                            <option value=""></option>
                            <?php
                            if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL' or $prioridade == '')) {
                                include('conexao.php');
                                $stmt = "Select descricao, id, perigo from medicamentos where id not in(105352,105213,106625,105088,105383,105364,107221,105377,105457,105034,104928,105285,105371,105072,105013,104900,104898,105037)";
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
                            } else {
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
                        <input type="text" class="form-control square dosagem" id="dosagem" name="dosagem" onkeyup="numero(this);" value="<?php echo $dosagem; ?>">
                    </td>
                    <td>
                        <input type="button" name='add' id='add' class="btn btn-primary" value="+">
                    </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-2 ml-4">
                <div class="form-group">
                    <input class="form-check-input" type="checkbox" value="" id="bomba">
                    <label class="control-label">Bomba de Infusao</label>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label>Diluente</label>
                    <select class="form-control square selectnew" data-size="4" data-live-search="true" name="diluente" id="diluente">
                        <option value=""></option>
                        <option value="9999">EM BOLUS</option>
                        <option value="8888">PURO</option>
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
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Informações Adicionais</label>
                    <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true" id="complemento" name="complemento" value="<?php echo $complemento; ?>">
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
        <div class="row">
            <div class="col-12" align="center">
                <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary" value="ADICIONAR">
                <br>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div id="solucoes">
        <div class="col-md-12 mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                                                                                                                        echo "checked";
                                                                                                                    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos" onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()" <?php if ($optradio == 'solucoes') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>Soluções
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados" onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                                                                                                                                            echo "checked";
                                                                                                                                        } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()" <?php if ($optradio == 'paciente') {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>medicamento particular do paciente
                </label>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="control-label">Medicamento paciente</label>
                <input type="text" class="form-control square" id="paciente" name="paciente">
            </div>
        </div>
        <div class="col-md-12" align="center" id="teste" name="teste" style="display:none;">
            <label>Hipótese Diagnostica</label>
            <textarea class="form-control square" id="obs_text" name="obs_text"></textarea>
            <br>
        </div>

        <div class="col-md-12" align="center">
            <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary" value="ADICIONAR">
            <br>
        </div>
    </div>
<?php } ?>
<script>
    //$("#escolha").chosen({width: "100%"})
    //$("#via").chosen({width: "100%"})
    //$("#aprazamento").chosen({width: "100%"})
    // $("select").chosen({
    //     width: "100%"
    // })

    $('.selectnew').selectpicker();

    $("#add").click(function() {
        var campos_novos = "<tr id='remove'><td><select class='form-control square teste selectnew' data-size='4' data-live-search='true' name='escolha' id='escolha[]' ><option value=''></option><?php if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL' or $prioridade == '')) {
                                                                                                                                                                                                        include('conexao.php');
                                                                                                                                                                                                        $stmt = 'Select descricao, id,perigo from medicamentos where id not in(105352,105213,106625,105088,105383,105364,107221,105377,105457,105034,104928,105285,105371,105072,105013,104900,104898,105037)';
                                                                                                                                                                                                        $sth = pg_query($stmt) or die($stmt);
                                                                                                                                                                                                        while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                                                            if ($row->perigo == 1) {
                                                                                                                                                                                                                echo '<option style=\'color : #FFFFFF; background-color: red\' value=\'' . $row->id . '\'';
                                                                                                                                                                                                                if ($row->descricao == $escolha) {
                                                                                                                                                                                                                    echo 'selected';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                            } else if ($row->perigo == 2) {
                                                                                                                                                                                                                echo '<option style=\'background-color: yellow\' value=\'' . $row->id . '\'';
                                                                                                                                                                                                                if ($row->descricao == $escolha) {
                                                                                                                                                                                                                    echo 'selected';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo '<option value=\'' . $row->id . '\'';
                                                                                                                                                                                                                if ($row->descricao == $escolha) {
                                                                                                                                                                                                                    echo 'selected';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        include('conexao.php');
                                                                                                                                                                                                        $stmt = 'Select descricao, id,perigo from medicamentos';
                                                                                                                                                                                                        $sth = pg_query($stmt) or die($stmt);
                                                                                                                                                                                                        while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                                                            if ($row->perigo == 1) {
                                                                                                                                                                                                                echo '<option style=\'color : #FFFFFF; background-color: red\' value=\'' . $row->id . '\'';
                                                                                                                                                                                                                if ($row->descricao == $escolha) {
                                                                                                                                                                                                                    echo 'selected';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                            } else if ($row->perigo == 2) {
                                                                                                                                                                                                                echo '<option style=\'background-color: yellow\' value=\'' . $row->id . '\'';
                                                                                                                                                                                                                if ($row->descricao == $escolha) {
                                                                                                                                                                                                                    echo 'selected';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo '<option value=\'' . $row->id . '\'';
                                                                                                                                                                                                                if ($row->descricao == $escolha) {
                                                                                                                                                                                                                    echo 'selected';
                                                                                                                                                                                                                }
                                                                                                                                                                                                                echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } ?></select></td><td><input type='text' class='form-control square dosagem' id='dosagem' name='dosagem' onkeyup='numero(this);'></input></td><td><button class='btn btn-primary remove' type='button' >-</button></td></tr>";
        $("#adc_esc").append(campos_novos);
        $('.selectnew').selectpicker();
    });

    $(document).on('click', 'button.remove', function() {
        $(this).closest('#remove').remove();
    });
</script>