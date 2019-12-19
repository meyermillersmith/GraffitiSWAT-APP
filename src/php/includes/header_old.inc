<?php
session_start();
header('P3P: CP="CAO PSA OUR"');
if (!isset($using_error_handler)) {
	require __DIR__.'/errorhandler.inc';
}

require __DIR__.'/fbheader.inc';
use Facebook\FacebookRedirectLoginHelper;

if (!isset($connection)) {
	require __DIR__.'/../../php/db/dbconnect.php';
}

$asset_path = $GLOBALS["page"] == 'gallery' || $GLOBALS["page"] == 'competition' ?"../":"";
$protocol = isset($_SERVER['HTTPS'])? 'https':'http';
$language = 'en';

// Login or logout url will be needed depending on current user state.
if (isset($fbuser) && isset($user_profile['id']) && $user_profile['id']!='') {
	
	if ($GLOBALS["page"] == 'index' && isset($_GET["image"])){
		$redirectPage = "gallery/image.php";
		$redirectParams = "image=".$_GET["image"];
		if (isset($_GET["params"])){
			$redirectParams .= '&'.urldecode($_GET["params"]);
		}
		echo "<script type=\"text/javascript\">location.href = '".$redirectPage.'?'.$redirectParams."';</script>";
		die();
	}
  
	if ($GLOBALS["page"] == 'game'){
		if (isset($_GET["surface"])){
			$surfaceid = $_GET["surface"];
		} else {
			 echo "<script type=\"text/javascript\">location.href = 'index.php';</script>"; 
			 die();
		}
  }
	
  $fbid = $user_profile['id'];
  $name = $user_profile['name'];
  $first_name = $user_profile['first_name'];
  $locale = $user_profile['locale'];
  $locale_explode = explode("_", $locale);
  $lang = $locale_explode[0];
  $language = $lang == 'es' ? 'es' : 'en';
  
  if ($GLOBALS["page"] == 'index' || (isset($_GET['autolike']) && $_GET['autolike'] == "1")){
  	swat_login($fbid, $name, false, $lang);
  }
} else {
	if ($GLOBALS["page"] == 'index'){
	//if (!isset($_GET["cookieIssue"]) || $_GET["cookieIssue"] != "true"){
		$redirectParams = "";
		$redirectPage = "";
		if (isset($_GET["image"])){
			$redirectPage = "gallery/image.php";
			$redirectParams = "image=".$_GET["image"];
			if (isset($_GET["params"])){
				$redirectParams .= '&'.urldecode($_GET["params"]);
			}
		} else {
			if (isset($_GET["source"])) $redirectParams = 'source='.$_GET["source"];
			$redirectParams .= isset($_GET["collab_id"])?'&collab_id='.$_GET["collab_id"]:('');
			$redirectParams .= isset($_GET["collab_request_id"])?'&collab_request_id='.$_GET["collab_request_id"]:('');
		}
		$redirectScript = "<script type=\"text/javascript\">top.location.href = '".getLoginUrl($redirectParams,$redirectPage)."';</script>"; 
		userNullRedirect($redirectScript);
	//}
	} else if ($GLOBALS["page"] == 'gallery') {
		 $fbid = '-1';
	} else {
		 $redirectScript = "<script type=\"text/javascript\">location.href = 'index.php".($GLOBALS["page"] == 'game'?"?source=game&lostSession=true":"")."';</script>";
		 userNullRedirect($redirectScript);
	} 
}

if ($GLOBALS["page"] == 'index' && (isset($_GET["collab_id"]) || !(isset($_GET["source"]) && $_GET["source"] == 'game') ) && !isset($_GET["fb_notification"])){
	$response =  isset($_GET["collab_id"])? getCollab($fbid,$_GET["collab_id"]) : getSavedGraffiti($fbid);
	if ($response['status'] == 'success'){
		$redirectParams = $response['surface'];
		$redirectParams .= "&collab_id=".$response['id'];
		$redirectParams .= isset($_GET["collab_request_id"])?'&collab_request_id='.$_GET["collab_request_id"]:('');
	 	$redirectScript = "<script type=\"text/javascript\">location.href = '".$url.$redirectParams."';</script>"; 
	 	echo $redirectScript;
		die();
	}
}

function userNullRedirect($redirectScript){
	$url_link = $_SERVER['QUERY_STRING'];
	$_SESSION['login_count'] = isset($_SESSION['login_count'])? $_SESSION['login_count'] + 1 : 0; 
	if ($_SESSION['login_count'] > 0){
		$fbuser = empty($fbuser)? '' : $fbuser;
		$user_profile['id'] = empty($user_profile['id'])? '' : $user_profile['id'];
		logToFile($user_profile['id'],'Cannot find facebook user! $fbuser '.$fbuser.' $user_profile(id) '.$user_profile['id'].' $session(id) '.$_SESSION['user_profile']['id'].' RETRIES:'.$_SESSION['login_count'].' page '.$GLOBALS["page"].' params '.$url_link);
	}
	echo $redirectScript;
	die();
}


function getLoginUrl($redirectParams = '', $redirectPage = ''){
	global $facebook,$protocol;
	$app_url = 'https://apps.facebook.com/'.$GLOBALS["appAlias"].'/'.$redirectPage.'?'.$redirectParams;
	$helper = new FacebookRedirectLoginHelper($app_url );
	$loginUrl = $helper->getLoginUrl(array("publish_actions"));
	return $loginUrl;
}
//maintance script!
//die("<script type=\"text/javascript\">location.href = '".$asset_path."maintenance.php';</script>");

// if ($fbid == '100002572844577') $fbid = '100003970975122';
$numCollabs = countUserOpenCollabs($fbid);
$numCollabs = isset($numCollabs['num'])?intval($numCollabs['num']) : 0;
$_SESSION['login_count'] = 0;
require __DIR__.'/../lang/lang_'.$language.'.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" 
		xmlns:og="http://opengraphprotocol.org/schema/" 
		xmlns:fb="http://ogp.me/ns/fb#"> 
	
	<head prefix="og: http://ogp.me/ns# object: http://ogp.me/ns/object#">
			
		<title><?php echo $title;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="graffiti, online graffiti, vandalsquad, graffiti s.w.a.t., graffiti swat, facebook, facebook app, graffiti gallery, artwork, aerosol, art, airbrush, virtual, paint" />
		<meta name="description" content="Online Graffiti Tool. Bomb freight containers, the Berlin U-Bahn, the London Underground, classic NY trains and more. Submit your graffiti to the SWAT Gallery to compete for the Best of the Day / Week and Month awards." />
		
		
		<link rel="stylesheet" type="text/css" href="<?php echo $asset_path;?>css/styles.css" />

		<?php 
			if ($GLOBALS["page"] == 'index'){
		?>
	
			<script type="text/javascript" src="js/external/jquery-1.2.5.min.js"></script>
			<script type="text/javascript" src="js/external/jquery-md5.js"></script>
			<script type="text/javascript" src="js/pulldown.js"></script>
			<link rel="stylesheet" type="text/css" href="css/pulldown.css" />
		<?php 
			} else if ($GLOBALS["page"] == 'gallery'){
			if (!isset($metaTags['image'])){
				$metaTags = array ('image' => $protocol.'://graffiti.mee-mail.com/gallery/icon.png', 'type' => 'website', 'description' => 'Come and see the best and the newest Graffiti by our Graffiti S.W.A.T users. All Graffiti on this page have been created using Graffiti S.W.A.T (facebook.com/GraffitiSWAT).');
			} else {
				$metaTags['type'] = $GLOBALS["appAlias"].':graffiti';
				if (isset($metaTags['image_missing_server'])) {
					$metaTags['image'] = $GLOBALS["server"].$metaTags['image_missing_server'];
				}
				$metaTags['image'] = $GLOBALS["server"].'php/utils/show_small_graffiti.php?photo='.urlencode($metaTags['image']);
			}
			if (!isset($metaTags['title'])) $metaTags['title'] = 'S.W.A.T. Gallery';
			if (!isset($metaTags['author'])) $metaTags['author'] = '0';
		?>
			<!--  fb meta tags -->
			<meta property="og:image" content="<?php echo $metaTags['image'];?>" />
			<meta property="og:title" content="<?php echo $metaTags['title'];?>" />
			<meta property="og:site_name" content="Graffiti S.W.A.T."/>
			<meta property="og:type" content="<?php echo $metaTags['type'];?>" />
			<meta property="fb:admins" content="639681407,100002572844577" />
			<meta property="fb:app_id" content="<?php echo $GLOBALS["appId"];?>" />
			<meta property="og:description" content="<?php echo $metaTags['description'];?>" />
		<?php
			if ($metaTags['type'] == $GLOBALS["appAlias"].':graffiti'){ 
		?>
			<meta property="<?php echo $GLOBALS["appAlias"];?>:author" content="<?php echo $metaTags['author'];?>" />
		<?php
			}
		?>
			<meta name="description" content="<?php echo $metaTags['description'];?>" />
			<link rel="stylesheet" type="text/css" href="css/gallery.css" />
			<link rel="image_src" href="<?php echo $metaTags['image'];?>" />

		 	<script type="text/javascript" src="js/gallery.js"></script>
			<script type="text/javascript" src="<?php echo $protocol;?>://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
			<script type="text/javascript" src="../js/pulldown.js"></script>
			<link rel="stylesheet" type="text/css" href="../css/pulldown.css" />
		<?php 
			} else if ($GLOBALS["page"] == 'collabs'){
		?>
			<link rel="stylesheet" type="text/css" href="gallery/css/gallery.css" />
		 	<script type="text/javascript" src="gallery/js/gallery.js"></script>
			<script type="text/javascript" src="<?php echo $protocol;?>://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<?php 
			} else if ($GLOBALS["page"] == 'competition'){
		?>
			<style type="text/css">
  				body {
    				background-color: #000 
    			}
 			</style>
		<?php 
			} else if ($GLOBALS["page"] == 'game'){
				if (isset($_GET['collab_request_id'])){
					deleteAppRequest($_GET["collab_request_id"]);
					removeRequestID($_GET["collab_id"],$_GET["collab_request_id"]);
				}
				$albumData = getUserAlbum($fbid);
				$album_id = $albumData["album_id"]; 
				$album_count = $albumData["album_count"];
		?>
		<link rel="stylesheet" type="text/css" href="css/flash_embed.css" />
		<!--[if lte IE 6]>
		<link rel="stylesheet" type="text/css" href="css/flash_embed_ie6.css" />
		<![endif]-->
		<script type="text/javascript" src="<?php echo $protocol;?>://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		
		<script type="text/javascript" src="js/swfaddress.js"></script>
		<script type="text/javascript" src="js/swfobject.js"></script>
		<script type="text/javascript" src="js/swfmacmousewheel.js"></script>

		
		
			<!--	SWF object and fullscreen functions	-->
		<script type="text/javascript">
			var flashvars = {};
			flashvars.assetsSrc = "data/<?php  echo $language; ?>/assets.xml";
			flashvars.title = escape(document.title);
			flashvars.fbid  = "<?php  echo $fbid; ?>"; 
			flashvars.name  = "<?php  echo $name; ?>"; 
			flashvars.first_name  = "<?php  echo $first_name; ?>";
			flashvars.appid  = "<?php  echo $GLOBALS['appId']; ?>";
			flashvars.appalias  = "<?php  echo $GLOBALS['appAlias']; ?>";
			flashvars.appserver  = "<?php  echo $GLOBALS['server']; ?>";
			flashvars.surfaceid  = "<?php  echo $surfaceid; ?>";
			flashvars.collabid  = "<?php  echo isset($_GET['collab_id'])?$_GET['collab_id']:''; ?>";
			flashvars.collab_request_id  = "<?php  echo isset($_GET['collab_request_id'])?$_GET['collab_request_id']:''; ?>";
			flashvars.showBanner  = "<?php echo getShowBanner($fbid); ?>";
			flashvars.album_id  = "<?php  echo $album_id; ?>";
			flashvars.album_count  = "<?php  echo $album_count; ?>";
			
			var params = {};
			params.bgcolor = "#000000";
			params.wmode = "opaque";
			params.allowFullscreen = "true";
			params.allowScriptAccess = "always";
			
			var attributes = {};
			attributes.id = "swfContainer";
			attributes.name = "swfContainer";
		
			swfobject.embedSWF("flash/main.swf?v=4", "swfContainer", "100%", "100%", "10.2", "flash/expressInstall.swf", flashvars, params, attributes,onSwfLoaded);
			
			function onSwfLoaded(event){
				if (event.success) {
					swfmacmousewheel.registerObject(event.id);
					swfmacmousewheel.setWMode('window');
					swfmacmousewheel.setPreventDefault(false);
				}
			}

			window.onbeforeunload = confirmExit;
			function confirmExit(){
				return "<?php echo $GLOBALS["PROMPT.GAME.STAY_ON_PAGE"]; ?>";
			}
		</script>
		<?php 
			}
		?>		
		<script type="text/javascript" src="<?php echo $asset_path;?>js/transitions.js"></script>
		<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-28975922-1']);
			  _gaq.push(['_trackPageview']);
			
			  (function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			  })();
		</script>

		<script type="text/javascript">
		
			var fBAccessToken = "<?php echo $_SESSION['fb_token']?>";
			
			var initDone = false;


			window.fbAsyncInit = function() {
				initFacebook();
				
			}
			function initFacebook(){
				if (!initDone){
					FB.init({
						  appId  : <?php echo $GLOBALS["appId"];?>,
						  version: 'v2.8',
						  status : true, // check login status
						  cookie : true, // enable cookies to allow the server to access the session
						  xfbml  : true  // parse XFBML
					});
					initDone = true;
					initPage();
				}
			}
			
			function initPage(){
				//override;
				console.log("override");
			}
				
			(function(d, s, id){
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js";
				fjs.parentNode.insertBefore(js, fjs);
			  }(document, 'script', 'facebook-jssdk'));

			function inviteFriends() {
				initFacebook();
				FB.ui(	{	
							method: 'apprequests', 
							message: 'Create and share realistic graffiti.',
							filters: ['app_non_users']
						},
						function(response,fail)
						{
						}
				);   
			}

			function earnCredits() {
				initFacebook();
				FB.ui(	{
						    method: 'pay',
						    action: 'earn_credits'
						},
						function(response,fail)
						{
						}
				);
			}

			function subscribe() {
				initFacebook();
				var obj = {
					    method: 'pay',
					    action: 'create_subscription',
					    product: 'https://projects.lessrain.net/public/graffiti/fb_obs/en/subscription_dev.html'
					     };
				FB.ui(obj, function(response,fail)
						{
							console.log('response '+response+' fail '+fail);
							if (response){
								console.log('subscription_id '+response.subscription_id+' status '+response.status);
							}
						});
			}
		</script>
	</head>
	<body>
	
		<div id="fb-root"></div><!-- required div tag -->
		<script type="text/javascript">
		</script>
		<div id="headerWrapper">
			<div class="header">
				<div class="banner">
					<div class="image_holder"><!-- -->
						<script type="text/javascript"><!--
						google_ad_client = "ca-pub-5461937364848646";
						/* GS Leaderboard */
						google_ad_slot = "8939541713";
						google_ad_width = 728;
						google_ad_height = 90;
						//-->
						</script>
						<script type="text/javascript"
						src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
						</script>
					</div>
				</div>
				<div class="navigation">
					<div class="top_mail">
						<div class="top_mail_content">
							<ul>
								<li>
									<a href="<?php echo $asset_path;?>index.php?source=<?php echo $GLOBALS["page"];?>"<?php if ($GLOBALS["page"] == 'index'){ echo ' class="active"';}?>>
										<?php echo $GLOBALS["MENU.MAKE"];?>
									</a>
								</li>
								<li>
									<a href="<?php echo $asset_path;?>gallery" title="Gallery"<?php if ($GLOBALS["page"] == 'gallery'){ echo ' class="active"';}?>>
										<?php echo $GLOBALS["MENU.GALLERY"];?>
									</a>
								</li>
<!--
								<li>
									<a href="<?php echo $asset_path;?>collabs.php" title="Collabs" <?php if ($GLOBALS["page"] == 'collabs'){ echo ' class="active"';} ?>>
										<?php echo $GLOBALS["MENU.COLLABS"];?> <?php if ($numCollabs > 0){ echo '('.$numCollabs.')';} ?> 
									</a>
								</li>
-->
								<li>
									<a class="mails" href="<?php echo $protocol;?>://www.facebook.com/GraffitiSWAT" title="Wall" target="_top">
										<?php echo $GLOBALS["MENU.WALL"];?>
									</a>
								</li>
							</ul>
							
						</div>
					</div>
				</div>

			</div>
			<div style="clear: both;"></div>
			<div class="line_seperator"></div>

		</div> <!-- end header wrapper-->
		
		<div id="fb-invite">
			<!--  <a id="fb-credits-link" href="javascript:subscribe();"><?php echo $GLOBALS["INDEX.EARN.CREDITS"];?></a>-->
			<a id="fb-credits-link" href="javascript:earnCredits();"><?php echo $GLOBALS["INDEX.EARN.CREDITS"];?></a>
		</div>
		
		 <div id="fb-like" class="fb-like" >
			<iframe id="fb-like-iframe" frameborder="0" height="20px" width="100px" scrolling="no" allowTransparency="true" class="fb-like" src="<?php echo $asset_path; ?>php/facebook/likeplugin.php?appId=<?php echo $GLOBALS["appId"]; ?>&url=https://www.facebook.com/GraffitiSWAT?"></iframe> 
		</div>
