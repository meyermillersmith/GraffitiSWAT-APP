<?php 
	date_default_timezone_set('UTC'); 
	//if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
		// get bytearray
		// $png = $GLOBALS["HTTP_RAW_POST_DATA"]; 
$png = file_get_contents('php://input');
		$filename = isset($_GET["name"])? $_GET["name"] : "graffiti_swat_".time().".png";
		//$handler = fopen($filename, 'w');
		//fwrite($handler, $png);
		//fclose($handler); 
		header('Content-Type: image/jpeg');
		header("Content-Disposition: attachment; filename=".$filename);
		
	    echo $png;

		//readfile($filename);
		//}
	?>
<!--	
<html>
	<head>
		<title>Download Graffiti</title>
	</head>
	<body style="font-family:Arial;font-weight:bold;">
	<?php
		if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
			echo('<img src="'.$filename.'" />');
			echo('<br/>If you cannot see this graffiti above, left click on the <a href="'.$filename.'">link</a> and choose "Save Link As".');
		} else {
			echo 'An Error occurred. Please try again';
		}
	?>
	<body>
</html> -->