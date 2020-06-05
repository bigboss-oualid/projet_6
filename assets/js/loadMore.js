function loaddmore(divElt, pagesNumber){
    let page = 1
    let incrementPages = $('span.js-label')
    $('a.js-load').on('click', function (e) {
        e.preventDefault();
        let url = this.href;
        $.ajax(
            {
                url: url,
                data: {
                    page: page
                },
                dataType: "html",
                beforeSend: function() {
                    incrementPages.text('...')
                },
                success: function(data) {
                    divElt.append(data);
                    page++;
                    if (page > pagesNumber){
                        e.currentTarget.remove()
                    } else{
                        incrementPages.text(page)
                    }
                    let elts = $('html,body');
                    elts.animate({scrollTop: elts.height()}, 1000);
                },
                error: function(e)
                {
                    alert('Error: ' + e);
                }
            }
        );
    })
}

let divContent = $(".load-content");
let pages = divContent.data('pages');
$(document).ready(loaddmore(divContent, pages));