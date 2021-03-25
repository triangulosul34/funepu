<?php
include 'conexao.php';
$sql = 'select * from receituario_remedio where transacao = ' . $_GET['transacao'] . ' and medicamentos =\'' . $_GET['medicamento'] . '\'';
$result = pg_query($sql);
$row = pg_fetch_object($result);
?>
<div class="modal fade" id="modalEditaReceituario" aria-hidden="true" aria-labelledby="exampleModalTabs" role="dialog"
    tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalTabs">Receituário</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id='modalbody'>
                <div id="bloco_receituario">
                    <div class="row">

                        <div class="col-4">
                            <div class="form-group">
                                <label>Item/Medicamento</label>
                                <input id="medicamentoedit" maxlength="100" name="medicamentoedit" class="form-control"
                                    value="<?= $row->medicamentos; ?>"
                                    onkeyup="maiuscula(this)">
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input id="quantidadeedit" maxlength="50" name="quantidadeedit" class="form-control"
                                    value="<?= $row->quantidade; ?>"
                                    onkeyup="maiuscula(this)">
                            </div>
                        </div>

                        <div class="col-5">
                            <div class="form-group">
                                <label>Modo de usar</label>
                                <textarea id="usaredit" name="usaredit" class="form-control" value=""
                                    onkeyup="maiuscula(this)"><?= $row->modo_usar; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="salvar_edicao_prescricao" value="" class="btn btn-default"
                    onclick="salvar_edicao_prescricao()">Salvar</button>
                <button type="button" id="closemodal" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>



        </div>
    </div>
</div>