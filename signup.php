<?php
session_start();
if (isset($_SESSION['email'])) {
  header('Location:index.php');
}

if (!isset($_SESSION['lang']))
  $_SESSION['lang'] = "ar";
else if (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
  if ($_GET['lang'] == "en")
    $_SESSION['lang'] = "en";
  else if ($_GET['lang'] == "ar")
    $_SESSION['lang'] = "ar";
}

require_once "languages/" . 'signUp_' . $_SESSION['lang'] . ".php";

?>
<!DOCTYPE html>
<html>

<head>
  <title>sign up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="img/HIH2.jpg" rel="icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" href="style/style2.css">
  <!-- ajax file code -->
  <script src="js/ajax.js"></script>
  <script src="js/signup.js"></script>
</head>

<body>
  <div class="container">
    <form method="POST" id="signup">
      <div class="row">
        <h2><?php echo $lang['signUp']?></h2>
        <div class="input-group input-group-icon">
          <input id="fname" name="fname" type="text" placeholder="<?php echo $lang['fName']?>" required />
          <div class="input-icon"><i class="fa fa-user"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="lname" name="lname" type="text" placeholder="<?php echo $lang['lName']?>" required />
          <div class="input-icon"><i class="fa fa-user"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="email" name="email" type="email" placeholder="<?php echo $lang['email']?>" required />
          <div class="input-icon"><i class="fa fa-envelope"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="password" name="password" type="password" placeholder="<?php echo $lang['password']?>" required />
          <div class="input-icon"><i class="fa fa-key"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="repasword" name="repassword" type="password" placeholder="<?php echo $lang['rePassword']?>" required />
          <div class="input-icon"><i class="fa fa-key"></i></div>
        </div>
      </div>
      <div class="row">
        <div class="col-half">
          <h2><?php echo $lang['birth']?></h2>
          <input id="birth" type="date" name="brith" class="form_control" required>
        </div>
        <div style='padding:10px' class="col-half ">
          <h3><?php echo $lang['Gender']?></h3>
          <div class="input-group">
            <input type="radio" name="gender" value="male" id="gender-male" required />
            <label for="gender-male"><?php echo $lang['male']?></label>
            <input type="radio" name="gender" value="female" id="gender-female" />
            <label for="gender-female"><?php echo $lang['female']?></label>
          </div>
          <div id="error"></div>
        </div>
      </div>
      <div class="row">
        <h3><?php echo $lang['condition']?></h3>
        <div class="input-group">
          <input type="checkbox" id="terms" required />
          <label for="terms"><?php echo $lang['message']?></label>
        </div>
        <div id="error2"></div>
      </div>
      <input type="hidden" name="page" value="user">
      <button name="submit" type="submit" class="signup" onclick="check()"> <?php echo $lang['signUp']?></button>
      <div id="error3"></div>
      <input id="lang" type="hidden" value="<?php echo $_SESSION['lang']?>">

  </div>
  </form>
  </div>
</body>

</html>