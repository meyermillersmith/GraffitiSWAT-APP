<?php
// get the variables
$userId = $_GET['applicationUserId']; 
$eventId = $_GET['eventId'];
$rewards = $_GET['rewards']; 
$signature = $_GET['signature']; 
$timestamp = $_GET['timestamp']; 
$itemName = $_GET['itemName']; 
$privateKey = '123456';
// validate the call using the signature
if (md5($timestamp.$eventId.$userId.$rewards.$privateKey) != $signature){
	echo "Signature doesn’t match parameters";
	return;
}
// check that we haven't processed the very same event before
if (!alreadyProcessed($eventId)){ // grant the rewards
	doProcessEvent($eventId, $userId, $rewards);
}
// return ok
echo $eventId.":OK";
?>