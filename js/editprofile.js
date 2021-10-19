function HideShow() {
      $('#change_password').toggle();
      $('#show1').toggle();
    }


    $(document).on('submit', '#update', function(e) {
      e.preventDefault();
      var Form = $(this);
      $.ajax({
        type: 'POST',
        url: 'php/editProfile.php',
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function(data) {
          if (data == 'doctorProfile.php') {
            location.href = 'doctorProfile.php';
          } else if (data == 'userProfile.php') {
            location.href = 'userProfile.php';
          } else {
            $("#errorPHP").html(data);
          }
        },
      })
    });

    function cancel() {
      $('#change-photo').val('');
    }

    function changeImg() {
      var input = document.getElementById("file");
      var fReader = new FileReader();
      fReader.readAsDataURL(input.files[0]);
      fReader.onloadend = function(event) {
        var img = document.getElementById("img1");
        img.src = event.target.result;
      }
    }