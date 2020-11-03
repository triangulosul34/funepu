<?php
$optradio = $_GET['radio'];
$prioridade = $_GET['prioridade'];
$idade = substr($_GET['idade'], 0, 2);
$complemento     = $_GET['complemento'];
$dosagem         = $_GET['dosagem'];
$escolha         = $_GET['escolha'];
$escolha_id         = ($_GET['escolha_id'] == '' ? 0 : $_GET['escolha_id']);
$via             = $_GET['via'];
$aprazamento     = $_GET['aprazamento'];
$antibiotico = 0;

if ($_GET['outro']==1) {
    $antibiotico = 1;
    $med = $_GET['outro_antibiotico'];
    $arr_med = explode(",", $_GET['outro_antibiotico']);
    if (count($arr_med) > 1) {
        $arr = 1;
        $optradio= 'solucoes';
    } elseif ($row->via == 'Via Endo-venoso - EV') {
        $optradio= 'solucoes';
    } else {
        $optradio= 'medicamentos';
    }
}

if ($_GET['antibiotico'] == 1) {
    $antibiotico = 1;
    $controle_id = $_GET['controle_id'];

    include('conexao.php');
    $sql = "select * from controle_antimicrobiano where controle_id = $controle_id";
    $result = pg_query($sql) or die($sql);
    $row=pg_fetch_object($result);

    $med = $row->medicamento;
    $via             = $row->via;
    $dosagem = $row->quantidade;
    $aprazamento = $row->aprazamento;
    $arr_med = explode(",", $row->medicamento);
    if (count($arr_med) > 1) {
        $arr = 1;
        $optradio= 'solucoes';
    } elseif ($row->via == 'Via Endo-venoso - EV') {
        $optradio= 'solucoes';
    } else {
        $optradio= 'medicamentos';
    }
}
?>
<?php
if ($optradio == 'medicamentos') {
    ?>
<div id="solucoes">
    <div class="row">
        <div class="col-12  mt-2">
            <div class="form-group">
                <label class="radio-inline">
                    <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()"
                        <?php if ($optradio == 'dietas') {
        echo "checked";
    } ?>>Dieta
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos"
                        onclick="radio()" <?php if ($optradio == 'medicamentos') {
        echo "checked";
    } ?>>Medicamento
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes"
                        onclick="radio()" <?php if ($optradio == 'solucoes') {
        echo "checked";
    } ?>>Soluções
                </label>
                <label>
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="antibioticos"
                        onclick="radio()" <?php if ($optradio == 'antibioticos') {
        echo "checked";
    } ?>>Antibioticos
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados"
                        onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
        echo "checked";
    } ?>>Cuidado
                </label>
                <label class="radio-inline">
                    <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente"
                        onclick="radio()" <?php if ($optradio == 'paciente') {
        echo "checked";
    } ?>>medicamento
                    particular do paciente
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label>Escolha</label>
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha"
                    id="escolha" onchange="obs_medi(this)">
                    <option value=""></option>
                    <?php
                        if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL' or $prioridade == '')) {
                            include('conexao.php');
                            $stmt = "Select a.descricao,a.id,a.perigo from $optradio a inner join medicamentos3 b on a.id = b.id where a.id not in(105352,105213,106625,105088,105383,105364,107221,105377,105457,105034,104928,105285,105371,105072,105013,104900,104898,105037) and b.antibiotico = 0";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                if ($row->perigo == 1) {
                                    echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                    if ($row->descricao == $escolha) {
                                        echo "selected";
                                    }
                                    echo ">" . $row->descricao . "</option>";
                                } elseif ($row->perigo == 2) {
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
                            $stmt = "Select a.descricao,a.id,a.perigo from $optradio a inner join medicamentos3 b on a.id = b.id where b.antibiotico = 0";
                            $sth = pg_query($stmt) or die($stmt);
                            while ($row = pg_fetch_object($sth)) {
                                if ($row->perigo == 1) {
                                    echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                    if ($row->descricao == $escolha) {
                                        echo "selected";
                                    }
                                    echo ">" . $row->descricao . "</option>";
                                } elseif ($row->perigo == 2) {
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
    if ($antibiotico == 1) {
        include('conexao.php');
        $stmt = "Select a.descricao,a.id,a.perigo from $optradio a inner join medicamentos3 b on a.id = b.id where a.id = $med";
        $sth = pg_query($stmt) or die($stmt);
        while ($row = pg_fetch_object($sth)) {
            if ($row->perigo == 1) {
                echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                echo "selected";
                echo ">" . $row->descricao . "</option>";
            } elseif ($row->perigo == 2) {
                echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                echo "selected";
                echo ">" . $row->descricao . "</option>";
            } else {
                echo "<option value=\"" . $row->id . "\"";
                echo "selected";
                echo ">" . $row->descricao . "</option>";
            }
        }
    } ?>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Via</label>
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="via" id="via"
                    <?php if ($optradio == 'medicamentos') { ?>onchange="en()"
                    <?php } ?>>
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
    } ?>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Aprazamento</label>
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento"
                    id="aprazamento">
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
    } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <div class="form-group">
                <label>Quantidade</label>
                <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true"
                    id="dosagem" name="dosagem" onkeyup="somenteNumeros(this);"
                    value="<?php echo $dosagem; ?>">
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                <label>Informações Adicionais</label>
                <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true"
                    id="complemento" name="complemento"
                    value="<?php echo $complemento; ?>">
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
            <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary"
                value="ADICIONAR">
            <br>
        </div>
    </div>
</div>
<?php
} elseif ($optradio == 'prescricao_cuidados') { ?>
<div id="solucoes">
    <div class="col-md-12 mt-2">
        <div class="form-group">
            <label class="radio-inline">
                <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
        echo "checked";
    } ?>>Dieta
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos"
                    onclick="radio()" <?php if ($optradio == 'medicamentos') {
        echo "checked";
    } ?>>Medicamento
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()"
                    <?php if ($optradio == 'solucoes') {
        echo "checked";
    } ?>>Soluções
            </label>
            <label>
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="antibioticos"
                    onclick="radio()" <?php if ($optradio == 'antibioticos') {
        echo "checked";
    } ?>>Antibioticos
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados"
                    onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
        echo "checked";
    } ?>>Cuidado
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()"
                    <?php if ($optradio == 'paciente') {
        echo "checked";
    } ?>>medicamento
                particular do paciente
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <label>Escolha</label>
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha"
                    id="escolha" onchange="obs_medi(this)">
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
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento"
                    id="aprazamento">
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
                <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true"
                    id="complemento" name="complemento"
                    value="<?php echo $complemento; ?>">
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
            <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary"
                value="ADICIONAR">
            <br>
        </div>
    </div>
</div>
<?php } elseif ($optradio == 'dietas') { ?>
<div id="solucoes">
    <div class="col-12 mt-2">
        <div class="form-group">
            <label class="radio-inline">
                <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                            echo "checked";
                        } ?>>Dieta
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos"
                    onclick="radio()" <?php if ($optradio == 'medicamentos') {
                            echo "checked";
                        } ?>>Medicamento
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()"
                    <?php if ($optradio == 'solucoes') {
                            echo "checked";
                        } ?>>Soluções
            </label>
            <label>
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="antibioticos"
                    onclick="radio()" <?php if ($optradio == 'antibioticos') {
                            echo "checked";
                        } ?>>Antibioticos
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados"
                    onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                            echo "checked";
                        } ?>>Cuidado
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()"
                    <?php if ($optradio == 'paciente') {
                            echo "checked";
                        } ?>>medicamento
                particular do paciente
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <label>Escolha</label>
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="escolha"
                    id="escolha" onchange="obs_medi(this)">
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
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="via" id="via"
                    <?php if ($optradio == 'medicamentos') { ?>onchange="en()"
                    <?php } ?>>
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
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento"
                    id="aprazamento">
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
                <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true"
                    id="dosagem" name="dosagem"
                    value="<?php echo $dosagem; ?>">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Informações Adicionais</label>
                <input type="text" class="form-control square" id="complemento" name="complemento"
                    value="<?php echo $complemento; ?>">
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
            <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary"
                value="ADICIONAR">
            <br>
        </div>
    </div>
</div>
<?php } elseif ($optradio == 'solucoes') {
                            $dil = "105472,105471,105340,105203,105204,105941,105942,105334,105335,105336,105337,104942,104941,107578"; ?>
<div id="solucoes">
    <div class="col-12 mt-2">
        <div class="form-group">
            <label class="radio-inline">
                <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                                echo "checked";
                            } ?>>Dieta
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos"
                    onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                echo "checked";
                            } ?>>Medicamento
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()"
                    <?php if ($optradio == 'solucoes') {
                                echo "checked";
                            } ?>>Soluções
            </label>
            <label>
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="antibioticos"
                    onclick="radio()" <?php if ($optradio == 'antibioticos') {
                                echo "checked";
                            } ?>>Antibioticos
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados"
                    onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                                echo "checked";
                            } ?>>Cuidado
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()"
                    <?php if ($optradio == 'paciente') {
                                echo "checked";
                            } ?>>medicamento
                particular do paciente
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
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select class="form-control square teste selectnew" data-size="4" data-live-search="true"
                                name="escolha" id="escolha[]" onchange="obs_medi(this)">
                                <option value=""></option>
                                <?php
                                if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL' or $prioridade == '')) {
                                    include('conexao.php');
                                    $stmt = "Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where a.id not in(105352,105213,106625,105088,105383,105364,107221,105377,105457,105034,104928,105285,105371,105072,105013,104900,104898,105037) and b.antibiotico = 0 or (a.id = '$escolha_id')";
                                    $sth = pg_query($stmt) or die($stmt);
                                    while ($row = pg_fetch_object($sth)) {
                                        if ($row->perigo == 1) {
                                            echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                            if ($row->descricao == $escolha or $row->id == $escolha_id) {
                                                echo "selected";
                                            }
                                            echo ">" . $row->descricao . "</option>";
                                        } elseif ($row->perigo == 2) {
                                            echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                                            if ($row->descricao == $escolha or $row->id == $escolha_id) {
                                                echo "selected";
                                            }
                                            echo ">" . $row->descricao . "</option>";
                                        } else {
                                            echo "<option value=\"" . $row->id . "\"";
                                            if ($row->descricao == $escolha or $row->id == $escolha_id) {
                                                echo "selected";
                                            }
                                            echo ">" . $row->descricao . "</option>";
                                        }
                                    }
                                } else {
                                    include('conexao.php');
                                    $stmt = "Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where b.antibiotico = 0 or a.id = '$escolha_id'";
                                    $sth = pg_query($stmt) or die($stmt);
                                    while ($row = pg_fetch_object($sth)) {
                                        if ($row->perigo == 1) {
                                            echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                            if ($row->descricao == $escolha or $row->id == $escolha_id) {
                                                echo "selected";
                                            }
                                            echo ">" . $row->descricao . "</option>";
                                        } elseif ($row->perigo == 2) {
                                            echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                                            if ($row->descricao == $escolha or $row->id == $escolha_id) {
                                                echo "selected";
                                            }
                                            echo ">" . $row->descricao . "</option>";
                                        } else {
                                            echo "<option value=\"" . $row->id . "\"";
                                            if ($row->descricao == $escolha or $row->id == $escolha_id) {
                                                echo "selected";
                                            }
                                            echo ">" . $row->descricao . "</option>";
                                        }
                                    }
                                }
                            if ($antibiotico == 1 and $arr <> 1) {
                                include('conexao.php');
                                $stmt = "Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where a.id = $med";
                                $sth = pg_query($stmt) or die($stmt);
                                while ($row = pg_fetch_object($sth)) {
                                    if ($row->perigo == 1) {
                                        echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    } elseif ($row->perigo == 2) {
                                        echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    } else {
                                        echo "<option value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    }
                                }
                            } elseif ($antibiotico == 1 and $arr == 1) {
                                include('conexao.php');
                                $stmt = "Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where a.id = ".$arr_med[0];
                                $sth = pg_query($stmt) or die($stmt);
                                while ($row = pg_fetch_object($sth)) {
                                    if ($row->perigo == 1) {
                                        echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    } elseif ($row->perigo == 2) {
                                        echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    } else {
                                        echo "<option value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    }
                                }
                            } ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control square dosagem" id="dosagem" name="dosagem"
                                onkeyup="numero(this);"
                                value="<?php echo $dosagem; ?>">
                        </td>
                        <td>
                            <input type="button" name='add' id='add' class="btn btn-primary" value="+">
                        </td>
                    </tr>
                    <?php
                    if ($antibiotico == 1 and $arr == 1) {
                        for ($i=1; $i < count($arr_med); $i++) { ?>
                    <tr id='remove'>
                        <td><select class='form-control square teste selectnew' data-size='4' data-live-search='true'
                                name='escolha' id='escolha[]'>
                                <option value=''></option>
                                <?php include('conexao.php');
                                $stmt = "Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where a.id = ".$arr_med[$i];
                                $sth = pg_query($stmt) or die($stmt);
                                while ($row = pg_fetch_object($sth)) {
                                    if ($row->perigo == 1) {
                                        echo "<option style='color : #FFFFFF; background-color: red' value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    } elseif ($row->perigo == 2) {
                                        echo "<option style='background-color: yellow' value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    } else {
                                        echo "<option value=\"" . $row->id . "\"";
                                        echo "selected";
                                        echo ">" . $row->descricao . "</option>";
                                    }
                                }  ?>
                            </select></td>
                        <td><input type='text' class='form-control square dosagem' id='dosagem' name='dosagem'
                                onkeyup='numero(this);'></input></td>
                        <td><button class='btn btn-primary remove' type='button'>-</button></td>
                        <?php } ?>
                    </tr>
                    <?php
                    } ?>
                </tbody>
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
                <select class="form-control" name="diluente" id="diluente">
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
                            } ?>
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
                            } ?>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="form-group">
                <label>Aprazamento</label>
                <select class="form-control square selectnew" data-size="4" data-live-search="true" name="aprazamento"
                    id="aprazamento">
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
                            } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Informações Adicionais</label>
                <input type="text" class="form-control square selectnew" data-size="4" data-live-search="true"
                    id="complemento" name="complemento"
                    value="<?php echo $complemento; ?>">
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
            <input type="button" name='adicionar' id='adicionar' onclick='adc()' class="btn btn-primary"
                value="ADICIONAR">
            <br>
        </div>
    </div>
</div>
<?php
                        } elseif ($optradio == 'antibioticos') { ?>
<div id="solucoes">
    <div class="col-md-12 mt-2">
        <div class="form-group">
            <label class="radio-inline">
                <input class="mr-2" type="radio" name="optradio" id="optradio" value="dietas" onclick="radio()" <?php if ($optradio == 'dietas') {
                            echo "checked";
                        } ?>>Dieta
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos"
                    onclick="radio()" <?php if ($optradio == 'medicamentos') {
                            echo "checked";
                        } ?>>Medicamento
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()"
                    <?php if ($optradio == 'solucoes') {
                            echo "checked";
                        } ?>>Soluções
            </label>
            <label>
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="antibioticos"
                    onclick="radio()" <?php if ($optradio == 'antibioticos') {
                            echo "checked";
                        } ?>>Antibioticos
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados"
                    onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                            echo "checked";
                        } ?>>Cuidado
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()"
                    <?php if ($optradio == 'paciente') {
                            echo "checked";
                        } ?>>medicamento
                particular do paciente
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse1" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse1" aria-expanded="false" aria-controls="collapse1"
                        class="card-title lead collapsed">CABEÇA E PESCOÇO</a>
                </div>
                <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb1_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 1 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse2" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse2" aria-expanded="false" aria-controls="collapse2"
                        class="card-title lead collapsed">CUTÂNEA</a>
                </div>
                <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb2_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 2 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse3" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse3" aria-expanded="false" aria-controls="collapse3"
                        class="card-title lead collapsed">GENITOURINÁRIA</a>
                </div>
                <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb3_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 3 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse4" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse4" aria-expanded="false" aria-controls="collapse4"
                        class="card-title lead collapsed">PNEUMONIAS</a>
                </div>
                <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb4_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 4 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse5" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse5" aria-expanded="false" aria-controls="collapse5"
                        class="card-title lead collapsed">OSTEOARTICULAR</a>
                </div>
                <div id="collapse5" role="tabpanel" aria-labelledby="headingCollapse5" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb5_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 5 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse6" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse6" aria-expanded="false" aria-controls="collapse6"
                        class="card-title lead collapsed">GASTROINTESTINAL</a>
                </div>
                <div id="collapse6" role="tabpanel" aria-labelledby="headingCollapse6" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb6_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 6 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse7" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse7" aria-expanded="false" aria-controls="collapse7"
                        class="card-title lead collapsed">SNC</a>
                </div>
                <div id="collapse7" role="tabpanel" aria-labelledby="headingCollapse7" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb7_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 7 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse8" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse8" aria-expanded="false" aria-controls="collapse8"
                        class="card-title lead collapsed">FOURNIER</a>
                </div>
                <div id="collapse8" role="tabpanel" aria-labelledby="headingCollapse8" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb8_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 8 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse9" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse9" aria-expanded="false" aria-controls="collapse9"
                        class="card-title lead collapsed">DST'S</a>
                </div>
                <div id="collapse9" role="tabpanel" aria-labelledby="headingCollapse9" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb9_antibiotico">
                                <tbody>
                                    <?php include('conexao.php');
                                                                    $sql = "select a.controle_id, a.ordem, a.via, a.aprazamento, a.quantidade, a.categoria, array_to_string(array_agg(b.descricao),' + ') as descricao from (select controle_id, ordem, via, aprazamento, quantidade, categoria, unnest(string_to_array(medicamento, ',')) medicamento from controle_antimicrobiano) a inner join medicamentos b on a.medicamento::integer = b.id where categoria = 9 group by 1,2,3,4,5,6 order by ordem";
                                                                    $sth = pg_query($sql) or die($sql);
                                                                    while ($row = pg_fetch_object($sth)) { ?>
                                    <tr
                                        onclick="antibioticos(<?= $row->controle_id; ?>)">
                                        <td><?= $row->ordem; ?>
                                        </td>
                                        <td><?= $row->descricao; ?>
                                        </td>
                                        <td><?= $row->via; ?>
                                        </td>
                                        <td><?= $row->aprazamento; ?>
                                        </td>
                                        <td><?= $row->quantidade; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->categoria; ?>
                                        </td>
                                        <td style='display:none;'><?= $row->controle_id; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card collapse-icon accordion-icon-rotate">
                <div id="headingCollapse9" class="card-header pb-3">
                    <a data-toggle="collapse" href="#collapse10" aria-expanded="false" aria-controls="collapse10"
                        class="card-title lead collapsed">OUTROS</a>
                </div>
                <div id="collapse10" role="tabpanel" aria-labelledby="headingCollapse10" class="collapse" style="">
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-responsive-sm" id="tb10_antibiotico">
                                <tbody>
                                    <tr>
                                        <td><select class='form-control selectnew multat' multiple='multiple'
                                                name='antibiotico[]' id='antibiotico'>
                                                <option value=''></option><?php
                                                                                                                                                                                        include('conexao.php');
                                                                                                                                                                                        $stmt = 'Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where b.antibiotico = 1';
                                                                                                                                                                                        $sth = pg_query($stmt) or die($stmt);
                                                                                                                                                                                        while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                                            echo '<option value=\'' . $row->id . '\'';
                                                                                                                                                                                            echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                        } ?>
                                            </select></td>
                                        <td><input type="button" class='btn btn-sm btn-primary'
                                                onclick='outros_antibioticos()' value="Selecionar"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="medicamentos"
                    onclick="radio()" <?php if ($optradio == 'medicamentos') {
                                                                                                                                                                                            echo "checked";
                                                                                                                                                                                        } ?>>Medicamento
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="solucoes" onclick="radio()"
                    <?php if ($optradio == 'solucoes') {
                                                                                                                                                                                            echo "checked";
                                                                                                                                                                                        } ?>>Soluções
            </label>
            <label>
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="antibioticos"
                    onclick="radio()" <?php if ($optradio == 'antibioticos') {
                                                                                                                                                                                            echo "checked";
                                                                                                                                                                                        } ?>>Antibioticos
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="prescricao_cuidados"
                    onclick="radio()" <?php if ($optradio == 'prescricao_cuidados') {
                                                                                                                                                                                            echo "checked";
                                                                                                                                                                                        } ?>>Cuidado
            </label>
            <label class="radio-inline">
                <input class="mr-2 ml-2" type="radio" name="optradio" id="optradio" value="paciente" onclick="radio()"
                    <?php if ($optradio == 'paciente') {
                                                                                                                                                                                            echo "checked";
                                                                                                                                                                                        } ?>>medicamento
                particular do paciente
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
        var campos_novos =
            "<tr id='remove'><td><select class='form-control square teste selectnew' data-size='4' data-live-search='true' name='escolha' id='escolha[]' ><option value=''></option><?php if ($idade > 14 and ($prioridade == 'VERDE' or $prioridade == 'AZUL' or $prioridade == '')) {
                                                                                                                                                                                            include('conexao.php');
                                                                                                                                                                                            $stmt = 'Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where a.id not in(105352,105213,106625,105088,105383,105364,107221,105377,105457,105034,104928,105285,105371,105072,105013,104900,104898,105037) and b.antibiotico = 0';
                                                                                                                                                                                            $sth = pg_query($stmt) or die($stmt);
                                                                                                                                                                                            while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                                                if ($row->perigo == 1) {
                                                                                                                                                                                                    echo '<option style=\'color : #FFFFFF; background-color: red\' value=\'' . $row->id . '\'';
                                                                                                                                                                                                    if ($row->descricao == $escolha) {
                                                                                                                                                                                                        echo 'selected';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                } elseif ($row->perigo == 2) {
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
                                                                                                                                                                                            $stmt = 'Select a.descricao,a.id,a.perigo from medicamentos a inner join medicamentos3 b on a.id = b.id where b.antibiotico = 0';
                                                                                                                                                                                            $sth = pg_query($stmt) or die($stmt);
                                                                                                                                                                                            while ($row = pg_fetch_object($sth)) {
                                                                                                                                                                                                if ($row->perigo == 1) {
                                                                                                                                                                                                    echo '<option style=\'color : #FFFFFF; background-color: red\' value=\'' . $row->id . '\'';
                                                                                                                                                                                                    if ($row->descricao == $escolha) {
                                                                                                                                                                                                        echo 'selected';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    echo '>' . $row->descricao . '</option>';
                                                                                                                                                                                                } elseif ($row->perigo == 2) {
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