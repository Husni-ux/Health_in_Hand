<?php
include 'php/connect.php';
session_start();

if (!isset($_SESSION['lang']))
  $_SESSION['lang'] = "ar";
else if (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
  if ($_GET['lang'] == "en")
    $_SESSION['lang'] = "en";
  else if ($_GET['lang'] == "ar")
    $_SESSION['lang'] = "ar";
}

require_once "languages/" . 'ressetPass_' . $_SESSION['lang'] . ".php";

if (isset($_SESSION['email'])) {
  header('Location:index.php');
}
if (!isset($_GET['email']) || !isset($_GET['id']) || !isset($_GET['you'])) {
  header('Location:index.php');
} else {
  $email =  $_GET['email'];
  $id =  $_GET['id'];
  $you =  $_GET['you'];
  $stmt = $con->prepare("select * from $you where id = ? and email = ?");
  $stmt->execute([$id, $email]);
  if ($stmt->rowCount() == 0) {
    header('Location:index.php');
  }
}
$email =  $_GET['email'];
$id =  $_GET['id'];
$you =  $_GET['you'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="img/HIH2.jpg" rel="icon">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" href="style/style1.css">
  <!-- ajax file code -->
  <script src="js/ajax.js"></script>
  <title>Resset Password</title>
</head>

<body>
  <div class="container">
    <form method="post" id="confirm_pass">
      <div class="row">
        <h1><?php echo $lang['confirmPassword']?></h1>
        <h3><?php echo $lang['message']?> <a href="login.php"><?php echo $lang['login']?></a> <?php echo $lang['page']?>.</h3>
        <div class="input-group input-group-icon">
          <input id="pass" type="password" placeholder="<?php echo $lang['password']?>" name="password" required>
          <div class="input-icon"><i class="fa fa-key"></i></div>
        </div>
        <div class="input-group input-group-icon">
          <input id="Re-pass" type="password" placeholder="<?php echo $lang['rePassword']?>" name="repassword" required>
          <div class="input-icon"><i class="fa fa-key"></i></div>
        </div>
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="email" value="<?php echo $email ?>">
        <input type="hidden" name="you" value="<?php echo $you ?>">
        <button name="submit" type="submit" class="confirm"><?php echo $lang['confirm']?></button>
      </div>
      <div id="error"></div>
    </form>
  </div>
</body>

</html>