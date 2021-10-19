<?php
session_start();

if (!isset($_SESSION['lang']))
  $_SESSION['lang'] = "ar";
else if (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
  if ($_GET['lang'] == "en")
    $_SESSION['lang'] = "en";
  else if ($_GET['lang'] == "ar")
    $_SESSION['lang'] = "ar";
}

require_once "languages/" . 'login_' . $_SESSION['lang'] . ".php";

if (isset($_SESSION['email'])) {
  header("location:index.php");
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, 
     user-scalable=0'>
  <title>Login</title>
  <link rel="stylesheet" href="style/style4.css">
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- model for signup click -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <!-- ajax file code -->
  <script src="js/ajax.js"></script>
  <link href="img/HIH2.jpg" rel="icon">

</head>

<body>
  <div class="container">
    <div class="content">
      <header><?php echo $lang['login'] ?></header>
      <form method="POST" id="login">
        <div class="field">
          <span class="fa fa-user"></span>
          <input type="email" required placeholder="<?php echo $lang['email'] ?>" name="email">
        </div>
        <div class="field space">
          <span class="fa fa-lock"></span>
          <input type="password" class="pass-key" required placeholder="<?php echo $lang['password'] ?>" name="password">
          <span class="show"><?php echo $lang['show'] ?></span>
        </div>
        <div class="pass">
          <a href="email.php"><?php echo $lang['lostPassword'] ?></a>
        </div>
        <div class="field">
          <input id='enter' type="submit" value="<?php echo $lang['login'] ?>" name="login">
        </div>
        <div id="error"></div>`
      </form>
      <div class="login">
      </div>
      <div class="signup">
        <?php echo $lang['dontHaveAccount'] ?>
        <a data-toggle="modal" data-target="#myModal"><?php echo $lang['signUp'] ?></a>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php echo $lang['signUpAsa'] ?></h4>
        </div>
        <div class="modal-body">
          <a href="signupDoc.php"><button class="button"><?php echo $lang['doctor'] ?></button< /a>
              <a href="signup.php"><button class="button"><?php echo $lang['user'] ?></button< /a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['close'] ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const pass_field = document.querySelector('.pass-key');
    const showBtn = document.querySelector('.show');
    showBtn.addEventListener('click', function() {
      if (pass_field.type === "password") {
        pass_field.type = "text";
        <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') { ?>
          showBtn.textContent = "اخفاء";
        <?php } else { ?>
          showBtn.textContent = "HIDE";
        <?php } ?>
        showBtn.style.color = "#3498db";
      } else {
        pass_field.type = "password";
        <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') { ?>
          showBtn.textContent = "اظهار";
        <?php } else { ?>
          showBtn.textContent = "SHOW";
        <?php } ?>
        showBtn.style.color = "#222";
      }
    });
  </script>
</body>

</html>