$('body').hammer()
    .on('touch', '.hammer-tappable', function() {
        $(this).addClass('hammer-touch');
    })
    .on('release', '.hammer-tappable', function() {
        $(this).removeClass('hammer-touch');
    });
