<?php
require '../includes/fbheader.inc';
require '../db/dbconnect.php';
$allEntries = getGalleryEntries('fbid, id', 0, 0, 'id', 'desc', '','');

echo ' all entries '.count($allEntries);

for ($i = 0; $i < count($allEntries); $i++){
	$fbid = $allEntries[$i]['fbid'];
	$entry_id = $allEntries[$i]['id'];
	
	
	//$like_request = $facebook->api($fbid.'/likes?limit=1200','GET');
	$request = new FacebookRequest($session, 'GET', $fbid.'/likes?limit=1200');
	$like_request = $request->execute()->getGraphObject()->asArray();
	if (array_key_exists("data",$like_request)){
		$likes = $like_request["data"];
		if ($likes > 25) echo '<br/> likes for '.$fbid.' = '.count($likes).' likes';
		for ($j = 0; $j < count($likes); $j++){
			$like = $likes[$j];	
			//echo '<br /> new like '.$like["id"].' likes '.$entry_id;
			$response = saveLike($entry_id,$like["id"]);
			if (!querySuccess($response)){
				echo '<br />saveLike:'.$response;
			}
		}
	} else {
		echo '<br/> cant find "data" '.$like_request;
	}
}
?>