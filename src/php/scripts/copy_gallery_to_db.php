<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$title = 'Graffiti S.W.A.T.';
$GLOBALS["page"] = 'gallery';
require '../includes/fbheader.inc';
require '../db/dbconnect.php';
?>
	<div class="gallery_wrapper">
		Very long script. Take a seat. Take you time. Take a nap. Cuz you're gonna be here for a loooooongg... oh there it is. <br/>
		<?php
				//$albums = array('435342679812953','429774160369805','420817551265466','411894798824408','402502126430342','388597147820840','361194967227725');
				$albums = array('388597147820840','361194967227725');
				
				// 435342679812953 gallery VII
				// 429774160369805 gallery VI
				// 420817551265466 gallery V
				// 411894798824408 gallery IV
				// 402502126430342 gallery III
				// 388597147820840 gallery II
				// 361194967227725 gallery I
				
				for ($j = 0; $j < count($albums); $j++){
					$album_id = $albums[$j];
					echo '<br />Gallery '.(count($albums)-$j);
					//$photos = $facebook->api($album_id.'/photos?limit=1010','GET');
					$request = new FacebookRequest($session, 'GET', $album_id.'/photos?limit=1010');
					$photos = $request->execute()->getGraphObject()->asArray();
					pickupFotos($photos);
				}
				
				function pickupFotos($photos){
					global $facebook;
					if (array_key_exists("data",$photos)){
						$photos_content = $photos["data"];
						echo ' count: '.count($photos_content).'<br />';
						for ($i = 0; $i < count($photos_content); $i++){
							$photo = $photos_content[$i];
							if (isset($photo["picture"])){
								$fbid = $photo["id"];
								$created_time = $photo["created_time"];
								$created_time = join(' ',explode('T',$created_time));
								//echo ' created_time '.$created_time.' <br/>';
								//$uid = $photo["from"]["id"]; // use if from name
								$surface = 'null'; //later calby size							
								$caption = search('/[^"]*[^"]/', $photo["name"]);
								//echo 'caption '.$caption;
								$uid = search('/id=*[0-9]+/', $photo["name"]);
								$uid = $uid = ''?'xxx' : substr($uid,3);
								//echo ' uid '.$uid.' <br/>';
								$images = $photo["images"];
								$pic = $images[0];
								$path_720 = $photo["source"];
								$iconIndex = count($images)-3;
								$path_icon = $images[$iconIndex]["source"];
								$path = $pic["source"];
								$width = $pic["width"];
								$height = $pic["height"];
								//echo '<br/> '.$path;
								//echo '<br/> '.$path_720;
								//echo '<br/> '.$path_icon;
								//echo '<br/> ';
								switch ($width.'x'.$height) {
									case '1321x661':
										$surface = 'airport_shuttle';
										break;
									case '0x2048':
									case '2048x1336':
									case '2048x1339':
									case '1057x691':
									case '720x470':
									case '720x471':
										$surface = 'brickwall';
										break;
									case '1249x937':
										$surface = 'bundestag';
										break;
									case '1280x370':
									case '1280x370':
									case '2048x1012':
										$surface = 'container';
										//echo '<br />'.$surface.' - '.$width.'x'.$height.' <a href="'.$path.'" target="_blank" >image</a>';
										break;
									//case '1280x370':
									//	$surface = 'container_balloon';
									//	break;
									case '1624x496':
									case '2048x637':
										$surface = 'deck';
										break;
									case '624x510':
									case '2048x1673':
										$surface = 'electric';
										break;
									case '1403x603':
										$surface = 'falcon';
										break;
									case '512x384':
									case '2048x1536':
										$surface = 'hello';
										//echo '<br />'.$surface.' - '.$width.'x'.$height.' <a href="'.$path.'" target="_blank" >image</a>';
									//case '512x384':
									//	$surface = 'container_jrf';
									//	break;
										break;
									case '1552x534':
									case '1552x560':
										$surface = 'ny_train';
										break;
									case '2048x696':
									case '1552x530':
									case '1552x528':
										$surface = 'ny_train_2';
										break;
									case '1024x768':
										$surface = 'papa_crepe';
										break;
									case '1000x750':
									case '720x540':
										$surface = 'rooftop';
										break;
									case '720x356':
									case '771x382':
										$surface = 'swat';
										break;
									case '2048x756':
									case '852x315':
									case '720x266':
										$surface = 'timeline';
										break;
									case '2037x530':
									case '2048x531':
										$surface = 'tokyo';
										break;
									case '927x576':
									case '720x447':
									case '2048x1271':
										$surface = 'toytrain';
										break;
									//	break;
									case '2288x500':
										echo 'ori tube';
									case '2048x447':
										$surface = 'tube';
										break;
									case '1900x500':
										$surface = 'ubahn';
										//echo '<br />'.$surface.' - '.$width.'x'.$height.' <a href="'.$path.'" target="_blank" >image</a>';
									//case '1900x500':
									//	$surface = 'tram';
										break; 
									case '1028x716':
										$surface = 'wall';
										break;
									case '1578x2048':
									case '400x519':
										$surface = 'grenade';
										break;
									case '576x687':
									case '1717x2048':
										$surface = 'egg';
										break;
									case '1208x599':
										$surface = 'cardboard';
										break;
									case '1008x868':
										$surface = 'eastend';
										break;
									case 'xx':
									default:
										echo '<br />'.$fbid.'!!!!!'.$surface.' - '.$width.'x'.$height.' <a href="'.$path.'" target="_blank" >image</a>';
										
										//foreach ($photo["images"] as $key => $item){
										//		echo '<br/>null! key is '. $key.' contains '.$item["width"].'x'.$item["height"].' <a href="'.$item["source"].'" target="_blank" >image</a>';
										//}
								}
								//echo '<br />'.$surface.' - '.$width.'x'.$height.' path '.$path;
								if ($surface != 'null'){
									$response = saveGalleryEntry($fbid,$uid,$surface,$caption,null,null,$path,$path_720,$path_icon,$created_time);
									if (!querySuccess($response)){
										echo '<br />saveGalleryEntry:'.$response;
									}
								}

								analyzeLikes($photo);
							}
						}
					} else {
						echo 'ERROR!!! data not found:';
						print_r($photos);
					}
				} 
				
			function analyzeLikes($photo){
				global $facebook;
				if (array_key_exists("likes",$photo)){
					$likes = $photo["likes"]["data"];
					$likeCount = count($likes);
					if ($likeCount == 25){
						$fbid = $photo["id"];
						$like_request = $facebook->api($fbid.'/likes?limit=1200','GET');
						if (array_key_exists("data",$like_request)){
							$likes = $like_request["data"];
							saveLikes($photo,$likes);
							//die(' found all '.count($likes).' for id '.$fbid);
						}
					} else {
						saveLikes($photo,$likes);
					}
					
				}
			}
			
			
			function saveLikes($photo,$likes){
				for ($j = 0; $j < count($likes); $j++){
					$like = $likes[$j];	
					//echo '<br /> new like '.$like["id"].' likes '.$photo["id"];
					$response = saveLikeFromFB($photo["id"],$like["id"]);
					if (!querySuccess($response)){
						echo '<br />saveLike:'.$response;
					}
				}
			}
			
			
			function search($pattern, $subject){
				preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
				if ($matches!= 0 && count($matches) > 0 && $matches[0][1] > 0){
						return addslashes($matches[0][0]);
				} //else {print_r($matches);}
				return '';
			}
		?>
	</div>
	<script type="text/javascript">
		  function FBLike(id){
		  	FB.api(id+'/likes', 'post', {access_token:fBAccessToken}, function(response) {
  				console.log('like response '+response);
			});
		  	console.log('sent '+id+'/likes');
		  }
	</script>
<?php 
	require '../includes/footer.inc';
?>