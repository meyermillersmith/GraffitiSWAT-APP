<?php
require 'php/includes/fbheader.inc';
$locale = $user_profile['locale'];
$locale_explode = explode("_", $locale);
$lang = $locale_explode[0];
$language = strtoupper($lang);
$maintenance_text = '';

require 'php/lang/maintenance_lang.php';

if (!isset($GLOBALS["MAINTENANCE_".$language])){
	$language = 'EN';
}

$maintenance_text = $GLOBALS["MAINTENANCE_".$language];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>Graffiti S.W.A.T. under Maintenance</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	
		<link rel="stylesheet" type="text/css" href="css/maintenance.css" />
		<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-28975922-1']);
			  _gaq.push(['_trackPageview']);
			
			  (function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
		</script>
	</head>
	<body>
	<div id="upperBG"></div>
	<div id="lowerBG"></div>
	<div id="bgLine"></div>
	
	<div id="mainImage"></div>
	<div id="mainText"><?php echo $maintenance_text; ?></div>
	</body>
</html>