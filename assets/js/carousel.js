import 'slick-carousel/slick/slick.min.js'

$(document).ready(function() {
    $(".slider").slick({
        infinite: false,
        responsive: [{
            breakpoint: 1024,
            settings: {
                slidesToShow: 2,
                dots: true
            }
        }, {
            breakpoint: 780,
            settings: {
                slidesToShow: 1,
                dots: true
            }
        }, {
            breakpoint: 300,
            settings: "unslick" // destroys slick
        }]
    });
});

import 'simplelightbox/dist/simple-lightbox.jquery.min'
$('.gallery a').simpleLightbox();

/*$(function() {
    $('.pop').on('click', function() {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
    });
});*/
