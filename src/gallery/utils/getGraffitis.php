<?php
	require 'fnc.php';

	if (!isset($connection)) {
		require dirname(__FILE__).'/../../php/db/dbconnect.php';
	}
	
	$startindex = isset($_GET['start'])? $_GET['start'] : 0;
	$maximum_items = isset($_GET['max'])? $_GET['max'] : 24;
	$sortby = isset($_GET['sortby'])? $_GET['sortby'] : 'created';
	$order = isset($_GET['order'])? $_GET['order'] : 'DESC';
	$user = isset($_GET['user'])? $_GET['user'] : '';
	$surface = isset($_GET['surface'])? $_GET['surface'] : '';
	$level = isset($_GET['level'])? $_GET['level'] : '';
	$forSlides = isset($_GET['slides'])? true : false;
	
	
	$photos;
	$jsonEncodedAnswer = isset($_GET['jscall']);
	
	/*if (!empty($isExternalLink)){
		$photos = getGalleryEntryForSlidesById($currentImageId);
	} else */
	
	if ($forSlides){
		if (!empty($currentImageId)){
			$maximum_items = 1;
			$currentImageIndexResult = getGalleryPositionByID($currentImageId, $sortby, $order, $user, $surface, $level);
			$currentImageIndex = empty($currentImageIndexResult)? 0 : $currentImageIndexResult['rank'] - 1; 
		}
		$photos = getGalleryEntriesForSlides($startindex, $maximum_items, $sortby, $order, $user, $surface,$level,$currentImageId);
	} else {
		$jsonEncodedAnswer = true;
		$photos = getGalleryEntriesForList($startindex, $maximum_items, $sortby, $order, $user, $surface,$level);
	}
	
	$displayed_amount = count($photos);
	
	if ($jsonEncodedAnswer){
		$photos = json_encode($photos);
	}
	
	if(isset($_GET['jscall'])){
		echo $photos;
	}
	
?>