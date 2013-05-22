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
        this.el[0].addEventListener('webkitTransitionEnd', function (e) {
            $(this).css('display', 'none');
        });
    }
};

$('.card').each(function (index, card) {
    card = $(card);
    card.height(card.outerWidth() * card.data('ratio'));
});

$('body').removeClass('loading');

cardContainer.masonry({
    itemSelector: '.card-wrapper'
});

cardContainer.hammer().on('tap', '.card', function (event) {
    overlay.show();
    var card = $(this);
    card.parent().addClass('focus');
});
