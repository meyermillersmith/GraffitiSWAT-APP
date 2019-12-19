/**
 * SWFMacMouseWheel v2.0*** (Thomas Meyer): Added params to enable the Javascript Mousewheel on Windows when wmode is "opaque" or "transparent" 
 *
 * SWFMacMouseWheel v2.0: Mac Mouse Wheel functionality in flash - http://blog.pixelbreaker.com/
 *
 * SWFMacMouseWheel is (c) 2007 Gabriel Bucknall and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Dependencies: 
 * SWFObject v2.0 rc1 <http://code.google.com/p/swfobject/>
 * Copyright (c) 2007 Geoff Stearns, Michael Williams, and Bobby van der Sluis
 * This software is released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
var swfmacmousewheel = function()
{
	if( !swfobject ) return null;
	
	var u = navigator.userAgent.toLowerCase();
	var p = navigator.platform.toLowerCase();
	var mac = p ? /mac/.test(p) : /mac/.test(u);
	
	//if( !mac ) return null;

	var regObjArr = [];
	var regObjRefArr = [];

	var preventDefault = true;
	var isWModeWindow = true;
	var listenersAdded = false;
	
	var deltaFilter = function(event)
	{
		var delta = 0;
        if (event.wheelDelta) {
			delta = event.wheelDelta/120;
			if (window.opera) delta = -delta;
        } else if (event.detail) {
            delta = -event.detail;
        }

		return delta;
	}
	
	var setEventProperties = function(event)
	{
        /**
         * Prevent default actions caused by mouse wheel if set to do so
         */
        if (preventDefault)
        {
	        if (event.preventDefault) event.preventDefault();
	        event.preventDefault = true;
			event.returnValue = false;
		}
	}
	
	var deltaDispatcher = function(event)
	{
        /* For IE. */
		if (!event) event = window.event;
		
		if (mac || !isWModeWindow)
		{
			var delta = deltaFilter(event);
			var obj;
			for(var i=0; i<regObjArr.length; i++ )
			{
				obj = regObjRefArr[i];
				if (obj==null)
				{
					obj = swfobject.getObjectById(regObjArr[i]);
					regObjRefArr[i] = obj;
				}
				if( obj.externalMouseEvent && typeof( obj.externalMouseEvent ) == 'function' ) obj.externalMouseEvent( delta );
			}
		}
		
		setEventProperties(event);
	}
	
	var registerListeners = function()
	{
		listenersAdded = true;
		if (window.addEventListener) window.addEventListener('DOMMouseScroll', deltaDispatcher, false);
		window.onmousewheel = document.onmousewheel = deltaDispatcher;
	}
	
	if( mac ) registerListeners();
			
	return {
		/*
		Public API
		*/
		registerObject: function(objectIdStr)
		{
			regObjArr[regObjArr.length] = objectIdStr;
			regObjRefArr[regObjRefArr] = null;
		},
		
		setPreventDefault: function(preventDefault_)
		{
			preventDefault = preventDefault_;
	
			if( preventDefault && !listenersAdded ) registerListeners();
		},

		setWMode: function(wmode_)
		{
			isWModeWindow = (wmode_=="window");

			if( !isWModeWindow && !listenersAdded ) registerListeners();
		}
	};
}();
