<?php

include('tsul_ssl.php');
include('conexao.php');
$stmt = "select pessoa_id, nome from pessoas_ssl where pessoa_id>1990675 order by pessoa_id";
$sth = pg_query($stmt) or die($stmt);
while ($row = pg_fetch_object($sth)) {
    
    $nomessl = ts_decodifica($row->nome);

    
    echo $row->pessoa_id.'-'. $nomessl.'<br>';


  

}


?>