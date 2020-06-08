/* show picture on placeholder after added*/
function readURL(input) {
    let inputFiles = this.files;
    if(inputFiles === undefined || inputFiles.length === 0) return;
    let index = input.currentTarget.id.split("_")[2];

    let reader = new FileReader();
    let holderId = "#illustration-holder-" +index;
    $(holderId).width(120).height(120);

    reader.onload = function () {
        $(holderId).attr('src', this.result);
        showDefaultImg();
    };
    reader.onerror = function() {
        alert("Une erreur est servenue: " + this.error.code);
    };
    reader.readAsDataURL(inputFiles[0]);

}
/*Hide or remove default img btn*/
function toggleBtnDefault(){
    let imgs = $('div[id*=illustration-]');

    if (imgs.length === 0) {
        $('#default-remove').hide();
    } else {
        $('#default-remove').show();
    }
}

/*show default img */
function showDefaultImg() {
    let nextImg = $('div[id*=illustration-]');
    let src = '';
    $(nextImg).each(function () {
        src = $(this).find('img').attr('src');
        if (src !== '/images/defaults/placeholder.png'){
            return false;
        }
    });
    if(src ==="/images/defaults/placeholder.png" || src === '') {
        $('#default-img-trick').attr('src','/images/defaults/illustration-default.png');
    } else {
        $('#default-img-trick').attr('src',src);
    }
}

/* Change placeholder with selected picture*/
$(function(){
    $('.custom-file-input').change('change', readURL);
});

/* Add illustration*/
$(".btn-add-image").on("click", function() {
    /*Get the number of future fields*/
    let $collectionHolder = $($(this).data("rel"));
    let index = $collectionHolder.data("index");

    /*Get the prototype of the inputs*/
    let prototype = $collectionHolder.data("prototype");

    /* Inject code into the div*/
    $collectionHolder.append(prototype.replace(/__name__/g, index));

    /* Change placeholder with selected picture*/
    $('.custom-file-input').change('change', readURL);

    $collectionHolder.data("index", index+1);
    toggleBtnDefault();
});

$(document).ready(function () {
    toggleBtnDefault();
    showDefaultImg();
});

/* Remove default img & show next one if exist*/
$('#default-remove').on('click', function () {
    let imgs = $('div[id*=illustration-]');
    console.log(imgs.length);
    if(imgs.length > 0){
        $(imgs[0]).find('.btn-remove').click();
    }
    if (imgs.length === 1) {
        $(this).hide();
    }
});

/*Edit default-img*/
$('#default-edit').on('click', function () {
    let imgs = $('div[id*=illustration-]');
    if(imgs.length > 0){
        $(imgs[0]).find('.btn-edit').trigger('click');
        $('div[id*=illustration-] .btn-edit')[0].click();
    } else {
        $('.btn-add-image').first().click();
        $('div[id*=illustration-] .btn-edit')[0].click();
    }
});

$(document).on('focus', '[id*="trick_videos_"]', function(){
    /*Save value by focus*/
    $(this).data('val', $(this).val());
}).on('click','[id*="modal-btn-"]', function(){
    let index = ($(this).attr('id').split("-"))[2];
    let embedCode = $('#trick_videos_'+ index+'_embedCode');
    let oldEmbedCode = embedCode.data('val');

    $('#trick_videos_'+index+'_embedCode_errors').remove();
    let patt = /(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))/g;
    let result = patt.test(embedCode.val());

    if(result){
        /*Adjust width & height in iframe*/
        embedCode.val(embedCode.val().replace(/width="\d+"/gi, 'width="120"'));
        embedCode.val(embedCode.val().replace(/height="\d+"/gi, 'height="120"'));
        /*Hide video placeholder*/
        $('#video-holder-'+ index ).hide();
        /*remove old Iframe*/
        $('#video-value-'+ index + '> iframe').remove();
        /*Show video*/
        $('#video-value-'+ index).prepend(embedCode.val());
        /*hide modal*/
        $('#modal-'+index).modal('hide');
    }else{
        let videoHolder = $('#video-holder-'+ index );
        if (videoHolder.length > 0){
            videoHolder.show();
            $('#video-value-'+ index + '> iframe').remove();
        } else if(!patt.test(oldEmbedCode)){
            let holder = '<img id="video-holder-'+index+'" width="120" height="118" src="{{ asset(\'images/defaults/video-placeholder.png\') }}"/>';
            $(holder).insertBefore($('#video-value-'+index));
            $('#video-value-'+ index + '> iframe').remove();
        }
        let error = '<div id="trick_videos_'+index+'_embedCode_errors" class="mb-2"><span class="invalid-feedback d-block"><span class="d-block"><span class="form-error-icon badge badge-danger text-uppercase">'+'Error'+'</span><span class="form-error-message"> Code de la video n\'est pas correct</span></span></span></div>';
        /*Add error*/
        $(error).insertBefore(embedCode);
        embedCode.val(oldEmbedCode);
    }
});

/*remove video from modal*/
$(document).on('click','[id*="modal-remove-"]', function(){
    let index = $(this).attr('id').split("-");
    setTimeout(function() {
        $('[data-rel="#video-'+index[2]+'"]').click();
    }, 200);
});

/* Add Video*/
$(".btn-add-video").on("click", function() {
    let $collectionHolder = $($(this).data("rel"));
    let index = $collectionHolder.data("index");
    let prototype = $collectionHolder.data("prototype");
    $collectionHolder.append(prototype.replace(/__name__/g, index));

    $collectionHolder.data("index", index+1);
});


/*Remove video or image*/
$("body").on("click", ".btn-remove", function() {
    let strBtn = $(this).data("rel");
    if(strBtn.startsWith("#illustration-")){
        setTimeout(function() {
            toggleBtnDefault();
            showDefaultImg();
        }, 100);
    }
    $(strBtn).remove();
});

/*Add disbled att to select category input*/
$('select > option[value= ""]').attr('disabled', true);

/*Add select2*/
import 'select2';

$('select').select2({
    placeholder: 'Choisir les groupes',
    allowClear: true
});

import '@ckeditor/ckeditor5-build-classic/build/translations/fr';
const ClassicEditor = require( '@ckeditor/ckeditor5-build-classic/build/ckeditor' );
ClassicEditor
    .create( document.querySelector( '#trick_description' ), {
        language: 'fr',
        toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote','undo','redo' ],
        height: 500
    })
    .catch( error => {
        console.log( error );
    }
);