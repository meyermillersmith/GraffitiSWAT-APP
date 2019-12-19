<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
		xmlns:fb="http://ogp.me/ns/fb#"> 
	
	<head prefix="og: http://ogp.me/ns# object: http://ogp.me/ns/object#">
		<title>Like Graffiti S.W.A.T</title>
		<link rel="stylesheet" type="text/css" href="../../css/likeplugin.css" />
	</head>
	<body height="100px" width="200px" background-color>
		<div id="fb-root"></div><!-- required div tag -->
		<div id="fb-like" class="fb-like" data-href="<?php echo $_GET['url']; ?>" data-layout="button_count" data-show-faces="true" data-colorscheme="dark" data-font="lucida grande"></div>
		<script type="text/javascript">(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $_GET['appId']; ?>";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
	</body>
</html>