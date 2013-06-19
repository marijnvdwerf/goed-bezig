$('body').css('height', $('body').height());//$(window).height() + getExtraViewportHeight());
console.log($('body').height());
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
    });

    cardContainer.masonry({
        itemSelector: '.card-wrapper'
    });
}

function createCard(achievement) {
    var template = $('#template-card').html();
    var stampsTemplate = $('#template-stamp').html();
    var goodieTemplate = $('#template-goodie').html();
    var stampsFinal = "";
    template = template.replace(':achievementTitle', achievement.name);
    template = template.replace(':achievementDescription', achievement.description);

    if(achievement.goodie === null){
        template = template.replace(':achievementGoodie', "");
        console.log("goodie is null");
    } else {
        template = template.replace(':achievementGoodie', goodieTemplate);
        console.log("FOUND goodie");
    }


    switch (achievement.stamps_required) {
        case 5:
            template = template.replace(':achievementDataRatio', '100px');
            break;
        case 10:
            template = template.replace(':achievementDataRatio', '150px');
            break;
        case 15:
            template = template.replace(':achievementDataRatio', '200px');
            break;
        case 20:
            template = template.replace(':achievementDataRatio', '250px');
            break;
    }

    var height = (achievement.stamps_required) * 10 + 50;

    $.each(achievement.stamps, function (i, stamp){
        //console.log(stamp.type); //also timestamp
        stampsFinal  += stampsTemplate.replace(':stampStamp',stamp.type);
    })

    template = template.replace(':achievementStamps', stampsFinal);

    template = $(template);
template.css('height', height);
    template.css('width', 96);
    template.css('padding', '4px');
    $("#home > .page > .content-main > .scrollable > .wrapper").append(template);

}


$('#settings')
    .hammer()
    .on('tap', function () {
        if ($('.page-main').attr('data-state') === 'settings') {
            $('.page-main').attr('data-state', 'overview');
        } else {
            $('.page-main').attr('data-state', 'settings');
        }
    });


$('.content-settings')
    .hammer()
    .on('tap', ".option-checkable", function (e) {
        e.preventDefault();
        var input = $(this).find('input');
        if (input.attr('type') === 'radio' && input.prop('checked')) {
            // do nothing
        } else if (e.target !== input[0]) {
            input.prop('checked', !input.prop('checked'));
        }

        $('input[name="' + input.attr('name') + '"]').trigger('change');
    })
    .on('tap', '.option-checkable input', function () {
        var input = $(this);
        var container = input.parent();

        if (input.prop('checked')) {
            //container.removeClass('un-checked');
        } else {
            //container.addClass('un-checked');
        }
    });


var cardContainer = $('#card-container');
var overlay = {
    el: $('.overlay'),

    show: function () {
        this.el.css('display', 'block');

        window.setTimeout(function (el) {
            el.css('opacity', 1);
        }, 1, this.el);
    },

    hide: function () {
        this.el.css('opacity', 0);

        var onTransitionEnd = function (event) {
            $(this).css('display', 'none');
            this.removeEventListener('webkitTransitionEnd', onTransitionEnd);
        };

        this.el[0].addEventListener('webkitTransitionEnd', onTransitionEnd);
    }
};


$('body').removeClass('loading');

console.log(cardContainer);


cardContainer.hammer()
    .on('tap', '.card', function (event) {
        var card = $(this);

        var viewportHeight = $(window).height();
        var wrapperTop = $(this).parent().offset().top;

        var viewportWidth = $(window).width();
        var wrapperLeft = $(this).parent().offset().left;

        overlay.show();
        card.parent().addClass('focus');
        card.css('transform', 'scale(1) rotateY(180deg)');
        card.css('top', viewportHeight / 2 - wrapperTop);
        card.css('left', viewportWidth / 2 - wrapperLeft);
        $('.navbar-overlay').show();
    })
    .on('touch', '.card-wrapper', function (event) {
        $(this).addClass('hover');
    })
    .on('release', '.card-wrapper', function (event) {
        $(this).removeClass('hover');
    });



$('.overlay').hammer().on('tap', function (event) {
    var activeCardWrapper = $('.card-wrapper.focus');
    var card = activeCardWrapper.children('.card');

    card.css('top', '50%');
    card.css('left', '50%');
    card.css('transform', 'scale(' + card.data('scale') + ')');

    var onTransitionEnd = function (event) {
        activeCardWrapper.removeClass('focus');
        this.removeEventListener('webkitTransitionEnd', onTransitionEnd);
    };
    card[0].addEventListener('webkitTransitionEnd', onTransitionEnd);

    overlay.hide();
    $('.navbar-overlay').hide();
});