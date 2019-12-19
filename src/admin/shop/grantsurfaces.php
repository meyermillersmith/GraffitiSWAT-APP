<?php
require '../../php/includes/fbheader.inc';
$status = 'failed';
if ($fbuser) {
	$fbid = $user_profile['id'];
	require '../../php/db/dbconnect.php';
	if (isAdmin($fbid)){
		$status = 'success:access granted';
		 
		?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
        
        <title>Grant Item</title>
    
    
		<script type="text/javascript" src="../../js/external/jquery-1.2.5.min.js"></script>
		<script type="text/javascript">
			  function setAction(){
				  	var variables = $("#variables").serialize();
				  	var variablesOK = $("#user").val() != '' && $("#items").val() != '';
				  	if (variablesOK){
				   		//$("#upload").attr("action", "../../php/action/secure/k27bo0HjWx.php?" + variables);
				  	} else {
				   		alert('Please enter Facebook User ID and at least one Surface Key');
				  	}
				    return variablesOK; //;
			  }
		</script>
    </head>
    
    <body>
    
    
    <h1>
       Upload form
    </h1>
    <form id="variables" enctype="multipart/form-data" method="get" onsubmit="return false;">
    </form>
    
    <form id="upload" action="../php/action/secure/pxTrTxi88.php" enctype="multipart/form-data" method="post" onsubmit="return setAction();">
		<p>
   		 <label for="user">Facebook User ID</label>
   		 <input id='user' name='user' type='text'>
   		</p>
		<p>
   		 <label for="items">Surface Keys</label>
   		 <input id='items' name='items' type='text'>
   		</p>
                
        <p>
            <input id="submit" type="submit" name="submit" value="Grant!">
        </p>
    
    </form>
    
    </body>

</html>
<?php
	 } else {
		$status = 'failed::no access granted::'.$fbid;
	 }
} else {
	$status = 'login::'.$_POST['entry_id'];
}
echo $status;
?>