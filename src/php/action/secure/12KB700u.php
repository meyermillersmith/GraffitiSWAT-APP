<?php
    require_once '../../db/dbconnect.php';
    require_once '../../utils/SimpleImage.php';
    require_once '../../utils/encoding.php';
    require_once '../../facebook/fbconfig.php';
	
	date_default_timezone_set('UTC'); 
	
	$security_test_passed = false;
	$response = 'failed';
	
	if (isset($_GET['chunks']) && isset($_GET['gid'])){
		$security_test_passed = decrypt($_GET['chunks'],$_GET['gid']) == $GLOBALS["appId"];
	}
	
	if ($security_test_passed && isset($GLOBALS["HTTP_RAW_POST_DATA"])) {

		// get bytearray
		
		$image = $GLOBALS["HTTP_RAW_POST_DATA"]; 
	
		$filename = 'public/files/tshirts/'.(isset($_GET["filename"])? $_GET["filename"] : "graffiti_swat_".time());
		$filename.='.png';
		$path = '../../../';
		
	
		$handler = fopen($path.$filename, 'wb');
		fwrite($handler, $image);
		fclose($handler);
		
		ini_set('memory_limit', '-1');
		$simpleImage = new SimpleImage;
		$load_successfull = $simpleImage->loadWithFileCheck($path.$filename);
		
		
		if(!$load_successfull){
			deleteFile($filename);
			logToFile($_GET['fbid'], 'saveShirt::image data incomplete by '.$_GET['fbid']);
			$response = 'failed::'.$filename.'::file incomplete';
		} else {
			$response = 'success::'.$filename;
		}
		
	} else {
		$response = 'failed::'.(!isset($GLOBALS["HTTP_RAW_POST_DATA"])?'image data missing':'security test failed!! user:'.$_GET['fbid']);
		logToFile($_GET['fbid'], 'saveShirt::'.$response);
	}
	
	echo $response;
	?>