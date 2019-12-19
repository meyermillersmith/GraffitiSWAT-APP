<?php
if(!isset($_SESSION)) {
     session_start();
}

date_default_timezone_set('Europe/Berlin');
	
define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ .'/../facebook/facebook-php-sdk-v4-4.0-dev/src/Facebook/');
require __DIR__ . '/../facebook/facebook-php-sdk-v4-4.0-dev/autoload.php';

require_once dirname(__FILE__).'/../facebook/fbconfig.php';

//ini_set('display_errors', 1);
// error_reporting(E_ALL);

if (!isset($using_error_handler)) {
    require_once dirname(__FILE__).'/errorhandler.inc';
}

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookSDKException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\FacebookCanvasLoginHelper;

FacebookSession::setDefaultApplication($GLOBALS["appId"], $GLOBALS["secret"]);

//used signed request if exists
$signed_request_data = get_signed_request();

if (isset($signed_request_data)) {
	if (isset($signed_request_data["user_id"])){
		$signed_req_user = $signed_request_data["user_id"];
		if ($_SESSION['fbuser'] != $signed_req_user){
			$_SESSION['fbuser'] = $signed_req_user;
			$_SESSION['fb_token'] = $signed_request_data["oauth_token"];
			unset($_SESSION['user_profile']);
		}
	} else {
		unset($_SESSION['fb_token']);
		unset($_SESSION['fbuser']);
		unset($_SESSION['user_profile']);
	}
}

// see if a existing session exists
if (isset($_SESSION['fb_token'] )) {
    // create new session from saved access_token
    $session = new FacebookSession($_SESSION['fb_token']);
    // validate the access_token to make sure it's still valid
    try {
		$valid = $session->validate();
		/*echo "session valid?".$_SESSION['user_profile']["name"]."<br/>";
		echo "token = ".$_SESSION['fb_token']."<br/>";
		print_r($valid);*/
        if (!$session->validate()) {
            $session = null;
			$_SESSION['fb_token'] = null;
        }
    } catch (Exception $e) {
        // catch any exceptions
		logError("session->validate()::Facebook Exception " . $e->getMessage());
        $session = null;
		$_SESSION['fb_token'] = null;
    }
} 

if(!isset($session)) {
    // no session exists
    try {
		$helper = new FacebookCanvasLoginHelper();
		$session = $helper->getSession();
    } catch (FacebookRequestException $e) {
        // When Facebook returns an error
		logError("helper->getSession()::Facebook FacebookRequestException " . $e->getMessage());
    } catch (Exception $e) {
        // When validation fails or other local issues
		logError("helper->getSession()::Facebook Exception " . $e->getMessage());
    }
}
// see if we have a session
if (isset($session)){
	
	//echo "session is iset, fbuser in session? ".$_SESSION['fbuser']."<br/>";
	if(!isset($_SESSION['fb_token']) || !isset($_SESSION['fbuser']) || $_SESSION['fbuser'] == 0 || !isset($_SESSION['user_profile'])) {
	  try {
		  
		// save the session
		$_SESSION['fb_token'] = $session->getToken();
		// create a session using saved token or the new one we generated at login
		$session = new FacebookSession($session->getToken());
		// graph api request for user data
		$request = new FacebookRequest($session, 'GET', '/me?fields=id,name,locale,first_name');
		$response = $request->execute();
		$user_profile = $response->getGraphObject()->asArray();
		 
		//echo "look 4 user ".$user_profile["id"];
		
		$fbuser = $user_profile["id"];
		$_SESSION['fbuser'] = empty($fbuser)? 0 : $fbuser;
		$_SESSION['user_profile'] = $user_profile;
		
		if (!empty($fbuser)) {
			try {
				// get permissions
				$request = new FacebookRequest($session, 'GET', '/me/permissions');
				$user_permissions = $request->execute()->getGraphObject()->asArray();
				//$action_permission = isset($user_permissions['data'][0]['publish_actions']);
				
				$_SESSION['user_permissions'] = $user_permissions;
				$_SESSION['action_permission'] = $action_permission;
				
			} catch (FacebookSDKException $e) {
				logError("getUserDetails::$fbuser::Facebook Exception " . $e->getMessage());
				$fbuser = null;
			}
		}
		
		//echo "Name: " . $user_profile["name"]." fbuser ".$fbuser;
	  } catch(FacebookSDKException $e) {
			logError("getUser::Facebook Exception " . $e->getCode()." ".$e->getMessage());
	  }   
	} else {
		$fbuser = $_SESSION['fbuser'];
		$user_profile = $_SESSION['user_profile'];
		$user_permissions = $_SESSION['user_permissions'];
		$action_permission = $_SESSION['action_permission'];
	}
	
	$request_ids = array();
		
	if (isset($_REQUEST['request_ids']) && trim($_REQUEST['request_ids']) !== ''){
		//get the request ids from the query parameter
		$request_ids = explode(',', $_REQUEST['request_ids']);

		if ($GLOBALS["page"] != 'collabs'){
			$collabCount = count($request_ids);
			$collabListRedirect = "<script type=\"text/javascript\">location.href = 'collabs.php?collabCount=".$collabCount."&request_ids=".$_REQUEST['request_ids']."' ;</script>";

			if ($collabCount > 1){
				echo $collabListRedirect;
				die();
			} else {
				checkRequestIds();
			}
		}

	}
}

//echo "session ".(isset($session)?"SET":"NOT SET")." fbid $fbuser fbtoken ".$_SESSION['fb_token'];

function checkRequestIds($collabListCheck = false){
	global $request_ids, $facebook, $collabListRedirect, $session;
	foreach ($request_ids as $request_id){
		try {
			$request = new FacebookRequest($session, 'GET', "/".$request_id);
			$requestObj = $request->execute()->getGraphObject()->asArray();
			$deleteRequest = $requestObj && $collabListCheck;
			if ($requestObj && array_key_exists("data",$requestObj)){
				$requestData = json_decode($requestObj['data']);
				if ($requestData->type){
					if ($requestData->type == 'collab'){
						if ($requestData->collab_id && $requestData->collab_id != ''){
							$_GET["collab_id"] = $requestData->collab_id;
							$_GET["collab_request_id"] = $request_id;
							if ($collabListCheck){
								$deleteRequest = !matchRequestAndCollab($requestData, $_GET["collab_id"], $request_id);
							}
						}
					}
				}

			}
			if ($deleteRequest){
				
				deleteAppRequest($request_id);
				if (!$collabListCheck){
					echo $collabListRedirect;
					die();
				}
			}
		}
		catch (FacebookSDKException $e) {
		 	logError("checkRequestIds::".$_REQUEST["request_ids"]."::Facebook Exception " . $e->getMessage());
		}
	}
}
function getAppAccessToken(){
	$token_url = "https://graph.facebook.com/oauth/access_token?" .
			"client_id=" . $GLOBALS["appId"] .
			"&client_secret=" . $GLOBALS["secret"] .
			"&grant_type=client_credentials";
	$app_token = file_get_contents($token_url);
	$app_token_bits = explode('=', $app_token);
	$app_token = count($app_token_bits) > 1? $app_token_bits[1] : $app_token;
	return $app_token;
}

function deleteAppRequest($request_id) {
	global $facebook, $session;
	$appAccessToken = getAppAccessToken();
	//$facebook->setAccessToken($appAccessToken); not needed?
	try{
		//$delete_success = $facebook->api("/$request_id",'DELETE');
		$request = new FacebookRequest($session, 'DELETE', "/$request_id");
		$delete_success = $request->execute()->getGraphObject()->asArray();
	}catch (FacebookSDKException $e) {
		 logError("deleteAppRequest::$request_id::Facebook Exception " . $e->getMessage());
	}
	$delete_success = "false";
	setUserAccessToken();
}


function setUserAccessToken($accessToken = null) {/*
	global $facebook;
	$facebook->setAccessToken($accessToken);
	$facebook->getAccessToken();*/
}

function get_signed_request() {
	//echo "isset( signed_request) ".(isset($_POST["signed_request"])?$_POST["signed_request"]:"null")."<br/>"; 
	return isset($_POST["signed_request"])? parse_signed_request($_POST["signed_request"]) : null;
}
function parse_signed_request($signed_request) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  $secret = $GLOBALS["secret"]; // Use your app secret here

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  // confirm the signature
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}
?>