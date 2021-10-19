function removeComplaint(idComplaint) {
            id = 'row-' + idComplaint;
            $(`#${id}`).hide();
        }

        $(document).on('submit', '#deleteReport', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../php/deleteComplaint.php',
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