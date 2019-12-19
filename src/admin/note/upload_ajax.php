<?php
require '../../php/includes/fbheader.inc';
$status = 'failed';
$now = date( 'Y-m-d H:i:s', time() );
if ($fbuser) {
	$fbid = $user_profile['id'];
	require '../../php/db/dbconnect.php';
	require '../../php/lang/notification_lang.php';
	 if (isAdmin($fbid)){
	 	$status = 'success::access granted::'.$fbid;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
        
        <title>Ka-Ching!!</title>
    
    
		<script type="text/javascript" src="../../js/external/jquery-1.2.5.min.js"></script>
    </head>
    
    <body>
    
    
    <h1>
       Send notifications
    </h1>
    <div id="buttons">
    	<div id="button_stop" style="background-color:#AACC88;width:100px">START</div>
    </div>
    <div id="mainContent">
    Hey there!
    </div>
    <div id="waitContent">
    </div>
    <br/>
    <br/>
    <span id="sound"></span>
    
    </body>
    
		<script type="text/javascript">

			var trials = 0;
			var stopped = true;

			function reloadNotes(){
				trials++;
				var url = "upload.php";
				var request = {url: url, type:'POST', statusCode: { 500: onServerTimeout },success:onMoreLoaded, error:onMoreFailed};
				$.ajax(request);
			}

			function onServerTimeout() {
				console.log('500 error ');	
				addContent('onServerTimeout! trials '+trials);
				if (!stopped) window.setTimeout(reloadNotes, 1000);
			}
			
			function onMoreFailed(data){
				console.log('other error, try again ');	
				addContent('onMoreFailed! trials '+trials);
				if (!stopped) window.setTimeout(reloadNotes, 1000);
			}

			function onMoreLoaded(data){
				addContent('DONE!! finally!!!! trials '+trials);
				playSound('kaching');

				$('#button_stop').unbind("click");
				$('#button_stop').html("DONE!");
			}
			
			function addContent(string,div,omitTimestamp){
				div = div || "mainContent";
				string=(omitTimestamp?'':'<span style="color:#CCCCCC">['+getMySQLTime(new Date())+']</span> ')+string;
				$('#'+div).append(string+'<br/>');
				//TODO play katchiiing sound
			}

			function emptyDiv(div){
				console.log('empty '+div );	
				$('#'+div).empty();
				//TODO play katchiiing sound
			}

			function playSound(sound) {
				var soundfile = "../assets/sounds/"+sound+".mp3";
				document.getElementById("sound").innerHTML=
				"<embed src=\""+soundfile+"\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			}

			$('#button_stop').click(toggleStop);

			function toggleStop(){
				stopped = !stopped;
				$('#button_stop').html(stopped?'CONTINUE':'STOP');
				if (!stopped){
					reloadNotes();	
				}
			}

			function getMySQLDate(date){
				var mydate = date.getUTCFullYear() + '-' +
	            ('00' + (date.getUTCMonth()+1)).slice(-2) + '-' +
	            ('00' + date.getUTCDate()).slice(-2) + ' ' + 
	            getMySQLTime(date);

	            return mydate;
			}

			function getMySQLTime(date){
				var mytime = ('00' + date.getUTCHours()).slice(-2) + ':' + 
	            ('00' + date.getUTCMinutes()).slice(-2) + ':' + 
	            ('00' + date.getUTCSeconds()).slice(-2);

	            return mytime;
			}
			
		</script>

</html>


<?php 
	 	
	 } else {
		$status = 'failed::no access granted::'.$fbid;
	 }
} else {
	$status = 'login::'.$_POST['entry_id'];
}
echo $status;
?>