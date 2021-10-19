$(document).on('submit', '#acceptUpdate', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'php/acceptUpdate.php',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                    console.log(data);
                    window.location.href = "admin/contactUs.php";
                }
            })
        });

        $(document).on('submit', '#removeUpdate', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'php/removeUpdate.php',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                    window.location.href = "admin/contactUs.php";
                }
            })
        });