<?php

require_once '../../includes/fbheader.inc';
// $exp1 = isset($_GET['albumId']);
// $exp2 = $exp1 && $_GET['albumId'] == '416269841802107';
// $exp3 = $exp1 && $_GET['albumId'] == '314067388722409';//416197801809311
// $exp4 = $exp2 || $exp3;
// if ($exp4){
// 	die('{"error" : { "message" : "(#321) Album is full", "type" : "OAuthException", "code" : "321" }}');
// }
$status= array();
if (isset($_GET['albumId']) && $_GET['albumId']!='' && isset($GLOBALS["HTTP_RAW_POST_DATA"])){
	
	$filename = '../../../'.'public/files/temp_private/'.(isset($_GET["filename"])? $_GET["filename"] : "graffiti_swat_".time().".png");
		
	//save temp file
	$handler = fopen($filename, 'wb');
	fwrite($handler, $GLOBALS["HTTP_RAW_POST_DATA"]);
	fclose($handler);
		
	//send file to fb
	$_GET['message'] = stripslashes($_GET['message']);
	$args = array('message' => $_GET['message'], 'image' => '@' . realpath($filename));
	//try with graph api
// 	$fbuser = null;
	if (isset($fbuser)) {
		
		/* GRAPH API VERSION (BUGGED STARGING 17.04) */
		 try {
			setUserAccessToken($_GET['access_token']);
			
// 			$data = $facebook->api('/'. $_GET['albumId'] . '/photos?access_token=' . $_GET['access_token'], 'post', $args);
			//$data = $facebook->api('/'. $_GET['albumId'] . '/photos', 'post', $args);
			$request = new FacebookRequest($session, 'POST', '/'. $_GET['albumId'] . '/photos', $args);
			$data = $request->execute()->getGraphObject()->asArray();
			$status = $data;
		} catch (FacebookSDKException $e) {
			logError("chr0MeEx7.php::FacebookSDKException " . $e->getMessage());
			logError("chr0MeEx7.php::FacebookSDKException flash access token:" . $_GET['access_token']);
			logError("chr0MeEx7.php::FacebookSDKException __php access token:" . $_SESSION['fb_token']);
			//
			$status[1] = $e->getResult();
		}
	} else {
		
		/* SIMPLE POST VERSION */
		    $ch = curl_init();
			$url = "https://graph.facebook.com/". $_GET['albumId']. "/photos?access_token=" . $_GET['access_token'];
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_HEADER, false);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_POST, true);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		    if (false === ($data = curl_exec($ch))) {
// 		    	logError("chr0MeEx7.php::Sending via CURL - fbuser is empty. failed ".curl_error($ch));
		    	$errorObject = array('type'=>'SWATException','code'=>'SWAT_UPLOAD_CURL_FAILED','message'=>'Curl failed to upload / retrieve answer:'.curl_error($ch));
				$status[1] = array('error'=> $errorObject);
		    } else {
		  	 	$status = json_decode($data,true);
		    }
		    curl_close ($ch);
	}
			
		//delete file
		unlink($filename);
} else {
	$errorObject = array('type'=>'SWATException');
	if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
		$errorObject['code'] = 'SWAT_UPLOAD_ALBUM_MISSING';
		$errorObject["message"] = "Album id null.";
	} else {
		$errorObject['code'] = 'SWAT_UPLOAD_IMAGE_MISSING';
		$errorObject["message"] = "Image Data Empty.";
	}
	$status[1] = array('error'=> $errorObject);
}
echo json_encode($status);
?>