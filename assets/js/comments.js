new WOW().init();
let titreDiscussion = $('#titre-discussions > .js-no-comment');

//Add comment
$('#add-comment').on('click', function (e) {
    e.preventDefault();
    let errorComment = $('#error-comment');
    if (errorComment !== "undefined"){
        errorComment.remove()
    }
    let formComment = $('#formsComment');
    let url = formComment.attr('action');
    $.ajax({
        url: url,
        type: 'POST',
        data: formComment.serialize(),
        dataType : 'json',
        success: function (data) {
            if (titreDiscussion.text() !== ""){
                parentElt = titreDiscussion.parent().hide('slow');
                spanElt = '<span class="js-comment">Espace de discussion :</span>';
                $( ".js-no-comment" ).detach();
                parentElt.prepend(spanElt).show('slow');
            }

            let commentContent =$('#comment_content');
            commentContent.val('');
            commentContent.css({"border":"1px solid #ced4da", "box-shadow": "1px 1px #ced4da"});
            $(data.comment).prependTo('#comments').hide().show('slow');

        },
        error: function (data) {
            console.log(data.responseJSON.errors.content[0]);
            let commentError;
            if (data.status === 400) {
                let commentContent = $('#comment_content');
                commentContent.css({"border": "1px solid red"});
                commentError = '<div class="col-md-8 offset-md-1"><span id="error-comment" class="invalid-feedback d-block"><span class="d-block"><span class="mb-1 form-error-icon badge badge-danger">ERREUR </span><span class="form-error-message">' + data.responseJSON.errors.content[0] + '</span></span></span></div>';
                $(commentError).insertBefore($('#my-error-comment')).hide().show('slow')
            }
        }
    });
});

/*resize height from input textarea automatically*/
let observe;
if (window.attachEvent) {
    observe = function (element, event, handler) {
        element.attachEvent('on'+event, handler);
    };
}
else {
    observe = function (element, event, handler) {
        element.addEventListener(event, handler, false);
    };
}
function initTextarea() {
    let text = document.getElementById('comment_content');
    function resize () {
        text.style.height = 'auto';
        text.style.height = text.scrollHeight+'px';
    }
    /* 0-timeout to get the already changed text */
    function delayedResize () {
        window.setTimeout(resize, 0);
    }
    observe(text, 'change',  resize);
    observe(text, 'cut',     delayedResize);
    observe(text, 'paste',   delayedResize);
    observe(text, 'drop',    delayedResize);
    observe(text, 'keydown', delayedResize);

    text.focus();
    text.select();
    resize();
}
document.getElementById('comment_content').addEventListener("focus", function () {
    initTextarea();
});

//Load first comments
/*
$("document").ready(function() {
    setTimeout(function() {
        $("a.js-load").trigger('click');
    },10);
});*/
