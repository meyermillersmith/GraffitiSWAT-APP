<?php
	require '../../../../php/db/dbconnect.php';
	require '../../../../php/utils/SimpleImage.php';
	require '../../../../php/utils/encoding.php';
	require '../../../../php/facebook/fbconfig_dev.php';
	
	date_default_timezone_set('UTC'); 
	
	$collab_id = -1;
	
	if (isset($_FILES['image']) && $_FILES["image"]["error"] < 1) {
		// get bytearray
	
		$filename = 'public/files/gallery/'.(isset($_POST["filename"])? $_POST["filename"] : "graffiti_swat_".$_POST['surface_key'].'_'.time());
		$filename_o = $filename.'_o.jpg';
		$filename_n = $filename.'_n.jpg';
		$filename_i = $filename.'_i.jpg';
		$filename_png = isset($_POST["pngSrc"])?$_POST["pngSrc"] : '';
		$path = '../../../../';
		
		$response = saveGalleryEntry('',$_POST['uid'],$_POST['surface_key'],$_POST['caption'],null,null,$filename_o,$filename_n,$filename_i,'NOW()',$filename_png);
		
		if (querySuccess($response)){
			$responseData = queryResponseData($response);
			$entry_id = isset($responseData[1]) ? $responseData[1] : 0;
			
			if(move_uploaded_file($_FILES['image']['tmp_name'], $path.$filename_o)) {
				saveCopy($entry_id, $path, $filename_o, $filename_n, 720, 0, 'path_720');
				saveCopy($entry_id, $path, $filename_o, $filename_i, 180, 130, 'path_icon');
			} else{
			    echo "There was an error uploading the file, please try again!";
				removeGalleryEntry($entry_id);
			}
			
			
			
		}
		
	    echo $response;
	} else {
		$response = 'failed::'.(!isset($_FILES['image'])?'image data missing':'security test failed!! user:'.$_POST['uid']);
		logToFile($_POST['uid'], 'saveGalleryEntry::'.$response);
		echo $response;
	}
	
	function saveCopy($entry_id, $path, $original, $filename, $targetW, $targetH, $purpose){
			
			$simpleImage = new SimpleImage;
			$load_successfull = $simpleImage->loadWithFileCheck($path.$original);
			
			$image_width = $simpleImage->getWidth();
			$image_height = $simpleImage->getHeight();
			
			//echo '::$image_width-'.$image_width.'-$image_height-'.$image_height.'-$targetW-'.$targetW;
			if (!$load_successfull){
				removeGalleryEntry($entry_id);
				logToFile($_POST['uid'], 'saveGalleryEntry::image data incomplete for imageid '.$_POST['uid']);
			}
			else if ($image_width > $targetW || ($targetH > 0 && $image_height > $targetH )){
				$w_offset = $image_width - $targetW;
				$h_offset = $image_height - $targetH;
				
				if($targetH > 0 && $h_offset > $w_offset){
					$simpleImage->resizeToHeight($targetH);
				} else {
					$simpleImage->resizeToWidth($targetW);
				}
				$simpleImage->save($path.$filename);
				
			} else {
				//echo '::removeGalleryEntryPath('.$filename.', '.$purpose.')';
				removeGalleryEntryPath($filename, $purpose);
			}
	}
	?>