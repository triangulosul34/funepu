<?php
$id = $_GET['id'];

include('conexao.php');
$sql = "DELETE FROM destino_paciente where destino_id = $id";
$result = pg_query($sql) or die($sql);
?>
<table id="data_table" class="table">
    <thead>
        <tr>
            <th>Prontuário</th>
            <th>Paciente</th>
            <th>DT. Entrada</th>
            <th>DT. Saída</th>
            <th>Destino</th>
            <th>Dias de Permanência</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include('conexao.php');
        $sql = "SELECT a.destino_id, b.paciente_id, c.nome, b.dat_cad as data_entrada, a.data as data_saida, a.destino_encaminhamento as destino  FROM destino_paciente a INNER JOIN atendimentos b ON a.atendimento_id = b.transacao INNER JOIN pessoas c ON b.paciente_id = c.pessoa_id WHERE motivo = 'Finalizado pelo controle de Permanencia' AND data_controle = '" . date('Y-m-d') . "'";
        $result = pg_query($sql) or die($sql);
        while ($row = pg_fetch_object($result)) {
        ?>
            <tr>
                <td><?= $row->paciente_id; ?></td>
                <td><?= $row->nome; ?></td>
                <td><?= inverteData(substr($row->data_entrada, 0, 10)); ?></td>
                <td><?= inverteData($row->data_saida); ?></td>
                <?php
                if ($row->destino == '01') {
                    echo '<td>ALTA</td>';
                } else if ($row->destino == '02') {
                    echo '<td>ALTA / ENCAM. AMBUL.</td>';
                } else if ($row->destino == '07') {
                    echo '<td>EM OBSERVAÇÃO / MEDICAÇÃO</td>';
                } else if ($row->destino == '10') {
                    echo '<td>EXAMES / REAVALIACAO</td>';
                } else if ($row->destino == '03') {
                    echo '<td>PERMANÊCIA</td>';
                } else if ($row->destino == '04') {
                    echo '<td>TRANSF. OUTRA UPA</td>';
                } else if ($row->destino == '05') {
                    echo '<td>TRANSF. INTERN. HOSPITALAR</td>';
                } else if ($row->destino == '06') {
                    echo '<td>ÓBITO</td>';
                } else if ($row->destino == '09') {
                    echo '<td>NAO RESPONDEU CHAMADO</td>';
                } else if ($row->destino == '11') {
                    echo '<td>ALTA EVASÃO</td>';
                } else if ($row->destino == '12') {
                    echo '<td>ALTA PEDIDO</td>';
                } else if ($row->destino == '14') {
                    echo '<td>ALTA / POLICIA</td>';
                } else if ($row->destino == '15') {
                    echo '<td>ALTA / PENITENCIÁRIA</td>';
                } else if ($row->destino == '16') {
                    echo '<td>ALTA / PÓS MEDICAMENTO</td>';
                }
                ?>
                <td><?= date('d', (strtotime($row->data_saida) - strtotime(substr($row->data_entrada, 0, 10)))); ?></td>
                <td><button class="btn btn-raised btn-danger btn-min-width mr-1 mb-1" onclick="cancelar_permanencia(<?= $row->destino_id; ?>)">Cancelar</button></td>
            </tr>
        <?php } ?>
    </tbody>
</table>