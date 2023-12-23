$("button[name='filter']").on('click', function () {
    let url = $(location).prop('href');
    let apostadorNome = $('#apostador_nome').val();
    let apostadorPago = $('#apostador_pago').val();
    
    let params = [];
    
    if (apostadorNome) {
        params.push(['nome', apostadorNome]);
    }
    
    if (apostadorPago) {
        params.push(['pago', apostadorPago]);
    }
    
    redirectParamsUrl(url, params, true);
});