<?php
	require '../../utils/encoding.php';
	require '../../db/dbconnect.php';
	$user_id = decrypt($_POST['chunks'],$_POST['gid']);
 	$result = saveUserAlbum($user_id,$_POST['album_id'],$_POST['existing'] == 'true');
	echo json_encode($result);
?>