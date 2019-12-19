<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://www.facebook.com/2008/fbml">
	
	<head>
		<title>Share from Graffiti S.W.A.T</title>
		 <STYLE type="text/css">
			* {
				outline: none;
				-moz-outline-style: none;
				border: none;
				margin: 0;
				padding: 0;
			}
			
			body {
				margin:0; padding:0; border:0;
			}
 		</STYLE>
	</head>
	<body height="20px" width="60px" background-color>
		<a name="fb_share" id="fb_share" type="button" share_url="http://apps.facebook.com/graffitiswat/gallery/image.php?<?php echo $_GET['imageParams'];?>" ></a>
		<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
	</body>
</html>