<?php

function ts_codifica($texto)
{
	$texto = str_split($texto);
	for ($i = 0; $i < count($texto); $i++) {
		if ($texto[$i] == 'A') {
			$texto[$i] = 'CCR';
		} elseif ($texto[$i] == 'B') {
			$texto[$i] = '7AX';
		} elseif ($texto[$i] == 'C') {
			$texto[$i] = 'SG8';
		} elseif ($texto[$i] == 'D') {
			$texto[$i] = '7FX';
		} elseif ($texto[$i] == 'E') {
			$texto[$i] = 'K7M';
		} elseif ($texto[$i] == 'F') {
			$texto[$i] = 'CxM';
		} elseif ($texto[$i] == 'G') {
			$texto[$i] = 'jgT';
		} elseif ($texto[$i] == 'H') {
			$texto[$i] = 'NoP';
		} elseif ($texto[$i] == 'I') {
			$texto[$i] = 'tBN';
		} elseif ($texto[$i] == 'J') {
			$texto[$i] = 'p8c';
		} elseif ($texto[$i] == 'K') {
			$texto[$i] = 'iAe';
		} elseif ($texto[$i] == 'L') {
			$texto[$i] = '07e';
		} elseif ($texto[$i] == 'M') {
			$texto[$i] = 'MPl';
		} elseif ($texto[$i] == 'N') {
			$texto[$i] = 'LNr';
		} elseif ($texto[$i] == 'O') {
			$texto[$i] = 'iYo';
		} elseif ($texto[$i] == 'P') {
			$texto[$i] = 'wbc';
		} elseif ($texto[$i] == 'Q') {
			$texto[$i] = '8B9';
		} elseif ($texto[$i] == 'R') {
			$texto[$i] = 'ROn';
		} elseif ($texto[$i] == 'S') {
			$texto[$i] = 'i9o';
		} elseif ($texto[$i] == 'T') {
			$texto[$i] = 'wpm';
		} elseif ($texto[$i] == 'U') {
			$texto[$i] = 'NNn';
		} elseif ($texto[$i] == 'V') {
			$texto[$i] = 'rEk';
		} elseif ($texto[$i] == 'X') {
			$texto[$i] = 'MR1';
		} elseif ($texto[$i] == 'Y') {
			$texto[$i] = 'zdF';
		} elseif ($texto[$i] == 'Z') {
			$texto[$i] = 'gBd';
		} elseif ($texto[$i] == 'W') {
			$texto[$i] = 'DDD';
		} elseif ($texto[$i] == '0') {
			$texto[$i] = 'Lx5';
		} elseif ($texto[$i] == '1') {
			$texto[$i] = 'nw6';
		} elseif ($texto[$i] == '2') {
			$texto[$i] = 'mEL';
		} elseif ($texto[$i] == '3') {
			$texto[$i] = 'PmK';
		} elseif ($texto[$i] == '4') {
			$texto[$i] = '5Z1';
		} elseif ($texto[$i] == '5') {
			$texto[$i] = 'hmE';
		} elseif ($texto[$i] == '6') {
			$texto[$i] = 'KUI';
		} elseif ($texto[$i] == '7') {
			$texto[$i] = 'wia';
		} elseif ($texto[$i] == '8') {
			$texto[$i] = 'nX4';
		} elseif ($texto[$i] == '9') {
			$texto[$i] = 's36';
		} elseif (preg_match("/\s/", $texto[$i])) {
			$texto[$i] = 'MFt';
		} elseif ($texto[$i] == "'") {
			$texto[$i] = '';
		}
	}

	return implode('', $texto);
}

function ts_decodifica($texto)
{
	$texto = explode('-', chunk_split($texto, 3, '-'));
	for ($i = 0; $i < count($texto); $i++) {
		if ($texto[$i] == 'CCR') {
			$texto[$i] = 'A';
		} elseif ($texto[$i] == '7AX') {
			$texto[$i] = 'B';
		} elseif ($texto[$i] == 'SG8') {
			$texto[$i] = 'C';
		} elseif ($texto[$i] == '7FX') {
			$texto[$i] = 'D';
		} elseif ($texto[$i] == 'K7M') {
			$texto[$i] = 'E';
		} elseif ($texto[$i] == 'CxM') {
			$texto[$i] = 'F';
		} elseif ($texto[$i] == 'jgT') {
			$texto[$i] = 'G';
		} elseif ($texto[$i] == 'NoP') {
			$texto[$i] = 'H';
		} elseif ($texto[$i] == 'tBN') {
			$texto[$i] = 'I';
		} elseif ($texto[$i] == 'p8c') {
			$texto[$i] = 'J';
		} elseif ($texto[$i] == 'iAe') {
			$texto[$i] = 'K';
		} elseif ($texto[$i] == '07e') {
			$texto[$i] = 'L';
		} elseif ($texto[$i] == 'MPl') {
			$texto[$i] = 'M';
		} elseif ($texto[$i] == 'LNr') {
			$texto[$i] = 'N';
		} elseif ($texto[$i] == 'iYo') {
			$texto[$i] = 'O';
		} elseif ($texto[$i] == 'wbc') {
			$texto[$i] = 'P';
		} elseif ($texto[$i] == '8B9') {
			$texto[$i] = 'Q';
		} elseif ($texto[$i] == 'ROn') {
			$texto[$i] = 'R';
		} elseif ($texto[$i] == 'i9o') {
			$texto[$i] = 'S';
		} elseif ($texto[$i] == 'wpm') {
			$texto[$i] = 'T';
		} elseif ($texto[$i] == 'NNn') {
			$texto[$i] = 'U';
		} elseif ($texto[$i] == 'rEk') {
			$texto[$i] = 'V';
		} elseif ($texto[$i] == 'MR1') {
			$texto[$i] = 'X';
		} elseif ($texto[$i] == 'zdF') {
			$texto[$i] = 'Y';
		} elseif ($texto[$i] == 'gBd') {
			$texto[$i] = 'Z';
		} elseif ($texto[$i] == 'DDD') {
			$texto[$i] = 'W';
		} elseif ($texto[$i] == 'Lx5') {
			$texto[$i] = '0';
		} elseif ($texto[$i] == 'nw6') {
			$texto[$i] = '1';
		} elseif ($texto[$i] == 'mEL') {
			$texto[$i] = '2';
		} elseif ($texto[$i] == 'PmK') {
			$texto[$i] = '3';
		} elseif ($texto[$i] == '5Z1') {
			$texto[$i] = '4';
		} elseif ($texto[$i] == 'hmE') {
			$texto[$i] = '5';
		} elseif ($texto[$i] == 'KUI') {
			$texto[$i] = '6';
		} elseif ($texto[$i] == 'wia') {
			$texto[$i] = '7';
		} elseif ($texto[$i] == 'nX4') {
			$texto[$i] = '8';
		} elseif ($texto[$i] == 's36') {
			$texto[$i] = '9';
		} elseif ('MFt') {
			$texto[$i] = ' ';
		}
	}

	return trim(implode('', $texto));
}
