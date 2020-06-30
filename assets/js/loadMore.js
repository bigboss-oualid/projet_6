function loadMore(holder, pages, currentPage){
    let loadBtn = $('a.js-load');
    if (currentPage > pages){
        loadBtn.remove()
    }

    let incrementPages = $('span.js-label');
    loadBtn.on('click', function (e) {
        let offset = $('.js-offset').length;
        e.preventDefault();
        let url = this.href;
        $.ajax(
            {
                url: url,
                data: {
                    offset: offset
                },
                dataType: "html",
                beforeSend: function() {
                    incrementPages.text('...')
                },
                success: function(loadedData) {
                    holder.append(loadedData);
                    currentPage++;
                    if (currentPage > pages){
                        e.currentTarget.remove()
                    } else{
                        incrementPages.text(currentPage)
                    }
                    let elts = $('html,body');
                    elts.animate({scrollTop: elts.height()}, 1000);
                },
                error: function(error)
                {
                    console.log(error);
                    alert('Error: ' + "une erreur technique est survenue, essayez d'actualiser la page!");
                }
            }
        );
    })
}



let holderContent = $(".load-content");
let data = holderContent.data('pagination')[0];
let page = data.currentPage;
let pagesNumber = data.pagesNumber;
$(document).ready(loadMore(holderContent, pagesNumber, page));