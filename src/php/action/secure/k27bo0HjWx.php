<?php
	require '../../db/dbconnect.php';
	require '../../utils/SimpleImage.php';
	require '../../utils/encoding.php';
	require '../../facebook/fbconfig.php';
	
	date_default_timezone_set('UTC'); 
	
	$collab_id = -1;
	$security_test_passed = false;
	$saving_failed = false;
	$response = "failed::failed";
	$decrypt = "null";
	
	if (isset($_GET['chunks']) && isset($_GET['gid'])){
		$decrypt = decrypt($_GET['chunks'],$_GET['gid']);
		$security_test_passed = decrypt($_GET['chunks'],$_GET['gid']) == $GLOBALS["appId"];
	}
	
	if ($security_test_passed && isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
		// get bytearray
	
		$imageBytes = $GLOBALS["HTTP_RAW_POST_DATA"]; 
	
		$filename = 'public/files/gallery/'.(isset($_GET["filename"])? $_GET["filename"] : "graffiti_swat_".$_GET['surface_key'].'_'.time());
		$filename_o = $filename.'_o.jpg';
		$filename_n = $filename.'_n.jpg';
		$filename_i = $filename.'_i.jpg';
		$filename_png = isset($_GET["pngSrc"])?$_GET["pngSrc"] : '';
		$path = '../../../';
		
		if (empty($_GET['collab_id'])) $_GET['collab_id'] = '';
		if (empty($_GET['collaborators'])) $_GET['collaborators'] = '';
		
		$response = saveGalleryEntry($_GET['fbid'],$_GET['uid'],$_GET['surface_key'],$_GET['caption'],$_GET['collab_id'],$_GET['collaborators'],$filename_o,$filename_n,$filename_i,'NOW()',$filename_png);
		if (querySuccess($response)){
				
			$responseData = queryResponseData($response);
			$entry_id = isset($responseData[1]) ? $responseData[1] : 0;
			
			saveOriginal($imageBytes, $entry_id, $path, $filename_o);
			
			if (!$saving_failed) saveCopy($entry_id, $path, $filename_o, $filename_n, 720, 0, 'path_720');
			if (!$saving_failed) saveCopy($entry_id, $path, $filename_o, $filename_i, 180, 130, 'path_icon');
			
		}
		
	    echo $response;
	} else {
		$response = 'failed::'.(!isset($GLOBALS["HTTP_RAW_POST_DATA"])?'image data missing':'security test failed!! user:'.$_GET['uid'].' chunks '.$_GET['chunks'].' gid '.$_GET['gid'].' appId '.$GLOBALS["appId"].' decrypted '.$decrypt);
		logToFile($_GET['uid'], 'saveGalleryEntry::'.$response);
		echo $response;
	}
	
	function saveOriginal($imageBytes, $entry_id, $path, $filename){
			$success;
			$image = imagecreatefromstring($imageBytes);
			if ($image != false) {
				$success = imagejpeg($image,$path.$filename,100);
			}
			
			if (!$success){
				onFail($entry_id);
			}
	}
	
	function saveCopy($entry_id, $path, $original, $filename, $targetW, $targetH, $purpose){
			
			$simpleImage = new SimpleImage;
			$load_successfull = $simpleImage->loadWithFileCheck($path.$original);
			
			$image_width = $simpleImage->getWidth();
			$image_height = $simpleImage->getHeight();
			
			//echo '::$image_width-'.$image_width.'-$image_height-'.$image_height.'-$targetW-'.$targetW;
			if (!$load_successfull){
				onFail($entry_id);
			}
			else if ($image_width > $targetW || ($targetH > 0 && $image_height > $targetH )){
				$w_offset = $image_width - $targetW;
				$h_offset = $image_height - $targetH;
				
				if($targetH > 0 && $h_offset > $w_offset){
					$simpleImage->resizeToHeight($targetH);
				} else {
					$simpleImage->resizeToWidth($targetW);
				}
				$success = $simpleImage->save($path.$filename);
				
				if (!$success){
					onFail($entry_id);
				}
			} else {
				//echo '::removeGalleryEntryPath('.$filename.', '.$purpose.')';
				removeGalleryEntryPath($filename, $purpose);
			}
	}
	
	function onFail($entry_id){
		$GLOBALS['response'] = 'failed::saving images for entry '.$entry_id.' failed!! user:'.$_GET['uid'];
		removeGalleryEntry($entry_id);
		logToFile($_GET['uid'], 'saveGalleryEntry::image data incomplete for imageid '.$_GET['uid']);
		$GLOBALS['saving_failed'] = true;
	}
	?>