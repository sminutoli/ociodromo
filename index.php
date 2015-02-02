<?php
	require_once("classes/DB.class.php");
	
	if( $_GET['soundcloud_tag'] ){
		$client_id = "e9eb27d0a4d24bb3dce4db6cdfe8a9a6";
		$url = "http://api.soundcloud.com/tracks.json?client_id=$client_id&q=$_GET[soundcloud_tag]&offset=".rand(0,100);
		$response = file_get_contents($url);
		$obj = json_decode($response);
		foreach($obj as $snd){
			$snd->stream_url_parsed = $snd->stream_url."?client_id=$client_id";
		}
		$response = json_encode($obj);
		header('Content-type: application/json');
		die( $response );
	}

	if( $_GET['flickr_tag'] ){
		$flickrUrl = 'https://api.flickr.com/services/feeds/photos_public.gne';
		$flickrUrl .= '?format=json&nojsoncallback=1&tags='.$_GET['flickr_tag'];
		$response = file_get_contents($flickrUrl);
		//we clean the response...
		$response = str_replace("\\'", "'", $response);
		header('Content-type: application/json');
		die( $response );
	}

	if( $_GET['autocheck'] ){
		$words = DB::getDB()->getWords();
		foreach( $words as $w ){
			$buffer[] = $w['tag'];
		}
		header('Content-type: application/json');
		die( json_encode( $buffer ) );
	}
	
	if( $_GET['word'] ){
		
		$_GET['word'] = strtolower( $_GET['word'] );
		
		//$word = file_get_contents( "http://rae-quel.appspot.com/json?query=".urlencode($_GET['word']) );
        $word = $_GET['word'];
		if( $word != "[]" ){
			$check = DB::getDB()->saveWord( $_GET['word'] );
			if($check) $word = $_GET["word"];
			else $word = "";
		}
		header('Content-type: text/html; charset=utf-8');
		die( $word );
	}

	header('Content-type: text/html; charset=utf-8');
	
	$words = DB::getDB()->getWords();
	$wordsHTML = "";
	foreach( $words as $w ){
		$wordsHTML .= "<h2>$w[tag]</h2>\r\n";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>OCIODROMO</title>
		<link rel="stylesheet" type="text/css" href="css/ociodromo.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script src="js/jkey-1.2.js"></script>
		<script src="js/ociodromo.js"></script>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-28779175-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	</head>
	<body>
		<div id="back1">&nbsp;</div>
		<div id="back2">&nbsp;</div>
		<div id="tagcloud">
			<?=$wordsHTML?>
		</div>
		
		<div id="imgContainer">
			
		</div>
		
		<img id="logo" src="img/logo.png" />
		
		<form onsubmit="saveWord(); return false;">
			<input id="taginput" name="taginput" type="text" placeholder="¿Qué es el ocio para vos?" pattern="[A-zñÑáéíóúÁÉÍÓÚ]+" title="tipea una palabra, sin espacios ni números" required="required" />
		</form>
		
		<div id="footer">
			<span id="proyectInfo">Ociodromo es un proyecto <a href='http://www.facebook.com/groups/269985896396984/' target='_blank'>BISIESTO</a> de Sergio Minutoli</span>
			<div id="helpers">
				<a href="http://www.soundcloud.com/" target="_blank" title="con una ayudita de soundcloud"><img src="img/sound_cloud_icon.png" /></a>
				<a href="http://www.flickr.com" target="_blank" title="con una ayudita de flickr"><img src="img/flickr-icon.gif" /></a>
				<a href="http://www.google.com/Chrome" target="_blank" title="optimizado para google chrome"><img src="img/chrome_logo_24.png" /></a>
			</div>
			<span id="console">
				<div id="audioConsole" class="hidden">
					Estás escuchando <a id='song_title' href='http://soundcloud.com/esthelaone/03-pista-3' target='_blank'>"pista 3"</a> de <a id='song_author' href="http://soundcloud.com/esthelaone"  target='_blank'>esthelaone</a>
					| 
					<a href="#" onclick="document.getElementById('audio').pause()" >stop</a>
					-
					<a href="#" onclick="document.getElementById('audio').play()" >play</a>
					-
					<a href="#" onclick="getRandomAudio()" >siguiente</a>
									
				</div>
				
				<div id="audioLoading" class="hidden">
					Cargando...
				</div>
			</span>
			
		</div>
		
		<audio id="audio" autoplay="true" onended='getRandomAudio()'>
		  Your browser does not support the audio tag.
		</audio>
		
		<?=$pepe?>
	</body>
</html>