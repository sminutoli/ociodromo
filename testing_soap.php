<?php
	$url = 'http://cvc.cervantes.es/artes/p_corrientes/maquina/wspalabras/default.asmx?WSDL';
	$ws = new SoapClient($url);
	$res = $ws->damePalabrasValidas(1, false);
	var_dump($res);
?>