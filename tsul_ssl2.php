<?php

function ts_codifica($texto)
{
	$ivlen = openssl_cipher_iv_length($cipher = 'AES-128-CBC');
	$iv = openssl_random_pseudo_bytes($ivlen);
	$ciphertext_raw = openssl_encrypt($texto, $cipher, 'tsul', $options = OPENSSL_RAW_DATA, $iv);
	$hmac = hash_hmac('sha256', $ciphertext_raw, 'tsul', $as_binary = true);
	$ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

	return $ciphertext;
}

function ts_decodifica($texto)
{
	$c = base64_decode($texto);
	//$c = $iv.$hmac.$ciphertext_raw;
	$ivlen = openssl_cipher_iv_length($cipher = 'AES-128-CBC');
	$iv = substr($c, 0, $ivlen);
	$hmac = substr($c, $ivlen, $sha2len = 32);
	$ciphertext_raw = substr($c, $ivlen + $sha2len);
	$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, 'tsul', $options = OPENSSL_RAW_DATA, $iv);
	$calcmac = hash_hmac('sha256', $ciphertext_raw, 'tsul', $as_binary = true);
	if (hash_equals($hmac, $calcmac)) {//PHP 5.6+ timing attack safe comparison
		return $original_plaintext;
	} else {
		return 'Falha';
	}
}
