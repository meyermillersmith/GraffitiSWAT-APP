<?php
require_once '../../utils/encoding.php';
require_once '../../db/dbconnect.php';
 	$key = $_POST['gid'];
	$answer = checkGotSurface($_POST['fbid'],$_POST['surface']);
	$encoded = encrypt($answer,$key);
  	echo $encoded;
?>