<?php
include 'php/connect.php';
include 'googleTranslate/traslate.php';

session_start();

if (!isset($_SESSION['lang']))
    $_SESSION['lang'] = "ar";
else if (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
    if ($_GET['lang'] == "en")
        $_SESSION['lang'] = "en";
    else if ($_GET['lang'] == "ar")
        $_SESSION['lang'] = "ar";
}

if ($_SESSION['lang'] == 'en') {
    $from = "ar";
    $to = "en";
} else {
    $from = "en";
    $to = "ar";
}

require_once "languages/" . 'articleUpdate_' . $_SESSION['lang'] . ".php";

if (isset($_SESSION['email'])) {
    $person = $_SESSION['person'];
    if ($person != 'admin') {
        header('location:index.php');
    }
    $stmt = $con->prepare("SELECT * from $person where email = ?");
    $stmt->execute([$_SESSION['email']]);
    $row = $stmt->fetch();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('location:index.php');
}

if (isset($_POST['search'])) {
    if (!empty($_POST["searchBox"])) {
        $query = str_replace(" ", "+", $_POST["searchBox"]);
        header("location:resultPage.php?search=" . $query);
    }
}

if (isset($_GET['idUpdate'])) {
    $stmt = $con->prepare("SELECT * FROM article where id = ?");
    $stmt->execute([$_GET['idUpdate']]);
    $article = $stmt->fetch();
    if ($article['hide'] == 1) {
        header('location:admin/contactUs.php');
    }
    $bodyParts = $con->prepare("SELECT * from bodyparts ");
    $bodyParts->execute();
} else {
    header('location:index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="img/HIH2.jpg" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Custom Styling -->
    <link rel="stylesheet" href="style/article.css">

    <!-- Template Main CSS File -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="style/style.css" rel="stylesheet">
    <title>article</title>
</head>

<body>
    <!-- ======= Header ======= -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <h1 class="logo mr-auto">
            <a style='text-decoration: none;' href="../index.php">Health In Hand</a>
        </h1>
        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>
            <!-- Nav Item - User Information -->
            <form style="margin-right: 40px;" method="POST" class="form-inline my-2 my-lg-0">
                <input id="searchBox" class="form-control mr-sm-2" type="search" placeholder="<?php echo $lang['search'] ?>" aria-label="Search" name="searchBox">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="search"><?php echo $lang['search'] ?></button>
                <?php if (isset($_SESSION['email'])) { ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo $row['img'] ?>" width="40" height="40" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <?php if ($_SESSION['person'] != 'admin') {
                                    if ($_SESSION['person'] == 'doctor') { ?>
                                        <a class="dropdown-item" href="questionPage.php?dept=<?php echo $dept ?>"><?php echo $lang['questiopn'] ?></a>
                                    <?php } ?>
                                    <a class="dropdown-item" href="<?php echo $person ?>Profile.php"><?php echo $lang['profile'] ?></a>
                                    <a class="dropdown-item" href="editProfile.php"><?php echo $lang['editProfile'] ?></a>
                                <?php } else { ?>
                                    <a class="dropdown-item" href="admin/user.php"><?php echo $lang['dashboard'] ?></a>
                                <?php } ?>
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
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Content -->
        <div class="content clearfix">
            <!-- Main Content Wrapper -->
            <div class="main-content-wrapper">
                <a href="article.php?article=<?php echo $article['articleId'] ?>">
                    <h2><?php echo $lang['topic'] ?></h2>
                </a>
                <div class="main-content single">
                    <?php echo translate($article['postDoctor'], $from, $to) ?>
                </div>
            </div>
            <div class="sidebar single">
                <div class="section topics">
                    <h2 class="section-title"><?php echo $lang['bodyParts'] ?></h2>
                    <ul>
                        <?php foreach ($bodyParts as $ar) {
                            echo '<li><a href="article.php?bodypart=' . $ar["id"] . '">' . translate($ar['name'],$from , $to) . '</a></li>';
                        } ?>
                    </ul>
                </div>
            </div>
            <!-- // Main Content -->
        </div>
        <div class="content clearfix">
            <form id="acceptUpdate" method="POST">
                <button style="float:left" class="button"><?php echo $lang['accept'] ?></button>
                <input type="hidden" name="idArticleUpdate" value="<?php echo $_GET['idUpdate'] ?>">
            </form>
            <form id="removeUpdate" method="POST">
                <button class="button"><?php echo $lang['remove'] ?></button>
                <input type="hidden" name="idArticleUpdate" value="<?php echo $_GET['idUpdate'] ?>">
            </form>
        </div>
    </div>
    <!-- Template Main JS File -->
    <script src="js/main.js"></script>

    <!-- ======= Footer ======= -->
    <div class="footer">
        <footer id="footer" style='background-color:white'>
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="js/articleupdate.js"></script>
</body>

</html>