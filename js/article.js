$(document).on('submit', '#sendArticle', function(e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: 'php/sendArticle.php',
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function(data) {
          $("#messageContactUS").html(data);
          $("#message").val('');
        }
      })
    });

    function deletePost(idPost) {

      var modal = document.getElementById(`deleteModal`);

      var post = document.getElementById(`post-${idPost}`);

      post.onclick = function() {
        modal.style.display = "block";
      }

      // Get the <span> element that closes the modal
      var closeAlert = document.getElementById('closeAlert');
      var deletePost = document.getElementById('deletePostArticle');

      closeAlert.onclick = function() {
        modal.style.display = "none";
      }

      deletePost.onclick = function() {
        $('#idPostArticle').val(idPost);
      }
    }

    $(document).on('submit', '#deletePost', function(e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: 'php/deletePost.php',
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function(data) {
          location.reload();
        }
      })
    });

    function complaint(idPost) {

      var modal = document.getElementById(`complaint`);
      var closeAlert = document.getElementById('closeAlertCompaint');
      var sendCompalin = document.getElementById('sendComplaint');

      var complaint = document.getElementById(`complaint-${idPost}`);

      complaint.onclick = function() {
        modal.style.display = "block";
      }

      closeAlert.onclick = function() {
        modal.style.display = "none";
      }

      sendCompalin.onclick = function() {
        $('#idArticleForComlaint').val(idPost);
      }
    }

    $(document).on('submit', '#complaintForm', function(e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: 'php/complaint.php',
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function(data) {
          $('#sendComplaint').hide();
          $('#inputComplaint').hide();
          setTimeout(function() {
            location.reload()
          }, 2000);
          if (data == 'done' || data == 'تم') 
            $('#alertDone').show();
          else
            $('#alertError').show();
          console.log(data);
        }
      })
    });