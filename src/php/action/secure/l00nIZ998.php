<?php
	require '../../utils/encoding.php';
	require '../../db/dbconnect.php';
 	addRequestID(decrypt($_POST['chunks'],$_POST['gid']),$_POST['request_id']);
	echo 'success';
?>