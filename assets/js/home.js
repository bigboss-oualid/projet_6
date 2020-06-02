import $ from "jquery";

new WOW().init();

$('header.masthead').hover(function () {
    $('.overlay').fadeOut("slow")
});
$("document").ready(function() {
    setTimeout(function() {
        $("a.js-load").trigger('click');
        $("footer").hide();
    },10);
});
$(function() {
    $('#bottom').click(function() {
        $('#gobottom').show();
        $("footer").show();
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 500);
                return false;
            }
        }
    });
});
