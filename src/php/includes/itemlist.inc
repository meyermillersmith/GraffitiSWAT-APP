<?php
						$items = getSurfaces($fbid,false,false,$surfaceType);
						$items = array_reverse($items);

						$cols = 4;
						$rows = 3;
						$itemsPerPage = ($cols*$rows) - 2;
						$pageNum = ceil( sizeof($items)/ $itemsPerPage );
						$totalThumbs = $pageNum * $cols * $rows;

						$n = 0;
						$i = 0;
						for($n = 0; $n < $totalThumbs; ++$n) {

							// Page
							if ($n % ($cols * $rows) == 0) {
								if ($n>0) {
									// End page
										?>
										</div><!-- EOF row -->
									</div><!-- EOF page-->
									<?php
								}
								// Start page
								?>
								<div class="scrollableScreen">
									<div class="thumbRow">
							<?php
							}

							// Row
							else if ($n % $cols == 0) {
								if ($n>0) {
									// End row
									?>
									</div><!-- EOF row-->
									<?php
								}
								// Start row
								?>
								<div class="thumbRow">
								<?php
							}

							$currentRow = floor( ($n % ($cols * $rows)) / $cols);
							$currentCol = $n % $cols;

							// Cols
							if ($currentCol==0 && $currentRow==0) {
								// Page top left
								?>
								<div name="thumb" class="thumbnail"></div>
								<?php
							}
							else if ($currentCol==3 && $currentRow==2) {
								// Page bottom right
								?>
								<div name="thumb" class="thumbnail"></div>
								<?php
							}
							else {
								if (isset($items[$i])){
									$itemID 	= $items[$i]['id'];
									$title 		= $items[$i]['title'];
									$price 		= $items[$i]['price'];
									$bought		= $items[$i]['bought'] == 'true';
									$temp_free 	= $items[$i]['temp_free'] == 'true';
									$buyable 	= $price > 0 ? !$bought&&!$temp_free : false;
									$subtitle 	= $price > 0 ? ($bought ? $GLOBALS["INDEX.BOUGHT"] : ($temp_free?  '24 h '.$GLOBALS["INDEX.FREE"] : $GLOBALS["INDEX.DOLLARS"].$price)) : $GLOBALS["INDEX.FREE"];
									$href		= $buyable ? 'javascript:onBuy(\''.$itemID.'\',\'surface\');':$url.$itemID;
									$thumbSrc 	= 'images/surfaces/'.$itemID.'/thumbnail_90.png';

								} else {
									$title 		= "Coming Soon";
									$subtitle 	= "";
									$thumbSrc 	= '';
								}
								?>

								<div class="thumbnail">
									<div id="bg" class="thumbBG"></div>
									<?
										if (isset($items[$i])){
									?>
									<a id="<?php echo $itemID; ?>" href="<?php echo $href; ?>" <?php if ($buyable) { echo 'class="buyable"'; } ?> target="_self">
									<img src="<?php echo $thumbSrc; ?>" />
									</a>
									<?
										}
									?>
									<div class="thumbTitle"><?php echo $title; ?></div>
									<div class="thumbPrice"><?php echo $subtitle; ?></div>
									<?php
										if ($buyable) {
									?>
										<div class="buyHolder">
										<a class="buy_button" href="<?php echo $href; ?>">
											<span>
												<span><?php echo $GLOBALS["INDEX.BUY"]; ?></span>
											</span>
										</a>
										</div>
											<!-- <div class="buyHolder">
												<div class="buyBGLeft"></div>
												<div class="buy">buy me off</div>
												<div class="buyBGRight"></div>
											</div> -->
									<?php
										}
										if ($i == 0 && $items[$i] != null){
											?>

										<script type="text/javascript">
											doRoll($('#<?php echo $itemID; ?>'), true);
										</script>
											<?

										}
									?>
								</div>

								<?php
								$i++;
							}
						}

					?>
					</div><!-- EOF row -->
				</div><!-- EOF page-->