<?php
	require '../../utils/encoding.php';
	require '../../db/dbconnect.php';	
	saveAlbumId(decrypt($_POST['chunks'],$_POST['gid']),$_POST['albumId']);
?>