pullDownPosSet = false;
// DOM ready
$(document).ready( function() {
	$('html').click(closeSelector);
	$('#pulldownClicker').click(toggleSelector);
});



/**
* CONTROLS
*/

function setPullDownPosSet(bool){
	pullDownPosSet = bool;
}

function toggleSelector(event){
	var IE = document.all?true:false
	if (IE) {
		window.event.cancelBubble = true;
	} else {
		event.stopPropagation(); 
	}
	if ($('#pulldownSelector').css('visibility') == 'visible'){
		closeSelector(event);
	} else {
		$('#pulldownSelector').css({ 'visibility': 'visible'});
		if(!pullDownPosSet){
			setPullDownPos();
		}
	}
}

function closeSelector(event){
	//event.stopPropagation();
	//window.event.cancelBubble = true;
	$('#pulldownSelector').css({ 'visibility': 'hidden'});
}
