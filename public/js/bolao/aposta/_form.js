$('#bolao_aposta_form_dezenasMarcar').on('change', function () {
    let dezenasQuantidade = $(this).val();
    let dezenas = [];

    $('#bolao_aposta_form_dezenas').find('input').each(function (index, element) {
        dezenas.push($(element).val());
    });

    $('#bolao_aposta_form_dezenas').empty();

    for (var i = 0; i < dezenasQuantidade; i++) {
        var html = '<div class="col-2 mb-3"><input type="number" id="bolao_aposta_form_dezenas_' + i + '" name="bolao_aposta_form[dezenas][' + i + ']" required="required" min="0" max="0" class="form-control" value="0"></div>';
        
        $('#bolao_aposta_form_dezenas').append(html);
    }

    $('#bolao_aposta_form_dezenas').find('input').each(function (index, element) {
        $(element).val(dezenas[index]).prop('min', DEZENAS_MIN).prop('max', DEZENAS_MAX);
    });

});