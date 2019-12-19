/**
**	 GLOBAL GALLERY FUNCTIONS
*/
var nameMap = new Array();
var requestedNames = new Array();
var userAgent = navigator.userAgent.toLowerCase();
var assetpath = '../';
var urlToGallery = '';
var currentAdIndex = 0;

function getAdvertHTML(asset_path,css,adSlot,w,h,callback){
	callback = callback||'';
	currentAdIndex++;
	var adverId = '6816557265'+currentAdIndex;
	var htmlContent = '<div id="advert'+adverId+'" class="'+css+'" >';
	htmlContent += '<iframe id="advert-iframe'+adverId+'" frameborder="0" height="'+h+'px" width="'+w+'px" scrolling="no" allowTransparency="true" src="'+asset_path+'ads/adwrapper.php?ad_slot='+adSlot+'&w='+w+'&h='+h+'" onload="'+callback+'"></iframe> ';
	htmlContent += '</div>';
	return htmlContent;
}
 
// Figure out what browser is being used
browser = {
	version: (userAgent.match( /.+(?:rv|it|ra|ie|me)[\/: ]([\d.]+)/ ) || [])[1],
	chrome: /chrome/.test( userAgent ),
	safari: /webkit/.test( userAgent ) && !/chrome/.test( userAgent ),
	opera: /opera/.test( userAgent ),
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
};

function getUserName(uid,imageDivId,onGotUserNameFunction){
	//console.log('getUserName::'+uid+', write into '+imageDivId+' name known '+nameMap[uid]);
	if (nameMap[uid] && nameMap[uid]  !=''){
		onGotUserNameFunction(uid,imageDivId);
	} else {
		getUserNameFromFB(uid,imageDivId,onGotUserNameFunction);
	}
}


function getUserNameFromFB(uid,imageDivId,onGotUserNameFunction){
	var myNameIsOnTheList = addToUsernameRequestList(uid,imageDivId,onGotUserNameFunction);
	//console.log('getUserNameFromFB::'+uid+', write into '+imageDivId+' myNameIsOnTheList '+myNameIsOnTheList);
	if (!myNameIsOnTheList){
		FB.api(uid+'?fields=id,name', "GET" , {access_token:fBAccessToken}, function(response) {
			if (response['name'] != null && nameMap[uid] != response['name']){
				//console.log('getUserNameFromFB::'+uid+', name '+nameMap[uid]+' requestedNames[uid].length '+requestedNames[uid].length);
				nameMap[uid] = response['name'];
				for (var i = 0; i < requestedNames[uid].length; i++){
					//console.log('getUserNameFromFB::onGotUserNameFunction'+uid+' i= '+i);
					var requesterObject = requestedNames[uid][i];
					requesterObject.onGotUserNameFunction(uid,requesterObject.imageDivId);
				}
				saveUserName(uid);
			}
		});
	}
}

function addToUsernameRequestList(uid,imageDivId,onGotUserNameFunction){
	var myNameIsOnTheList = requestedNames[uid] != null;
	
	var listObject = new Object();
	listObject.imageDivId = imageDivId;
	listObject.onGotUserNameFunction = onGotUserNameFunction;
	
	if (myNameIsOnTheList){
		requestedNames[uid].push(listObject);
	} else {
		requestedNames[uid] = [listObject];
	}
	return myNameIsOnTheList;
}

function saveUserName(uid){
	var url = assetpath+"php/action/secure/rok6B007lP.php";
	var request = {url: url, type:'POST', data:'uid='+uid};
	$.ajax(request);
}

		
function onGotUserNameForLink(uid,imageDivId){
	$('#'+imageDivId).html(getUsernameLink(uid, nameMap[uid]));
}
		
function getUsernameLink(uid, username, css){
//	console.log('getUsernameLink '+uid+' username '+username+' css '+css+' urlToGallery '+urlToGallery);
	css = css||"username";
	var nameHTML = '';
	var nameParts = username.split(' ');
	nameHTML += '<a href="'+urlToGallery+'list.php?user='+uid+'" class="'+css+'">';
	for(var j=0; j<nameParts.length;j++){
		nameHTML += '<span class="listUsernamePart">'+(j > 0?'&nbsp;':'&gt;')+nameParts[j]+'</span>';
	}
	nameHTML += '</a>';
	return nameHTML;
}



function getPickTag(level,purpose){
	var size = "";
	var type = "";
	
	switch(purpose){
		case "detailed":
			type = 'Detailed';
			break;
		case "small":
			size = 'Small';
			type = 'Small';
			break;
	}
	
	var imageHtml = '';
	switch(level){
		case "1":
			imageHtml = '<span id="pickOfTheDay'+size+'" class="picktag'+type+'"></span>';
			break;
		case "2":
			imageHtml = '<span id="pickOfTheWeek'+size+'" class="picktag'+type+'"></span>';
			break;
		case "3":
			imageHtml = '<span id="pickOfTheMonth'+size+'" class="picktag'+type+'"></span>';
			break;
		case "4":
			imageHtml = '<span id="pickOfTheYear'+size+'" class="picktag'+type+'"></span>';
			break;
	}
	return imageHtml;
}
		
function toGalleryUrl(url){
	if (url.indexOf("http://") != 0){
		url = assetpath + url;
	}
	return url;
}

function cssPxToNum(cssNumString){
	cssNumString = cssNumString ? cssNumString : '';
	var splitted = cssNumString.split('px');
	return Number(splitted[0]);
}


function getDisplayDate(datetime){
	var date = datetime.split(' ')[0];
	var dateParts = date.split('-');
	dateParts.reverse();
	return dateParts.join('.');
}


function share(fbid, artist, logoSrc, mLink){
	var mName = 'From the S.W.A.T Gallery';
	var actionName = 'S.W.A.T Gallery';
	var mCaption = artist;
	var mDesc = 'Create and share realistic graffiti with Graffiti S.W.A.T.';
	var targetID = fbid;
	var postObj = {
			method: 'feed',
			from: targetID,
		    /*show_error: 'true',*/
			link: mLink,
			picture: logoSrc,
			name: mName,
			caption: mCaption,
			description: mDesc,
			actions: [
				{ name: actionName, link: mLink }
			]
	};
	
	FB.ui(postObj, 
		function(response) {
		}
	);
}
function encrypt(str, key) {
	key = key||'%key&';
	var result = '';
	for (var i = 0; i < str.length; i++) {
		var char = str.substr(i, 1);
		var keychar = key.substr((i % key.length) - 1, 1);
		var ordChar = char.charCodeAt(0);
		var ordKeychar = keychar.charCodeAt(0);
		var sum = ordChar + ordKeychar;
		char = String.fromCharCode(sum);
		result = result + char;
	}
	return result;
}