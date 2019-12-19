<?php

	$GLOBALS['unlock_timespan'] = 'INTERVAL 1 MONTH';
	
	function getAllTools($forFlash = false){
		return getTools(' ', $forFlash , true);
	}
	
	function getTools($fbid, $forFlash = false, $all = false, $toolType = "swat"){
	
		$selectAllTools = "SELECT tool.*, user_tool.acquisition as acquisition, user_tool.id IS NOT NULL as bought from tool LEFT JOIN user_tool ON ( user_tool.sid = tool.id  AND user_tool.fbid = '".$fbid."' AND (`acquisition`!='brandConnect' OR `timestamp` > NOW() - ".$GLOBALS['unlock_timespan'].")) ORDER BY `position` DESC ,tool.`id` ASC";
		$AllToolsQuery = mysql_query($selectAllTools) or die("Could not process this query ".$selectAllTools);
		$allTools = array();
		
		while ($ftools = mysql_fetch_array($AllToolsQuery)){
			$ishirt = $ftools['key'] == 'dudes';
			$isShirtUser = false;//$fbid == '100002572844577';
			if (!$ishirt || $isShirtUser){
				if ($forFlash) {
					$allTools[$ftools['id']] = array('key' => $ftools['key'], 'price' => $ftools['price'], 'bought' => $ftools['bought']? 'true' : 'false', 'acquisition' => $ftools['acquisition'], 'children' => $ftools['children'], 'position' => $ftools['position']);
				} else {
					$newItem = array('id' => $ftools['key'], 'price' => $ftools['price'], 'bought' => $ftools['bought']? 'true' : 'false', 'temp_free' => $ftools['temp_free']? 'true' : 'false');
					array_push($allTools,$newItem);
				}
			}
		}
		return $forFlash? json_encode($allTools) : $allTools;
	}
	
	function checkGotTool($fbid,$tool){
		$gotTool = false;
		$toolid = 0;
		$checkFreeTools = "SELECT * from tool WHERE `key` ='".$tool."'";
	
		$freeToolsQuery = mysql_query($checkFreeTools) or die("Could not process this query ".$checkFreeTools);
		
		$gotTool = mysql_num_rows($freeToolsQuery) > 0;
		if ($gotTool){
			while ($item = mysql_fetch_array($freeToolsQuery)){
				$gotTool = $item['price'] == 0 || $item['temp_free'];
				$toolid = $item['id'];
				if ($gotTool){
					if ($item['temp_free']){
						buyTemporaryTool($fbid,$tool);
					}
					break;
				}
			}
			if (!$gotTool){
				$checkBoughtTools = "SELECT * from user_tool WHERE `fbid` ='".$fbid."' AND `sid` ='".$toolid."' AND (`acquisition`!='brandConnect' OR `timestamp` > NOW() - ".$GLOBALS['unlock_timespan'].")";
				$boughtToolsQuery = mysql_query($checkBoughtTools) or die("Could not process this query ".$checkBoughtTools);
				$gotTool = mysql_num_rows($boughtToolsQuery) > 0;
			}
		}
		$gotTool = $gotTool? 'true':'false';
		return $gotTool;
	}
?>