<?php
	require '../../utils/encoding.php';
	require '../../db/dbconnect.php';
 	$key = $_POST['gid'];
	$jsonString = getTools($_POST['fbid'], true);
	$encoded = encrypt($jsonString,$key);
  	echo $encoded;
?>