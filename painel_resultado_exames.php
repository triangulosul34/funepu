<?php


function inverteData($data){
	if(count(explode("/",$data)) > 1)
	{
		return implode("-",array_reverse(explode("/",$data)));
	}
	elseif(count(explode("-",$data)) > 1)
	{
		return implode("/",array_reverse(explode("-",$data)));
	}
}
error_reporting(0);
$menu_grupo='3';
$menu_sgrupo='1';
$nome 		='';
$dtnasc 	='';
$telefone	='';
$mae     	='';
include ('verifica.php');
	$RX			 ='';
	$US		 	 ='';
	$CT			 ='';
	$MM			 ='';
	$RM			 ='';
	$DS			 ='';
	$ECO		 ='';
	


if ($_SERVER['REQUEST_METHOD']=='GET')
{
	$codigo = $_GET['id'];
	if ($codigo!="") { $where=' pessoa_id ='.$codigo; }
}

if ($_SERVER['REQUEST_METHOD']=='POST')
{
	$procedimentox=$_POST['procedimentox'];
	$situacao     =$_POST['situacao'];
	$nome 		  =$_POST['nome'];
	$xbox		  =$_POST['xbox'];
	$RX			  =$_POST['cb_rx'];
	$US		 	  =$_POST['cb_us'];
	$CT			  =$_POST['cb_tc'];
	$MM			  =$_POST['cb_mm'];
	$RM			  =$_POST['cb_rm'];
	$DS			  =$_POST['cb_ds'];
	$ECO		  =$_POST['cb_eco'];
	$start 		  =$_POST['start'];
	$end 		  =$_POST['end'];
	$transfere 	  =$_POST['cb_exame'];
	$profissional =$_POST['prof_transfere'];
	$cb_meus      =$_POST['cb_meus'];
	$cb_conf      =$_POST['cb_CONFERENCIA'];
	
	$where="";

	
	if (isset($_POST['semana']))
	{
		$start 		 =date('d/m/Y', strtotime("-7 days"));
		$end 		 =date('d/m/Y');
	}	
	if (isset($_POST['hoje']))
	{
		$start 		 =date('d/m/Y');
		$end 		 =date('d/m/Y');
	}
	if (isset($_POST['ontem']))
	{
		$start 		 =date('d/m/Y',strtotime("-1 days"));
		$end 		 =date('d/m/Y',strtotime("-1 days"));
	}
	$modalidades="";
	
	if ($RX!="")
	{
		$modalidades=$modalidades."'RX',";				
	}
	if ($US!="")
	{
		$modalidades=$modalidades."'US',";				
	}
	if ($CT!="")
	{
		$modalidades=$modalidades."'TC',";				
	}	
	if ($MM!="")
	{
		$modalidades=$modalidades."'MM',";				
	}
	if ($RM!="")
	{
		$modalidades=$modalidades."'RM',";				
	}	
	if ($ECO!="")
	{
		$modalidades=$modalidades."'EC',";				
	}
	if ($DS!="")
	{
		$modalidades=$modalidades."'DS',";				
	}

	$modalidades= substr($modalidades, 0, -1);	
	
	if ($nome!="")
	{ $where=$where." c.nome like '%".$nome."%' "; }

	if ($procedimentox!="")
	{ if ($where!="") {$where=$where." and a.exame_id = $procedimentox"; } else {$where=$where." a.exame_id = $procedimentox"; } }

	if ($xbox!="")
	{ if ($where!="") {$where=$where." and box = $xbox"; } else {$where=$where." box = $xbox"; } }

	if ($modalidades!="")
	{ if ($where!="") {$where=$where." and f.sigla in ($modalidades) "; } else {$where=$where." f.sigla in ($modalidades) "; } }

	if ($start!="")
	{   $data = inverteData($start);
	if ($where!="") {$where=$where." and (a.dat_cad >= '$data')"; } else {$where=$where." (a.dat_cad >= '$data')"; }
	}

	if ($end!="")
	{
		$data = inverteData($end);
		if ($where!="") {$where=$where." and (a.dat_cad <= '$data')"; } else {$where=$where." (a.dat_cad <= '$data')"; }
	}
	
	if ($situacao!="")
	{
		if ($situacao!="Pendentes")
		{	
			if ($where!="" ) {$where=$where." and (a.status = '$situacao')"; } else {$where=$where." (a.status = '$situacao')"; }
		}
		else
		{		
			if ($where!="" and $status=="Pendentes" ) {$where=$where." and (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )"; } else {$where=$where." (a.status not in ('Finalizado', 'Env.Recepção', 'Impresso') )"; }
		}	
	}

	if ($cb_meus!="")
	{
		if ($where!="") {$where=$where." and (a.med_analise = '$cb_meus' or a.med_confere = '$cb_meus' )"; } else {$where=$where." (a.med_analise = '$cb_meus' or a.med_confere = '$cb_meus')"; }
	}	
	
	if ($cb_conf!="")
	{
		if ($where!="") {$where=$where." and (a.med_confere = '$cb_conf')"; } else {$where=$where." (a.med_confere = '$cb_conf')"; }
	}		
	$stmtx="nao entrei";
	
	if ($transfere!="")
	{
		if (isset($_POST["transferir"]))
		{

			include('conexao.php');
			$stmty = "Select username from pessoas where pessoa_id = $profissional";
		
			$sth = pg_query($stmty) or die($stmty);
			$row= pg_fetch_object($sth);
			$username=$row->username;
			if ($username!="")
			{
				include('conexao.php');
				$stmtx = "Update itenspedidos set med_analise = '".$username."' where exame_nro in (".implode(',', $transfere).") and (situacao='Editado' or situacao='Cadastrado')";
				$sth = pg_query($stmtx) or die($stmtx);
					
				foreach ($transfere as $item)
				{
					include('conexao.php');
					$data  = date('Y-m-d H:i:s');
					$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Distribuicao', '$usuario', '$data' )";
					$sth   = pg_query($stmtx) or die($stmtx);
				}
			}
		}
		if (isset($_POST["transfconf"]))
		{

			include('conexao.php');
			$stmty = "Select username from pessoas where pessoa_id = $profissional";
		
			$sth = pg_query($stmty) or die($stmty);
			$row= pg_fetch_object($sth);
			$username=$row->username;
			if ($username!="")
			{
				include('conexao.php');
				$stmtx = "Update itenspedidos set med_confere = '".$username."' where exame_nro in (".implode(',', $transfere).") and (situacao='Laudado' or situacao='Editado')";
				$sth = pg_query($stmtx) or die($stmtx);
					
				foreach ($transfere as $item)
				{
					include('conexao.php');
					$data  = date('Y-m-d H:i:s');
					$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Distribuicao', '$usuario', '$data' )";
					$sth   = pg_query($stmtx) or die($stmtx);
				}
			}
		}		
		if (isset($_POST["imprimir"]))
		{
			echo "<script>alert('Imprimir')</script>";
		}
		if (isset($_POST["enviar"]))
		{

				include('conexao.php');
				$stmtx = "Update itenspedidos set situacao = 'Env.Recepção', envio_recepcao=now(), usu_envio_recepcao='$usuario'
                where exame_nro in (".implode(',', $transfere).") and (situacao='Finalizado' or situacao='Impresso')";
				$sth = pg_query($stmtx) or die($stmtx);
					
				foreach ($transfere as $item)
				{
					include('conexao.php');
					$data  = date('Y-m-d H:i:s');
					$stmtx = "Insert into log_exames (exame_nro, acao, usuario, data_hora) values ($item, 'Env Recepcao', '$usuario', '$data' )";
					$sth   = pg_query($stmtx) or die($stmtx);
				}
			
		}
	}
}


if($perfil == '03'){?>
<script>
	window.location = "monitor_medico.php";
</script>
<?php
}
?>
<!DOCTYPE html>
<html lang="pt-br" class="loading">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="tsul" content="tsul">
    <meta name="keywords" content="tsul">
    <meta name="author" content="TSUL">
    <title>FUNEPU | Resultado de Exames</title>
    <link rel="apple-touch-icon" sizes="60x60" href="app-assets/img/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="app-assets/img/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="app-assets/img/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="app-assets/img/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/png" href="app-assets/img/gallery/logotc.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/tsul.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/pickadate/pickadate.css">
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <!--load all styles -->
</head>
<style>
    hr {
        color: #12A1A6;
        background-color: #12A1A6;
        margin-top: 0px;
        margin-bottom: 0px;
        height: 4px;
        width: 300px;
        margin-left: 0px;
        border-top-width: 0px;
    }
</style>

<body class="pace-done" cz-shortcut-listen="true">
    <!-- <div class="pace pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div> -->

    <!-- <div class="wrapper"> -->
    <?php include('menu.php'); ?>
    <?php include('header.php'); ?>
    <div class="main-panel">
        <div class="main-content">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- <div class="card-header" style="display: flex;align-items: center;justify-content: space-between; background: #00777a"> -->

                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-12">
                                                <h4 class="card-title">
                                                    <p style="color: #12A1A6;display:inline;font-size: 18pt;font-weight: bold;">
                                                        » </p>Resultado de Exames
                                                </h4>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <ol class="breadcrumb">
                                                <li><a href="../index.html">Home</a></li>
                                                <li class="active">Resultado de Exames</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('footer.php'); ?>
        <!-- </div> -->

        <script src="app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/core/popper.min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/core/bootstrap.min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/prism.min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/jquery.matchHeight-min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/screenfull.min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/pace/pace.min.js" type="text/javascript"></script>
        <script src="app-assets/vendors/js/chartist.min.js" type="text/javascript"></script>
        <script src="app-assets/js/app-sidebar.js" type="text/javascript"></script>
        <script src="app-assets/js/notification-sidebar.js" type="text/javascript"></script>
        <script src="app-assets/js/customizer.js" type="text/javascript"></script>
        <script src="app-assets/js/dashboard1.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
        <script src="app-assets/js/scripts.js" type="text/javascript"></script>
        <script src="app-assets/js/popover.js" type="text/javascript"></script>
        <script src="app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
        <script defer src="/your-path-to-fontawesome/js/all.js"></script>
</body>

</html>