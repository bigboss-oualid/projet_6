/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)

import 'wowjs/css/libs/animate.css';
import 'select2/dist/css/select2.min.css';
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';
// create global $ and jQuery variables
global.$ = global.jQuery = $;

import 'bootstrap';
import 'sweetalert/dist/sweetalert.min';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

/*Confirm delete and go down to tricks*/
/*delete trick by editing*/
$(document).ready(function(){
    $(".js-del-out").click(function(e){
        e.preventDefault();

        let title = $(this).data('trickTitle');
        let form = $(this).parents('form:first');
        let timer = 1800;
        swal({
            title: "Confirmation",
            text: "Voulez-vous vraiment supprimer la figure " + title + "?",
            icon: "warning",
            dangerMode: true,
            buttons:["Annuler", "Supprimer"]
        }).then(function(isConfirm) {
            if (isConfirm ){
                swal("La figure "+title+" a été supprimé avec succès!", {
                    icon: "success",
                    timer: timer,
                });
                form.submit();
            }else {
                swal("La figure "+title+" n'est pas supprimer!", {
                    icon: "error",
                    timer: timer,
                });
            }
        })
    });
});
/*delete Trick with ajax*/
$(document).on('click','.js-del-in',function (e){
    e.preventDefault();
    let title = $(this).data('title');
    let button = $(this);
    let timer = 1800;

    swal({
        title: "Confirmation",
        text: "Voulez-vous vraiment supprimer la figure " + title + "?",
        icon: "warning",
        dangerMode: true,
        buttons:["Annuler", "Supprimer"]
    }).then(function(isConfirm) {
        if (isConfirm ){
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
                        swal("La figure "+title+" a été supprimé avec succès!", {
                            icon: "success",
                            timer: timer,
                        });
                        let trick = $('#'+ button.data('slug'));
                        trick.fadeOut(function(){
                            $(this).remove();
                        });
                    }
                },
                error: function(error)
                {
                    console.log('Error: ' + error);
                }
            });
        } else {
            swal("La figure "+title+" n'est pas supprimer!", {
                icon: "error",
                timer: timer,
            });
        }
    })
});



/*show media on sm*/
$(document).ready(function(){
    $('#media-show').on('click',function (e) {
        e.preventDefault();
        $('#trick-media').toggleClass('hidden');
        $(this).text(($(this).text() === 'Afficher media') ? 'Cacher media' : 'Afficher media').fadeIn();

            setTimeout(function() {
                $(".slider").slick('setPosition');
            }, 200);

    });
});

/*Close Flashes message*/
$(function (){
    $(".close").click(function() {
        $(".flash-container").hide("slow");
    });
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
    let top = $('#back-to-top');
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            top.fadeIn();
        } else {
            top.fadeOut();
        }
    });
    // scroll body to 0px on click
    top.click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    });
    let mobileIcon = $("#menu-icon-mobile");
    mobileIcon.click(function () {
        $(this).toggleClass('fa-bars');
        $(".navbar.fixed-top.navbar-expand-lg").toggleClass("menu-back");
        $(this).toggleClass('fa-caret-down');
        let goBottom = $("#gobottom-container");
        if (goBottom) $(goBottom).toggle();
    })
});

/*hide and show gobottom btn by scrolling the mouse or by clicking goBttom*/
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

/*show hide password*/
$(document).ready(function(){
    $('.ptxt').on('click', function(){
        if ($(this).hasClass('fa-eye')) {
            $(this).removeClass('fa-eye').addClass(' fa-eye-slash');
        } else {
            $(this).removeClass('fa-eye-slash').addClass(' fa-eye');
        }
        $('.show_pass').attr('type', function(index, attr){return attr === 'password' ? 'text' : 'password'; });
    });
});




