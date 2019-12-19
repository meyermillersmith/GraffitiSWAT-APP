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
        
        <title>Ka-Ching!!</title>
    
    
		<script type="text/javascript" src="../../js/external/jquery-1.2.5.min.js"></script>
		<script type="text/javascript">
			var timestamp;
			var maxTraceOutInterval = 1;
			var callTimes = 0;
			var totalMoneyMade = 0;
			var zeroCashChecks = 0;
			var zeroCashPhrases = new Array("not yet...", "not yet..", "yawn...","zzzzz.....", "...zzzzz....","......zzzzzz", "..man, it's gonna take forever for that jaguar..");
			
			$(window).load( function() {
				var timerTimeInMin = 1; // 1 min
				var timerTime = timerTimeInMin * 60 * 1000; //min to ms
				maxTraceOutInterval = 20 / timerTimeInMin; // every 20 mins

				window.setInterval(checkBoughtItems,timerTime);
				checkBoughtItems();	
			});
	
			function checkBoughtItems(){
				callCheckBought();
			}

			function callCheckBought(){
				var url = "../php/action/secure/9xwh00pEe.php";
				var request = {url: url, type:'POST', data:'timestamp='+timestamp,  success:onMoreLoaded, error:onMoreFailed};
				$.ajax(request);
			}

			function getMySQLDate(date){
				var mydate = date.getUTCFullYear() + '-' +
	            ('00' + (date.getUTCMonth()+1)).slice(-2) + '-' +
	            ('00' + date.getUTCDate()).slice(-2) + ' ' + 
	            getMySQLTime(date);

	            return mydate;
			}

			function getMySQLTime(date){
				var mytime = ('00' + date.getUTCHours()).slice(-2) + ':' + 
	            ('00' + date.getUTCMinutes()).slice(-2) + ':' + 
	            ('00' + date.getUTCSeconds()).slice(-2);

	            return mytime;
			}

			function onMoreLoaded(data){
				var jsContent = eval( "(" +data+ ")");
				var firstCall = eval(jsContent["firstCall"]);
				if (jsContent["tools"] != undefined && jsContent["surfaces"] != undefined){
					var toolCount = jsContent["tools"].length;
					var surfaceCount = jsContent["surfaces"].length;
					var splitTools = splitBuyTypes(jsContent["tools"]);
					var splitSurfaces = splitBuyTypes(jsContent["surfaces"]);
					var toolBuyCount = splitTools["bought"]? splitTools["bought"].length : 0;
					var surfaceBuyCount = splitSurfaces["bought"]? splitSurfaces["bought"].length : 0;
					if ((toolCount + surfaceCount) > 0) {
						emptyDiv('waitContent');
						zeroCashChecks = 0;
						
						var moneyMade = toolBuyCount*1.8 + surfaceBuyCount*0.8;
						totalMoneyMade += moneyMade;
						var roundMoneyPercent = mathRound(totalMoneyMade /6.24);
						
						if (splitTools["bought"] || splitSurfaces["bought"]){
							var mainSentence = toolBuyCount > 0? toolBuyCount+' tool'+(toolBuyCount>1?'s':'') : '';
							mainSentence += surfaceBuyCount > 0? (mainSentence == ''?'': ' and ') + surfaceBuyCount+' surface'+(surfaceBuyCount>1?'s':'') : '';
							addContent('KA-CHING!!! '+mainSentence+'  sold '+(firstCall? 'so far':'')+'! That\'s '+mathRound(moneyMade)+' bucks baby!! We gon\' be RICH!');
	
							addContent(roundMoneyPercent+' % of the server costs ('+mathRound(totalMoneyMade)+'$ of 624$) paid.','totalAmountContent',true);
							playSound('kaching');
						}
						if (splitTools["tested"] || splitSurfaces["tested"]){
							var toolTestCount = splitTools["tested"]? splitTools["tested"].length : 0;
							var surfaceTestCount = splitSurfaces["tested"]? splitSurfaces["tested"].length : 0;
							var mainSentence = toolTestCount > 0? toolTestCount+' tool'+(toolTestCount>1?'s':'') : '';
							mainSentence += surfaceTestCount > 0? (mainSentence == ''?'': ' and ') + surfaceTestCount+' surface'+(surfaceTestCount>1?'s':'') : '';
							addContent('Nice! '+mainSentence+'  tested '+(firstCall? 'so far':'')+'!');	
						}
						if (splitTools["granted"] || splitSurfaces["granted"]){
							var toolGrantCount = splitTools["granted"]? splitTools["granted"].length : 0;
							var surfaceGrantCount = splitSurfaces["granted"]? splitSurfaces["granted"].length : 0;
							var mainSentence = toolGrantCount > 0? toolGrantCount+' tool'+(toolGrantCount>1?'s':'') : '';
							mainSentence += surfaceGrantCount > 0? (mainSentence == ''?'': ' and ') + surfaceGrantCount+' surface'+(surfaceGrantCount>1?'s':'') : '';
							addContent('We\'re so nice! '+mainSentence+'  granted '+(firstCall? 'so far':'')+'!');	
						}
					} else {
						if (zeroCashChecks%maxTraceOutInterval == 0){
							if (zeroCashChecks == 0 && firstCall){
								addContent('Sorry, you didn\'t make squat so far!');
							} else if (zeroCashChecks != 0) {
								var index = ( zeroCashChecks / maxTraceOutInterval ) - 1;
								if (zeroCashPhrases.length > index) {
									addContent(zeroCashPhrases[index],'waitContent');
								}
							}
						}
						zeroCashChecks++;
					}
					if (jsContent["timestamp"] != undefined){
						timestamp = jsContent["timestamp"];
					}

					callTimes++;
				} else if (jsContent["error"]){
					onMoreFailed(jsContent["error"]);
				} else {
					onMoreFailed('cannot parse result');
				}
			}

			function splitBuyTypes(resultArray){
				var splitArray = new Array();
				for (var i=0;i<resultArray.length;i++)
				{
					var acquisition = resultArray[i]['acquisition'];
					if (!splitArray[acquisition]) splitArray[acquisition] = new Array();
					splitArray[acquisition].push(resultArray[i]);
				}
				return splitArray;
			}

			function mathRound(number){
				return Math.round(number * 100) / 100;
			}
			
			function onMoreFailed(data){
				console.log('onMoreFailed data '+data);	
				addContent('query failed! '+data);
			}
			
			function addContent(string,div,omitTimestamp){
				div = div || "mainContent";
				string=(omitTimestamp?'':'<span style="color:#CCCCCC">['+getMySQLTime(new Date())+']</span> ')+string;
				$('#'+div).append(string+'<br/>');
				//TODO play katchiiing sound
			}

			function emptyDiv(div){
				console.log('empty '+div );	
				$('#'+div).empty();
				//TODO play katchiiing sound
			}

			function playSound(sound) {
				var soundfile = "../assets/sounds/"+sound+".mp3";
				document.getElementById("sound").innerHTML=
				"<embed src=\""+soundfile+"\" hidden=\"true\" autostart=\"true\" loop=\"false\" />";
			}
			
		</script>
    </head>
    
    <body>
    
    
    <h1>
       The Official Graffiti SWAT KA-CHIIIING View.
    </h1>
    <div id="mainContent">
     Let the money shower begin!! Stay around for more, more and so much more!!!<br/><br/>
    </div>
    <div id="waitContent">
    </div>
    <br/>
    <div id="totalAmountContent">
    </div>
    <br/>
    <span id="sound"></span>
    
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