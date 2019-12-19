<?
$im = file_get_contents(urldecode($_GET["photo"]));
header('content-type: image/jpg');
echo $im;
?>