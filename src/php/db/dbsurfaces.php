<?php

	$GLOBALS['unlock_timespan'] = 'INTERVAL 1 MONTH';
	
	function getAllSurfaces($forFlash = false){
		return getSurfaces(' ', $forFlash , true);
	}
	
	function getSurfaces($fbid, $forFlash = false, $all = false, $surfaceType = "swat"){
	
		$selectAllSurfaces = "SELECT surface.*, user_surface.id IS NOT NULL as bought from surface LEFT JOIN user_surface ON ( user_surface.sid = surface.id  AND user_surface.fbid = '".$fbid."' AND (`acquisition`!='brandConnect' OR `timestamp` > NOW() - ".$GLOBALS['unlock_timespan'].")) WHERE `type` ='".$surfaceType."' ORDER BY `position` DESC ,surface.`id` ASC";
		$AllSurfacesQuery = mysql_query($selectAllSurfaces) or die("Could not process this query ".$selectAllSurfaces);
		$allSurfaces = array();
		
		while ($fsurfaces = mysql_fetch_array($AllSurfacesQuery)){
			$ishirt = $fsurfaces['key'] == 'dudes';
			$isShirtUser = false;//$fbid == '100002572844577';
			if (!$ishirt || $isShirtUser){
				if ($forFlash) {
					$allSurfaces[$fsurfaces['id']] = array('key' => $fsurfaces['key'], 'name' => $fsurfaces['name'], 'price' => $fsurfaces['price'], 'bought' => $fsurfaces['bought']? 'true' : 'false', 'temp_free' => $fsurfaces['temp_free']? 'true' : 'false');
				} else {
					$newItem = array('id' => $fsurfaces['key'], 'title' => $fsurfaces['name'], 'price' => $fsurfaces['price'], 'bought' => $fsurfaces['bought']? 'true' : 'false', 'temp_free' => $fsurfaces['temp_free']? 'true' : 'false');
					array_push($allSurfaces,$newItem);
				}
			}
		}
		return $forFlash? json_encode($allSurfaces) : $allSurfaces;
	}
	
	function getSurfacesExcludeBuyable($fbid, $forFlash = false, $all = false, $surfaceType = "swat"){
	
		$selectAllSurfaces = "SELECT surface.*, user_surface.id IS NOT NULL as bought from surface LEFT JOIN user_surface ON ( user_surface.sid = surface.id  AND user_surface.fbid = '".$fbid."' AND (`acquisition`!='brandConnect' OR `timestamp` > NOW() - ".$GLOBALS['unlock_timespan'].")) WHERE `type` ='".$surfaceType."' ORDER BY `position` DESC ,surface.`id` ASC";
		$AllSurfacesQuery = mysql_query($selectAllSurfaces) or die("Could not process this query ".$selectAllSurfaces);
		$allSurfaces = array();
		
		while ($fsurfaces = mysql_fetch_array($AllSurfacesQuery)){
			$ishirt = $fsurfaces['key'] == 'dudes';
			$isShirtUser = false;//$fbid == '100002572844577';
			if (!$ishirt || $isShirtUser){
				if ($forFlash) {
					$allSurfaces[$fsurfaces['id']] = array('key' => $fsurfaces['key'], 'name' => $fsurfaces['name'], 'price' => $fsurfaces['price'], 'bought' => $fsurfaces['bought']? 'true' : 'false', 'temp_free' => $fsurfaces['temp_free']? 'true' : 'false');
				} else {
					$newItem = array('id' => $fsurfaces['key'], 'title' => $fsurfaces['name'], 'price' => $fsurfaces['price'], 'bought' => $fsurfaces['bought']? 'true' : 'false', 'temp_free' => $fsurfaces['temp_free']? 'true' : 'false');
					if($newItem['bought']=='true' || $newItem['temp_free']=='true' || $newItem['price']=='0')
					{
						array_push($allSurfaces,$newItem);
					}
				}
			}
		}
		return $forFlash? json_encode($allSurfaces) : $allSurfaces;
	}
	
	function checkGotSurface($fbid,$surface){
		$gotSurface = false;
		$surfaceid = 0;
		$checkFreeSurfaces = "SELECT * from surface WHERE `key` ='".$surface."'";
	
		$freeSurfacesQuery = mysql_query($checkFreeSurfaces) or die("Could not process this query ".$checkFreeSurfaces);
		
		$gotSurface = mysql_num_rows($freeSurfacesQuery) > 0;
		if ($gotSurface){
			while ($item = mysql_fetch_array($freeSurfacesQuery)){
				$gotSurface = $item['price'] == 0 || $item['temp_free'];
				$surfaceid = $item['id'];
				if ($gotSurface){
					if ($item['temp_free']){
						buyTemporarySurface($fbid,$surface);
					}
					break;
				}
			}
			if (!$gotSurface){
				$checkBoughtSurfaces = "SELECT * from user_surface WHERE `fbid` ='".$fbid."' AND `sid` ='".$surfaceid."' AND (`acquisition`!='brandConnect' OR `timestamp` > NOW() - ".$GLOBALS['unlock_timespan'].")";
				$boughtSurfacesQuery = mysql_query($checkBoughtSurfaces) or die("Could not process this query ".$checkBoughtSurfaces);
				$gotSurface = mysql_num_rows($boughtSurfacesQuery) > 0;
			}
		}
		$gotSurface = $gotSurface? 'true':'false';
		return $gotSurface;
	}
	
	function buyTemporarySurface($fbid,$surface){
		$success = saveOrder($fbid,$surface,'surface','granted') == 'settled';
		if (!$success){
			logToFile($fbid, 'buyTemporarySurface:: could not automatically buy surface '.$surface);
		}
	}
?>