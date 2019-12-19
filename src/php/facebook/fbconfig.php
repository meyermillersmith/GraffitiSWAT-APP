<?php
if (strrpos($_SERVER['SERVER_NAME'],"graffiti.mee-mail.com") !== false){	
	$GLOBALS["appId"] = '139908232778316';
	$GLOBALS["appAlias"] = 'graffitiswat';
	$GLOBALS["secret"] = 'ae3f8b513d1fd144ac476e80f6475c20';
	$GLOBALS["server"] = 'https://graffiti.mee-mail.com/';
} else {
	$GLOBALS["appId"] = '173839762716148';
	$GLOBALS["appAlias"] = 'graffitiswat_dev';
	$GLOBALS["secret"] = 'd75e7c8d075befe9dbf35431f1e43a2e';
	$GLOBALS["server"] = 'https://projects.meyermillersmith.com/graffiti/';
}
?>