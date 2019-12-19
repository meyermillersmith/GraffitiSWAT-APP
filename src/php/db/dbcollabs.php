<?php
	function saveCollab($fbid,$friend_id,$surface_key,$collab_id,$filename,$albumlink){
		$returnValue = 'failed';
		$collab_exists = false;
		$updated_collaborators = $fbid;
		$old_graffiti;
		
		if ($collab_id != ''){
		
			$getCollaborators = "SELECT `graffiti`, `collaborators`, `done` from collaboration WHERE `id` = '".$collab_id."'";
		
			$getCollaboratorsQuery = mysql_query($getCollaborators) or die("Could not process this query ".$getCollaborators);
		
			$num_rows = mysql_num_rows($getCollaboratorsQuery);
		
		
			if ($num_rows > 0){
		
				$collab_exists = true;
				while ($item = mysql_fetch_array($getCollaboratorsQuery)){
		
					$updated_collaborators = getUpdatedCollaborators($item['collaborators'],$fbid);
					$old_graffiti = $item['graffiti'];
					$collab_exists = $item['done'] != '1';
				}
			}
		}
		
		$insertCollab;
		
		if($collab_exists){
			completeCollab($fbid,$collab_id,$old_graffiti,$albumlink,$updated_collaborators);
			//$insertCollab = "UPDATE `collaboration` SET `from`='".$fbid."', `to`='".$friend_id."', `graffiti`='".$filename."', `albumlink`='".$albumlink."', `surface`='".$surface_key."', `collaborators`='".$updated_collaborators."' WHERE `id`='".$collab_id."';";
		}
		$insertCollab = "INSERT INTO `collaboration` (`from`, `to`, `albumlink`, `graffiti`, `surface`, `collaborators`) VALUES ('".$fbid."', '".$friend_id."', '".$albumlink."', '".$filename."', '".$surface_key."', '".$updated_collaborators."');";
					
		$returnValue = $collab_id;
		$insertCollabQuery = mysql_query($insertCollab) or die("Could not process this query ".$insertCollab);
		
		if ($insertCollabQuery === false) {
			$returnValue = 'failed';
		} else {
			$collab_id = mysql_insert_id();
			$returnValue = $collab_id;
		}
		
		return $returnValue;
	}
	
	
	
	function getUpdatedCollaborators($updated_collaborators,$fbid){
					
					$has_collaborated = false;
		
					$collaborators = explode(",", $updated_collaborators);
		
					for ($i = 0; $i < sizeof($collaborators); $i++) {
		
   						if ($collaborators[$i] == $fbid){
		
   							$has_collaborated = true;
		
   							break;
   						}
					}
		
					if (!$has_collaborated){
		
						$updated_collaborators .=",".$fbid;
					}
					
		return $updated_collaborators;
	}
	
	function getUserOpenCollabs($fbid,$direction = 'to'){
		$queryString = "SELECT `id`, `graffiti`, `albumlink`, `surface`, `request_id`, `collaborators`, `".($direction == 'to'? 'from' : 'to')."` as collaborator  from collaboration WHERE `".$direction."` = '".$fbid."' AND `to`!=`from` AND `done` = 0 AND `graffiti` != 'DELETED' AND `graffiti` != 'CANCELLED' ORDER BY `last_change` DESC";
		return resultsToArray($queryString);
	}
	
	function countUserOpenCollabs($fbid,$direction = 'to'){
		$queryString = "SELECT COUNT(*) as num  from collaboration WHERE `".$direction."` = '".$fbid."' AND `to`!=`from` AND `done` = 0 AND `graffiti` != 'DELETED' AND `graffiti` != 'CANCELLED'";
		return singleResultAsArray($queryString);
	}
	
	function getCollab($fbid,$collab_id,$direction = 'to'){
		$queryString = "SELECT `id`, `graffiti`, `surface`, `collaborators` from collaboration WHERE `id` = '".$collab_id."' AND `".$direction."` = '".$fbid."' AND `done` = 0 AND `graffiti` != 'DELETED' AND `graffiti` != 'CANCELLED'";
		return getCollabByQuery($queryString);
	}
	
	function getSavedGraffiti($fbid){

		$queryString = "SELECT `id`, `graffiti`, `surface`, `collaborators` from collaboration WHERE `from` = '".$fbid."' AND `to` = '".$fbid."' AND `done` = 0 AND `graffiti` != 'DELETED' AND `graffiti` != 'CANCELLED'";
		return getCollabByQuery($queryString);
	}
	
	function getCollabByQuery($queryString){
		$response =  array('status' => 'fail');
		$response = singleResultAsArray($queryString);
		if ($response){
			$response['status'] = 'success';
		} else {
			$response = array('status' => 'fail');
		}
		return $response;
	}
	
	function completeCollab($fbid,$collab_id,$filename,$albumlink,$updated_collaborators){
		$completeCollab = "UPDATE `collaboration` SET `albumlink`='".$albumlink."', `collaborators`='".$updated_collaborators."' , `done`=1 WHERE `id`='".$collab_id."' AND `to`='".$fbid."';";
		$completeCollabQuery = mysql_query($completeCollab);
		//deleteFile($filename);
	}
	
	function refuseCollab($fbid,$collab_id){
		$completeCollab = "UPDATE `collaboration` SET `albumlink`='REFUSED_BY_RECIPIENT', `graffiti`='CANCELED', `done`=1 WHERE `id`='".$collab_id."' AND `to`='".$fbid."';";
		$completeCollabQuery = mysql_query($completeCollab);
		if ($completeCollabQuery === false) {
			$returnValue = 'failed';
		} else {
			$returnValue = 'success';
		}
		return $returnValue;
		//deleteFile($filename);
	}
	
	function addRequestID($collab_id,$request_id){
		$addResquestId = "UPDATE `collaboration` SET `request_id`='".$request_id."' WHERE `id`='".$collab_id."';";
		$addResquestIdQuery = mysql_query($addResquestId);
	}
	
	function removeRequestID($collab_id,$request_id){
		$addResquestId = "UPDATE `collaboration` SET `request_id`=NULL WHERE `id`='".$collab_id."';";
		$addResquestIdQuery = mysql_query($addResquestId);
	}
	
	function completeCollabBySave($fbid,$collab_id,$albumlink){
		echo('hi nisii '.$fbid.' $collab_id '.$collab_id);
		$collab = getCollab($fbid,$collab_id);
		echo('hi status - '.$collab['status']);
		if ($collab['status'] == 'success' && $collab['graffiti'] != 'DELETED' && $collab['graffiti'] != 'CANCELED'){
			$updated_collaborators = getUpdatedCollaborators($collab['collaborators'],$fbid);
			completeCollab($fbid,$collab_id,$collab['graffiti'],$albumlink,$updated_collaborators);
			echo('success');
		} else {
			echo('fail! getCollab:'.$collab['status'].' collab was '.$collab['graffiti']);
		}
	}
	
	function cancelCollab($fbid,$collab_id,$status='CANCELLED'){
		$collab = getCollab($fbid,$collab_id,'from');
		if ($collab['status'] == 'success'){
			$completeCollab = "UPDATE `collaboration` SET `graffiti`='".$status."', `done`=1 WHERE `id`='".$collab_id."' AND `from`='".$fbid."';";
			$completeCollabQuery = mysql_query($completeCollab);
			deleteFile($collab['graffiti']);
		}
	}
	
	function removeCollab($fbid,$collab_id){
		$removeCollab = "DELETE FROM `collaboration` WHERE `to`='".$fbid."' AND `id`='".$collab_id."';";
		$removeCollabQuery = mysql_query($removeCollab);
		deleteFile($collab['graffiti']);
	}
	
	function getOldCollabs($interval_months, $limit){
		$getCollabById = "SELECT * FROM `collaboration` where done=0 AND `last_change` < DATE_SUB(now(), INTERVAL ".$interval_months." MONTH) ORDER BY `last_change` ASC LIMIT 0,".$limit;
		echo $getCollabById.'<br>';
		return resultsToArray($getCollabById);
	}
	
	function countOldCollabs($interval_months){
		$getCollabById = "SELECT COUNT(*) AS num FROM `collaboration` where done=0 AND `last_change` < DATE_SUB(now(), INTERVAL ".$interval_months." MONTH)";
		return singleResultAsArray($getCollabById);
	}
	
	function removeOldCollab($collab, $status='REMOVED_BY_SCRIPT'){
		$fbid = $collab['from'];
		$collab_id = $collab['id'];
		$completeCollab = "UPDATE `collaboration` SET `graffiti`='".$status."', `done`=1 WHERE `id`='".$collab_id."' AND `from`='".$fbid."';";
		$completeCollabQuery = mysql_query($completeCollab);
		deleteFile($collab['graffiti']);
	}
?>