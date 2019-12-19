<?php
require '../../php/includes/fbheader.inc';
$status = 'failed';
$now = date( 'Y-m-d H:i:s', time() );
if ($fbuser) {
	$fbid = $user_profile['id'];
	require '../../php/db/dbconnect.php';
	require '../../php/lang/notification_lang.php';
	 if (isAdmin($fbid)){
	 	$status = 'success::access granted::'.$fbid;
		$url = 'game.php?surface=wall';
	 	$num_users = 1;
	 	$success_users = 0;
	 	$failed_users = 0;
	 	set_time_limit(300);
	 	while($num_users > 0){
		 	$users = getRecentUsers($now);
		 	$num_users = count($users);
		 	
		 	//echo '$num_users '.$num_users.'</br>';
		 	
		 	for ($i = 0; $i < $num_users; $i++) {
		 		$user_id =$users[$i]['fbid'];
		 		$user_name =$users[$i]['name'];
		 		$lang =$users[$i]['lang'];
				$language = strtoupper($lang);
		 		if (!isset($GLOBALS["NOTIFICATION_".$language])){
		 			$language = 'EN';
		 		}
		 		
		 		$text = $GLOBALS["NOTIFICATION_".$language];
		 		//echo 'user'.$user_id.'</br>';
		 	
		 		try {
			 		// Try send this user a notification
					
					$fb_response = (new FacebookRequest($session, 'POST', '/' . $user_id . '/notifications', 
						array(
										'access_token' => $_SESSION['fb_token'], // access_token is a combination of the AppID & AppSecret combined
										'href' => $url, // Link within your Facebook App to be displayed when a user click on the notification
										'template' => $text, // Message to be displayed within the notification
								)
					))->execute()->getGraphObject()->asArray();
					/*
			 		$fb_response = $facebook->api('/' . $user_id . '/notifications', 'POST',
			 				array(
			 						'access_token' => $facebook->getAppId() . '|' . $facebook->getApiSecret(), // access_token is a combination of the AppID & AppSecret combined
			 						'href' => $url, // Link within your Facebook App to be displayed when a user click on the notification
			 						'template' => $text, // Message to be displayed within the notification
			 				)
			 		); */
			 		if (!$fb_response['success']) {
			 			// Notification failed to send
			 			//logToFile($user_profile['id'],'Failed to send notification to "'.$user_name.'" ('.$user_id.')! FB ERROR:');
			 			$failed_users++;
			 		} else {
			 			// Success!
			 			//echo '<p>Your notification was sent successfully</p>'."\n";
			 			$success_users++;
			 		}
			 	} catch (FacebookSDKException $e) {
				 // Notification failed to send
			 			//logToFile($user_profile['id'],'Failed to send notification to "'.$user_name.'" ('.$user_id.')!FB EXCEPTION:');
			 			$failed_users++;
				 }
				 
				 setUserNotified($user_id);
		 	}
		 }
		 removeNotificationTag();
		 logToFile($user_profile['id'],'Failed to send notification to '.$failed_users.' users, sent to '.$success_users.' !');
	 	
	 } else {
		$status = 'failed::no access granted::'.$fbid;
	 }
} else {
	$status = 'login::'.$_POST['entry_id'];
}
echo $status;
?>