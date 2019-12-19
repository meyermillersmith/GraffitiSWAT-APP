<?php
if (!isset($using_error_handler)) {
	require '../php/includes/errorhandler.inc';
}
$title = 'S.W.A.T. Gallery Detailed';
$GLOBALS["page"] = 'gallery';

			$_GET['slides'] = 'true';
			$_GET['max'] = 2000;
			
			$currentImageId = isset($_GET['image'])? $_GET['image'] : 0;
			$isExternalLink = $currentImageId > 0 && (!isset($_GET['referral']) || $_GET['referral']!='internal');
			require 'utils/getGraffitis.php';
			
			$list_index = isset($_GET['list_index'])? $_GET['list_index'] : 0;
			$direction = isset($_GET['direction'])? $_GET['direction'] : 1;
			$id = isset($_GET['id'])? $_GET['id'] : 1;
			
				$total_photos = countGalleryEntries($user,$surface,$level);
				$metaTags = array();
				
				if ($currentImageId > 0){
					$metaTags['title'] = 'A Graffiti';
					$hasCaption = $photos[0]['caption'] != '';
					$authorText = 'by '.$photos[0]['username'];
					$metaTags['title'] = $hasCaption? $photos[0]['caption'] : ($metaTags['title'].' '.$authorText) ;
					$metaTags['description'] = $hasCaption? $authorText: 'From the Graffiti S.W.A.T. Gallery';
					$metaTags['author'] = $photos[0]['user'];
					if (strrpos($photos[0]['path'],'http') === 0){
						$metaTags['image'] = $photos[0]['path'];
					} else {
						$metaTags['image_missing_server'] = $photos[0]['path'];
						$metaTags['image'] = '0';
					}
				}
				
				if ($displayed_amount > 2000) {
					array_splice($photos, 2000);
					$displayed_amount = count($photos);
				}
				
				require '../php/includes/header.inc';
//die();
				
				//$images = pickupFotos();
			$userIsAdmin = isAdmin($fbid);
		?>
<div id="gallery_wrapper">	
	<div id="imageViewControls">
		<div id="backButtonWrapper">
			<a id='backToListButton' class="blackButton" href="list.php?<?php echo getGalleryParams(); ?>" target="_self" ><span><span>Back</span></span></a>
		</div>
		<div class='afterImageInfo' style='padding-right:10px;' ><a href="" id='fbShareLink' target="_blank" ><img src="../images/startpage/share.png" alt="share" /></a></div>
		<div id="imageInfo">
			<span id='imageCount'></span>
			<span id='imageCountAll'></span>
			<span class="imageInfoSeperator">|</span>
			<span id='imageDate'></span>
			<span class="imageInfoSeperator">|</span>
			<span id='imageUser'></span>
			<span class="imageInfoSeperator">|</span>
			<span id='imageLikes'></span>
		</div>
		<div id='imageLike' class='afterImageInfo'></div>
		<div class='afterImageInfo' style='padding-top: 3px;' id='metaLink'></div>
	</div>
	<div id="imageGallery">
		<div id="imageArrows" class="imageCenterBar">
			<span id='imagePrevious'><a href="javascript:nextImage(-1)" ><span class="imageArrowLeft">&nbsp;</span></a></span>
			<span id='imageNext'><a href="javascript:nextImage(1)" ><span class="imageArrowRight">&nbsp;</span></a></span>
		</div>
		<div id='loadingLayer' class="imageCenterBar">
				<!--<span id="imagePreviousLoadAnim" class="loadingAnim"></span> -->
				<span id="imageLoadAnim" class="loadingAnim" ></span>
				<!-- <span id="imageNextLoadAnim" class="loadingAnim" ></span> -->
		</div>
		<div id="imageData">
			<div id="image">
				<div id="pickTagHolder" style="z-index:2;"></div>
				<div id='imageClicker' class="overTheImage"></div>
                <div id="nextImageLayer" class="overTheImage" ></div>
				<div id="galleryImageObj" ></div>
			</div>
			<div  id='imageCaption'>
			</div>
		</div>
	</div>
</div>
	<script type="text/javascript" src="js/moreImages.js"></script>
	<script type="text/javascript">
		var galleryParams = '<?php echo getGalleryParams();?>';
		var galleryParams = '<?php echo getGalleryParams();?>';
		var totalImages = <?php echo $total_photos;?>;
		var startIndex = <?php echo $currentImageIndex;?>;
		var jsonImages = <?php echo json_encode($photos);?>;
		var targetImageId = <?php echo $currentImageId;?>;
		var currentImage = 0;
		var currentImageWidth = 0;
		var realCurrentIndex = getDatabaseIndex();
		var endIndex = startIndex;
		var showingAll = <?php echo ($displayed_amount == $total_photos? 'true':'false');?>;
		var appServerUrl = '<?php echo $GLOBALS["server"];?>';
		var autoLike = <?php echo (isset($_GET["autolike"]) && $_GET["autolike"] == '1'?'true':'false') ;?>;
		var appFbUrl = 'http://apps.facebook.com/<?php echo $GLOBALS["appAlias"]?>/';
		var simplePreviousLink;
		var simpleNextLink;
		var idMap;
		var fbid  = "<?php  echo $fbid == ''? -1 : $fbid; ?>";
		var imageLoadingComplete = false;
		var likesLoadingComplete = false;
		var fbShareUrl = 'http://www.facebook.com/sharer.php?u=';//'../php/facebook/shareplugin.php?imageParams=';
		var fbGraffitiUrl = appServerUrl+'gallery/image.php?image=';
		var loadedImages = new Array();
		var viewCount = 1;
		var showingAd = 0;
		
		showLoading();
		
		openGallery(currentImage);
		
		function openGallery(imageId){
			if (simplePreviousLink == undefined){
			 	simplePreviousLink = $('#imagePrevious').html();
			 	simpleNextLink = $('#imageNext').html();
			}
			currentImage = typeof imageId !== 'undefined' ? imageId : 0;
			//console.log('show '+imageId+' currentImage '+currentImage);
			image
			$("#image").click(openZoom);
			showImage(currentImage);
			initIdMap();
			getMoreItemsFirstCall();
		}
		
		function initIdMap(){
			idMap = new Array();
			for (var i = 0; i < jsonImages.length; i++){
				var dbId = jsonImages[i]['id']; 
				idMap[dbId] = i;
			}
		}
		
		function preloadImages(loadedImages){
            for (var i = 0; i < loadedImages.length; i++){
                var imgSrc = toGalleryUrl(loadedImages[i]['path_720']);
                var img = new Image ();
                img.src = imgSrc;
            }
		}
		
		function nextImage(increment){
			<?php
				if (true){//(!$userIsAdmin){
			?>
			$("#image").unbind("click");
			if (viewCount % 10 == 0){
			    showingAd = increment;
				showBeautifulAd();
			} else {
				$("#image").click(openZoom);
           		$("#imageClicker").html('');
           		$("#fbShareLink").css("display",'block');
           		$("#backButtonWrapper").css({'display':'block'});
           		$("#imageInfo").css({'display':'block'});
           		if (showingAd > 0 && increment < 0){
                    goToNextImage(0);
           		} else if (showingAd < 0 && increment > 0){
                    goToNextImage(0);
                } else {
                    goToNextImage(increment);
           	    }
			}
			viewCount++;
			<?php
				} else {
					echo "goToNextImage(increment);";
				}
			?>
		}
		
		function showBeautifulAd(){
            $("#galleryImageObj").css("background-image",'none');
            $("#fbShareLink").css("display",'none');
           	$("#backButtonWrapper").css({'display':'none'});
           	$("#imageInfo").css({'display':'none'});
           	$("#imageCaption").html('');
            
           resizeImageContainer(250);
           currentImageWidth = 300;
	       onResize();
           	
			var src = 'ad_'+currentAdIndex;
            $("#imageClicker").html(getCenteredAd(src));
			showLoading(src);
		}
		
		function getCenteredAd(src){
			var html = "<div>";
			html += getAdvertHTML("<?php echo $asset_path ?>","detailedInBetweenAd","3600588112",300,250,"onAdLoaded('"+src+"')");
			html += "</div>";
			return html;
		}
		
		function onAdLoaded(src){
			onImageComplete(true,src)
		}
		
		function goToNextImage(increment){
			currentImage+=increment;
			if (currentImage == jsonImages.length - 1){
				if (loadedAllRight){
					changeStateSideVisible(false,'right');
				} else {
					getMoreItems('right');
				}
			} else if (currentImage == 0){
				if (loadedAllLeft){
					changeStateSideVisible(false,'left');
				} else {
					getMoreItems('left');
				}
			} 

			if (increment < 0){
				$('#imageNext').css({'display': 'block'});
			} else if (increment > 0) {
				$('#imagePrevious').css({'display': 'block'});
			}
			showImage(currentImage);
		}
		
		function showImage(currentImage){
			var isFirstImage = currentImage == 0;
			var isLastImage = currentImage == jsonImages.length - 1;
			//console.log('showImage currentImage '+currentImage+'isFirstImage '+isFirstImage+' jsonImages.length '+jsonImages.length+' isLastImage '+isLastImage);
			var currentImageObj = jsonImages[currentImage];

			//change image
			//$('#galleryImageObj').attr("alt", currentImageObj['caption']);
			var imgSrc = toGalleryUrl(currentImageObj['path_720']);
			var img = new Image ();
			img.onload = onImageLoaded;
			img.onerror = onImageError;
	        img.src = imgSrc;
			showLoading(img.src);
            // $("#galleryImageObj").css("background-image",'url('+imgSrc+')');
            $("#nextImageLayer").css("background-image",'url('+imgSrc+')');
            $("#galleryImageObj").html('');

			//change caption
			if (currentImageObj['caption'] != ''){
				$('#imageCaption').html(currentImageObj['caption']);
			} else {
				$('#imageCaption').html('');
			}
			//change rest of description
			var realCurrentIndex = 1 + getDatabaseIndex();
			$('#imageCount').html(realCurrentIndex);
			$('#imageCountAll').html(' of '+totalImages);
			$('#imageDate').html(getDisplayDate(currentImageObj["created"]));
			
			var username = nameMap[currentImageObj['user']];
			//console.log('username nameMap '+username+' db '+currentImageObj['username']);
			if (username == null) {
				username = currentImageObj['username'];
				if (username == null){
					username = '....';
				} else {
					nameMap[currentImageObj['user']] = username;
				}
				getUserNameFromFB(currentImageObj['user'],'imageUser',onGotUserName);
			}
			$('#imageUser').html(getNameFBLinkHTML(username,currentImageObj['user']));

			//likes
			if (currentImageObj['ilikethis'] == null ) {
				getLikeList(currentImageObj['id']);
			} else {

				likesLoadingComplete = true;
				setLikeText();
				onLikesLoaded(true);
			}
			
			//change links
			
			$('#imageNext').html(simpleNextLink);
			$('#imagePrevious').html(simplePreviousLink);

			currentStartIndex = Math.floor((startIndex + currentImage) / 24)*24;
			document.getElementById("backToListButton").href = 'list.php?start='+currentStartIndex+'&<?php echo getGalleryParams(); ?>';
			

			//share
			document.getElementById("fbShareLink").href = fbShareUrl + escape(fbGraffitiUrl + currentImageObj['id']);// + escape('&title=by '+username);
			<?php
				if ($userIsAdmin){
			?>
				var downloadLink = '<a href="'+toGalleryUrl(currentImageObj['path_png'] == ''? currentImageObj['path'] : currentImageObj['path_png'])+'" target="_blank" >Download </a>';
				var deleteLink = '<span style="color:red;cursor:pointer;margin-left:10px;" onclick="remove('+currentImageObj['id']+')">X Delete</span>';
				var copyLink = '&nbsp;<a href="javascript:copyToClipboard(\''+fbGraffitiUrl + currentImageObj['id']+'\')">Copy Link</a>';
				$('#metaLink').html(downloadLink+deleteLink+copyLink);
			<?php
				}
			?>

			$('#pickTagHolder').html(getPickTag(currentImageObj["level"],'detailed'));

			//console.log('autolike '+autoLike+' targetImageId '+targetImageId+' currentImageObj[id] '+currentImageObj['id']+' fbid '+fbid);
			if (autoLike && targetImageId == currentImageObj['id']){
				//console.log('autolike curent '+targetImageId+' fbid '+fbid);
				like(currentImageObj['id']);
				autoLike = false;
			}
		}
		
		function getDatabaseIndex() {
			return startIndex + currentImage;
		}
		
		function copyToClipboard (text) {
			window.prompt ("Copy to clipboard: Ctrl+C, Enter", text);
		}
		
		function showLoading(src){
			if (!loadedImages[src]){
				imageLoadingComplete = false;
				likesLoadingComplete = false;
				$('#imageData').css({ 'display': 'none'});
				$('#imageLike').css({ 'display': 'none'});
				$('#imageLoadAnim').css({ 'display': 'block'});
				startAnimating('#imageLoadAnim');
			}
		}
		
		function hideLoading(loaded){
			$('#imageData').css({ 'display': 'block'});
			$('#imageLoadAnim').css({ 'display': 'none'});
			stopAnimating('#imageLoadAnim');
		}
		
		function onImageLoaded(event, imageObj){
			var img = imageObj? imageObj : this;
			loaded = img.width > 0;
			
			currentImageWidth = this.width;
	        
	        resizeImageContainer(img.height);
	        
	        onResize();
	        onImageComplete(loaded,img.src);
		}

		function onImageError(event, imageObj){
			var img = imageObj? imageObj : this;
			onImageComplete(false,img.src);
		}
		
		function onImageComplete(loaded,src){
			//console.log('onImageComplete '+loaded+' imageObj '+imageObj.src);
			loadedImages[src] = 1;
			imageLoadingComplete = true;
			hideLoading(loaded);
		}
		
		function onLikesLoaded(loaded){
			$('#imageLike').css({ 'display': 'block'});
		}
		
		function getLikeList(id){
			var url = "../php/action/getLikeList.php";
			var request = {url: url, type:'POST', data:'entry_id='+id,  error:onLikeListFailed, success:onLikeListCompleted};
			$.ajax(request);
		}

		function onLikeListFailed(data){
			likesLoadingComplete = true;
			//console.log('onLikeListFailed::'+data);
			onLikesLoaded(true);
		}
		
		function onLikeListCompleted(data){
			var response = data.split('::');
			var status = response.length > 0? response[0] : data;
			var imageId = response.length > 1? idMap[response[1]] : -1;
			var likeListStr = response.length > 2? response[2] : null;
			var ilikethis = false;
			
			if(status == 'success' && imageId > -1 && likeListStr != null && likeListStr != ''){
				var likes_list = likeListStr.split(',');
				for (var i=0;i<likes_list.length;i++){
					if (fbid == likes_list[i]){
						ilikethis = true;
						break;
					}
				}
				
			}
			
			if (imageId > -1){
				jsonImages[imageId]['ilikethis'] = ilikethis;
				if (imageId == currentImage){
					setLikeText(imageId);
				}
			}
			
			likesLoadingComplete = true;
			onLikesLoaded(true);
			
    		//console.log("onLikeListCompleted::Sample of data:", data);
		}
		
		function setLikeText(imageId){
			imageId = imageId == null ? currentImage : imageId;
			var ilikethis = jsonImages[imageId]['ilikethis'];
			var likes = jsonImages[imageId]['likes'];
			var id = jsonImages[imageId]['id'];
			if (ilikethis){
				$('#imageLike').html('<a href="javascript:unlike('+id+')" class="blackButton" ><span><span style="color:#444">Unlike</span></span></a>');
			} else {
				$('#imageLike').html('<a href="javascript:like('+id+')" class="yellowButton" ><span><span>Like!</span></span></a>');
			}
			var likeText = '';
			if (ilikethis){
				likeText = 'You';
				likeText += likes > 1 ? ' and '+(likes - 1)+' others' : '';
			} else {
				likeText += likes + (likes != 1 ?' people':' person');
			}
			likeText += likes == 1 && !ilikethis ? ' likes this' : ' like this';
			$('#imageLikes').html(likeText);
		}
		
		function like(id){
			if (fbid == "-1"){
				top.location.href = appFbUrl+'?image='+id+'&params='+escape(galleryParams+'&autolike=1'); 
				//top.location.href = fbGraffitiUrl+id+'&'+galleryParams+'&autolike=1';
			} else {
				$('#imageLike').css({ 'display': 'none'});
				//console.log('sent '+id+'/likes');
				sendLike(id, true);
			}
		}
		
		function unlike(id){
			$('#imageLike').css({ 'display': 'none'});
			//console.log('sent '+id+'/unlikes');
			sendLike(id, false);
		}
		
		function sendLike(id,ilikethis){
			var url = "../php/action/like.php";
			var key = Math.floor((Math.random()*990000)+10000);
			var request = {url: url, type:'POST', data:'fbid='+fbid+'&chunks='+encrypt(id.toString(),key.toString())+'&gid='+key+(ilikethis?'':'&undo=true'),  error:onLikeFailed, success:onLikeCompleted};
			$.ajax(request);
			
			// Open Graph Like
			var graffiti = appFbUrl+"gallery/image.php?image="+id;
			var postString = ilikethis?'POST':'DELETE';
			FB.api('me/og.likes', postString, { object : graffiti, access_token : fBAccessToken });
		}
		
		
		function onLikeCompleted(data){
			var response = data.split('::');
			var status = response.length > 0? response[0] : data;
			var realImageId = response.length > 1? response[1] : -1;
			var imageId = realImageId > -1? idMap[realImageId] : -1;
			//console.log('onLikeCompleted::imageId '+imageId);
			switch(status){
				case 'failed':
					alert('An error occured - please try later '+imageId);
				break;
//				case 'login':
//					top.location.href = fbGraffitiUrl+realImageId+'&'+galleryParams+'&autolike=1';
//				break;
				case 'success':
					if (imageId > -1){
						jsonImages[imageId]['ilikethis'] = !jsonImages[imageId]['ilikethis'];
						var increment = jsonImages[imageId]['ilikethis']? 1 : -1;
						jsonImages[imageId]['likes'] = parseInt(jsonImages[imageId]['likes']) + increment;
						if (imageId == currentImage){
							setLikeText(imageId);
						}
					}
				break;
				default:
					onLikeFailed(data);
				break;
			}
			$('#imageLike').css({ 'display': 'block'});
			//TODO report error detailes to log
    		//console.log("onLikeCompleted::Sample of data:", data);
		}
		
		function onLikeFailed(data){
    		alert("ERROR: please reload or try again later - ", data);
		}
		
		function onGotUserName(uid,imageDivId){
			if (jsonImages[currentImage]["user"] == uid){
				$('#'+imageDivId).html(getNameFBLinkHTML(nameMap[uid],uid));
			}
		}
		
		function getNameFBLinkHTML(username,uid){
			var nameHTML = '<a href="list.php?user='+uid+'">'+username+'</a>';
			return nameHTML;
		}

		/**
		* INIT FB
		*/
		$(document).ready( function() {
			initAnimations();
		});
		
		/**
		* ARROW BUTTONS
		*/
		
 		window.onresize = onResize;
 			onResize();
				
		function onResize(){
			var resizedButtonContainerW = $('.imageCenterBar').width();
			var resizedButtonContainerM =  ((resizedButtonContainerW*-1/2) + 5)+'px';
			$('.imageCenterBar').css({ 'margin-left':resizedButtonContainerM});
			
			var loadingImageCenter  = resizedButtonContainerW / 2 - $('#imageLoadAnim').width() / 2;
			$('#imageLoadAnim').css({ 'margin-left':loadingImageCenter+'px'});
			
	        //$("#imageClicker").width(img.width);
	        
	        resizeImageClickerWidth();
		}
		
		function resizeImageContainer(imgHeight){
			var margin_top = imgHeight < 460? (460 - imgHeight ) / 2  : 0;
			$('#imageData').css({"margin-top":margin_top+'px'});
			
           	$("#imageClicker").height(imgHeight);
	       // $("#galleryImageObj").height(imgHeight);
            $("#nextImageLayer").height(imgHeight);
		}
		
		function resizeImageClickerWidth(){
			if (currentImageWidth > 0){/*
		        var arrowWidth = 70;//cssPxToNum($('.imageArrowLeft').css('width'));
		        var arrowBarWidth = $('.imageCenterBar').width() - 10;
		        var arrowBarEmptySpace = arrowBarWidth -  2*arrowWidth;
		        var clickerWidth = currentImageWidth;
		        var leftMargin = 0;
		        
		        if (arrowBarEmptySpace<currentImageWidth){
		        	clickerWidth = arrowBarEmptySpace;
		        	leftMargin = (currentImageWidth - arrowBarEmptySpace) / 2;
		        }
           	
				$('#image').width(currentImageWidth);
				var margin = -(currentImageWidth) / 2;
				$('#image').css({"margin-left":margin+'px'});
								
		        $("#galleryImageObj").width(currentImageWidth);
		        
		        $("#imageClicker").width(clickerWidth);
		       	$("#imageClicker").css({'margin-left':leftMargin}); */
		       	
                $("#nextImageLayer").css({'margin-left':currentImageWidth});
                $("#nextImageLayer").width(currentImageWidth);
                console.log('resizeImageClickerWidth::nextImageLayer '+ $("#nextImageLayer").css('margin-left'));
		       	$("#nextImageLayer").animate({"margin-left": "0px" }, 800, 'linear', onAnimationDone);
	       }
		}
		
		function onAnimationDone() {
                console.log('onAnimationDone::nextImageLayer '+ $("#nextImageLayer").css('margin-left'));
                var arrowWidth = 70;//cssPxToNum($('.imageArrowLeft').css('width'));
                var arrowBarWidth = $('.imageCenterBar').width() - 10;
                var arrowBarEmptySpace = arrowBarWidth -  2*arrowWidth;
                var clickerWidth = currentImageWidth;
                var leftMargin = 0;
                
                if (arrowBarEmptySpace<currentImageWidth){
                    clickerWidth = arrowBarEmptySpace;
                    leftMargin = (currentImageWidth - arrowBarEmptySpace) / 2;
                }
            
                $('#image').width(currentImageWidth);
                var margin = -(currentImageWidth) / 2;
                $('#image').css({"margin-left":margin+'px'});
                                
                $("#galleryImageObj").width(currentImageWidth);
                $("#galleryImageObj").height($("#nextImageLayer").height());
                $("#galleryImageObj").css("background-image",'url('+imgSrc+')');
                $("#nextImageLayer").width(0);
                
                $("#imageClicker").width(clickerWidth);
                $("#imageClicker").css({'margin-left':leftMargin});
                
		    
		}

		document.body.onkeydown = function(evt) {
		    evt = evt || window.event;
		    //console.info('evt.keyCode '+evt.keyCode);
		    var target = evt.target || evt.srcElement;
		    var targetTagName = (target.nodeType == 1) ? target.nodeName.toUpperCase() : "";
//		    if ( !/INPUT|SELECT|TEXTAREA/.test(targetTagName) ) { 
	        switch (evt.keyCode) {
	            case 37:
		            var bHasPrev = currentImage > 0;
		            //console.info('click prev: '+bHasPrev);
		            if (bHasPrev) nextImage(-1);
	                break;
	            case 39:
		            var bHasNext = currentImage < jsonImages.length-1;
		            //console.info('click next: '+bHasNext);
		            if (bHasNext) nextImage(1);
	                break;
	            case 76:
		            //console.info('like '+jsonImages[currentImage]['id']);
		            if (!jsonImages[currentImage]['ilikethis']) like(jsonImages[currentImage]['id']);
	                break;
	            case 83:
		            shareCurrentImage();
	                break;
	        }
//		    }
		}
		/**
		* EXTRA
		*/
		
		

		function openZoom(){
			<?php
			if (true/*$fbid != -1*/){
			?>
			var imageLink = jsonImages[currentImage]['path'];
			var isLocalImage = imageLink.indexOf('public') == 0;
			var link = isLocalImage?'../'+imageLink: imageLink;
			window.open(link);
			
			<?php
				}else {
			?>
			//console.log("Please login to view the zoomed version");
			<?php
				}
			?>
		}
		
		<?php
			if ($userIsAdmin){
		?>
		
		function remove(id){
			var confirmed = confirm('Delete image '+id+'?');
			if (confirmed){
				var url = "../admin/php/action/secure/4kB8U08o.php";
				var request = {url: url, type:'POST', data:'entry_id='+id,  success:onRemoved, error:onRemoveFailed};
				$.ajax(request);
			}
		}
		
		
		function onRemoved(data){
			var response = data.split('::');
			var status = response.length > 0? response[0] : data;
			var realImageId = response.length > 1? response[1] : -1;
			
			switch(status){
				case 'failed':
					alert('An error occured - please try later '+realImageId);
				break;
				case 'login':
					alert('please login '+realImageId);
				break;
				case 'success':
					$('#listImage'+realImageId).remove();
				break;
				default:
					onRemoveFailed(data);
				break;
			}
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