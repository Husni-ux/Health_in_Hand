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
  <link rel="stylesheet" href="style/style5.css">
  <link rel="stylesheet" href="style/header.css">
  <!-- ajax file code -->
  <script src="js/ajax.js"></script>
</head>

<body>

  <div class="container">
    <form method="POST" id="signupDoc">
      <div class="row">
        <h2><?php echo $lang['signUpAs'] ?> <span style="color:#3385ff"><?php echo $lang['doctor'] ?></span></h2>

        <div class="input-group input-group-icon">
          <input id="fname" name="fname" type="text" placeholder="<?php echo $lang['fName'] ?>" required />
          <div class="input-icon"><i class="fa fa-user"></i></div>
        </div>

        <div class="input-group input-group-icon">
          <input id="lname" name="lname" type="text" placeholder="<?php echo $lang['lName'] ?>" required />
          <div class="input-icon"><i class="fa fa-user"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="email" name="email" type="email" placeholder="<?php echo $lang['email'] ?>" required />
          <div class="input-icon"><i class="fa fa-envelope"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input type="tel" name="phone" placeholder="<?php echo $lang['phone'] ?> (07xxxxxxxx )" pattern="(077|078|079)[0-9]{7}" required />
          <div class="input-icon"><i class="fa fa-phone-square"></i></div>
        </div>

        <div class="input-group input-group-icon">

          <select class="select" aria-label=".form-select-lg example" name="specialization">
            <option value="0" selected><?php echo $lang['major'] ?></option>
            <?php
            include 'php/connect.php';
            $dept = $con->prepare("select * from dept");
            $dept->execute();
            if ($_SESSION['lang'] == 'en') {
              foreach ($dept as $ar) { ?>
                <option value="<?php echo $ar['id'] ?>"><?php echo $ar['name'] ?></option>
              <?php }
            } else {
              foreach ($dept as $ar) { ?>
                <option value="<?php echo $ar['id'] ?>"><?php echo $ar['name_ar'] ?></option>
            <?php }
            }

            ?>
          </select>
        </div>

        <div class="input-group input-group-icon">
          <input type="text" placeholder="<?php echo $lang['numIicense'] ?>" name='license number' />
          <div class="input-icon"><i class="fa fa-id-card-o"></i></div>
        </div>
        <?php echo $lang['imgForLicence'] ?>
        <div class="input-group input-group-icon">
          <div class="input-group mb-3">
            <input type="file" class="form-control" name='license_img'>
          </div>
          <div class="input-icon"><i class="fa fa-file"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="password" name="password" type="password" placeholder="<?php echo $lang['password'] ?>" required />
          <div class="input-icon"><i class="fa fa-key"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="confirm Password" name="repassword" type="password" placeholder="<?php echo $lang['rePassword'] ?>" required />
          <div class="input-icon"><i class="fa fa-key"></i></div>
        </div>
      </div>
      <div class="row">
        <div class="col-half">
          <h2><?php echo $lang['birth'] ?></h2>
          <input id="birth" type="date" name="birth" class="form_control" required>
        </div>

        <div style='padding:10px' class="col-half">
          <h3><?php echo $lang['Gender'] ?></h3>
          <div class="input-group">
            <input type="radio" name="gender" value="male" id="gender-male" required />
            <label for="gender-male"><?php echo $lang['male'] ?></label>
            <input type="radio" name="gender" value="female" id="gender-female" />
            <label for="gender-female"><?php echo $lang['female'] ?></label>
          </div>
          <div id="error"></div>

        </div>
      </div>

      <div class="row">
        <h3><?php echo $lang['condition'] ?></h3>
        <div class="input-group">
          <input type="checkbox" id="terms" required />
          <label for="terms"><?php echo $lang['message'] ?></label>
          <div id="error2"></div>
        </div>
      </div>

      <div id="error"></div>
      <input type="hidden" name="page" value="doctor">
      <input type="hidden" name="lat" id="lat" value="">
      <input type="hidden" name="lng" id="lng" value="">
      <button name="submit" type="submit" class="signup" onclick="check()"> <?php echo $lang['signUp'] ?></button>
      <div id="error3"></div>

  </div>
  <input id="lang" type="hidden" value="<?php echo $_SESSION['lang']?>">

  </form>
  </div>


  <script src="js/signup.js"></script>


</body>

</html>