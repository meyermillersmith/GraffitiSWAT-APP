<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'SimpleImage.php';
//$im = file_get_contents(urldecode($_GET["photo"]));

$original = new SimpleImage;
$load_successfull = $original->loadWithFileCheck(urldecode($_GET["photo"]));
header('content-type: image/jpg');
if ($load_successfull){
	showCopy($original, 300, 300, 'path_720');
} else {
	$im = file_get_contents(urldecode($_GET["photo"]));
	echo $im;
}




function showCopy($original, $targetW, $targetH, $purpose){
		
	$image_width = $original->getWidth();
	$image_height = $original->getHeight();
		
	if ($image_height / $image_width < 0.4) {
		$original->resizePortion($targetH * (1/0.4), $targetH, $image_height * (1/0.4), $image_height);
		
	} else if ($image_width > $targetW || ($targetH > 0 && $image_height > $targetH )){
		$w_offset = $image_width - $targetW;
		$h_offset = $image_height - $targetH;

		if($targetH > 0 && $h_offset > $w_offset){
			$original->resizeToHeight($targetH);
		} else {
			$original->resizeToWidth($targetW);
		}


	} 
	$original->output();
}
?>