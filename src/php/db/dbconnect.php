<?php
	date_default_timezone_set('Europe/Berlin');

	require_once dirname(__FILE__).'/../../config/Configuration.php';
	require_once dirname(__FILE__).'/dbgallery.php';
	require_once dirname(__FILE__).'/dbcollabs.php';
	require_once dirname(__FILE__).'/dbsurfaces.php';
	require_once dirname(__FILE__).'/dbtools.php';
	require_once dirname(__FILE__).'/dbshop.php';
	if (!isset($using_error_handler)) {
		require_once dirname(__FILE__).'/../includes/errorhandler.inc';
	}

	$config = new Config;
   // database settings
   
	$connection=mysql_connect($config->DB_HOST, $config->DB_USER, $config->DB_PASS) or die ("Database Connection Failed");
	mysql_select_db($config->DB_NAME, $connection) or die("Database Selection Failed.");
	
	
  	function swat_login($fbid, $name, $updateNameOnly, $lang){
		$user_exists = false;
		
		if ($fbid){
		
			$getUser = "SELECT * FROM `user` WHERE `fbid` = '".$fbid."'";
		
			$getUserQuery = mysql_query($getUser) or die("failed::Could not process this query ".$getUser);
		
			$num_rows = mysql_num_rows($getUserQuery);
		
			$update_user;
			
			$now = date( 'Y-m-d H:i:s', time() );
			if ($num_rows > 0){
				$firstLoginSet = true;
				while ($item = mysql_fetch_array($getUserQuery)){
					$firstLoginSet = $firstLoginSet && $item['first_login'] > 0;
				}
				$update_user = "UPDATE `user` SET ".($firstLoginSet?"":"`first_login`='".$now."', ").($updateNameOnly?"":"`last_login`='".$now."', `lang`='".$lang."', ")."`name`='".addslashes($name)."' WHERE `fbid`='".$fbid."';";
			} else {
				$update_user = "INSERT INTO `user` (`fbid`, `name`".($updateNameOnly?"":", `lang`, `first_login`, `last_login`").") VALUES ('".$fbid."','".addslashes($name)."'".($updateNameOnly?"":",'".$lang."', '".$now."', '".$now."'").");";
			}
						
			$updateUserQuery = mysql_query($update_user) or die("failed::Could not process this query ".$update_user);
		}
		return "success";
  	}
  	
  	function getRecentUsers($now,$user){
  		$getUsers = "SELECT * FROM `user` WHERE `last_login` > '".$now."' - INTERVAL 28 DAY AND `notified`=0 ".($user?"AND `fbid`='".$user."' ":"")."LIMIT 0,1000";
  		return resultsToArray($getUsers);
  	}
  	
  	function setUserNotified($fbid){
  		$update_user = "UPDATE `user` SET `notified`=1 WHERE `fbid`='".$fbid."'";
  		$updateUserQuery = mysql_query($update_user) or die("failed::Could not process this query ".$update_user);
  	}
  	
  	function removeNotificationTag(){
  		$update_user = "UPDATE `user` SET `notified`=0";
  		$updateUserQuery = mysql_query($update_user) or die("failed::Could not process this query ".$update_user);
  	}
  	
  	function getUsername($fbid){
		$username = '';
		if ($fbid){
			$getUser = "SELECT name FROM `user` WHERE `fbid` = '".$fbid."'";
			$user = singleResultAsArray($getUser);
			if (isset($user["name"])) $username = $user["name"];
		}
		return $username;
  	}
  	
  	function saveUserAlbum($fbid,$album_id,$existing){
  		$albumChange = $existing? '': ', `album_count`=`album_count`+1';
  		$update_user = "UPDATE `user` SET `album_id`=$album_id $albumChange WHERE `fbid`='".$fbid."' and `album_id`!=$album_id";
  		$updateUserQuery = mysql_query($update_user) or die("failed::Could not process this query ".$update_user);
  		return getUserAlbum($fbid);
  	}
  	
  	function getUserAlbum($fbid){
  		$getUserAlbum = "SELECT `album_id`,`album_count` FROM `user` WHERE `fbid` = '".$fbid."'";
  		return singleResultAsArray($getUserAlbum);
  	}
	
	
	function resultsToArray($query_string){
		$query = mysql_query($query_string) or die("Could not process this query ".$query_string);

		$results = array();
				
		while ($item = mysql_fetch_array($query)){
			array_push($results,$item);
		}
		
		return $results;
	}
	
	function singleResultAsArray($query_string){

		$results = resultsToArray($query_string);
		
		if (count($results) > 0){
			$results = $results[0];
		} else {
			$results = null;
		}
		
		return $results;
	}
	
	function isAdmin($fbid){
		$userIsAdmin = false;
		$getUser = "SELECT `admin` FROM `user` WHERE `fbid` = '".$fbid."'";
		$user = singleResultAsArray($getUser);
		if (isset($user['admin'])){
			$userIsAdmin = $user['admin'];
		}
		return $userIsAdmin;
	}
	 
	function deleteFile($filename){
		if ($filename!= '' && file_exists(dirname(__FILE__).'/../../'.$filename)){
			$success = unlink(dirname(__FILE__).'/../../'.$filename);
			if (!$success) {
				echo 'failed::deletion failed.';
				logToFile('unknown', 'failed::deletion failed::'.$filename);
			}
		}
	}
	
	function getShowBanner($fbid){
		$show = "false";/*
		$getBannerDate = "SELECT `bannerSeen` from user WHERE `fbid` = '".$fbid."'";
		$getBannerDateQuery = mysql_query($getBannerDate);
		if ($getBannerDateQuery === false) {
			return $show;
		}
		
		while ($item = mysql_fetch_array($getBannerDateQuery)){
				$seenDate = $item['bannerSeen'];
				if (!is_null($seenDate)){
					$show = "false";
				}
		} */
		return $show;
	}
	
	function setBannerSeen($fbid){
		$status = "fail";
		$getBannerDate = "UPDATE `user` SET `bannerSeen` = NOW() WHERE `fbid` = '".$fbid."'";
		$getBannerDateQuery = mysql_query($getBannerDate);
		if ($getBannerDateQuery === true) {
			$status = "success";#
		}
		
		return $status;
	}
	
	function querySuccess($response){
		$responseData = explode('::',$response);
		$status = $responseData[0];
		return $status  == 'success';
	}
	
	function queryResponseData($response){
		return explode('::',$response);
	}
	
	function logToFile($user, $message){
		$_POST['user'] = $user;
		$_POST['message'] = $message;
		$_POST['flash'] = 'SERVER ERROR';
		$_POST['os'] = 'SERVER ERROR';
		$_POST['browser'] = 'SERVER ERROR';
		require dirname(__FILE__).'/../action/secure/meeKbooZ0.php';
	}
?>
