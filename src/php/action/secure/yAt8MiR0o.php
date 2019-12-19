<?php
if (isset($_POST["chunks"]) && isset($_POST["gid"]) && isset($_POST["item_type"]) && isset($_POST["item_key"]) ) {
	require '../../utils/encoding.php';
	require '../../db/dbconnect.php';	
 	echo saveOrder(decrypt($_POST['chunks'],$_POST['gid']),$_POST["item_key"],$_POST["item_type"],'tested');
} else {
	echo 'failed';
}
?>