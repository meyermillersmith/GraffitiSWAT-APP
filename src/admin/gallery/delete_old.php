<?php
//ini_set('display_errors', 1);
//error_reporting(E_ERROR | E_PARSE);
require '../../php/includes/fbheader.inc';
$status = 'failed';
$now = date( 'Y-m-d H:i:s', time() );
if ($fbuser) {
	$fbid = $user_profile['id'];
	$fbid = $user_profile['id'];
	require '../../php/db/dbconnect.php';
	require '../../php/lang/notification_lang.php';
	if (isAdmin($fbid)){
	 	$status = 'success::access granted::'.$fbid;
		$interval_months = 12;
		$limit = 10000;

		echo '<br/> <br/> ---------------- GALLERY ENTRIES ----------------- </br>';
		$total_entries = countOldUnpopularGalleryEntries($interval_months);
		$total_entries = isset($total_entries)? $total_entries['num'] : NULL;
		$old_gallery_entries = getOldUnpopularGalleryEntries($interval_months, $limit);
		$num_entries = count($old_gallery_entries);

		echo '<br/>deleting '.$num_entries.' gallery entries without votes, older than '.$interval_months.' months....</br>';

		for ($i = 0; $i < $num_entries; $i++) {
			//$entry_id =$old_gallery_entries[$i]['id'];
			removeGalleryEntryImage($old_gallery_entries[$i]);

		}
		echo '<br/>'.$num_entries.' gallery entries deleted of total '.$total_entries.'. </br>';
		if ($total_entries > $num_entries) echo '<br/> PRESS RELOAD TO DELETE THE REST.<br/><br/>';

		echo '<br/> <br/> ---------------- COLLABS (DISABLED)----------------- </br>';
		$interval_months = 0;

		$total_collabs = countOldCollabs($interval_months);
		$total_collabs = isset($total_collabs)? $total_collabs['num'] : NULL;
		$old_collabs = getOldCollabs($interval_months, $limit);
		$num_collabs = count($old_collabs);

		echo '<br/>deleting '.$num_collabs.' collabs untouched in more than '.$interval_months.' months....</br>';

		for ($j = 0; $j < $num_collabs; $j++) {
			removeOldCollab($old_collabs[$j]);
		}
		echo '<br/>'.$num_collabs.' collabs deleted of total '.$total_collabs.'. </br>';
		if ($total_collabs > $num_collabs) echo '<br/> PRESS RELOAD TO DELETE THE REST.<br/><br/>';

	 } else {
		$status = 'failed::no access granted::'.$fbid;
	 }
} else {
	$status = 'login::'.$_POST['entry_id'];
}
echo $status;
?>
