$(document).ready( function () {
   if($('.page-number').length ===1){
       $('.js-prev').addClass('disabled');
       $('.js-next').addClass('disabled');
   }
});

$('.page-link').on('click', function () {
    let href = $(this).attr('href');
    let path = window.location.pathname;

    let currentPage = path.match(/\bpage[\/]?[\d+]?/gi);
    currentPage = currentPage[0].split('/')[1];

    let nextPage = href.match(/\bpage[\/]?[\d+]?/gi);
    nextPage = nextPage[0].split('/')[1];

    //set offset only if we choose next page
    if (nextPage>currentPage)
        $(this).attr('href', href + '/' + $('.js-offset').length) ;
});