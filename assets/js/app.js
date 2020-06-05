/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import 'wowjs/css/libs/animate.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/css/v4-shims.css';

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';
// create global $ and jQuery variables
global.$ = global.jQuery = $;

import 'bootstrap'

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

/*Confirm delete and go down to tricks*/
$(document).ready(function(){
    $(".js-del-out").click(function(){
        let title = $(this).data('trickTitle');
        if (!confirm("Voulez-vous vraiment supprimer la figure " + title + "?")){
            return false;
        }
    });
});

/*suppression des tricks*/
    $(document).on('click','.js-del-in',function (e){
        e.preventDefault();

        let button = $(this);

        let c = confirm("Voulez-vous vraiment supprimer la figure " + button.data('title') + "?");

        if (c === true) {
            $.ajax({
                type: "DELETE",
                url: button.attr('href'),
                data: JSON.stringify({'_token' : button.data('token')}),
                dataType: 'JSON',
                beforeSend: function() {
                    console.log('beforeSend')
                },
                success: function (data) {
                    if (data.success) {
                        let trick = $('#'+ button.data('slug'));
                        trick.fadeOut(function(){
                            trick.remove();
                        });
                    }
                },
                error: function(e)
                {
                    console.log('Error: ' + e);
                }
            });

        } else {
            return false;
        }
    });


/*close dropdown menu after mouse leaves*/
const $dropdown = $(".dropdown");
const $dropdownToggle = $(".dropdown-toggle");
const $dropdownMenu = $(".dropdown-menu");
const showClass = "show";
$(window).on("load resize", function() {
    if (this.matchMedia("(min-width: 768px)").matches) {
        $dropdown.hover(
            function() {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "true");
                $this.find($dropdownMenu).addClass(showClass);
            },
            function() {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr("aria-expanded", "false");
                $this.find($dropdownMenu).removeClass(showClass);
            }
        );
    } else {
        $dropdown.off("mouseenter mouseleave");
    }
});

/*back to top*/
$(document).ready(function(){
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    });
    $("#menu-icon-mobile").click(function () {
        $("#menu-icon-mobile").toggleClass('fa-bars');
        $(".navbar.fixed-top.navbar-expand-lg").toggleClass("menu-back");
        $("#menu-icon-mobile").toggleClass('fa-caret-down');
        let goBottom = $("#gobottom-container");
        if (goBottom) $(goBottom).toggle();
    })
});

$(window).scroll( function(){
    let topWindow = $(window).scrollTop();
    topWindow = topWindow * 1.5;
    let windowHeight = $(window).height();
    let position = topWindow / windowHeight;
    position = 1 - position;

    $('#bottom').css('opacity', position);
});

$(window).on("scroll", function() {
    let BtnBottom = $('#bottom');
    let navBar = $("nav.navbar");
    if ($(this).scrollTop() > 10) {
        navBar.addClass("mybg-dark");
        navBar.addClass("navbar-shrink");
    } else {
        navBar.removeClass("mybg-dark");
        navBar.removeClass("navbar-shrink");
        BtnBottom.show();
    }
});

