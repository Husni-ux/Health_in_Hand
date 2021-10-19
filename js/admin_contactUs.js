function remove(id) {
            id = 'row-' + id;
            $(`#${id}`).hide();
        }

        $(document).on('submit', '#deleteReport', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../php/deleteContact.php',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                }
            })
        });

        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#table tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });