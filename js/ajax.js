// login form
$(document).on('submit', '#login', function (e) {
  e.preventDefault();
  var Form = $(this);
  $.ajax({
    type: 'POST',
    url: 'php/login.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $("#error").html(data);
    },
  })
});

// set email
$(document).on('submit', '#check', function (e) {
  e.preventDefault();
  var Form = $(this);
  $.ajax({
    type: 'POST',
    url: 'php/email.php',
    beforeSend: function () {
      Form.find("button[type='submit']").prepend('<i class="fa fa-circle-o-notch fa-spin" style="font-size:20px"> </i>');
      Form.find("button[type='submit']").attr('disabled', 'true');
    },
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $("#error").html(data);
    },
    complete: function () {
      $('.fa').remove();
      Form.find("button[type='submit']").removeAttr('disabled');
      $(document).ready(function () {
        $('#email').val('');
      });
    },
  })
});

// reset password
$(document).on('submit', '#confirm_pass', function (e) {
  e.preventDefault();
  var Form = $(this);
  $.ajax({
    type: 'POST',
    url: 'php/resetpass.php',
    beforeSend: function () {
      Form.find("button[type='submit']").prepend('<i class="fa fa-circle-o-notch fa-spin" style="font-size:20px"> </i>');
      Form.find("button[type='submit']").attr('disabled', 'true');
    },
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $("#error").html(data);
    },
    complete: function () {
      $('.fa').remove();
      Form.find("button[type='submit']").removeAttr('disabled');
      $(document).ready(function () {
        $('#pass').val('');
        $('#Re-pass').val('');
      });
    },
  })
});

// sign up
$(document).on('submit', '#signup', function (e) {
  e.preventDefault();
  var Form = $(this);
  $.ajax({
    type: 'POST',
    url: 'php/signup.php',
    beforeSend: function () {
      Form.find("button[type='submit']").prepend('<i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i>');
      Form.find("button[type='submit']").attr('disabled', 'true');
    },
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $("#error3").html(data);
    },
    complete: function () {
      $('.fa').remove();
      Form.find("button[type='submit']").removeAttr('disabled');
    },
  })
});

// sign uo doctor
$(document).on('submit', '#signupDoc', function (e) {
  e.preventDefault();
  var Form = $(this);
  $.ajax({
    type: 'POST',
    url: 'php/signupDoc.php',
    beforeSend: function () {
      Form.find("button[type='submit']").prepend('<i class="fa fa-circle-o-notch fa-spin" style="font-size:24px"></i>');
      Form.find("button[type='submit']").attr('disabled', 'true');
    },
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $("#error3").html(data);
    },
    complete: function () {
      $('.fa').remove();
      Form.find("button[type='submit']").removeAttr('disabled');
    },
  })
});
// contact us /** index ** /
$(document).on('submit', '#contactUs', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'POST',
    url: 'php/contactUs.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $('#messageContactUS').html(data);
      $('#name').val('');
      $('#message1').val('');
      $('#message2').val('');
      $('#email').val('');
    }
  })
});
// share post /* result page */
$(document).on('submit', '#share_post', function (e) {
  e.preventDefault();
  var Form = $(this);
  $.ajax({
    type: 'POST',
    url: 'php/share_post.php',
    data: new FormData(this),
    contentType: false,
    processData: false,
    success: function (data) {
      $("#error").html(data);
    },
  })
});
/* send image  result page */
function sendFile(file, el) {
  var data = new FormData();
  data.append('file', file);
  $.ajax({
      data: data,
      type: "POST",
      url: 'php/uploadeImage.php',
      cache: false,
      contentType: false,
      processData: false,
      success: function(url) {
          $(el).summernote('editor.insertImage', url);
          $('#image').val(url);
      }
  });
}
// /* admin register admin  */
$(document).on('submit', '#addAdmin', function(e) {
  e.preventDefault();
  $.ajax({
      type: 'POST',
      url: '../php/addAdmin.php',
      data: new FormData(this),
      contentType: false,
      processData: false,
      success: function(data) {
          $('#error').html(data);
          $('#fName').val('');
          $('#lName').val('');
          $('#email').val('');
          $('#password').val('');
          $('#conPassword').val('');

      }
  })
});
