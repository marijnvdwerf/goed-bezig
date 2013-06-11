$('body').css('height', $(window).height() + getExtraViewportHeight());

setTimeout(function () {
	window.scrollTo(0, 1);
}, 50);

setTimeout(function() {
	$('.page-login').attr('data-state', 'login');
}, 2000);

function getExtraViewportHeight() {
    var isIphone = ~navigator.userAgent.indexOf('iPhone') || ~navigator.userAgent.indexOf('iPod');
    var isSafari = ~navigator.userAgent.indexOf('Safari');
    var fullscreen = window.navigator.standalone;
    
    if(!fullscreen && isIphone && isSafari) {
    	return 60;
    }
    
    return 0;
}

$('button').click(function(){
    //$('.page-login').css('display','none');
});


$('.button-foursquare').on('click', function () {
    var appUri = new Uri(window.location.href);
    // remove ? and # parts
    appUri
        .setAnchor('')
        .setQuery('');

    var foursquareLoginUri = new Uri('https://foursquare.com/oauth2/authenticate');
    foursquareLoginUri
        .addQueryParam('client_id', config.foursquare_id)
        .addQueryParam('response_type', 'token')
        .addQueryParam('redirect_uri', appUri);

    // open page
    window.location.href = foursquareLoginUri.toString();
});
