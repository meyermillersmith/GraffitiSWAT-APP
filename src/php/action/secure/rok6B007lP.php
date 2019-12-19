<?php
	$status = 'failed';
	if (isset($_POST['uid']) && $_POST['uid']!=''){
		$status = 'failed::'.$_POST['uid'];
		$pageContent = file_get_contents('http://graph.facebook.com/'.$_POST['uid']);
		$parsedJson  = json_decode($pageContent);
 		$name = $parsedJson->name;
 		if ($name!= null && $name != ''){
			require '../../db/dbconnect.php';	
			$status = swat_login($_POST['uid'], $name, true, null);
		} else {
			$status = 'failed::name null '.$name;
		}
	}
	echo $status;
?>