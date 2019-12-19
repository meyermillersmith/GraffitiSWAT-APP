<?php
	require '../../utils/encoding.php';
	require '../../db/dbconnect.php';
 	cancelCollab(decrypt($_POST['chunks'],$_POST['gid']),$_POST['collab_id']);
	echo 'success';
?>