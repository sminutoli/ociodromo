$(window).load( init );

var client_id = "e9eb27d0a4d24bb3dce4db6cdfe8a9a6";
var flickr_id = "bb26164d9b12f118fb1fcc96fadde952";
var aAudio; //array de audios
var aImgs; //array de fotos
var autocheckID;

function init(){
	$(".hidden").removeClass("hidden");
	searchAudio( "ocio" );
	searchImg( "amarillo" );
	$("#tagcloud h2").css( { top: $(window).height()/2, left: $(window).width()/2 } );
	$("#tagcloud h2").click( function(e){ $("#taginput").attr("value", $(e.target).html() ); saveWord(); } );
	sortWords();
	autocheckID = setInterval( autocheck, 10000 );
}
function autocheck(){
	$.ajax({
		  url: "index.php?autocheck=true",
		  success: words_loaded
		});
}
function words_loaded(e){
	var words = eval(e);
	if( $("#tagcloud h2").length >= words.length ) return;
	
	for( var i=0; i < words.length; i++){
		var check = true;
		$("#tagcloud h2").each( function( index, el ){
			if( $(el).html() == words[i] ){
				check = false;
				return;
			}
		} );
		//console.log( words[i], check );
		if( check ){
			var h2 = document.createElement("h2");
			$("#tagcloud").append(h2);
			$(h2).html( words[i] ).css({ top: $(window).height()/2, left: $(window).width()/2 });
			$(h2).click( function(e){ saveWord($(e.target).html() ); } );
			sortWord( h2 );
		}
	}
}

function searchAudio( tag ){
	$("#audioConsole").hide(0);
	$("#audioLoading").show(0);
	$.ajax({
		  url: "http://api.soundcloud.com/tracks.json?client_id="+client_id+"&q="+tag+"&offset="+ Math.floor( Math.random()*100 ),
		  success: audio_loaded
		});
}
function audio_loaded(e){
	$("#audioConsole").show(0);
	$("#audioLoading").hide(0);
	aAudio = e;
	if( aAudio.length > 1000 ) aAudio = eval(e);
	getRandomAudio();
}
function getRandomAudio(){
	var nSong = Math.floor( Math.random()*aAudio.length + 1 );
	var theSong = aAudio[ nSong ];
	document.getElementById("audio").src = theSong.stream_url+"?client_id="+client_id;
	document.getElementById("audio").play();
	//console.log( theSong );
	$("#song_title").html( theSong.title );
	$("#song_title").attr("href", theSong.permalink_url );
	$("#song_author").html( theSong.user.username );
	$("#song_author").attr("href", theSong.user.permalink_url );
}

function searchImg( tag ){
	//console.log( "searchImg", tag );
	//console.log( "http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=29f1c5a736945a7df372f75c9c5912c8&tags=amarillo%2C+"+tag+"&format=json&nojsoncallback=1&auth_token=72157629076416151-9da82d93dd4a7a2c&api_sig=1981424fcf875d774a029d21057608e3" );
	$("#imgContainer").empty();
	$.ajax({
		  url: "http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key="+flickr_id+"&tags="+tag+"&format=json&nojsoncallback=1",
		  success: imgs_loaded
		});
}
function imgs_loaded(e){
	//console.log(e.photos.photo);
	aImgs = [];
	try {
	    aImgs = eval( e.substring( e.indexOf("["), e.lastIndexOf("]")+1 ) );
	} catch( error ){
	    aImgs = e.photos.photo;
	    }
	for( var i=0; i < 20; i++){
		getRandomImg();
	}

}


function getRandomImg(){
	var nImg = Math.floor( Math.random()*aImgs.length + 1 );
	var theImg = aImgs[ nImg ];
	if( !theImg || !theImg.farm ) getRandomImg();
	aImgs.splice( nImg, 1 );
	//console.log( theImg );
	try {
		var src = "http://farm"+theImg.farm+".staticflickr.com/"+theImg.server+"/"+theImg.id+"_"+theImg.secret+"_m.jpg"
		$("#imgContainer").append( "<a href='http://www.flickr.com/photos/"+theImg.owner+"/"+theImg.id+"' target='_blank'><img src='"+src+"' title='"+theImg.title+"' height='100px' /></a>");
	} catch (e){
		getRandomImg();
	}
}

function saveWord(word){
	if( word ){
		$("#taginput").attr("value", word );
	}
	var theWord = $("#taginput").attr("value");
	$.ajax({
		  url: "?word="+theWord,
		  //url: "http://es.wikipedia.org/w/api.php?format=json&action=query&titles="+theWord+"&prop=revisions&rvprop=content",
		  success: is_word_valid
		});
}
function is_word_valid(e){
	if( e == "[]" ){
		alert("no parece ser una palabra");
	} else {
		searchAudio( $("#taginput").attr("value") );
		searchImg( $("#taginput").attr("value") );
		$("#taginput").attr("value", "");
		if( e != "" ){
			var h2 = document.createElement("h2");
			$("#tagcloud").append(h2);
			$(h2).html(e).css({ top: $(window).height()/2, left: $(window).width()/2 });
			sortWord( h2 );
			$("#tagcloud h2").click( function(e){ saveWord( $(e.target).html() ); } );
			
		} else {
			//alert("la palabra ya está cargada");
		}
		
	}
}

function sortWords(){
			
	$("#tagcloud h2").each( function(i, el){ 
		sortWord( el );
	} );
}
function sortWord(el){
	var nHeight = $(window).height() - 100;
	var nWidth = $(window).width() - 100;
	var nT;
	do{
		nT = Math.random()* nHeight;
	} while( nT > nHeight/2 - 100 && nT < nHeight/2 + 100 );
	
	var nL;
	do{
		nL = Math.random()* nWidth;
	} while( nL > nWidth/2 - 200 && nL < nWidth/2 + 200 );
	$(el).animate( { top: nT+"px", left: nL+"px" }, 1000 );
}