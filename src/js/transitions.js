// Global Variables
var rolledItem;
var IE;
var tempX = 0
var tempY = 0
var offset;
var offsetX = 0;
var offsetY = 0;
var bTooltipVisible = false;
var currentItemLink;

// DOM ready
$(document).ready( function() {
	$('.thumbnail').children('a').mouseover( function() {
		doRoll(this, true);
	});
	$('.thumbnail').children('a').mouseout( function() {
		doRoll(this, false);
	});
	
	$('a.buy').mouseover( function() {
		$(this).parent().clearQueue();
		$(this).parent().stop();
		$(this).parent().css({ 'background-position': '11px -21px'});
	});
	$('a.buy').mouseout( function() {
		$(this).parent().clearQueue();
		$(this).parent().stop();
		$(this).parent().css({ 'background-position': '11px 0px'});
	});
	
	
	
	// Tooltip
	IE = document.all?true:false
	if (!IE) document.captureEvents(Event.MOUSEMOVE)
	document.onmousemove = getMouseXY;
	offset = $('#wrapper').offset();
	if (offset){
		offsetX = offset.left;
		offsetY = offset.top;
	
		// Scroll easing function
		$(function() {
			
			$.easing.custom = function (x, t, b, c, d, s) {
					if (s == undefined) s = 0;
					if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
					return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
			}
			
			//$(".scrollable").scrollable({easing: 'custom', speed: 700, circular:true});
			
			var api = $(".scrollable").data("scrollable");
		});
		
		$("#browsable").scrollable().navigator();
		$('#hires').click(navigateToItemLink);
	}
	$('#helpOpen').click(openHelp);
	
});

//scrollable
function srollToBeginning(){
	$("#browsable").scrollable().begin(0);
}

// RollOver-/Out Function
function doRoll(obj, bOver) {
	var duration = 200;
	$(obj).parent().children('.thumbBG').clearQueue();
	$(obj).parent().children('.thumbBG').stop();
	$(obj).parent().children('.thumbBG').animate({ top: bOver ? -5 : 0 }, duration );
	
	$(obj).parent().children('.thumbTitle').animate({ opacity: bOver ? 1 : .8, }, duration );
	
	$(obj).children('img').clearQueue();
	$(obj).children('img').stop();
	$(obj).children('img').animate({ top: bOver ? -5 : 0 }, duration );

	var bBuyThis = $(obj).hasClass('buyable');

	$('#takeThis').css('visibility', !bBuyThis ? 'visible' : 'hidden');
	$('#buyThis').css('visibility', bBuyThis ? 'visible' : 'hidden');
	
	$('#tooltip').clearQueue();
	$('#tooltip').stop();
	$('#tooltip').css('visibility', 'visible');
	$('#tooltip').animate({ opacity: bOver ? 1 : 0, }, duration );

	if (bOver) {
		$('#hires').clearQueue();
    	$('#hires').stop();
		rolledItem = obj;
		loadHiRes($(obj).attr('id'));
		currentItemLink = $(obj).attr('href');
	}
	else {
		/*rolledItem = null;
		$('#hires').clearQueue();
    	$('#hires').stop();
		$('#hires').animate({ opacity: 0 }, 300); */
	}
}

function navigateToItemLink() {
//console.log('navigateToItemLink '+currentItemLink);
	location.href=currentItemLink;
}

// Load HiRes Image
function loadHiRes(id_) {
	var url = "php/utils/load_hi_res_image.php?id=";
	if (id_!=undefined && id_.length>0) url += id_;
	else url += 'lessrain';

	$('#hires').clearQueue();
	$('#hires').stop();
	$('#hires').css({ opacity: 0});
	
	$('#hires').load(url, function() {
		if (rolledItem!=undefined) {
    		$('#hires').animate({ opacity: 1 }, 300);
		}
	});
}

function getMouseXY(e) {
  if (IE) { // grab the x-y pos.s if browser is IE
    tempX = event.clientX + document.body.scrollLeft
    tempY = event.clientY + document.body.scrollTop
  } else {  // grab the x-y pos.s if browser is NS
    tempX = e.pageX
    tempY = e.pageY
  }  
  // catch possible negative values in NS4
  if (tempX < 0){tempX = 0}
  if (tempY < 0){tempY = 0}  
  // show the position values in the form named Show
  // in the text fields named MouseX and MouseY

  $('#tooltip').css('left', tempX+15-offsetX);
  $('#tooltip').css('top',  tempY+10-offsetY);
  
  return true
}

function openHelp() {
	openPopup('#help','#helpClose',closeHelp,true);
}

function closeHelp() {
	closePopup('#help','#helpClose',true);
}

function openIEHelp() {
	openPopup('#ieHelp','#ieClose',closeIEHelp,false);
}

function closeIEHelp() {
	closePopup('#ieHelp','#ieClose',false);
}

function openPopup(popupName, buttonName, closefunction, animate, noBlocker) {
	if (!noBlocker) $('#helpBlocker').css({ 'visibility': 'visible'});
	$(popupName).css({ 'visibility': 'visible'});
	if (animate){
		$(popupName).stop();
		$(popupName).animate({top: '0' }, 500, function() {
			$(buttonName).css({ 'visibility': 'visible'});
		  });
	} else {
		$(buttonName).css({ 'visibility': 'visible'});
	}
	$(buttonName).click(closefunction);

}

function closePopup(popupName, buttonName, animate) {
	$(buttonName).css({ 'visibility': 'hidden'});
	$('#helpBlocker').css({ 'visibility': 'hidden'});
	if (animate){
		$(popupName).stop();
		$(popupName).animate({top: '-530' }, 500, function() {	
			$(popupName).css({ 'visibility': 'hidden'});
		  });
	} else {
		$(popupName).css({ 'visibility': 'hidden'});
	}
}