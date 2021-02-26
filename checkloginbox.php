<?php

include 'Config.php';

error_reporting(0);

include 'conexao.php';
include 'tsul_ssl.php';

$myusername = $_POST['myusername'];
$mypassword = md5($_POST['mypassword']);
$box = $_POST['box'];

$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);

$unidade = '';

$ip = getenv('REMOTE_ADDR');
if ($ip = '201.48.4.1') {
	$unidade == '1';
	$unid_desc = 'Mirante';
} elseif ($ip = '201.48.4.2') {
	$unidade == '2';
	$unid_desc = 'São Benedito';
}

$myusername = pg_escape_string($myusername);
$mypassword = pg_escape_string($mypassword);

$sql = "SELECT * FROM pessoas WHERE username='$myusername' and password='$mypassword'";
$result = pg_query($con, $sql) or die($sql);
$row = pg_fetch_array($result);
if (CONTROLE_ACESSO) {
	if (($_SERVER['REMOTE_ADDR'] == '179.104.42.235' or $_SERVER['REMOTE_ADDR'] == '189.41.99.44') or ($row['pessoa_id'] == USUARIO[0] or $row['pessoa_id'] == USUARIO[1] or $row['pessoa_id'] == USUARIO[2] or $row['pessoa_id'] == USUARIO[3] or $row['pessoa_id'] == USUARIO[4] or $row['pessoa_id'] == USUARIO[5] or $row['pessoa_id'] == USUARIO[6] or $row['pessoa_id'] == USUARIO[7] or $row['pessoa_id'] == USUARIO[8] or $row['pessoa_id'] == USUARIO[9] or $row['pessoa_id'] == USUARIO[10] or $row['pessoa_id'] == USUARIO[11] or $row['pessoa_id'] == USUARIO[12] or $row['pessoa_id'] == USUARIO[13])) {
		//if ($box == $_POST['conf_consultorio'] && $box != '') {
		if ($row['username'] != '') {
			session_start();
			$_SESSION['myusername'] = $myusername;
			$_SESSION['nome'] = ts_decodifica($row['nome']);
			$_SESSION['mypassword'] = $mypassword;
			$_SESSION['id'] = $row['pessoa_id'];
			$_SESSION['perfil'] = $row['perfil'];
			$_SESSION['imagem'] = $row['imagem'];
			$_SESSION['box'] = $box;
			$id = $row['pessoa_id'];
			$_SESSION['unidade'] = $unidade;

			if (($_SESSION['perfil'] == '03' and $_SESSION['box'] == '8') or ($_SESSION['perfil'] == '03' and $_SESSION['box'] == '9')) {
				$_SESSION['perfil'] = '08';
				$aux = 1;
			}

			include 'conexao.php';
			$data_hora = date('Y-m-d H:i:s');
			$sql = "insert into logons (data_hora, usuario, consultorio) values ('$data_hora', '$myusername', '$box') ";
			$result = pg_query($con, $sql) or die($sql);
			$row = pg_fetch_array($result);

			//validaçao de acesso
			// if ($box == '20' or $box == '21') {
			// 	header('location:index.php');
			// } elseif ($box == '11') {
			// 	header('location:painel_at_ala_vermelha.php');
			// } else
			if ($_SESSION['perfil'] == '03') {
				if ($box >= 1 and $box <= 5) {
					header('location:monitor_medico.php');
				} elseif ($box == 6) {
					header('location:painel_at_ortopedia.php');
				} elseif ($box == 7) {
					header('location:painel_at_ala_vermelha.php');
				} else {
					header('location:monitor_medico.php');
				}
			} elseif ($_SESSION['perfil'] == '08') {
				if ($aux == 1) {
					header('location:triagemRecepcao.php');
				} else {
					header('location:monitor_triagem.php');
				}
			} elseif ($_SESSION['perfil'] == '09') {
				header('location:painel_at_ortopedia.php');
			} elseif ($_SESSION['perfil'] == '05') {
				header('location:painel_rx.php');
			} else {
				header('location:index.php');
			}
		} else {
			if ($box == '') {
				//('location:login.html');
			} else {
				//header('location:loginbox.php');
			}
		}
		// } else {
// 	header('location:loginbox.php');
// }
	} else {
		echo "<script>alert('Acesso não permitido por esse dispositivo!!!');window.location.href = 'http://www.google.com.br';</script>";
	}
} else {
	//if ($box == $_POST['conf_consultorio'] && $box != '') {
	if ($row['username'] != '') {
		session_start();
		$_SESSION['myusername'] = $myusername;
		$_SESSION['nome'] = ts_decodifica($row['nome']);
		$_SESSION['mypassword'] = $mypassword;
		$_SESSION['id'] = $row['pessoa_id'];
		$_SESSION['perfil'] = $row['perfil'];
		$_SESSION['imagem'] = $row['imagem'];
		$_SESSION['box'] = $box;
		$id = $row['pessoa_id'];
		$_SESSION['unidade'] = $unidade;

		if (($_SESSION['perfil'] == '03' and $_SESSION['box'] == '8') or ($_SESSION['perfil'] == '03' and $_SESSION['box'] == '9')) {
			$_SESSION['perfil'] = '08';
			$aux = 1;
		}

		include 'conexao.php';
		$data_hora = date('Y-m-d H:i:s');
		$sql = "insert into logons (data_hora, usuario, consultorio) values ('$data_hora', '$myusername', '$box') ";
		$result = pg_query($con, $sql) or die($sql);
		$row = pg_fetch_array($result);

		//validaçao de acesso
		// if ($box == '20' or $box == '21') {
		// 	header('location:index.php');
		// } elseif ($box == '11') {
		// 	header('location:painel_at_ala_vermelha.php');
		// } else
		if ($_SESSION['perfil'] == '03') {
			if ($box >= 1 and $box <= 5) {
				header('location:monitor_medico.php');
			} elseif ($box == 6) {
				header('location:painel_at_ortopedia.php');
			} elseif ($box == 7) {
				header('location:painel_at_ala_vermelha.php');
			} else {
				header('location:monitor_medico.php');
			}
		} elseif ($_SESSION['perfil'] == '08') {
			if ($aux == 1) {
				header('location:triagemRecepcao.php');
			} else {
				header('location:monitor_triagem.php');
			}
		} elseif ($_SESSION['perfil'] == '09') {
			header('location:painel_at_ortopedia.php');
		} elseif ($_SESSION['perfil'] == '05') {
			header('location:painel_rx.php');
		} else {
			header('location:index.php');
		}
	} else {
		if ($box == '') {
			header('location:login.html');
		} else {
			header('location:loginbox.php');
		}
	}
	// } else {
// 	header('location:loginbox.php');
// }
}
