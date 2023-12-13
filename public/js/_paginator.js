$("select[name='maxResult']").on('change', function () {
    let url = $(location).prop('href');
    url = urlParamUpdate(url, 'page', 1);
    redirectUrl(url, 'maxResult', $(this).val());
});

$("button[data-paginator-page]").on('click', function () {
    let url = $(location).prop('href');
    redirectUrl(url, 'page', $(this).data('paginator-page'));
});