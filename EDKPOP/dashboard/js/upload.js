function upload(url) {
    var progressBar = $('#progress_bar');
    var percentText = $('#percent_text');

    $('#upload_form').ajaxForm({
        beforeSubmit: function() {
            var percentValue = '0%';

            document.getElementById("progress_div").style.display = "block";
            progressBar.width(percentValue);
            percentText.html(percentValue);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentValue = percentComplete + '%';

            progressBar.width(percentValue);
            percentText.html(percentValue);
        },
        success: function() {
            var percentValue = '100%';

            progressBar.width(percentValue);
            percentText.html(percentValue);

            if (url != null) {
                location.href = url;
            }
        },
        complete: function(xhr) {
        }
    });
}
