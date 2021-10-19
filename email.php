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

require_once "languages/" . 'email_' . $_SESSION['lang'] . ".php";

if (isset($_SESSION['email'])) {
    header('Location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="img/HIH2.jpg" rel="icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style/style1.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- ajax file code -->
    <script src="js/ajax.js"></script>
    <title>set email</title>
</head>

<body>
    <div class="container">
        <form id="check">
            <div class="row">
                <h1><?php echo $lang['setEmail'] ?></h1>
                <h3><?php echo $lang['mess'] ?> <a href="http://localhost:82/healthinhand/login.php"><?php echo $lang['login'] ?></a> <?php echo $lang['page'] ?>.</h3>
                <div class="input-group input-group-icon">
                    <input id="email" type="email" name="email" placeholder="Email Adress" required />
                    <div class="input-icon"><i class="fa fa-envelope"></i></div>
                </div>
                <button type="submit" class="confirm"><?php echo $lang['reset'] ?></button>
            </div>
        </form>
        <div id="error" style="text-align: center;"></div>
</body>

</html>