$('#doctor').hide();
        $('#disease').hide();
        $('.post').hide();

        if ($('#sql').val() == 'post') {
            $('#doctor').hide();
            $('#disease').hide();
            $('.post').show();
        } else if ($('#sql').val() == 'doctor') {
            $('#doctor').show();
            $('#disease').hide();
            $('.post').hide();
        } else if ($('#sql').val() == 'disease') {
            $('#doctor').hide();
            $('#disease').show();
            $('.post').hide();
        } else {
            $('.post').show();

        }

        function clickToShow(dept) {
            $('.post').hide();
            $('#doctor').hide();
            $('#disease').hide();
            $(`#${dept}`).show();
            $(`.${dept}`).show();
        }

        $('#summernote').summernote({
            placeholder: 'Write Question',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['insert', ['picture']],
            ],
            callbacks: {
                onImageUpload: function(files, editor, welEditable) {
                    sendFile(files[0], this);
                }
            }
        });