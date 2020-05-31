let titreDiscussion = $('#titre-discussions > .js-no-comment')

//Add comment
$('#add-comment').on('click', function (e) {
    e.preventDefault();
    if ($('#error-comment')!== "undefined"){
        $('#error-comment').remove()
    }
    let url = $('#formsComment').attr('action');
    $.ajax({
        url: url,
        type: 'POST',
        data: $('#formsComment').serialize(),
        dataType : 'json',
        success: function (data) {
            if (titreDiscussion.text() !== ""){
                parentElt = titreDiscussion.parent().hide('slow')
                spanElt = '<span class="js-comment">Espace de discussion :</span>';
                $( ".js-no-comment" ).detach()
                parentElt.prepend(spanElt).show('slow')
            }
            $('#comment_content').val('');
            $('#comment_content').css({"border":"1px solid #ced4da", "box-shadow": "1px 1px #ced4da"})
            $(data.comment).prependTo('#comments').hide().show('slow');

        },
        error: function (data) {
            console.log(data.responseJSON.errors.content[0]);
            let commentError;
            if (data.status === 400) {
                $('#comment_content').css({"border": "1px solid red"})
                commentError = '<span id="error-comment" class="invalid-feedback d-block"><span class="d-block"><span class="mb-2 form-error-icon badge badge-danger">ERREUR </span><span class="form-error-message"> ' + data.responseJSON.errors.content[0] + '</span></span></span>'
                $(commentError).insertBefore($('textarea')).hide().show('slow')
            }
        }
    });
});

//Load first comments
$("document").ready(function() {
    setTimeout(function() {
        $("a.js-load").trigger('click');
    },10);
});