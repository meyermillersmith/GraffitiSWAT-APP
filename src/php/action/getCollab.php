<?php
	require_once '../db/dbconnect.php';
	$response = getCollab($_POST['fbid'],$_POST['collab_id']);
	echo 'status='.$response['status'].'&graffiti='.$response['graffiti'].'&collaborators='.$response['collaborators'];