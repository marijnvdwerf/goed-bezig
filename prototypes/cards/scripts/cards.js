var cardContainer = $('#card-container');

$('.card').each(function (index, card) {
    card = $(card);
    card.height(card.outerWidth() * card.data('ratio'));
});

$('body').removeClass('loading');

cardContainer.masonry({
    itemSelector: '.card-wrapper'
});
