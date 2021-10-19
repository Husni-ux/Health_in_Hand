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

require_once "languages/" . 'passwordChange_' . $_SESSION['lang'] . ".php";

?>
<!DOCTYPE html>
<html>

<head>
  <title>Change Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=Fira+Sans:300" rel="stylesheet">
  <link rel="stylesheet" href="style/style11.css">
  <link href="img/HIH2.jpg" rel="icon">
</head>

<body>
  <div class="container">
    <form>
      <div class="row">
        <h1><?php echo $lang['welcome'] ?>!</h1>
        <h3><?php echo $lang['messagePassword'] ?> <a href="login.php"><?php echo $lang['login'] ?><a> <?php echo $lang['process'] ?>.</h3>
      </div>
    </form>
  </div>
</body>

</html>