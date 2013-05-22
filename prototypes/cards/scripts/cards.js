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

$('.card').each(function (index, card) {
    card = $(card);
    var thumbWidth = card.outerWidth();

    var width = 320;
    var height = width * card.data('ratio');

    var scale = thumbWidth / width;

    card.width(width);
    card.height(height);
    card.data('scale', scale);
    card.css('transform', 'scale(' + scale + ')');
    card.css('margin-left', -width / 2);
    card.css('margin-right', -width / 2);
    card.css('margin-top', -height / 2);
    card.css('margin-bottom', -height / 2);

    card.parent().width(thumbWidth);
    card.parent().height(thumbWidth * card.data('ratio'));
});

$('body').removeClass('loading');

cardContainer.masonry({
    itemSelector: '.card-wrapper'
});

cardContainer.hammer().on('tap', '.card', function (event) {
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
});

$('#btn-close-overlay').hammer().on('tap', function (event) {
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