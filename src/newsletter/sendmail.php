<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="en" />
		<title>L.R.P.D. Announcement / Send Mail</title>
	</head>

	<body>
<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	require 'mailbody.php.inc';
	require 'mailadresses.php.inc';
	
    $email = 'Graf S.W.A.T. <graffitiswat@googlemail.com>'; //$_POST["email"];
    $from = 'Graf S.W.A.T. <graffitiswat@googlemail.com>'; //$_POST["from"];
    $subject = 'L.R.P.D. Announcement'; //$_POST["subject"];
    
    
    for ($i = 0; $i < $total; $i++) {
	    
    	$headers = "From: " . $from . "\r\n";
	    $headers .= "Reply-To: ". $email . "\r\n";
	    $headers .= "Bcc: ". strip_tags($addresses[$i]) . "\r\n";
	    $headers .= "MIME-Version: 1.0\r\n";
	    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	    
    	$ok = mail($email, $subject, $mailBody, $headers);
	    echo $i.'. '.$ok.' -> '.$addresses[$i].'<br/>';
	}
    
?>

	</body>
</html>