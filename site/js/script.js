$('body').css('height', $(window).height() + getExtraViewportHeight());

setTimeout(function () {
	window.scrollTo(0, 1);
}, 50);

function getExtraViewportHeight() {
    var isIphone = ~navigator.userAgent.indexOf('iPhone') || ~navigator.userAgent.indexOf('iPod');
    var isSafari = ~navigator.userAgent.indexOf('Safari');
    var fullscreen = window.navigator.standalone;
    
    if(!fullscreen && isIphone && isSafari) {
    	return 60;
    }
    
    return 0;
}