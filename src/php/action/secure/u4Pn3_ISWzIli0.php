<?php
	require '../../utils/encoding.php';
	require '../../db/dbconnect.php';	
	completeCollabBySave(decrypt($_POST['chunks'],$_POST['gid']),$_POST['collab_id'],$_POST['albumlink']);
?>