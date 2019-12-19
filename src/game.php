<?php
$title = 'Graffiti S.W.A.T. Spray Tool';
$GLOBALS["page"] = 'game';
require 'php/includes/header.inc';
?>

	<!--[if lte IE 6]><div class="fullFlashWrapperIE6"><![endif]-->

	<div id="swfWrapper">
	<div id="swfContainer">
			<h1>Please update your Flash Player</h1>
			<p>This site makes use of the Adobe Flash Player 10 and Javascript.</p>
			<p>The latest versions of browsers such as Firefox, Netscape or Internet Explorer usually have the Flash Player pre-installed. If your browser doesn't or has an older version of the player, you can <a href="https://www.adobe.com/go/getflashplayer" target="_blank"><b>download it here</b></a>.</p>
			<p>Please make sure you have Javascript turned on in the settings of your browser.</p>
			<p><a href="https://www.adobe.com/go/getflashplayer" target="_blank"><img src="images/160x41_Get_Flash_Player.jpg" alt="Get Adobe Flash Player" title="Get Adobe Flash Player" border="0" /></a><br /><a href="?detectflash=false"><b>Click here</b></a> if you're quite sure to have Flash 10 installed.</p>
		</div>
		</div>
	<!--[if lte IE 6]></div><![endif]-->
	
	<script type="text/javascript" src="js/browserdetect.js"></script>
	<script type="text/javascript">
		resizeFlash();
		function resizeFlash(){
			$("#swfWrapper").height($(window).height() - $('#headerWrapper').height());
		}
 		window.onresize = resizeFlash;


	</script>
	<!--<div id="fb-like" class="fb-like" data-href="http://www.facebook.com/GraffitiSWAT" data-layout="button_count" data-show-faces="true" data-colorscheme="dark" data-font="lucida grande"></div> -->

<?php 
	require 'php/includes/footer.inc';
?>