<?php
	require_once '../utils/encoding.php';
	require_once '../db/dbconnect.php';
	echo setBannerSeen(decrypt($_POST['chunks'],$_POST['gid']));