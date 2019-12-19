<?php
 	$sep = '||';
 	$sep2 = ':';
 	$end = "\n";
	
	date_default_timezone_set('Europe/Berlin');
 	
 	$ipaddress = 'IP'.$sep2.$_SERVER['REMOTE_ADDR'];
 	$user = 'USER'.$sep2.$_POST['user'];
 	$message = 'ERROR DETAILS:'.$sep2.$_POST['message'];
 	$flash = 'FLASH'.$sep2.$_POST['flash'];
 	$os = 'OS'.$sep2.$_POST['os'];
 	$browser = 'BROWSER'.$sep2.$_POST['browser'];
 	
 	$log_string = date("y.m.j-G:i:s").$sep.$ipaddress.$sep.$user.$sep.$message.$sep.$os.$sep.$browser.$sep.$flash.$end;
 	$filename = dirname(__FILE__)."/../../../public/files/logs/error_log_".date("y-m-j").".txt";
 	if (!file_exists($filename)){
		$filehandler = fopen($filename, 'wb') or die("can't open file ");
		fclose($filehandler);
 	}
 	error_log($log_string, 3, $filename);
 	//error_log($log_string, 1,"denise@lessrain.com");
?>