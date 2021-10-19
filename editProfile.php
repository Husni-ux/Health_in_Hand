<?php
include "php/connect.php";
session_start();

if (!isset($_SESSION['lang']))
  $_SESSION['lang'] = "ar";
else if (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
  if ($_GET['lang'] == "en")
    $_SESSION['lang'] = "en";
  else if ($_GET['lang'] == "ar")
    $_SESSION['lang'] = "ar";
}

require_once "languages/" . 'editProfile_' . $_SESSION['lang'] . ".php";

$row;
if (isset($_SESSION['email'])) {
  $person = $_SESSION['person'];
  $stmt = $con->prepare("select * from $person where email  = ?");
  $stmt->execute([$_SESSION['email']]);
  $row = $stmt->fetch();

  if ($person == 'doctor') {

    $dept = $con->prepare("select * from dept where id = ?");
    $dept->execute([$row['deptno']]);
    $dept = $dept->fetch();
    $deptName = $dept['name'];
    $dept = $dept['id'];
  }
} else {
  header('location:index.php');
}

if (isset($_POST['cancel'])) {
  if ($person == 'doctor') {
    header('Location:doctorProfile.php');
  } else {
    header('Location:userProfile.php');
  }
}

if (isset($_POST['logout'])) {
  session_destroy();
  header("location:index.php");
}

?>
<!DOCTYPE html>
<html>

<head>
  <title>Edit profile</title>
  <link href="img/HIH2.jpg" rel="icon">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="style/style6.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>


  <!-- ======= Header ======= -->
  <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <h1 class="logo mr-auto">
      <a style='text-decoration: none;' href="index.php">Health In Hand</a>
    </h1>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
      <div class="topbar-divider d-none d-sm-block"></div>
      <!-- Nav Item - User Information -->
      <form style="margin-right: 40px;" method="POST" class="form-inline my-2 my-lg-0">
        <?php if (isset($_SESSION['email'])) { ?>
          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="<?php echo $row['img'] ?>" width="40" height="40" class="rounded-circle">
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="<?php echo $person ?>Profile.php"><?php echo $lang['profile'] ?></a>
                <a class="dropdown-item" href="editProfile.php"><?php echo $lang['editProfile'] ?></a>
                <input class="dropdown-item" type="submit" name="logout" value="<?php echo $lang['logout'] ?>">
              </div>
            </li>
          </ul>
        <?php } else { ?>
          <a href="login.php" class="btn btn-outline-secondary" style='margin-left:10px'><?php echo $lang['login'] ?></a>
        <?php } ?>
      </form>
    </ul>
  </nav>
  <div class="row">
    <div class="rightcolumn">
      <div class="card">
        <form id='update' method="POST" class="form-horizontal" enctype="multipart/form-data">
          <div class="container">
            <h1><?php echo $lang['editProfile'] ?></h1>
            <hr>
            <div class="row">
              <!-- left column -->
              <div class="col-md-3">
                <div class="profile-photo-div" id="profile-photo-div">
                  <div class="profile-img-div" id="profile-img-div">
                    <div id="loader"></div><img id="profile-img" src="<?php echo $row['img'] ?>" />
                    <input id="x-position" type="range" name="x-position" value="0" min="0" />
                    <input id="y-position" type="range" name="y-position" value="0" min="0" />
                  </div>
                  <div class="profile-buttons-div">
                    <div class="profile-img-input" id="profile-img-input">
                      <label class="button" id="change-photo-label" for="change-photo"><?php echo $lang['uploadPhoto'] ?></label>
                      <input id="change-photo" name="img" type="file" style="display: none" />
                    </div>
                    <div class="profile-img-confirm" id="profile-img-confirm" style="display: none">
                      <div class="button half green" id="save-img"><i class="fa fa-check" aria-hidden="true"></i></div>
                      <div onclick='cancel()' class="button half red" id="cancel-img"><i class="fa fa-remove" aria-hidden="true"></i></div>
                    </div>
                  </div>
                </div>
                <div class="error" id="error">min sizes 400*400px</div>
                <canvas id="croppedPhoto" width="400" height="400"></canvas>
              </div>
              <!-- edit form column -->
              <div class="col-md-9 personal-info">
                <h3><?php echo $lang['info'] ?></h3>
                <div class="form-group">
                  <label class="col-lg-3 control-label"><?php echo $lang['firstName'] ?>:</label>
                  <div class="col-lg-8">
                    <input name="fname" class="form-control" id="fname" type="text" value='<?php echo $row['first_name'] ?>' disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-3 control-label"><?php echo $lang['lastName'] ?>:</label>
                  <div class="col-lg-8">
                    <input name="lname" class="form-control" id="laname" type="text" value='<?php echo $row['last_name'] ?>' disabled>
                  </div>
                </div>
                <?php
                if ($person == 'doctor') {
                ?>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['major'] ?>:</label>
                    <div class="col-lg-8">
                      <input name="major" class="form-control" id="major" type="text" value='<?php echo $deptName ?>' disabled>
                    </div>
                  </div>
                <?php } ?>
                <div class="form-group">
                  <label class="col-lg-3 control-label"><?php echo $lang['email'] ?>:</label>
                  <div class="col-lg-8">
                    <input name="email" class="form-control" id="email" type="text" value='<?php echo $row['email'] ?>' disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-3 control-label"><?php echo $lang['gender'] ?></label>
                  <div class="col-lg-8">
                    <input name="gender" class="form-control" id="gender" type="text" value='<?php echo $row['gender'] ?>' disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-3 control-label"><?php echo $lang['birth'] ?></label>
                  <div class="col-lg-8">
                    <input class="form-control" type="text" value='<?php echo $row['birth'] ?>' disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-3 control-label"><?php echo $lang['country'] ?>:</label>
                  <div class="col-lg-8">
                    <input name="country" class="form-control" id="country" type="text" value='<?php echo $row['country'] ?>'>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-lg-3 control-label"><?php echo $lang['phone'] ?></label>
                  <div class="col-lg-8">
                    <input name="phone" class="form-control" id="phone" type="text" value='<?php echo '0'.$row['phone'] ?>' pattern="(077|078|079)[0-9]{7}" <?php if($_SESSION['person'] == 'doctor') echo 'required' ?> >
                  </div>
                </div>
                <?php if ($person == 'doctor') { ?>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['licenseNumber'] ?></label>
                    <div class="col-lg-8">
                      <input name="license_number" class="form-control" id="license_number" type="text" value='<?php echo $row['license_number'] ?>'>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['licenseImg'] ?></label>
                    <div class="col-lg-8">
                      <img id="img1" src="<?php echo $row['license_img'] ?>" style="height:150px;width:200px">
                      <input type="file" id="file" name="license_img" onchange="changeImg()">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['nameClinic'] ?></label>
                    <div class="col-lg-8">
                      <input name="n_clinic" class="form-control" id="n_clinic" type="text" value='<?php echo $row['name_clinic'] ?>'>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['addressClinic'] ?></label>
                    <div class="col-lg-8">
                      <input name="a_clinic" class="form-control" id="a_clinic" type="text" value='<?php echo $row['address_clinic'] ?>'>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['sTime'] ?></label>
                    <div class="col-lg-8">
                      <input name="sTime" class="form-control" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" id="sTime" type="text" value='<?php echo substr($row['sTime'], 0, -3) ?>'>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['eTime'] ?></label>
                    <div class="col-lg-8">
                      <input name="eTime" class="form-control" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" id="eTime" type="text" value='<?php echo  substr($row['eTime'], 0, -3) ?>'>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['lat'] ?></label>
                    <div class="col-lg-8">
                      <input name="lat" class="form-control" type="text" value='<?php echo $row['lat'] ?>'>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-lg-3 control-label"><?php echo $lang['lng'] ?></label>
                    <div class="col-lg-8">
                      <input name="lng" class="form-control" type="text" value='<?php echo $row['lng'] ?>'>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label" for="textarea"><?php echo $lang['bio'] ?></label>
                    <div class="col-lg-8">
                      <textarea id="textarea" name="bio" cols="40" rows="3" class="form-control"><?php echo $row['bio'] ?></textarea>
                    </div>
                  </div>
                <?php } ?>
                <div class="form-group">
                  <label style='cursor:pointer' id='show1' onclick="HideShow()" class="col-md-3 control-label"><?php echo $lang['changePassword'] ?></label>
                </div>
                <div id='change_password' style='display:none'>
                  <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo $lang['password'] ?>:</label>
                    <div class="col-md-8">
                      <input name="pass" class="form-control" id="pass" type="password" value="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo $lang['confirmPassword'] ?></label>
                    <div class="col-md-8">
                      <input name="c_pass" class="form-control" id="c_pass" type="password" value="">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label"></label>
                  <div class="col-md-8">
                    <div id='errorPHP'></div>
                    <input id='save' name='submit' type="submit" class="btn btn-primary" value="<?php echo $lang['save'] ?>">
                    <a class="btn btn-default" href='<?php echo $person ?>Profile.php'><?php echo $lang['cancel'] ?></a>
                  </div>
                </div>
        </form>
      </div>
    </div>
  </div>
  <hr>
  <div class="footer">
    <!-- ======= Footer ======= -->
    <footer id="footer">
      <div class="container d-md-flex py-4">
        <div class="mr-md-auto text-center text-md-left">
          <div class="copyright">
            &copy; Copyright <strong><span>AHU</span></strong>. All Rights Reserved
          </div>
          <div class="credits">
            Designed by <a href="#">Health In Hand</a>
          </div>
        </div>
      </div>
    </footer><!-- End Footer -->
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></script>
  <script src="js/changePhoto.js"></script>
  <script src="js/editprofile.js"></script>
</body>

</html>