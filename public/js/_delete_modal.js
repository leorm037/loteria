$('a.btn-danger[data-delete]').each(function () {
    console.log(this);
    $(this).on('click', function () {
        $('#deleteModal').modal('show');

        let id = $(this).data('delete');

        $('#deleteModalConfirm').on('click', function () {
            $.LoadingOverlay('show');
            let url = BASE_URL + ENTITY_URL + '/' + id + '/delete';
            
            redirectUrl(url);
        });
    });
});