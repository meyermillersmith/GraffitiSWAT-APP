<?php

	function getGalleryParams(){
		global $sortby, $order, $list_index;
		return 'referral=internal&sortby='.$sortby.'&order='.$order.'&list_index='.$list_index.getGalleryOptionalParams();
	}
	
	function getGalleryOptionalParams(){
		return getGalleryParamUser().getGalleryParamSurface().getGalleryParamLevel();
	}
	
	function getGalleryParamUser(){
		global $user;
		return ($user!=''? '&user='.$user : '');
	}
	
	function getGalleryParamSurface(){
		global $surface;
		return ($surface!=''? '&surface='.$surface : '');
	}
	
	function getGalleryParamLevel(){
		global $level;
		return ($level!=''? '&level='.$level : '');
	}
	
	function getPreviousUrl(){
		global $startindex, $displayed_amount, $sortby, $order, $user, $total_photos, $maximum_items;
		if ($total_photos > $displayed_amount){
			$previous = $startindex - $maximum_items;
			if ($previous < 0){
				$mod = $total_photos %  $maximum_items;
				$previous = $mod == 0? $total_photos -  $maximum_items : $total_photos - $mod;
			}
			echo '?'.getGalleryParams().'&start='.$previous.'&direction=-1';
		}
	}
	
	function getNextUrl(){
		global $startindex, $displayed_amount, $sortby, $order, $user, $total_photos;
		if ($total_photos > $displayed_amount){
			$next = $startindex + $displayed_amount;
			//echo "startindex $startindex displayed_amount $displayed_amount";
			$next = $startindex + $displayed_amount < $total_photos ? $next : 0;
			echo '?'.getGalleryParams().'&start='.$next.'&direction=1';
		}
	}

?>