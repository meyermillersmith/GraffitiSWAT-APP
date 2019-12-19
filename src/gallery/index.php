<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
$title = 'S.W.A.T. - Gallery Index';
$GLOBALS["page"] = 'gallery';
require '../php/includes/header.inc';
?>
	<?php
		
		$leftGalleries = array();
		array_push($leftGalleries,getPickOfThe("DAY"));
		array_push($leftGalleries,getPickOfThe("WEEK"));
		array_push($leftGalleries,getPickOfThe("MONTH"));
		
		$mainGalleries = array();
		array_push($mainGalleries,getMainGalleryPick("created"));
		array_push($mainGalleries,getMainGalleryPick("likes"));
		array_push($mainGalleries,getMainGalleryPick("created",$fbid));

		$rightGalleries = getRandomGalleries(3);
		 
	?>
	<div id="index_wrapper" class="gallery_wrapper">
		<div id="sideContainerLeft" class="sideContainer"></div>
		<div id="mainContainer"></div>
		<div id="sideContainerRight" class="sideContainer"></div>
		<div id="galleryAdLeft">
			<script type="text/javascript"><!--
				google_ad_client = "ca-pub-5461937364848646";
				/* GS text gallery front page */
				google_ad_slot = "2341278110";
				google_ad_width = 180;
				google_ad_height = 150;
				//-->
				</script>
				<script type="text/javascript"
				src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		<div id="galleryAdRight">
			<script type="text/javascript"><!--
				google_ad_client = "ca-pub-5461937364848646";
				/* GS text gallery front page */
				google_ad_slot = "2341278110";
				google_ad_width = 180;
				google_ad_height = 150;
				//-->
				</script>
				<script type="text/javascript"
				src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
	</div>
	
	<script type="text/javascript">
		var unknownUser = '<?php echo $GLOBALS["TXT_DEFAULT_GALLERY_USER"];?>';
		var fbid = '<?php echo $fbid;?>';
		var user_name = '<?php if (!empty($name)) echo $name;?>';
	
		var leftGalleries = <?php echo json_encode($leftGalleries);?>;
		var mainGalleries = <?php echo json_encode($mainGalleries);?>;
		var rightGalleries = <?php echo json_encode($rightGalleries);?>;
		
		var label_best_1 =  "<?php echo $GLOBALS["TEXT_BEST_OF_1"];?>";
		var label_best_2 =  "<?php echo $GLOBALS["TEXT_BEST_OF_2"];?>";
		var label_best_3 =  "<?php echo $GLOBALS["TEXT_BEST_OF_3"];?>";
		var label_best_gallery =  "<?php echo $GLOBALS["TXT_BEST_OF_GALLERY"];?>";
		var label_latest_gallery =  "<?php echo $GLOBALS["TXT_LATEST_GALLERY"];?>";
		var label_user_gallery =  user_name+"<?php echo $GLOBALS["TXT_USER_GALLERY"];?>";
		
		if (initDone) initPage();
		
		function initPage(){
			addGallery("sideContainerLeft", "sideContainer", leftGalleries[0], "id", label_best_1, "pod");
			addGallery("sideContainerLeft", "sideContainer", leftGalleries[1], "id", label_best_2, "pow");
			addGallery("sideContainerLeft", "sideContainer", leftGalleries[2], "id", label_best_3, "pom");

			addGallery("mainContainer", "mainContainer", mainGalleries[0], "created", label_latest_gallery,"sortby");
			addGallery("mainContainer", "mainContainer", mainGalleries[2], fbid, label_user_gallery,"user");
			addGallery("mainContainer", "mainContainer", mainGalleries[1], "likes", label_best_gallery,"sortby");

			addGallery("sideContainerRight", "sideContainer", rightGalleries[0], "", "");
			addGallery("sideContainerRight", "sideContainer", rightGalleries[1], "", "");
			addGallery("sideContainerRight", "sideContainer", rightGalleries[2], "", "");
			
			$('#sideContainerRight').append($('#galleryAdRight'));
			$('#sideContainerLeft').append($('#galleryAdLeft'));
		}
		
	function addGallery(container, imageClass, images, key, label, type){
		image = images.length > 0? images[0] : null;
		if (image != null){
			var src = image == null? '' : (image["path_icon"] == null? image["path_720"] : image["path_icon"]);
			var imageHtml = '<div class="indexPreview">';
			var href;
			
			switch(container){
				case "sideContainerLeft":
					href = 'list.php?'+(image['level']?'level='+image['level']+'&' : '')+'&sortby=created';
					break;
				case "mainContainer":
					href = 'list.php?'+type+'='+key;
					break;
				case "sideContainerRight":
					href = 'list.php?surface='+image["galleryKey"];
					label = image["galleryTitle"] + ' Gallery';
					break;
				default:
					href = 'list.php';
			}
			
			imageHtml += '<a href="'+href+'" class="'+imageClass+'Pic" style="background-image: url('+toGalleryUrl(src)+')" >';
			
			switch(type){
				case "pod":
					imageHtml += '<span id="pickOfTheDay" class="picktag"></span>';
					break;
				case "pow":
					imageHtml += '<span id="pickOfTheWeek" class="picktag"></span>';
					break;
				case "pom":
					imageHtml += '<span id="pickOfTheMonth" class="picktag"></span>';
					break;
				case "poy":
					imageHtml += '<span id="pickOfTheYear" class="picktag"></span>';
					break;
				default:
					href = 'list.php';
			}
			
			imageHtml += '<span class="galleryLabel" >'+label+'</span>';
			imageHtml += '<span class="galleryLabel '+imageClass+'LabelBG" >&nbsp;</span>';
			
			var count = '>'+ (container != 'sideContainerLeft'? image['numItems']+' items' : image["likes"]+' likes');
			imageHtml += '<span class="galleryCount" >'+count+'</span>';
			imageHtml += '<span class="galleryCount '+imageClass+'LabelBG" >&nbsp;</span>';
			
			imageHtml += '</a>';
			
			if (container == "sideContainerLeft"){
				var username = image['username'];
				imageHtml += '<div id="username'+type+'" class="username">';
				if (username == null || username == ''){
					username = unknownUser;
					getUserName(image['user'],'username'+type,onGotUserNameForLink);
				}
				imageHtml += getUsernameLink(image['user'],username);
				imageHtml += '</div>';
			}
			
			imageHtml += '</div>';
			
			$('#'+container).append(imageHtml);
		}
	}
	
	function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}
	</script>
<?php 
	require '../php/includes/footer.inc';
?>