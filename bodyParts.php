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

require_once "languages/" . 'bodyParts_' . $_SESSION['lang'] . ".php";

$stmt = $con->prepare("SELECT * FROM bodyparts");
$stmt->execute();
$stmt = $stmt->fetch();

if (isset($_SESSION['email'])) {
    $person = $_SESSION['person'];
    $stmt = $con->prepare("SELECT * from $person where email = ?");
    $stmt->execute([$_SESSION['email']]);
    $row = $stmt->fetch();

    if ($person == 'doctor') {
        $stmt = $con->prepare("SELECT * from dept where id = ?");
        $stmt->execute([$row['deptno']]);
        $dept = $stmt->fetch();
        $dept = $dept['id'];
    }
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="img/HIH2.jpg" rel="icon">
    <title>part of body</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="style/style3.css">
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
                                        <a class="dropdown-item" href="questionPage.php?dept=<?php echo $dept ?>"><?php echo $lang['question'] ?></a>
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
    <div class="part-select ">
        <div class="desc">
            <h1 class="bold"><?php echo $lang['body'] ?></h1>
            <p><?php echo $lang['desc'] ?></p>
        </div>
        <div class="body">
            <div class="organs">
                <img width="696" height="875" src="img/bodypart.png">
                <ul>
                    <li>
                        <a class="sys-1" href="article.php?article=3">
                            <span class="text bold"><?php echo $lang['system1'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-2" href="article.php?article=2">
                            <span class="text bold"><?php echo $lang['system2'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-3" href="article.php?article=5">
                            <span class="text bold"><?php echo $lang['system3'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-4" href="article.php?article=6">
                            <span class="text bold"><?php echo $lang['system4'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-5" href="article.php?article=8">
                            <span class="text bold"><?php echo $lang['system5'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-6" href="article.php?article=12">
                            <span class="text bold"><?php echo $lang['system6'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-7" href="article.php?article=9">
                            <span class="text bold"><?php echo $lang['system7'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-8" href="article.php?article=11">
                            <span class="text bold"><?php echo $lang['system8'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-9" href="article.php?article=10">
                            <span class="text bold"><?php echo $lang['system9'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-10" href="article.php?article=4">
                            <span class="text bold"><?php echo $lang['system10'] ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="sys-11" href="article.php?article=7">
                            <span class="text bold"><?php echo $lang['system11'] ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

</html>