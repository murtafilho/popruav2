
function select2(obj, url, caption) {
    $(obj).select2({
        placeholder: caption,
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true,
            minimumInputLength: 2
        }
    });
}


