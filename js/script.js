$('body').css('height', $('body').height());//$(window).height() + getExtraViewportHeight());
console.log($('body').height());
setTimeout(function () {
    window.scrollTo(0, 1);
}, 50);

var uri = new Uri(window.location.href);
// parse #foo=bar as if it was ?foo=bar
uri.query(uri.anchor());

if (localStorage.getItem('foursquareToken') !== null) {
    $('.page-login').attr('data-state', 'loading');
    foursquareLogin(localStorage.getItem('foursquareToken'));
} else if (uri.getQueryParamValue('login-foursquare') !== undefined
    || uri.getQueryParamValue('login-facebook') !== undefined) {
    // #login-foursquare or #login-facebook are set
    $('.page-login').attr('data-state', 'loading');

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
    localStorage.setItem('foursquareToken', token);
    $.ajax({
        url: "/api/login/foursquare",
        type: "POST",
        data: {
            token: token
        }

    }).done(function (data) {
            sortData(data);

            $('.page-login').hide();
            $('.page-main').addClass('show');
        });

}

function sortData(data) {
    data.achievements.sort(function (a, b) {
        return b.progress - a.progress;
    });
    $.each(data.achievements, function (i, achievement) {
        createCard(achievement);
    });

    cardContainer.masonry({
        itemSelector: '.card-wrapper'
    });

    if (uri.getQueryParamValue('c') !== undefined) {
        var cardId = uri.getQueryParamValue('c');
        var card = $('.card[data-id="' + cardId + '"]');

        // hide last stamp
        card.find('.stamp').last().addClass('fresh');
        card.hammer().trigger('tap');

        var overlayCard = $('.overlay .card');
        setTimeout(function () {
            overlayCard.find('.stamp').removeClass('fresh');
            card.find('.stamp').removeClass('fresh');
        }, 1000);
    }
}

function createCard(achievement) {
    var template = $('#template-card').html();
    var stampsTemplate = $('#template-stamp').html();
    var goodieTemplate = $('#template-goodie').html();
    var stampsFinal = "";
    template = template.replace(':achievementId', achievement.id);
    template = template.replace(':achievementTitle', achievement.name);
    template = template.replace(':achievementIcon1', achievement.icon);
    template = template.replace(':achievementIcon2', achievement.icon);
    template = template.replace(':achievementIcon3', achievement.icon);
    template = template.replace(':achievementIcon4', achievement.icon);
    template = template.replace(':achievementDescription', achievement.description);

    var cardWrapper = $(template);

    if (achievement.goodie !== null) {
        cardWrapper.find('.card').addClass('has-goodie');
    }


    for (var i = 0; i < achievement.stamps_required; i++) {
        var stampBox = $('<div class="stamp-box"></div>');
        if (achievement.stamps[i] !== undefined) {
            $('<div/>')
                .addClass('stamp type-' + achievement.stamps[i].type)
                .css({
                    left: (Math.random() * 40 + 30) + '%',
                    top: (Math.random() * 40 + 30) + '%',
                    transform: 'rotate(' + (Math.random() * 50 - 25) + 'deg)'
                })
                .appendTo(stampBox);
        }
        stampBox.appendTo(cardWrapper.find('.stamp-wrapper'));
    }


    $("#home > .page > .content-main > .scrollable > .wrapper").append(cardWrapper);

    var back = cardWrapper.find('.card-back');
    var backRatio = back.outerHeight() / back.outerWidth();
    back.css({
        margin: '-' + (back.outerHeight() / 2) + 'px -' + (back.outerWidth() / 2) + 'px'
    });

    var card = cardWrapper.find('.card');
    card.css('width', 96);
    card.css('height', 96 * backRatio);

    cardWrapper.css('height', 96 * backRatio);
    cardWrapper.css('padding', '4px');
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

        window.setTimeout(function () {
            overlay.el.find('.curtain').css('opacity', 1);
        }, 1);
    },

    hide: function () {
        this.el.find('.curtain').css('opacity', 0);

        var onTransitionEnd = function (event) {
            overlay.el.css('display', 'none');
            this.removeEventListener('webkitTransitionEnd', onTransitionEnd);
        };

        this.el.find('.curtain')[0].addEventListener('webkitTransitionEnd', onTransitionEnd);
    },

    empty: function () {
        overlay.el.find('.card-wrapper').empty();
    }
};

var activeWrapper;
var activeCard;

cardContainer.hammer()
    .on('tap', '.card', function (event) {
        var card = $(this);

        var cardOffset = card.offset();
        var clonedCard = card.clone();

        var centerX = $(window).width() / 2;
        var centerY = $(window).height() / 2;

        var thumbWidth = card.outerWidth();
        var thumbHeight = card.outerHeight();

        var cardWidth = card.find('.card-back').outerWidth();
        var cardHeight = card.find('.card-back').outerHeight();

        var cardWrapper = overlay.el.find('.card-wrapper');
        cardWrapper.css({
            'width': cardWidth,
            'height': cardHeight
        });

        cardWrapper.parent().css({
            'padding-top': centerY - (cardHeight / 2),
            'padding-bottom': centerY - (cardHeight / 2)
        });

        clonedCard
            .css({
                margin: '-' + (card.outerHeight() / 2) + 'px -' + (card.outerWidth() / 2) + 'px',
                transform: 'translate(' + (cardOffset.left + thumbWidth / 2 - centerX ) + 'px, ' + (cardOffset.top + thumbHeight / 2 - centerY) + 'px)'
            })
            .appendTo(cardWrapper);

        overlay.show();

        setTimeout(function () {
            clonedCard
                .css({
                    transform: 'translate(0, 0) scale(3.0833333333) rotateY(-180deg)'
                })
                .addClass('focus');
        }, 1);
        card.hide();
    })
    .on('touch', '.card-wrapper', function (event) {
        $(this).addClass('hover');
    })
    .on('release', '.card-wrapper', function (event) {
        $(this).removeClass('hover');
    });


overlay.el.hammer().on('tap', function (event) {
    overlay.hide();

    var overlayCard = $(this).find('.card');
    var cardId = overlayCard.data('id');
    overlayCard.remove();

    var originalCard = cardContainer.find('.card[data-id="' + cardId + '"]');
    originalCard.show();

    overlay.hide();
    overlay.empty();

    return;

    var activeCardWrapper = $('.card-wrapper.focus');
    var card = activeCardWrapper.find('.card');

    card.css('top', '50%');
    card.css('left', '50%');
    card.css('transform', 'scale(1)');

    var onTransitionEnd = function (event) {
        activeCardWrapper.removeClass('focus');
        this.removeEventListener('webkitTransitionEnd', onTransitionEnd);
    };
    card[0].addEventListener('webkitTransitionEnd', onTransitionEnd);

    //overlay.hide();
    $('.navbar-overlay').hide();
});
