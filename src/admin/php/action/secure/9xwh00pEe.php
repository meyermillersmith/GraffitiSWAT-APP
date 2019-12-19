<?php
$response = array("tools" => array(), "surfaces" => array());
if (isset($_POST["timestamp"]) ) {
	require '../../../../php/db/dbconnect.php';
	$firstCall = ($_POST["timestamp"] == 'undefined');
	if ($firstCall){
		$_POST["timestamp"] = '2013-03-08 09:00:00';
	}
	$response["tools"] = checkLastBoughtItem('tool',$_POST["timestamp"]);
	$response["surfaces"] = checkLastBoughtItem('surface',$_POST["timestamp"]);
	$response["timestamp"] = date( 'Y-m-d H:i:s', time() );
	if ($firstCall){
		$response["firstCall"] = true;
	}
} else {
	$response["error"] = "timestamp and/or itemtype not set";
}
echo json_encode($response);
?>