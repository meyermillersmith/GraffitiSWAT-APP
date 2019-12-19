<?php
$title = 'S.W.A.T. Gallery List';
$GLOBALS["page"] = 'gallery';
require '../php/includes/header.inc';
	
$sort_key_likes = 'likes';
$sort_key_date = 'created';
	
require 'utils/getGraffitis.php';
	
function createLink($sortLink, $label){
	global $sortby, $order;
	$isSortedByThis = $sortby==$sortLink;
	$isThisDesc = $isSortedByThis&&$order=='DESC';
	$htmlText = '<a class="listControlLink'.($isSortedByThis? ' listControlLinkActive' : '').'"';
	$htmlText .= 'href="?sortby='.$sortLink.($isThisDesc? '&order=ASC' : '').getGalleryOptionalParams().'"';
	$htmlText .= ' target="_self" >'.$label;
	$htmlText .= ' <span class='.($isThisDesc? '"sortArrowUp">^' : '"sortArrowDown">v').'</span>';
	$htmlText .= '</a>';

	return $htmlText;
}
	
function createGlobalGalleryItem($sortLink, $label){
	global $sort_key_date, $pulldownItemLabels;
	$label = $sortLink==$sort_key_date? $GLOBALS["TXT_LATEST_GALLERY"] : $GLOBALS["TXT_BEST_OF_GALLERY"];
	$pulldownItemLabels[$sortLink] = $label;
	return createPulldownItem("sortby", $sortLink, $label);
}
	
function createGlobalGalleryItemBest($level){
	global $pulldownItemLabels;
	$label = $GLOBALS["TEXT_BEST_OF_".$level];
	$pulldownItemLabels[$level] = $label;
	return createPulldownItem("level", $level, $label);
}
	
function createPulldownItem($itemKey, $itemKeyValue, $label){
	$pulldownHTML = '<div class="pulldownSelectorItem" onclick="javascript:onSurfaceChosen(\''.$itemKey.'\', \''.$itemKeyValue.'\');">'.$label.'</div>';
	return $pulldownHTML;
}
	
	
function createPulldownTitle(){
	global $sortby, $user, $surface, $pulldownItemLabels, $sort_key_date, $level;
	$htmlText;
	if ($surface !=""){
		$htmlText = $pulldownItemLabels[$surface];
	} else if ($level !=""){
		$htmlText =$pulldownItemLabels[$level] ;
	} else if ($sortby !=""){
		$htmlText = $pulldownItemLabels[$sortby] ;
	} else{
		$htmlText = $GLOBALS["TXT_DEFAULT_GALLERY_PULLDOWN_LABEL"] ;
	}
	return $htmlText;
}
	
$allSurfaces = getAllGalleries(false);
usort($allSurfaces, "sortByTitle");
	
function sortByTitle($a, $b){
	return strcmp($a["name"], $b["name"]);
}
	
$pulldownItemLabels;
$pulldownHTML = '';
	
if(count($allSurfaces) > 1){
	for ($i = 0; $i < count($allSurfaces); $i++){
		$currSurface = $allSurfaces[$i];
		$currSurface["label"] = $currSurface['name'].' Gallery';
		$pulldownItemLabels[$currSurface['surface']] = $currSurface["label"];
		$pulldownHTML .= createPulldownItem("surface", $currSurface['surface'], $currSurface['name']);
	}
}
	
$userIsAdmin = isAdmin($fbid);
function getAdditional(){
	global $userIsAdmin;
	if ($userIsAdmin){
		echo '<div class="listUsername" style="color:red;cursor:pointer;" data-id="entry_id">';
		echo 'X Delete';
		echo '</div>';
	}
}
	
$total_photos = countGalleryEntries($user,$surface,$level);
?>
<div id="gallery_wrapper">
	<div id="pulldownSelector">
		<?php
		echo createGlobalGalleryItem($sort_key_likes, $GLOBALS["TXT_LIKES"]);
		echo createGlobalGalleryItem($sort_key_date, $GLOBALS["TXT_DATE"]);
		?>
		<div class="pulldownSelectorLine"></div>
		<?php
		echo createGlobalGalleryItemBest(1);
		echo createGlobalGalleryItemBest(2);
		echo createGlobalGalleryItemBest(3);
		?>
		<div class="pulldownSelectorLine"></div>
		<?php
		echo $pulldownHTML;
		?>
	</div>
	<div id='listControls'>
		<div id="userGalleryTitle">
			<?php
			if ($user !=""){
				echo '<a class="blackButton" href="?" target="_self" ><span><span>Back</span></span></a>';
				echo '<span class="userGalleryUser">';
				echo 'By: ';
				echo '<a id="userGalleryUserName" class="galleryTitleUsername" href="http://www.facebook.com/profile.php?id='.$user.'" target="_blank" ><span id="pageUsername">'.getUsername($user).'</span></a>';
				echo '</span >';
			} else {
				echo '<a class="blackButton" href="index.php" target="_self" ><span ><span>Back</span></span></a>';
				echo '<span style="margin-right:15px" ></span >';
			}
			?>
		</div>
		<div id='sorting'>
			<span class="sortby">Sort by: </span>
			<?php
			echo createLink($sort_key_date, $GLOBALS["TXT_DATE"]);
			echo createLink($sort_key_likes, $GLOBALS["TXT_LIKES"]);
			?>
		</div>
		<div id='galleryPicker'>
			<a id="pulldownClicker" class="pulldownClicker"
				href="javascript:void(0);"> <span id="pulldownClickerTitle"
				class="pulldownClickerMiddle"><?php echo createPulldownTitle(); ?> </span>
				<span class="pulldownClickerRight"></span>
			</a>
		</div>
	</div>
	<div id='listMainContent'>
		<!--<div id='rightBanner'>-->
		<!-- begin ryad tag -->
		<!-- <div id="_ryad_9DDCF572901"></div>
				<script type="text/javascript">
				   _ryadConfig = new Object();
				   _ryadConfig.thirdPartyId = "";
				   _ryadConfig.placeguid = "9DDCF572901";
				   _ryadConfig.type = "MediumRectangle";
				   _ryadConfig.width = "300";
				   _ryadConfig.height = "250";
				   //Set to 1 if modifying width and height
				   _ryadConfig.customSizeAd = "0";
				   _ryadConfig.popup = 1;
				   //Do not modify below this line 
				   document.write(unescape("%3Cscript src='" + document.location.protocol + "//cdnrockyou-a.akamaihd.net/apps/ams/tag_os.js' type='text/javascript'%3E%3C/script%3E"));
				</script> -->
		<!-- end ryad tag -->
		<!--</div>-->
		<div id='skipButtonContainer1' class='skipButtonContainer'>
			<div class='previousButton'>
				<a href="<?php echo getPreviousUrl();?>" class="yellowButton"><span><span>previous</span>
				</span> </a>
			</div>
			<div class='listCounter'><?php echo (($startindex/24)+1).'/'.ceil($total_photos/24);?></div>
			<div class='nextButton'>
				<a href="<?php echo getNextUrl();?>" class="yellowButton"><span><span>next</span>
				</span> </a>
			</div>
		</div>
		<div id='listImages'></div>
		<div id='skipButtonContainer2' class='skipButtonContainer'></div>
	</div>
</div>
<script type="text/javascript">
	
		/**
		* IMAGES
		*/
		var jsonImages = <?php echo $photos;?>;
		var allstugg = <?php echo count($photos);?>;
		var fbid  = "<?php  echo $fbid == ''? -1 : $fbid; ?>";
		var galleryParams = '<?php echo getGalleryParams();?>';
		var startIndex = <?php echo $startindex;?>;
		var imageCount = jsonImages.length;
		var totalImages = <?php echo $total_photos;?>;
		var pageUser = '<?php echo $user;?>';
		var meta = '<?php echo getAdditional();?>';
		var unknownUser = '<?php echo $GLOBALS["TXT_DEFAULT_GALLERY_USER"];?>';
		
		if (initDone) initPage();
		
		function showImages(){
			var imageHtml = '<div id="imageScreen">';
			for (var i = 0; i < jsonImages.length; i++){
				var image = jsonImages[i];
				var src = image["path_icon"] == ''? image["path"] : image["path_icon"];
				imageHtml += '<div class="listImage" id="listImage'+image["id"]+'">';
				imageHtml += getPickTag(image["level"],'small');
				imageHtml += '<a href="image.php?'+galleryParams+'&image='+image["id"]+'" class="listImagePic" style="background-image: url('+toGalleryUrl(src)+')" >';
				imageHtml += '</a>';
				imageHtml += '<div>';
				imageHtml += '<div class="listLikeCount">'+image["likes"]+' Likes'+'</div>';
				imageHtml += '<div class="listDate">'+getDisplayDate(image["created"])+'</div>';
				imageHtml += '</div>';
				imageHtml += setId(meta,image["id"]);
				imageHtml += '<div id="image'+i+'" class="listUsername">';
				var username = image["username"];
				if (username == null || username == ''){
					username = unknownUser;
					getUserName(image['user'],'image'+i,onGotUserNameForLink);
				}
				imageHtml += getUsernameLink(image['user'], username, "");
				imageHtml += '</div>';
				imageHtml += '</div>';
				
			}
			imageHtml += '</div>';
			$('#listImages').append(imageHtml);
			
			if (imageCount < totalImages){
				//$('#showMoreButton').html(getShowMoreHTML());
			} else {
				hideMoreButton();
			}
			$('#listImages').append(getAdvertHTML("<?php echo $asset_path ?>","listBetweenAd","1503673317",728,90));

			$('#skipButtonContainer2').html($('#skipButtonContainer1').html());
			
		}
		
		function onGotUserNameForFBLink(uid,imageDivId){
			$('#'+imageDivId).html(nameMap[uid]);
		}
		
		/**
		* ON READY
		*/
		
		$(document).ready( function() {
			setPullDownPosSet(false);
	 		window.onresize = resizeButtons;
	 		resizeButtons();
	 		getButtonVisibility();

            $('.listUsername').click(function() {
                remove($(this).attr('data-id'));
            });
		});
		
		
		$(window).load( function() {
			//resizeButtons();
		});

		window.onload = resizeButtons;

		function getButtonVisibility(){
			if (startIndex == 0){
				$('.previousButton').css({ 'display': 'none'});
			} else {
				$('.previousButton').css({ 'display': 'block'});
			}

			if (startIndex + imageCount < totalImages){
				$('.nextButton').css({ 'display': 'block'});
			} else {
				$('.nextButton').css({ 'display': 'none'});
			}
		}

		function resizeButtons(){
			var imageWidthWithBorder = 180 + 10;
			var containerWidth = $('#imageScreen').width();
			var imageInARow = Math.floor(containerWidth / imageWidthWithBorder);
			var fixedWidth = imageInARow * imageWidthWithBorder;
			
			$(".skipButtonContainer").width(fixedWidth);
			//var counterPosition = fixedWidth/2 - $(".listCounter").width() / 2 - $(".previousButton").width();
			//$(".listCounter").css({ 'left': counterPosition+'px'});

			//console.log('counterPosition '+counterPosition+' listCounter ' +$(".listCounter").width()+' fixedWidth '+fixedWidth);
		}
		
		function hideMoreButton(){
			$('#showMoreButton').html('');
		}
		
		/**
		* CONTROLS
		*/

		var pulldownItemLabels = <?php echo json_encode($pulldownItemLabels);?>;
		
		function initPage(){
			showImages();
			getUserName(pageUser,'pageUsername',onGotUserNameForFBLink);
		}


		function onSurfaceChosen(key, value){
			//console.log('onSurfaceChosen::'+key+'='+value);
			$('#pulldownClickerTitle').html(pulldownItemLabels[value]);
			location.href = '?'+key+'='+value;
		}

		function setPullDownPos(){

			var IE = document.all?true:false
			//var ihatejquerymargin = IE? 19 : (pageUser == ''? 37 : 70);
			var ihatejquerymargin = 20;
			if(browser.safari || browser.chrome){
				//ihatejquerymargin += 10 - 4;
			} else if (browser.msie){
				//ihatejquerymargin += 19;
			}
			var clickerSmallOffset = $('#galleryPicker').offset().left;
			//clickerSmallOffset = browser.safari || browser.chrome? clickerSmallOffset - 4 : clickerSmallOffset;
			var clickerOffset = parseInt(clickerSmallOffset + ihatejquerymargin);
			$('#pulldownSelector').css({ 'left': clickerOffset+'px'});
			//alert('browser? '+browser.safari+' plus '+ihatejquerymargin+' clickerSmallOffset '+clickerSmallOffset+' clickerOffset '+clickerOffset);

			setPullDownPosSet(true);
		}
		
		/**
		* EXTRA
		*/
		
		
		function setId(metastuff,entryid){
			return metastuff.replace("entry_id",entryid); 
		}
		
		<?php
			if ($userIsAdmin){
		?>
		
		function remove(id){
			highlightImage(id, true);
			var confirmed = confirm('Delete image '+id+'?');
			if (confirmed){
				var url = "../admin/php/action/secure/4kB8U08o.php";
				var request = {url: url, type:'POST', data:'entry_id='+id,  success:onRemoved, error:onRemoveFailed};
				$.ajax(request);
			} else highlightImage(id, false);
		}
		
		
		function onRemoved(data){
			var response = data.split('::');
			var status = response.length > 0? response[0] : data;
			var realImageId = response.length > 1? response[1] : -1;
			
			switch(status){
				case 'failed':
					alert('An error occured - please try later '+realImageId);
					highlightImage(realImageId, false);
				break;
				case 'login':
					alert('please login '+realImageId);
					highlightImage(realImageId, false);
				break;
				case 'success':
					$('#listImage'+realImageId).remove();
				break;
				default:
					onRemoveFailed(data);
					highlightImage(realImageId, false);
				break;
			}
		}
		
		function highlightImage(id, on){
			$('#listImage'+id).css({ 'border': on?'5px solid red':'none'});
		}
		
		function onRemoveFailed(data){
    		alert("ERROR: please reload or try again later - "+data);
		}
		<?php
			}
		?>
		
	</script>
<?php 
require '../php/includes/footer.inc';
?>