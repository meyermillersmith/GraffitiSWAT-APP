<?php ?>
	<script type="text/javascript">

	window.fbAsyncInit = function() {
		initFacebook();
	}

	function initFacebook(){
		if (!initDone){
			FB.init({
			      appId  : '',
			      version: 'v2.2',
			      status : true, // check login status
			      cookie : true, // enable cookies to allow the server to access the session
			      xfbml  : true  // parse XFBML
			    });
				initDone = true;
			}
		}

	(function(d, s, id){
	    var js, fjs = d.getElementsByTagName(s)[0];
	    if (d.getElementById(id)) {return;}
	    js = d.createElement(s); js.id = id;
	    js.src = "//connect.facebook.net/en_US/sdk.js";
	    fjs.parentNode.insertBefore(js, fjs);
	  }(document, 'script', 'facebook-jssdk'));
	</script>
