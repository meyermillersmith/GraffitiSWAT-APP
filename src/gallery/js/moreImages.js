	
		/*LOAD MORE */
		
		var loadedAll = false;
		var waitingForMoreLeft = false;
		var waitingForMoreRight = false;
		var loadedAllLeft = false;
		var loadedAllRight = false;
		var loadedAnimOffset = 40;
		var loadedAnimFames = 17;
		var loadedAnimMaxWidth = loadedAnimOffset * loadedAnimFames;
		var loadedAnimDirection = 1;
		var onEnterFrameNum = 0;
		var tmpStartIndex = 0;
		var nextFrameCounts = new Array();
		var animatedDivs = new Array();

		
		function getMoreItemsFirstCall(){
			getMoreItems('left');
			getMoreItems('right');
		}
		
		function getMoreItems(side){
			//console.log('getMoreItems::'+side);
			if ((side=='left' && loadedAllLeft) || (side=='right' && loadedAllRight)){
				//console.log('getMoreItems::LOADED ALL '+side);
			} else {
				var waitingSignal =  side == 'left'? waitingForMoreLeft : waitingForMoreRight;
				if (!waitingSignal){
					setWaitingSignal(side,true);
					changeStateLoadingVisible(true,side);
					getNextImageBundle(side);
				}
			}
		}
		
		function getNextImageBundle(side){
			realCurrentIndex = getDatabaseIndex();
			var allLoaded = getAllLoaded(side);
			if (allLoaded){
				return;
			}
			var imageAmount = 5;
			var queryStartIndex = side == 'left'? realCurrentIndex -  imageAmount: realCurrentIndex + 1;
			if (queryStartIndex < 0){
				imageAmount += queryStartIndex;
				queryStartIndex = 0;
			}
			if (side == 'left') {
				tmpStartIndex = queryStartIndex;
			}
			var url = "utils/getGraffitis.php";
			var callback = side == 'left'? onMoreLoadedLeft : onMoreLoadedRight;
			var callbackFailed = side == 'left'? onMoreFailedLeft : onMoreFailedRight;
			var request = {url: url, type:'GET', data:'jscall=true&slides=true&start='+queryStartIndex+'&max='+imageAmount+'&'+galleryParams,  success:callback, error:callbackFailed};
			$.ajax(request);
		}
		
		function getAllLoaded(side){
//			console.log('getAllLoaded::'+side+' start '+startIndex+' end '+endIndex+' totalImages '+totalImages);
			var thereIsMore = side == 'left'? startIndex > 0 : endIndex < totalImages - 1;
			if (!thereIsMore){
				changeStateSideVisible(false,side);
				if (side == 'left'){
					loadedAllLeft = true;
				} else {
					loadedAllRight = true;
				}
			}
//			console.log('getAllLoaded::'+side+':thereIsMore?'+thereIsMore);
			return side == 'left'? loadedAllLeft : loadedAllRight;
		}
		
		function onMoreLoadedLeft(data){
			onMoreLoaded(data,'left');
		}
		
		function onMoreLoadedRight(data){
			onMoreLoaded(data,'right');
		}
		
		function onMoreLoaded(data,side){
//    		console.log("onMoreLoaded::"+side+":jsonImages:", jsonImages.length);
			var loadedImages = eval(data);
			if (loadedImages.length > 0){
				if (side == 'left'){
					jsonImages = loadedImages.concat(jsonImages);
					var currentImageObj = jsonImages[0];
					startIndex = tmpStartIndex;
//					console.log('onMoreLoaded::startIndex '+startIndex+' tmpStartIndex '+tmpStartIndex);
					currentImage += loadedImages.length;
				} else {
					jsonImages = jsonImages.concat(loadedImages);
					endIndex =  startIndex + jsonImages.length;
//					console.log('onMoreLoaded::endIndex '+endIndex);
				}
				initIdMap();
				preloadImages(loadedImages);
				getAllLoaded(side);
			} else {
				onMoreFailed(data);
			}
//    		console.log("onMoreLoaded::"+side+":jsonImages:", jsonImages.length);
			onRequestDone(side);
		}
		
		function onMoreFailedLeft(data){
			onMoreFailed(data,'left');
		}
		
		function onMoreFailedRight(data){
			onMoreFailed(data,'right');
		}
		
		function onMoreFailed(data,side){
    		//console.log("onMoreFailed::"+side+":Sample of data:", data);
    		alert("ERROR: please reload or try again later - ");
			onRequestDone(side);
		}
		
		function onRequestDone(side){
			setWaitingSignal(side,false);
			changeStateLoadingVisible(false,side);
		}
		
		function setWaitingSignal(side,value){
			if (side == 'left'){
				waitingForMoreLeft = value;
			} else {
				waitingForMoreRight = value;
			}
		}
		
		function changeStateLoadingVisible(visible,side){
//			visible = true;
			var displayAnim = visible? 'block' : 'none';
			var displayArrow = visible? 'none' : 'block';
			changeStateVisible(side,displayAnim,displayArrow);
		}
		
		function changeStateSideVisible(visible,side){
			var display = visible? 'block' : 'none';
			changeStateVisible(side,display,display);
		}
		
		function changeStateVisible(side,displayAnim,displayArrow){
//			var anim = side == 'left' ? '#imagePreviousLoadAnim' : '#imageNextLoadAnim';
			var arrow = side == 'left' ? '#imagePrevious' : '#imageNext';
			
//			$(anim).css({'display': displayAnim});
			$(arrow).css({ 'display': displayArrow});

//			if (displayAnim == 'none') {
//				stopAnimating(anim);
//			} else {
//				startAnimating(anim);
//			}
		}
		
		function startAnimating(div){
			animatedDivs[div] = true;
		}
		
		function stopAnimating(div){
			delete animatedDivs[div];
			setBackgroundPos(0,div);
			//console.log('stopAnimating::'+div);
		}
		
		// shim layer with setTimeout fallback
		window.requestAnimFrame = (function(){
		  return  window.requestAnimationFrame       ||
		          window.webkitRequestAnimationFrame ||
		          window.mozRequestAnimationFrame    ||
		          function( callback ){
		            window.setTimeout(callback, 1000 / 60);
		          };
		})();
		
		/*
		//stop it
		window.cancelRequestAnimFrame = ( function() {
		    return window.cancelAnimationFrame          ||
		        window.webkitCancelRequestAnimationFrame    ||
		        window.mozCancelRequestAnimationFrame       ||
		        window.oCancelRequestAnimationFrame     ||
		        window.msCancelRequestAnimationFrame        ||
		        clearTimeout
		} )();
*/

		// usage:
		// instead of setInterval(render, 16) ....

		function animloop(){
			requestAnimFrame(animloop);
			if (onEnterFrameNum % 5 == 0){
				for(var key in animatedDivs) {
				  render(key);
				}
			}
			onEnterFrameNum++;
		}
		
		function initAnimations(){
			animloop();
		}

		// place the rAF *before* the render() to assure as close to
		// 60fps with the setTimeout fallback.

		function render(div){
			var nextFrame = nextFrameCounts[div];
			if (nextFrameCounts[div] == undefined){
				nextFrame = nextFrameCounts[div] = 0;
			} else {
				nextFrame = nextFrame + loadedAnimDirection * loadedAnimOffset;
			}
			if (nextFrame >= loadedAnimMaxWidth){
				loadedAnimDirection = -1;
				nextFrame  += 2 * loadedAnimDirection * loadedAnimOffset;
			} else if (nextFrame<0){
				loadedAnimDirection = 1;
				nextFrame  += 2 * loadedAnimDirection * loadedAnimOffset;
			}
			setBackgroundPos(nextFrame,div);
		}
		
		function setBackgroundPos(nextFrame,div){
			var background_position = '-'+nextFrame+'px 0px';
			$(div).css({ 'background-position': background_position});
			nextFrameCounts[div] = nextFrame;
//			console.log('render::'+div+':set '+background_position+' is '+$(div).css('background-position'));
		}
