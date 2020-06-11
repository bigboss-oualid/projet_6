import 'slick-carousel/slick/slick.min.js'

$(document).ready(function() {
    let illustrations = $('.illustrations');
    let videos = $('.videos');
    if (illustrations.length>2)
        illustrations.parent().addClass('slider');
    if (videos.length>2)
        videos.parent().addClass('slider');
    $(".slider").slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: true,
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
