<?php

$cid = $_GET['cid'];

include('conexao.php');
if ($cid != '') {

    $stmt = "select cid,descricao from cid10 where upper(descricao) like upper('%$cid%') order by descricao LIMIT 15";
    $sth = pg_query($stmt) or die($stmt);
    $data = '';
    while ($row = pg_fetch_object($sth)) {
?>
        <tr>
            <td>
                <a onclick="preencheCid('<?php echo $row->cid ?>', '<?php echo $row->descricao ?>') "><?php echo $row->descricao ?></a>
            </td>
        </tr>
<?php }
} ?>