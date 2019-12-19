<?php	
	require '../../db/dbconnect.php';
	require '../../utils/SimpleImage.php';
	require '../../utils/encoding.php';
	require '../../facebook/fbconfig.php';
	
	date_default_timezone_set('UTC'); 
	
	$collab_id = -1;
	$security_test_passed = false;
	
	if (isset($_GET['chunks']) && isset($_GET['gid'])){
		$security_test_passed = decrypt($_GET['chunks'],$_GET['gid']) == $GLOBALS["appId"];
	}
	
	if ($security_test_passed && isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
		// get bytearray
		$savingAllowed = true;
		
		if ($_GET['fbid'] == $_GET['friend_id']){
			$savedGraffiti = getSavedGraffiti($_GET['fbid']);
			if ($savedGraffiti['status'] == 'success' && $_GET['overrideGraffiti'] == 'true'){
				cancelCollab($_GET['fbid'],$savedGraffiti['id'],'OVERRIDE');
			} else {
				$savingAllowed = $savedGraffiti['status'] != 'success';
			}
		}
		
		if ($savingAllowed){
			$png = $GLOBALS["HTTP_RAW_POST_DATA"]; 
		
			$filename = 'public/files/collabs/'.(isset($_GET["filename"])? $_GET["filename"] : "graffiti_swat_".time().".png");
			
			$_GET['albumlink'] = isset($_GET['albumlink'])? $_GET['albumlink'] : '';
			$collab_id = saveCollab($_GET['fbid'],$_GET['friend_id'],$_GET['surface_key'],$_GET['collab_id'],$filename,$_GET['albumlink']);
			
			
			if ($collab_id > 0){
				$handler = fopen('../../../'.$filename, 'wb');
				fwrite($handler, $png);
				fclose($handler); 
				
				
				$simpleImage = new SimpleImage;
				$load_successfull = $simpleImage->loadWithFileCheck('../../../'.$filename);
				
				if(!$load_successfull){
					removeCollab($_GET['friend_id'],$collab_id);
					logToFile($_GET['uid'], 'saveCollab::image data incomplete by '.$_GET['fbid'].' for '.$_GET['friend_id'].' original: '.$_GET['albumlink']);
					
					echo 'failed::file could not be saved.. ';
					$collab_id = -1;
				}
			}
			
		    echo $collab_id;
		} else {
			$response = 'failed::cant_save_twice::'.$_GET['fbid'].' already owns a saved graffiti';
			echo $response;
		}
	} else {
		$response = 'failed::'.(!isset($GLOBALS["HTTP_RAW_POST_DATA"])?'image data missing':'security test failed!! user:'.$_GET['fbid']);
		logToFile($_GET['fbid'], 'saveCollab::'.$response);
		echo $response;
	}
	?>