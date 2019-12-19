<?php
// get the variables
$userId = $_GET['applicationUserId'];
$eventId = $_GET['eventId'];
$rewards = $_GET['rewards'];
$signature = $_GET['signature'];
$timestamp = $_GET['timestamp'];
$itemName = $_GET['itemName'];
$privateKey = '2b2d5e';

logToFile($fbid,'grant.php::post='.print_r($_GET,true); );
// validate the call using the signature
if (md5($timestamp.$eventId.$userId.$rewards.$privateKey) != $signature){
	echo "Signature doesn’t match parameters";
	return;
}

require '../../db/dbconnect.php';
// check that we haven't processed the very same event before
if (!checkBrandConnectProcessed($eventId)){ // grant the rewards
	echo saveOrderBrandConnect($userId,$itemName,$eventId);
	
	logToFile($fbid,'grant.php::saving='.);
} else {
	echo $eventId.":OK:already processed";
}
// return ok
// echo $eventId.":OK";
?>