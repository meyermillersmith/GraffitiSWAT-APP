<?php
	$status = 'failed';
	if (isset($_POST['entry_id']) && $_POST['entry_id']!=''){
		$status = 'failed::'.$_POST['entry_id'];
        require_once '../db/dbconnect.php';
		$like_list_results = getGalleryEntryLikes($_POST['entry_id']);
		$like_list = isset($like_list_results["likes_list"]) ? $like_list_results["likes_list"] : '';
		$status = 'success::'.$_POST['entry_id'].'::'.$like_list;
	}
	echo $status;