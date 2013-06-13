$('body').css('height', $(window).height() + getExtraViewportHeight());

setTimeout(function () {
    window.scrollTo(0, 1);
}, 50);

var uri = new Uri(window.location.href);
// parse #foo=bar as if it was ?foo=bar
uri.query(uri.anchor());

if (uri.getQueryParamValue('login-foursquare') !== undefined
    || uri.getQueryParamValue('login-facebook') !== undefined) {
    // #login-foursquare or #login-facebook are set
    $('.page-login').attr('data-state', 'loading');

    // TODO: Post token to server in exchange for user information
    foursquareLogin(uri.getQueryParamValue('access_token'));
} else {
    // Animate the logo out

    setTimeout(function () {
        $('.page-login').attr('data-state', 'login');
    }, 2000);
}

function getExtraViewportHeight() {
    var isIphone = ~navigator.userAgent.indexOf('iPhone') || ~navigator.userAgent.indexOf('iPod');
    var isSafari = ~navigator.userAgent.indexOf('Safari');
    var fullscreen = window.navigator.standalone;

    if (!fullscreen && isIphone && isSafari) {
        return 60;
    }

    return 0;
}

$('button').click(function () {
    //$('.page-login').css('display','none');
});


$('.button-foursquare')
    .hammer()
    .on('tap', function () {
        var appUri = new Uri(window.location.href);
        // remove ? and # parts
        appUri
            .setAnchor('login-foursquare')
            .setQuery('');

        var foursquareLoginUri = new Uri('https://foursquare.com/oauth2/authenticate');
        foursquareLoginUri
            .addQueryParam('client_id', config.foursquare_id)
            .addQueryParam('response_type', 'token')
            .addQueryParam('redirect_uri', appUri);

        // open page
        window.location.href = foursquareLoginUri.toString();
    });


function foursquareLogin(token) {

    $.ajax({
        url: "http://goedbezig.marijnvdwerf.nl/app/api/login/foursquare",
        type: "POST",
        data: {
            token: token
        }

    }).done(function (data) {
            sortData(data);
            
            $('.page-login').hide();
            $('.page-main').show();
        });

}

function sortData(data) {
    $.each(data.achievements, function (i, achievement) {
        createCard(achievement);
    })

}

function createCard(achievement) {
    //create div
    console.log(achievement);
}



$('#settings')
    .hammer()
    .on('tap', function() {
        if($('.page-main').attr('data-state') === 'settings') {
            $('.page-main').attr('data-state', 'overview');
        } else {
            $('.page-main').attr('data-state', 'settings');
        }
    });



$('.content-settings')
    .on('click', ".option-checkable", function () {
        var input = $(this).find('input');
        if(input.attr('type') === 'radio' && input.prop('checked')) {
            // do nothing
        } else {
            input.prop('checked', !input.prop('checked'));
            $('input[name="' + input.attr('name') + '"]').trigger('change');
        }
    })
    .on('change', '.option-checkable input', function() {
        var input = $(this);
        var container = input.parent();
        
        if(input.prop('checked')) {
            //container.removeClass('un-checked');
        } else {
            //container.addClass('un-checked');
        }
    });
