<?php
	$status = 'failed';
	if (isset($_POST['entry_id']) && $_POST['entry_id']!=''){
		$status = 'failed::'.$_POST['entry_id'];
		require '../../../../php/includes/fbheader.inc';
		if ($fbuser) {
			$fbid = $user_profile['id'];
			require '../../../../php/db/dbconnect.php';
			$status = adminRemoveGalleryEntry($_POST['entry_id'], $fbid);
		} else {
			$status = 'login::'.$_POST['entry_id'];
		}
	}
	echo $status.'::hallo';
?>