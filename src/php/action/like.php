<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
	$status = 'failed';
	if (isset($_POST['fbid']) && isset($_POST['chunks']) && isset($_POST['gid'])){
		$fbid = $_POST['fbid'];
		$undo = isset($_POST['undo']) && $_POST['undo']=='true';
        require_once '../utils/encoding.php';
		$entry_id = decryptNoBase($_POST['chunks'], $_POST['gid']);
        require_once '../db/dbconnect.php';
		$status = saveLike($entry_id, $fbid, $undo);
	}
	echo $status;