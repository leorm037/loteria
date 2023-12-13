function urlParamUpdate(url, param, value, clearParams = false) {
    let newUrl = new URL(url);

    if (clearParams) {
        newUrl.searchParams.forEach((value, key, object) => object.delete(key));
    }

    if (!!value) {
        newUrl.searchParams.set(param, value);
    } else {
        newUrl.searchParams.delete(param);
    }

    return newUrl.href;
}

function redirectUrl(url, param = null, value = null, clearParams = false) {
    let newUrl = url;
    
    $.LoadingOverlay('show');
    
    if (!!param) {
        newUrl = urlParamUpdate(url, param, value, clearParams);
    }

    $(location).attr('href', newUrl);
    $(window).attr('location', newUrl);
    $(location).prop('href', newUrl);
    
    $.LoadingOverlay('hide');
}

$('input[required], select[required]').each(function (index, element) {
    let id = $(element).attr('id');
    
    $('label[for="' + id + '"').append(' *');
});