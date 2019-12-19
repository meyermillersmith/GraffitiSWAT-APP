<?php
	date_default_timezone_set('Europe/Berlin');
 
	function saveGalleryEntry($fbid,$uid,$surface_key,$caption,$collab_id,$collaborators,$path,$path_720,$path_180,$created_time = 'NOW()',$filename_png = ''){
		$returnValue = 'failed';
		
		$existingEntry = $fbid == ''? false : galleryEntryExits($fbid, 'fbid');
		
		//die('saveGalleryEntry '.$fbid.' exisits? '.($existingEntry !== true).' result '.$existingEntry);
		if ($existingEntry == false){
			if ($created_time != 'NOW()'){
				$created_time = "'".$created_time."'";
			}
			$caption = addslashes($caption);
			
			if(isset($collab_id) && isset($collaborators)){
				$insertGalleryEntry = "INSERT INTO `gallery_entries` (`fbid`, `user`, `surface`, `caption`, `collab_id`, `collaborators`, `path`, `path_720`, `path_icon`, `path_png`, `created`) VALUES ('".$fbid."', '".$uid."', '".$surface_key."', '".$caption."', '".$collab_id."', '".$collaborators."', '".$path."', '".$path_720."', '".$path_180."', '".$filename_png."', ".$created_time.");";
			} else {
				$insertGalleryEntry = "INSERT INTO `gallery_entries` (`fbid`, `user`, `surface`, `caption`, `path`, `path_720`, `path_icon`, `path_png`, `created`) VALUES ('".$fbid."', '".$uid."', '".$surface_key."', '".$caption."', '".$path."', '".$path_720."', '".$path_180."', '".$filename_png."', ".$created_time.");";
			}
			$insertGalleryEntryQuery= mysql_query($insertGalleryEntry) or die("Could not process this query ".$insertGalleryEntry);
	
			if ($insertGalleryEntryQuery === false) {
				$returnValue = 'failed';
			} else {
				$returnValue = 'success::'.mysql_insert_id().'::'.$path_180;
			}
		} else {
			while ($entry = mysql_fetch_array($existingEntry)){
				$returnValue = 'success::'.$entry['id'].'::'.$entry['path'];
				break;
			}
		}
		
		return $returnValue;
	}
	
	function galleryEntryExits($id, $idtype){
		$galleryEntry = getGalleryEntryById($id, $idtype);
		return isset($galleryEntry);
	}
	
	function getGalleryEntryById($id, $idtype){
		$getGalleryEntryById = "SELECT * FROM gallery_entries WHERE `".$idtype."` = '".$id."'";
		return singleResultAsArray($getGalleryEntryById);
	}
	
	function getOldUnpopularGalleryEntries($interval_months, $limit){
		$getGalleryEntryById = "SELECT * FROM `gallery_entries` where !`image_deleted` AND `likes`=0 AND `created` < DATE_SUB(now(), INTERVAL ".$interval_months." MONTH) ORDER BY `created` ASC LIMIT 0,".$limit;
		echo $getGalleryEntryById.'<br>';
		return resultsToArray($getGalleryEntryById);
	}
	
	function countOldUnpopularGalleryEntries($interval_months){
		$getGalleryEntryById = "SELECT COUNT(*) AS num FROM `gallery_entries` where !`image_deleted` AND `likes`=0 AND `created` < DATE_SUB(now(), INTERVAL ".$interval_months." MONTH)";
		return singleResultAsArray($getGalleryEntryById);
	}
	
	function removeGalleryEntryPath($path, $purpose){
		$updateGalleryEntry = "UPDATE `gallery_entries` set `".$purpose."` = `path` WHERE `".$purpose."` = '".$path."';";
		$updateGalleryEntryQuery= mysql_query($updateGalleryEntry) or die("Could not process this query ".$updateGalleryEntry);

	}
	
	function adminRemoveGalleryEntry($entry_id,$fbid){
		$status = 'failed::'.$entry_id; 
		if(isAdmin($fbid)){
			$status =  removeGalleryEntry($entry_id);
		}
		return $status;
	}
	
	function removeGalleryEntry($entry_id){
		$returnValue = 'success::'.$entry_id;
		$galleryEntry = getGalleryEntryById($entry_id,'id');
		if (isset($galleryEntry)){
			deleteFile($galleryEntry['path']);
			deleteFile($galleryEntry['path_720']);
			deleteFile($galleryEntry['path_icon']);
			deleteFile($galleryEntry['path_png']);
			
			removeAllLikes($entry_id);
			
			$deleteGalleryEntry = "DELETE FROM `gallery_entries` WHERE `id` = '".$entry_id."';";
					
			$deleteGalleryEntryQuery= mysql_query($deleteGalleryEntry) or die('failed::'.$entry_id.'::'.$deleteGalleryEntry);
		}
		return $returnValue;
	}
	
	function removeGalleryEntryImageById($entry_id){
		$galleryEntry = getGalleryEntryById($entry_id,'id');
		return removeGalleryEntryImage(galleryEntry);
	}
	
	function removeGalleryEntryImage($galleryEntry){
		$entry_id = $galleryEntry['id'];
		$returnValue = 'success::'.$entry_id;
		if (isset($galleryEntry)){
			deleteFile($galleryEntry['path']);
			deleteFile($galleryEntry['path_720']);
			deleteFile($galleryEntry['path_icon']);
			deleteFile($galleryEntry['path_png']);
			
			$updateGalleryEntry = "UPDATE `gallery_entries` set `image_deleted` = TRUE WHERE `id` = '".$entry_id."';";
			$deleteGalleryEntryQuery= mysql_query($updateGalleryEntry) or die('failed::'.$entry_id.'::'.$updateGalleryEntry);
		}
		return $returnValue;
	}
	
	function getGalleryEntriesForList($startindex, $maximum_items, $sortby, $order, $user = '', $surface = '', $level = ''){
		$params = "gallery_entries.id, created, path, path_icon, likes, user, user.name as username, gallery_picks.level as level";
		return getGalleryEntries($params, $startindex, $maximum_items, $sortby, $order, $user, $surface, $level);
	}
	
	function getGalleryEntriesForSlides($startindex, $maximum_items, $sortby, $order, $user = '', $surface = '', $level = '', $image_id = ''){
		$params = "gallery_entries.id, created, path, path_icon, path_720, path_png, user, user.name as username, caption, likes, gallery_picks.level as level";
		return getGalleryEntries($params, $startindex, $maximum_items, $sortby, $order, $user, $surface, $level, $image_id);
	}
	
	function getGalleryEntryForSlidesById($image_id){
		return getGalleryEntriesForSlides(0, 1, 'created', 'asc', '', '', '', $image_id);
	}
	
	function getGalleryEntries($params, $startindex, $maximum_items, $sortby, $order, $user = '', $surface = '', $level = '', $image_id = ''){
		$joinUser = strrpos($params, 'user.') !== false;
		$joinLevel = strrpos($params, 'gallery_picks.') !== false ? true : $level != "";
		
		$wheres = array();
 		if ($user != "") array_push( $wheres, "gallery_entries.user = '".$user."' " );
 		if ($surface != "") array_push( $wheres, "gallery_entries.surface = '".$surface."' " );
 		if ($level != ""){
 			$levelKey=getPickKeyByNum($level);
 			if ($levelKey != ""){
 				array_push( $wheres, "gallery_picks.".$levelKey." IS NOT NULL " );
 			} else {
 				array_push( $wheres, "gallery_picks.level = '".$level."' " );
 			}
 		}
 		if ($image_id != "") array_push( $wheres, "gallery_entries.id = '".$image_id."' " );
 		
 		$whereStatement = "";
 		if (count($wheres) > 0){
 			$whereStatement = "WHERE !`image_deleted` AND ".join($wheres, ' AND ');
 		}
 		
		$selectEntries = "SELECT ".$params
			." FROM gallery_entries "
 			.($joinUser? "LEFT JOIN user ON ( user.fbid = gallery_entries.user ) " :"")
 			.($joinLevel? "LEFT JOIN gallery_picks ON ( gallery_picks.entry_id = gallery_entries.id ) " :"")
 			.$whereStatement
 			."ORDER BY ".$sortby." ".$order
 			.($sortby != "created"? ", created " :" ")
 			.($maximum_items > 0 ? "LIMIT ".$startindex." , ".$maximum_items :"");
		
		return resultsToArray($selectEntries);
	}
	
	function getGalleryPositionByID($image_id, $sortby, $order, $user = '', $surface = '', $level = ''){
		$joinLevel = strrpos($params, 'gallery_picks.') !== false ? true : $level != "";
		
		$wheres = array();
 		if ($user != "") array_push( $wheres, "gallery_entries.user = '".$user."' " );
 		if ($surface != "") array_push( $wheres, "gallery_entries.surface = '".$surface."' " );
 		if ($level != ""){
 			$levelKey=getPickKeyByNum($level);
 			if ($levelKey != ""){
 				array_push( $wheres, "gallery_picks.".$levelKey." IS NOT NULL " );
 			} else {
 				array_push( $wheres, "gallery_picks.level = '".$level."' " );
 			}
 		}
 		
 		$whereStatement = "";
 		if (count($wheres) > 0){
 			$whereStatement = "WHERE !`image_deleted` AND ".join($wheres, ' AND ');
 		}
 		
 		$findEntry;
 		
 		if ($joinLevel){
 		
 		$findEntry = "SELECT id, rank FROM 
			(
				SELECT @rownum := @rownum + 1 AS rank, T1.* FROM
    			(		
    				SELECT gallery_entries.id
    				FROM gallery_entries 
    				LEFT JOIN gallery_picks ON ( gallery_picks.entry_id = gallery_entries.id )"
 					.$whereStatement."
    		 		ORDER BY ".$sortby." ".$order."
    			)AS T1, (SELECT @rownum := 0) AS r
    		) `selection` WHERE id = '".$image_id."' ";
 		
 		} else {
 		
 		$findEntry = " SELECT id, rank FROM 
    	(	SELECT gallery_entries.id,
      		@rownum := @rownum + 1 AS rank
    		FROM gallery_entries 
    		".($joinLevel? "LEFT JOIN gallery_picks ON ( gallery_picks.entry_id = gallery_entries.id ) " :"")
    		.", (SELECT @rownum := 0) r "
 			.$whereStatement."
    		 ORDER BY ".$sortby." ".$order."
    		) `selection` WHERE id = '".$image_id."' ";
    		
 		}
		return singleResultAsArray($findEntry);
	}
	
	function getGalleryEntryLikes($index){
		$selectEntries = "SELECT 
			GROUP_CONCAT(gallery_likes.user) AS likes_list,
			COUNT(gallery_likes.id) as likes
			FROM gallery_entries 
			LEFT JOIN gallery_likes ON ( gallery_likes.entry_id = gallery_entries.id )
 			WHERE gallery_entries.id = ".$index;
		
		return singleResultAsArray($selectEntries);
	}
	function countGalleryEntries($user = '', $surface = '', $level = ''){
		$wheres = array();
 		array_push( $wheres, "!gallery_entries.image_deleted " );
 		if ($user != "") array_push( $wheres, "gallery_entries.user = '".$user."' " );
 		if ($surface != "") array_push( $wheres, "gallery_entries.surface = '".$surface."' " );
 		if ($level != ""){
 			$levelKey=getPickKeyByNum($level);
 			if ($levelKey != ""){
 				array_push( $wheres, "gallery_picks.".$levelKey." IS NOT NULL " );
 			} else {
 				array_push( $wheres, "gallery_picks.level = '".$level."' " );
 			}
 		}
 		$whereStatement = "";
 		if (count($wheres) > 0){
 			$whereStatement = "WHERE !`image_deleted` AND ".join($wheres, ' AND ');
 		}
 		
		$countEntries = "SELECT COUNT(*) AS num FROM gallery_entries "
 			.($level != ""? "LEFT JOIN gallery_picks ON ( gallery_picks.entry_id = gallery_entries.id ) " :"")
 			.$whereStatement;
		$countEntriesQuery = mysql_query($countEntries);// or die("Could not process this query ".$countEntries);
		$row = mysql_fetch_array($countEntriesQuery);
		$num_entries = $row['num'];
		return $num_entries;
	}
	
	function removeAllLikes($entry_id){
		$removeLikes = "DELETE FROM `gallery_likes` where `entry_id`=".$entry_id;
		$removeLikesQuery = mysql_query($removeLikes) or die('failed::'.$entry_id.'::'.$removeLikes);
		return !($removeLikesQuery === false);
	}
	
	function saveLike($entry_id,$user,$undo = false){
		$returnValue = 'failed';
		
		
		$existingEntry = galleryEntryExits($entry_id, 'id');
		
		if ($existingEntry != false){
		
			$likeExtists = "SELECT * FROM gallery_likes WHERE entry_id = ".$entry_id." AND user = ".$user;
			$likeExtistsQuery = mysql_query($likeExtists) or die('failed::'.$entry_id.'::'.$likeExtists);
			$numLikeExtists = mysql_num_rows($likeExtistsQuery);
			if ($numLikeExtists == 0){
				$returnValue = $undo? 'success::'.$entry_id : insertLike($entry_id,$user,$undo);
			} else {
				$returnValue = $undo? insertLike($entry_id,$user,$undo) :'success::'.$entry_id;
			}
		} else {
			$returnValue = 'failed::'.$entry_id.'::entry does not exist.';
		}
		
		return $returnValue;
	}	
	
	function saveLikeFromFB($fbid,$user){
		$returnValue = 'failed';
		
		
		$existingEntry = galleryEntryExits($fbid, 'fbid');
		
		if ($existingEntry != false){
			while ($entry = mysql_fetch_array($existingEntry)){
				$returnValue =  saveLike($entry['id'],$user);
				break;
			}
		} else {
			$returnValue = 'failed::fbid '.$fbid.'::entry does not exist.';
		}
		
		return $returnValue;
	}	
		
	function insertLike($entry_id,$user,$undo = false){
		$insert = insertLikeIntoGalleryLikes($entry_id,$user,$undo);
		
		if (!$insert) {
			$returnValue = 'failed::'.$entry_id.'::'.$insertLike;
		} else {
			$returnValue = updateGalleryEntryLikes($entry_id);
			if ($returnValue != 'success::'.$entry_id){
				insertLikeIntoGalleryLikes($entry_id,$user,!$undo);
			}
		}
		
		return $returnValue;
	}
	
	function insertLikeIntoGalleryLikes($entry_id,$user,$undo){
		$insertLike = "INSERT INTO `gallery_likes` (`entry_id`, `user`) VALUES (".$entry_id.", '".$user."');";
		if ($undo){
			$insertLike = "DELETE FROM `gallery_likes` where `entry_id`=".$entry_id." AND `user`='".$user."'";
		}
					
		$insertLikeQuery = mysql_query($insertLike) or die('failed::'.$entry_id.'::'.$insertLike);
		return !($insertLikeQuery === false);
	}
	
	function updateGalleryEntryLikes($entry_id){
		$returnValue = 'failed';
		
		$getLikes = "SELECT * FROM `gallery_likes` WHERE `entry_id` = ".$entry_id;
		$getLikesQuery = mysql_query($getLikes);
		$likes = mysql_num_rows($getLikesQuery);
		
		if ($getLikesQuery === false) {
			return "failed::".$entry_id."::".$getLikes;
		}
		
		$insertLike = "UPDATE `gallery_entries` SET `likes` = ".$likes." WHERE id =".$entry_id;
		$insertLikeQuery = mysql_query($insertLike);
		
		if ($getLikesQuery === false) {
			return "failed::".$entry_id."::".$insertLike;
		}
		
		$returnValue = "success::".$entry_id;
		return $returnValue;
	
	}
	
	function updateGalleryAllEntriesLikes(){
		$returnValue = 'failed';
		$getEntries = "SELECT * FROM gallery_entries WHERE  !`image_deleted`";
		$getEntriesQuery = mysql_query($getEntries) or die("Could not process this query ".$getEntries);
		
		while ($entry = mysql_fetch_array($getEntriesQuery)){
			$returnValue .= updateGalleryEntryLikes($entry['id']);
		}
		
		return $returnValue;
	}
	
	function getMainGalleryPick($sortyby, $user=''){
		$mainGalleryPick = getGalleryEntries("path_720", 0, 1, $sortyby, "desc", $user);
		if (count($mainGalleryPick) > 0){
			$mainGalleryPick[0]['numItems'] = countGalleryEntries($user);
		}
		return $mainGalleryPick;
	}
	
	function becomesPick($entry_id){
		$level = getPickLevel($timespan);
		$key = getPickKey($timespan);
		
		$params = "gallery_entries.id, path_icon, user, user.name as username, likes";
		$selectEntries = "SELECT ".$params
			." FROM gallery_entries "
 			."LEFT JOIN user ON ( user.fbid = gallery_entries.user ) "
 			."LEFT JOIN gallery_picks ON ( gallery_picks.entry_id = gallery_entries.id ) "
 			."WHERE !`image_deleted` AND created >= NOW() - INTERVAL 1 ".$timespan." "
 			."AND likes > 0 "
 			."ORDER BY likes DESC, gallery_picks.".$key." DESC "
 			."LIMIT 0 , 1";
		
		$pickArray = resultsToArray($selectEntries);
		
		if(count($pickArray) > 0){
			$insertPick;
			
			if($level > 0 && $key != ""){
				$pick = $pickArray[0];
				$pickArray[0]['level'] = $level;
				$entry_id = $pick['id'];
				$get_pick_entry = "SELECT `level` from `gallery_picks` where `entry_id`='".$entry_id."'";
				
				$pick_entry = singleResultAsArray($get_pick_entry);
				$entry_exists = isset($pick_entry['level']);
				if ($entry_exists){
					if ($level > $pick_entry['level']){
						$insertPick = "UPDATE `gallery_picks` SET `level` = ".$level.", `".$key."` = NOW()  WHERE `entry_id` =".$entry_id;
					}
				} else {
						$insertPick = "INSERT INTO `gallery_picks` (`entry_id`, `level`, `".$key."`) VALUES (".$entry_id.", ".$level.", NOW());";
				}
				
				if(isset($insertPick)){
					$insertPickQuery = mysql_query($insertPick) or die("Could not process this query ".$insertPick);
				}
			}
		}
		return $pickArray;
	}
	
	function getPickOfThe($timespan){
		$level = getPickLevel($timespan);
		$key = getPickKey($timespan);
		
		$params = "gallery_entries.id, path_icon, user, user.name as username, likes";
		$selectEntries = "SELECT ".$params
			." FROM gallery_entries "
 			."LEFT JOIN user ON ( user.fbid = gallery_entries.user ) "
 			."LEFT JOIN gallery_picks ON ( gallery_picks.entry_id = gallery_entries.id ) "
 			."WHERE !`image_deleted` AND created >= NOW() - INTERVAL 1 ".$timespan." "
 			."AND likes > 0 "
 			."ORDER BY likes DESC, gallery_picks.".$key." DESC "
 			."LIMIT 0 , 1";
		
		$pickArray = resultsToArray($selectEntries);
		
		if(count($pickArray) > 0){
			$insertPick;
			
			if($level > 0 && $key != ""){
				$pick = $pickArray[0];
				$pickArray[0]['level'] = $level;
				$entry_id = $pick['id'];
				$get_pick_entry = "SELECT `level` from `gallery_picks` where `entry_id`='".$entry_id."'";
				
				$pick_entry = singleResultAsArray($get_pick_entry);
				$entry_exists = isset($pick_entry['level']);
				if ($entry_exists){
					if ($level > $pick_entry['level']){
						$insertPick = "UPDATE `gallery_picks` SET `level` = ".$level.", `".$key."` = NOW()  WHERE `entry_id` =".$entry_id;
					}
				} else {
						$insertPick = "INSERT INTO `gallery_picks` (`entry_id`, `level`, `".$key."`) VALUES (".$entry_id.", ".$level.", NOW());";
				}
				
				if(isset($insertPick)){
					$insertPickQuery = mysql_query($insertPick) or die("Could not process this query ".$insertPick);
				}
			}
		}
		return $pickArray;
	}
	
	
	function getPickLevel($timespan){
		$level = 0;
		switch ($timespan) {
			case "DAY":
				$level = 1;
				break;
			case "WEEK":
				$level = 2;
				break;
			case "MONTH":
				$level = 3;
				break;
			case "YEAR":
				$level = 4;
				break;
		}
		return $level;
	}
	
	function getPickKey($timespan){
		$key = "";
		switch ($timespan) {
			case "DAY":
				$key = "pod";
				break;
			case "WEEK":
				$key = "pow";
				break;
			case "MONTH":
				$key = "pom";
				break;
			case "YEAR":
				$key = "poy";
				break;
		}
		return $key;
	}
	
	function getPickKeyByNum($timespan){
		$key = "";
		switch ($timespan) {
			case 1:
				$key = "pod";
				break;
			case 2:
				$key = "pow";
				break;
			case 3:
				$key = "pom";
				break;
			case 4:
				$key = "poy";
				break;
		}
		return $key;
	}
	
	function getAllGalleries(){
		$allGalleries = "SELECT `gallery_entries`.`surface`, COALESCE(`galleries`.`name`, `surface`.`name`) as name
		 	FROM `gallery_entries` 
			LEFT JOIN `surface` ON ( `surface`.`key` = `gallery_entries`.`surface` )
			LEFT JOIN `galleries` ON ( `galleries`.`key` = `gallery_entries`.`surface` )
			WHERE !`gallery_entries`.`image_deleted`
			GROUP BY surface";
		return resultsToArray($allGalleries);
	}

	function getRandomGalleries($count){
		$galleries = array();
		$surfaces = getAllGalleries();
		
		for ($i = 0; $i < $count; $i++){
			$remaining = count($surfaces);
			if ($remaining == 0){
				break;
			}
			$randIndex = rand(0,$remaining-1);
			$surfaceKey = $surfaces[$randIndex]["surface"];
			$surfaceTitle = $surfaces[$randIndex]["name"];
			unset($surfaces[$randIndex]);
			$surfaces = array_values($surfaces);
			$image = getGalleryEntries("path_icon", 0, 1, "created", "desc","", $surfaceKey);
			if (count($image) > 0){
				$image[0]['galleryKey'] = $surfaceKey;
				$image[0]['galleryTitle'] = $surfaceTitle;
				$image[0]['numItems'] = countGalleryEntries('',$surfaceKey);
				array_push($galleries,$image);
			} else {
				$i--;
			}
		}
		
		return $galleries;
	}
?>