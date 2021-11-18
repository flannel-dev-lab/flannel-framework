$(document).ready(function () {
    $('.dataTable').DataTable();
    $('#htmlEditor').summernote({
        tabsize: 2,
        height: 300,
        callbacks: {
            onBlur: function (e) {
                $('#html').val($('#htmlEditor').summernote('code'));
            }
        }
    });
});