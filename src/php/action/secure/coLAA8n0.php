<?php
	$status = 'failed';
	if (isset($_POST['collab_id']) && $_POST['collab_id']!=''){
		$status = 'failed::'.$_POST['collab_id'];
        require_once '../../includes/fbheader.inc';
		if ($fbuser) {
			$fbid = $user_profile['id'];
            require_once '../../db/dbconnect.php';
			$status = refuseCollab($fbid, $_POST['collab_id']);
			if (isset($_POST['collab_request_id']) && trim($_POST['collab_request_id']) !== ''){
				deleteAppRequest($_POST['collab_request_id']);
			}
		} else {
			$status = 'login::'.$_POST['collab_id'];
		}
	}
	echo $status.'::'.$_POST['collab_id'];
?>