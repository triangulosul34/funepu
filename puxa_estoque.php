<?php
include('conexao.php');
$sqlx = "delete from medicamentos";
$sthx = pg_query($sqlx);
$rowx = pg_fetch_object($sthx);

include('conexaovalimh.php');
$sql = "select * from QtdeProdutoUPA WHERE produtoquantidade > 0 and subgrupoprodutodescricao='MEDICAMENTOS' order by produtodescricao";
$sth = pg_query($sql) or die($sql);
while ($row = pg_fetch_object($sth)) {
    include('conexao.php');
    $sqlx = "insert into medicamentos (descricao, id) values ('$row->produtodescricao', $row->produtoid) ";
    $sthx = pg_query($sqlx) or die($sqlx);
}



?>
<script>
    window.close();
    window.setTimeout(function() {
        location.href = 'https://www.google.com/';
    }, 1000);
</script>