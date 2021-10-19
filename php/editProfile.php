<?php
include 'connect.php';
session_start();
$row;
$message;

if (isset($_SESSION['email'])) {
  $person = $_SESSION['person'];
  $stmt = $con->prepare("select * from $person where email  = ?");
  $stmt->execute([$_SESSION['email']]);
  $row = $stmt->fetch();

  if ($person == 'doctor') {
    $stmt = $con->prepare('select * from dept where id  = ?');
    $stmt->execute([$row['deptno']]);
    $dept = $stmt->fetch();
    $deptName = $dept['name'];
  }
}

$country = strip_tags($_POST['country']);
$phone = strip_tags($_POST['phone']);
$password = strip_tags($_POST['pass']);
$c_pass = strip_tags($_POST['c_pass']);

// if(isset($_POIST['bio'])){
//   $bio = strip_tags($_POST['bio']);

// }

if (($row['country'] == null || $country != $row['country']) && $country != null) {
  $stmt = $con->prepare("UPDATE $person SET country = '$country' where id= ?");
  $stmt->execute([$row['id']]);
}

if (($row['phone'] == null || $phone != $row['phone']) && $phone != null) {
  $stmt = $con->prepare("UPDATE $person SET phone = $phone where id= ?");
  $stmt->execute([$row['id']]);
}

if (isset($_POST['n_clinic'])) {
  $n_clinic =  strip_tags($_POST['n_clinic']);
  if (($row['name_clinic'] == null || $n_clinic != $row['name_clinic']) && $n_clinic != null) {
    $stmt = $con->prepare("UPDATE $person SET name_clinic = '$n_clinic' where id= ?");
    $stmt->execute([$row['id']]);
  }
}
if (isset($_POST['a_clinic'])) {
  $a_clinic =  strip_tags($_POST['a_clinic']);
  if (($row['address_clinic'] == null || $a_clinic != $row['address_clinic']) && $a_clinic != null) {
    $stmt = $con->prepare("UPDATE $person SET address_clinic = '$a_clinic' where id= ?");
    $stmt->execute([$row['id']]);
  }
}

if (isset($_POST['lat'])) {
  $lat =  strip_tags($_POST['lat']);
  if (($row['lat'] == null || $lat != $row['lat']) && $lat != null) {
    $stmt = $con->prepare("UPDATE $person SET lat = '$lat' where id= ?");
    $stmt->execute([$row['id']]);
  }
}

if (isset($_POST['license_number'])) {
  $license_number =  strip_tags($_POST['license_number']);
  if (($row['lat'] == null || $license_number != $row['license_number']) && $license_number != null) {
    $stmt = $con->prepare("UPDATE $person SET license_number = '$license_number' , hide = 0 where id= ?");
    $stmt->execute([$row['id']]);
  }
}

if (isset($_FILES["license_img"]["name"]) && $_FILES["license_img"]["name"] != null) {
  $filename = $_FILES["license_img"]["name"];
  $tempname = $_FILES["license_img"]["tmp_name"];
  $folder = "img/doctor/license_img" . $filename;
  $stmt = $con->prepare("UPDATE $person SET license_img = ? where id= ?");
  $stmt->execute([$folder, $row['id']]);
  move_uploaded_file($tempname, '../' . $folder);
}

if (isset($_POST['lng'])) {
  $lng =  strip_tags($_POST['lng']);
  if (($row['lng'] == null || $lat != $row['lng']) && $lat != null) {
    $stmt = $con->prepare("UPDATE $person SET lng = '$lat' where id= ?");
    $stmt->execute([$row['id']]);
  }
}
$j = 0;
if (isset($_POST['sTime'])) {
  $sTime =  strip_tags($_POST['sTime']);
  $eTime =  strip_tags($_POST['eTime']);
  if ($sTime >= $eTime) {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
      echo "<div class='alert alert-warning' role='alert'>وقت البدء أكبر من أو يساوي وقت الانتهاء. </div>";
    } else {
      echo "<div class='alert alert-warning' role='alert'>The start time is greater than or equal to the end time . </div>";
    }
    $j = 1;
  } else {
    $stmt = $con->prepare("UPDATE $person SET sTime = ? , eTime = ?  where id= ?");
    $stmt->execute([$sTime, $eTime, $row['id']]);
    $j = 0;
  }
}

if(isset($_POST['bio'])){
  $bio = strip_tags($_POST['bio']);
  if (($row['bio'] == null || $bio != $row['bio']) && $bio != null) {
    $stmt = $con->prepare("UPDATE $person SET bio = '$bio' where id= ?");
    $stmt->execute([$row['id']]);

    $bio = str_replace("&nbsp;", " ", $bio);
    $update = $con->prepare("update doctor set bio = ? where id = ?");
    $update->execute([$bio, $row['id']]);

  }
}

$i = 0;
if ($password != null) {
  if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
      echo "<div class='alert alert-warning' role='alert'>يجب أن تحتوي كلمة المرور على أرقام وأحرف وأحرف خاصة ،
      ولا يقل عن 8 أحرف.</div>";
    } else {
      echo "<div class='alert alert-warning' role='alert'>Password must contain numbers, letters and special characters, 
        and it is not less than 8 characters. </div>";
    }

    $i = 1;
  } else {
    if ($password == $c_pass) {
      $password = md5($password);
      $stmt = $con->prepare("UPDATE $person SET password = '$password' where id= ?");
      $stmt->execute([$row['id']]);
    } else {
      if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo "<div class='alert alert-warning' role='alert'>كلمة المرور غير متطابقة. </div>";
      } else {
        echo "<div class='alert alert-warning' role='alert'>password is not match. </div>";
      }
      $i = 1;
    }
  }
}

if (isset($_FILES["img"]["name"]) && $_FILES["img"]["name"] != null) {
  $filename = $_FILES["img"]["name"];
  $tempname = $_FILES["img"]["tmp_name"];
  $folder = "img/" . $filename;
  $stmt = $con->prepare("UPDATE $person SET img = ? where id= ?");
  $stmt->execute([$folder, $row['id']]);
  move_uploaded_file($tempname, '../' . $folder);
}

if ($j == 0) {
  if ($i == 0) {
    if ($person == 'doctor') {
      echo 'doctorProfile.php';
    } else {
      echo 'userProfile.php';
    }
  }
}
