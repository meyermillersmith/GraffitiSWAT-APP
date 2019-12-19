<?php
$title = 'Graffiti S.W.A.T. Collaborations';
$GLOBALS["page"] = 'collabs';
require 'php/includes/header.inc';


$collabs = getUserOpenCollabs($fbid);

completeRequestIds();
checkRequestIds(true);
function matchRequestAndCollab($requestData, $request_collab_id, $full_request_id) {
	global $collabs;
	$found = false;
	for ($i = 0; $i < count($collabs); $i++){
		if ($collabs[$i]['id'] == $request_collab_id){
			$collabs[$i]['collab_request_id'] = $full_request_id;
			$found = true;
		}
	}
	return $found;
}

function completeRequestIds(){
	global $collabs, $fbid;
	for ($i = 0; $i < count($collabs); $i++){
		if ($collabs[$i]['request_id']){
			$collabs[$i]['collab_request_id'] = $collabs[$i]['request_id'].'_'.$fbid;
		}
	}
}
?>
	<div id="gallery_wrapper">
		</div>
		<div id='listMainContent'>
			<div id='listImages'>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	
		/**
		* IMAGES
		*/
		var jsonImages = <?php echo json_encode($collabs);?>;
		var unknownUser = '<?php echo $GLOBALS["TXT_DEFAULT_GALLERY_USER"];?>';
		urlToGallery = 'gallery/';
		assetpath = 'xxx';
		
		if (initDone) initPage();
		
		function initPage(){
			showCollabImages();
		}
		function showCollabImages(){
			console.log("collab.php showCollabImages ");
			for (var i = 0; i < jsonImages.length; i++){
				var image = jsonImages[i];
				var src = image["graffiti"];
				var surface = 'images/surfaces/'+image["surface"]+'/background.jpg';
				var imageHtml = '<div class="listImage" id="listImage'+image["id"]+'">';
				//imageHtml = '<span id="collabImage'+i+'" class="listImageCollab"'+getResizedImage(src)+' ></span>';
				imageHtml += '<a href="game.php?surface='+image["surface"]+'&collab_id='+image["id"]+(image["collab_request_id"]?'&collab_request_id='+image["collab_request_id"]:'')+'" class="listImagePic"'+getResizedImage(src)+' >';
				imageHtml += '</a>';
				imageHtml +=  '<div class="listUsername delete_button" id="delete_button" style="color:red;cursor:pointer;" data-id="'+image["id"]+'" data-collab-request-id="'+image["collab_request_id"]+'">';
				imageHtml +=  'X Delete';
				imageHtml +=  '</div>';
				imageHtml += '<div id="image'+i+'" class="listUsername">';
				var username = image["username"];
				if (username == null || username == ''){
					username = image['collaborator'];//unknownUser;
					getUserName(image['collaborator'],'image'+i,onGotUserNameForLink);
				}
				imageHtml += getUsernameLink(image['collaborator'], username);
				imageHtml += '</div>';
				imageHtml += '</div>';
				
				$('#listImages').append(imageHtml);
				if (i > 0 && i%10 == 0){
				  $('#listImages').append(getAdvertHTML((i/10)+''));
				}
				
				$('.delete_button').click(function() {
					console.log("collab.php >>>click! ");
					remove($(this).attr('data-id'), $(this).attr('data-collab-request-id'));
				});
			}
			checkCollabsEmpty();
		}
		function getAdvertHTML(adverId){
			
			var htmlContent = '<div id="advert'+adverId+'" class="listBetweenAd" >';
			htmlContent += '<iframe id="advert-iframe'+adverId+'" frameborder="0" height="90px" width="728px" scrolling="no" allowTransparency="true" src="<?php echo $asset_path; ?>ads/adwrapper.php?ad_slot=1503673317&w=728&h=90"></iframe> ';
			htmlContent += '</div>';
			console.log('getAdvertHTML::htmlContent '+htmlContent);
			return htmlContent;
		}

		function checkCollabsEmpty(){
			if (jsonImages.length == 0){
				$('#listImages').append('<div style="padding:0px 20px 0px 20px;"><?php echo $GLOBALS["TXT_NO_COLLABS"]?></div>');
			}
		}

		function getResizedImage(src){
			var imageHtml = 'style="';
			imageHtml += 'background-image: url('+src+');';	
			imageHtml += '-moz-background-size:180px 175px;';		
			imageHtml += 'background-size:180px 175px;"';	
			return imageHtml;
		}
		
		function remove(id, collab_request_id){
			console.log("collab.php remove id "+id+" collab_request_id "+collab_request_id);
			highlightImage(id, true);
			var confirmed = confirm('Delete collab?');
			if (confirmed){
				var url = "php/action/secure/coLAA8n0.php";
				var request = {url: url, type:'POST', data:'collab_id='+id+(collab_request_id?'&collab_request_id='+collab_request_id:''),  success:onRemoved, error:onRemoveFailed};
				$.ajax(request);
			} else highlightImage(id, false);
		}
		
		function onRemoved(data){
			console.log("collab.php onRemoved data>"+data);
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
					removeImage(realImageId);
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

		function removeImage(realImageId){
			$('#listImage'+realImageId).remove();
			for (var i = 0; i < jsonImages.length; i++){
				var image = jsonImages[i];
				if (image["id"] == realImageId){
					jsonImages.splice(i,1); 
				}
			}
			checkCollabsEmpty();
		}
	</script>
<?php 
	require 'php/includes/footer.inc';
?>