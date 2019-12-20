<?php /*
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);*/
	$themeID = 'graffiti';
	$title = 'Graffiti S.W.A.T.';
	$url = 'game.php?surface=';
	$GLOBALS["page"] = 'index';
	require 'php/includes/header.inc';
	
?>

	<script type="text/javascript"> 
	var fbid  = "<?php  echo $fbid == ''? -1 : $fbid; ?>";
	var key = '2bb3eb01';
	var waitingForAd = false;
	// definitions by JSON
		var ssa_json;

		function myBrandConnectReadyEvent(campaigns){
			console.log('>>myBrandConnectReadyEvent '+campaigns.length);
			for (var i = 0; i < campaigns.length; i++){
				var campaign = campaigns[i];
				/*console.log('myBrandConnectReadyEvent::campaign '+i+' width '+campaign.width);
				console.log('myBrandConnectReadyEvent::campaign '+i+' height '+campaign.height);
				console.log('myBrandConnectReadyEvent::campaign '+i+' rewards '+campaign.rewards);
				console.log('myBrandConnectReadyEvent::campaign '+i+' url '+campaign.url);
				console.log('myBrandConnectReadyEvent::campaign '+i+' width '+campaign.width);*/
			}
			if (waitingForAd){
				startVideo();
			}
		}
		function myBrandConnectDoneEvent(){
			console.log('>>myBrandConnectDoneEvent');
		}
		
		function setBrandConnectItem(itemKey){
			console.log('>>setBrandConnectItem '+itemKey);
			if (!ssa_json || ssa_json.itemName != itemKey){
				if (!ssa_json){
					ssa_json = {
							'applicationUserId': fbid,
							'applicationKey': key,
							'onCampaignsReady': myBrandConnectReadyEvent,
							'onCampaignsDone': myBrandConnectDoneEvent
							};
					// the actual script included from our servers is minified and compressed
					(function(d,t){
					var g = d.createElement(t), s = d.getElementsByTagName(t)[0]; g.async = true;
					g.src = ('https:' != location.protocol ? 'http://jsd.supersonicads.com' : 'https://supersonicads.cotssl.net') + '/inlineDelivery/delivery.min.gz.js'; s.parentNode.insertBefore(g,s);
					}(document,'script'));
					console.log('>>setBrandConnectItem '+itemKey+' >> create ssa');
				}
				var timestamp = Math.round(Date.now()/1000);
				var itemCount = 1;
				ssa_json.itemName = 'this Surface';
				ssa_json.itemKey = itemKey;
				ssa_json.itemCount = itemCount;
				ssa_json.timestamp = timestamp;
				ssa_json.itemSignature = $.md5(timestamp+itemKey+itemCount+key);
				SSA_CORE.start();
			} else {
				startVideo();
			}
		}
		
		function startVideo(){
			console.log('>>startVideo');
			waitingForAd = false;
			SSA_CORE.BrandConnect.engage();
		}
	
	</script>
	<div id="usergenerated" style="position:absolute;visibility:hidden" >
	<?php $surfaceType = 'user'; require 'php/includes/itemlist_new.inc';?>
	</div>
	<div id="wrapper">	
			
				<div id="indexPulldown">
					<div id="pulldownSelector" style="top:0px;left:0px;z-index:53;" >
					<div class="pulldownSelectorItem" id="pulldown_selector_item_swat" onclick="javascript:onSurfaceTypeChosen('swat');">Graffiti SWAT Surfaces</div>
					<div class="pulldownSelectorItem" id="pulldown_selector_item_user" onclick="javascript:onSurfaceTypeChosen('user');">User Surfaces</div>
					</div>
					<div id='surfacePicker'>
						<a id="pulldownClicker" class="pulldownClicker" href="javascript:void(0);">
							<span id="pulldownClickerTitle" class="pulldownClickerMiddle">Choose Category</span>
							<span class="pulldownClickerRight"></span>
						</a>
					</div>
				</div>
				
			<div class="teaserMain">
				<div id="hsP"><a class="prev browse left"></a></div>
				<div id="hsN"><a class="next browse right"></a></div>	
				
				<div id="logo">
				</div>
				
				<div id="hiresBG">
					<div id="hires">
					</div>
				</div>

				<div class="scrollable" id="browsable">
					<div id="items" class="items" style="width:2000em; position:absolute;">
					
		
						<?php if (!isset($_GET["cookieIssue"]) || $_GET["cookieIssue"] != "true"){
								$surfaceType = 'swat';
								require 'php/includes/itemlist_new.inc';
							}?>
					</div><!-- END items -->
				</div><!-- END scrollable -->
			</div><!-- END teaserMain -->
				 
				<script>
					/**
					* BUYING
					*/
					var initDone = false;
					var itemToBuy = '';
				
					
					function onBuy(itemId,itemType){
						buyItem(itemId,itemType);
					}
					
					/*function onBuy(itemId,itemName){
						var answer = confirm ("Would you like to buy these surfaces permanently? Press OK to buy.")
						if (answer){
							buyItem(itemId,itemName);
						}
						else {
							unlockItem(itemId);
						}
					} */
					
					function unlockItem(itemId){
						waitingForAd = true;
						setBrandConnectItem(itemId);
						//SSA_CORE.BrandConnect.engage();
					}
					
					function buyItem(itemId,itemType){
						initFacebook();
						itemToBuy = itemId;
						console.log('onBuy '+itemToBuy);

                        $.ajax({
                            url: 'php/facebook/init_payment.php?item_key=' + itemToBuy + '&item_type=' + itemType + '&fbid=' + fbid,
                            dataType: 'json',
                            success: function (data) {
                                if (data.request_id.length > 0) {
                                    FB.ui({
                                        method: 'pay',
                                        action: 'purchaseitem',
                                        product: '<?php echo $GLOBALS["server"]; ?>php/facebook/item_object.php?item_key=' + itemToBuy + '&item_type=' + itemType,
                                        quantity: 1,
                                        request_id: data.request_id
                                    }, handleBuyItem);
                                }
                                console.log('--> FB.ui app pay');
                            }
                        });
					}
					
					function handleBuyItem(response) {
						if (response.status == 'completed' && typeof response.signed_request != 'undefined') {
							console.log('FacebookConnector::handleBuyItem: orderid ' + response['order_id']+' itemToBuy '+itemToBuy);
                            $.ajax({
                                url: 'php/facebook/check_payment.php?signed_request=' + response.signed_request,
                                dataType: 'json',
                                success: function (data) {
                                    if (data.status == 'completed') {
                                        location.href = itemToBuy == ''? '?' : '<?php echo $url; ?>'+itemToBuy;
                                    }
                                }
                            });
						} else {
							console.log('FacebookConnector::NOOOOOO: error ' + response.error_message);
						}
					}

					/**
					* SWAP SURFACES
					*/	

					setPullDownPosSet(true);
					

					var lessRainSurfaces; 
					var userSurfaces;
					$(document).ready( function() {
						userSurfaces = $.trim($('#usergenerated').html());
						$('#usergenerated').html('');
						if (userSurfaces != ''){
							lessRainSurfaces = $.trim($('#items').html());
							$('.teaserMain').css({ 'height': '600px','top': '75px'});
							$('#pulldownClickerTitle').html($('#pulldown_selector_item_swat').html());
						} else {
							$('#indexPulldown').css({ 'visibility': 'hidden'});
						}
					});

					function onSurfaceTypeChosen(value){
						//console.log('onSurfaceTypeChosen::'+value+' titre '+$('#pulldown_selector_item_'+value).html());
						srollToBeginning();
						$('#items').html(value == 'user'? userSurfaces : lessRainSurfaces);
						$('#pulldownClickerTitle').html($('#pulldown_selector_item_'+value).html());
					}

				</script>
				<div id="tooltip">
					<div id="takeThis"></div>
					<div id="buyThis"></div>
				</div>
				<div id="helpOpen"></div>
			
			<div class="line_seperator"></div>
			<div class="top_mail_content">
				<div class="teaser_footer">
					<div class="teaser_footer_ad">
						<script type="text/javascript"><!--
						google_ad_client = "";
						/* GS Related Topics */
						google_ad_slot = "";
						google_ad_width = 728;
						google_ad_height = 15;
						//-->
						</script>
						<script type="text/javascript"
						src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
						</script>
					</div>
				</div>
			</div>
	</div><!-- END wrapper -->
	<div id="ieHelp">
	</div>
    
	<div style="clear: both;"></div>
	
	
	<?php if (isset($_GET["cookieIssue"]) && $_GET["cookieIssue"] == "true") echo "<script type=\"text/javascript\">openIEHelp();</script>"?>
	    
<?php 
	require 'php/includes/footer.inc';
?>
