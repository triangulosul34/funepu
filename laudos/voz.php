<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="tsul" content="tsul">
    <meta name="keywords" content="tsul">
    <meta name="author" content="TSUL">
    <title>TSul | Pagina Padrao</title>
    <link rel="apple-touch-icon" sizes="60x60" href="../app-assets/img/ico/apple-icon-60.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../app-assets/img/ico/apple-icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../app-assets/img/ico/apple-icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../app-assets/img/ico/apple-icon-152.png">
    <link rel="shortcut icon" type="image/png" href="../app-assets/img/gallery/logotc.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../app-assets/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/fonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/chartist.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/tsul.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/pickadate/pickadate.css">
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
	<style>
		* {margin:0; padding:0;
			font-family: arial;
		}


		#ola {
			font-size: 50px;
			color: #fff;
			text-align: center;
			text-shadow: -1px -1px 0 #ccc; margin: 50px 0 30px}

		#transcription {
			width: 100%;
			border-radius: 5px;
			height: 200px;
			margin: 0 auto;
			display: block;
			font-size: 16px;
			padding: 11px;
			color: #666;
			background: #fff;
		}

		#gravar {
			border: none;
			background: transparent;
			font-size: 40px;
			color: #fff;
			width: 100%;
			outline-color: transparent;
			padding-top: 20px;
		}

		#gravar i { cursor: pointer;
		width: 80px;
		height: 80px;
		line-height: 80px;
		border-radius: 100%;
		box-shadow: inset 0 0 0 transparent;
		-webkit-transition: all 0.5s linear;
		-moz-transition: all 0.5s linear;
		-ms-transition: all 0.5s linear;
		-o-transition: all 0.5s linear;
		transition: all 0.5s linear;
	margin-bottom: 15px;}

		#gravar i:hover {
			box-shadow: inset 0 0 20px #fff;
		}
		#gravar i:active {box-shadow: inset 0 0 20px 100px #fff; color:#E81D62;  }

		#status {color: #fff; text-align: center; display: block}
		#status span {font-weight: bold;}
		#status span.gravando {color: rgb(70, 232, 29);}
		#status span.pausado {color: rgb(173, 115, 229);}

		.hidden {display: none;}
		#ws-unsupported {
font-size: 60px;
position: fixed;
width: 140%;
text-align: center;
height: 100px;
background: red;
color: #000;
-webkit-transform: rotateZ(-30deg);
-ms-transform: rotateZ(-30deg);
-o-transform: rotateZ(-30deg);
box-shadow: 0 0 7px rgba(0, 0, 0, 0.67);
transform: rotateZ(-30deg);
top: 190px;
		}

	#rect {
		display: block;
		margin: 30px auto;
		background: #fff;
		padding: 10px;
		border: none;
		font-size: 18px;
		border-radius: 5px;
		color: rgb(232, 29, 98);
		font-family: arial;
	}
	</style>
</head>

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
                                                        » </p>Pedidos
                                                </h4>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-6">

                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form name="total" method="post">
                                        <div class="panel">
                                            <div class="panel-body">


                                                <div class="row">
                                                    
                                                    <div class="col-3">
                                                        <label class="control-label" for="inputBasicFirstName">Prontuário</label>
                                                        <input type="text" class="form-control" id="inputBasicFirstName" name="prontuario" placeholder="Número do Prontuário" autocomplete="off" value="<?php echo $prontuario; ?>" onkeyup="somenteNumeros(this);" />
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="control-label" for="inputBasicFirstName">Paciente</label>
                                                        <input type="text" class="form-control" id="inputBasicFirstName" name="nome" placeholder="Parte do Nome" autocomplete="off" value="<?php echo $nome; ?>" onkeyup="maiuscula(this)" />
                                                    </div>

                                                    <div class="col-3">
                                                        <label class="control-label">Situacao</label>
                                                        <select class="form-control" name="situacao" id="situacao">
                                                            <option value="">Todos</option>
                                                            <option value="Pendentes" <?php if ($situacao == 'Pendentes') {
                                                                                            echo "selected";
                                                                                        } ?>>Pendentes</option>
                                                            <option value="Cadastrado" <?php if ($situacao == 'Cadastrado') {
                                                                                            echo "selected";
                                                                                        } ?>>Cadastrado</option>
                                                            <option value="Realizado" <?php if ($situacao == 'Realizado') {
                                                                                            echo "selected";
                                                                                        } ?>>Realizado</option>
                                                            <option value="Alterado" <?php if ($situacao == 'Alterado') {
                                                                                            echo "selected";
                                                                                        } ?>>Alterado</option>
                                                            <option value="Editado" <?php if ($situacao == 'Editado') {
                                                                                        echo "selected";
                                                                                    } ?>>Editado</option>
                                                            <option value="Laudado" <?php if ($situacao == 'Laudado') {
                                                                                        echo "selected";
                                                                                    } ?>>Laudado</option>
                                                            <option value="Conferido" <?php if ($situacao == 'Conferido') {
                                                                                            echo "selected";
                                                                                        } ?>>Conferido</option>
                                                            <option value="Finalizado" <?php if ($situacao == 'Finalizado') {
                                                                                            echo "selected";
                                                                                        } ?>>Finalizado</option>
                                                            <option value="Env.Recepção" <?php if ($situacao == 'Env.Recepção') {
                                                                                                echo "selected";
                                                                                            } ?>>Enviado Recepção</option>
                                                            <option value="Rec.Recepção" <?php if ($situacao == 'Rec.Recepção') {
                                                                                                echo "selected";
                                                                                            } ?>>Recebido Recepção</option>
                                                            <option value="Entregue" <?php if ($situacao == 'Entregue') {
                                                                                            echo "selected";
                                                                                        } ?>>Entregue</option>
                                                            <option value="Cancelado" <?php if ($situacao == 'Cancelado') {
                                                                                            echo "selected";
                                                                                        } ?>>Cancelado</option>

                                                        </select>
                                                    </div>


                                                    <div class="col-12 mt-3" align="center">
                                                    <button type="submit" name="pesquisa" value="semana" class="btn btn-primary">Gravar</button>
                                                    <button type="submit" name="ontem" value="ontem" class="btn btn-warning">Imprimir</button>
                                                    <button type="submit" name="hoje" value="hoje" class="btn btn-success">Voltar</button>
                                                    </div>

                                                </div>


                                            </div>

		<div class="col-12 mt-3" align="center">
		<textarea class="form-control" id="transcription"> 
		</textarea>
		</div>
 
		<button id="rect" type='button'>Ditar</button>
 
	    <span id="unsupported" class="hidden">API not supported</span>
 
    <script type="text/javascript">

      // Test browser support
      window.SpeechRecognition = window.SpeechRecognition       ||
                                 window.webkitSpeechRecognition ||
                                 null;
 
		//caso não suporte esta API DE VOZ                              
		if (window.SpeechRecognition === null) {
	    	document.getElementById('unsupported').classList.remove('hidden');
        }else {
            var recognizer = new window.SpeechRecognition();
            var transcription = document.getElementById("transcription");

        	//Para o reconhecedor de voz, não parar de ouvir, mesmo que tenha pausas no usuario
        	recognizer.continuous = true

        	recognizer.onresult = function(event){
        		transcription.textContent = "";
        		for (var i = event.resultIndex; i < event.results.length; i++) {
        			if(event.results[i].isFinal){
        				transcription.textContent = event.results[i][0].transcript;
        			}else{
		            	transcription.textContent += event.results[i][0].transcript;
        			}
        		}
        	}

        	document.querySelector("#rect").addEventListener("click",function(){
        		try { 
				    alert('Modo Ditado Ligado');
		            recognizer.start();
		          } catch(ex) {
		          	alert("error: "+ex.message);
		          }
        	});
        }

	</script>
    <script src="../app-assets/vendors/js/core/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/core/popper.min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/prism.min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/jquery.matchHeight-min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/screenfull.min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/pace/pace.min.js" type="text/javascript"></script>
    <script src="../app-assets/vendors/js/chartist.min.js" type="text/javascript"></script>
    <script src="../app-assets/js/app-sidebar.js" type="text/javascript"></script>
    <script src="../app-assets/js/notification-sidebar.js" type="text/javascript"></script>
    <script src="../app-assets/js/customizer.js" type="text/javascript"></script>
    <script src="../app-assets/js/dashboard1.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
    <script src="../app-assets/js/scripts.js" type="text/javascript"></script>
    <script src="../app-assets/js/popover.js" type="text/javascript"></script>
    <script src="../app-assets/js/pick-a-datetime.js" type="text/javascript"></script>
    <script defer src="/your-path-to-fontawesome/js/all.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>	
</body>
</html>