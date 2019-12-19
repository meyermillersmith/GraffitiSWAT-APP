<?php
if (isset($_POST["user"]) && isset($_POST["items"] ) ) {
	require '../../../../php/db/dbconnect.php';
	$items = explode(",", $_POST["items"]);
	if (count($items) > 0){
		for($i = 0; $i < count($items); $i++) {
			echo 'result for id='.$items[$i].' --> '.saveOrder($_POST["user"],$items[$i],'surface','granted').' <br/>';
		}
	} else{
		echo 'failed: user='.$_POST["user"].' items='.print_r($items).' <br/>';
	}
} else {
	echo 'failed: user='.$_POST["user"].' item='.$_POST["item"].' <br/>';
}
?>
<a href='../../../shop/grantsurfaces.php'>BACK</a>