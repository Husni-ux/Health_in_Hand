<?php
include '../php/connect.php';
session_start();

if (!isset($_SESSION['lang']))
    $_SESSION['lang'] = "ar";
else if (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
    if ($_GET['lang'] == "en")
        $_SESSION['lang'] = "en";
    else if ($_GET['lang'] == "ar")
        $_SESSION['lang'] = "ar";
}

require_once "../languages/" . 'admin_' . $_SESSION['lang'] . ".php";
if (isset($_SESSION['person'])) {
    if ($_SESSION['person'] != 'admin') {
        header('location:../index.php');
    }
} else {
    header('location:../index.php');
}

include '../googleTranslate/traslate.php';
if ($_SESSION['lang'] == 'en') {
    $from = "ar";
    $to = "en";
} else {
    $from = "en";
    $to = "ar";
}


$stmt = $con->prepare("select * from contactUs ");
$stmt->execute();
$report = $stmt->fetchAll(PDO::FETCH_ASSOC);

$admin = $con->prepare("select * from admin where email = ? ");
$admin->execute([$_SESSION['email']]);
$admin = $admin->fetch();

if (isset($_POST['logout'])) {
    session_destroy();
    header("location:../index.php");
}
if (!(isset($_GET['email']) && isset($_GET['person']))) {
    header('location:user.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <link href="img/HIH2.jpg" rel="icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Admin - Change Password</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    <link href="css/register.css" rel="stylesheet" media="all">

    <style>
        .button,
        .button_log {
            background-color: Transparent;
            background-repeat: no-repeat;
            border: none;
            cursor: pointer;
            overflow: hidden;
            outline: none;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-text mx-3"> <?php echo $lang['admin'] ?> </div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Tables -->
            <li class="nav-item ">
                <a class="nav-link" href="user.php">
                    <i class="fas fa-users"></i>
                    <span><?php echo $lang['users'] ?></span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="doctor.php">
                    <i class="fas fa-user-md"></i>
                    <span><?php echo $lang['doctors'] ?></span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="report.php">
                    <i class="fas fa-flag"></i>
                    <span><?php echo $lang['reports'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contactUs.php">
                    <i class="fas fa-address-book"></i>
                    <span><?php echo $lang['contactUs'] ?></span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="verificationRequest.php">
                    <i class="fas fa-check-square"></i>
                    <span><?php echo $lang['verificationRequests'] ?></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="registerAdmin.php">
                    <i class="fas fa-user-plus"></i>
                    <span><?php echo $lang['addAdmin'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="commentUser.php">
                    <i class="fas fa-comments"></i>
                    <span><?php echo translate('Deleted Comments ', $from, $to) ?> </span></a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="postUser.php">
                    <i class="far fa-clipboard"></i>
                    <span><?php echo translate('Deleted Posts ', $from, $to) ?> </span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <h1 class="logo mr-auto">
                        <a style='text-decoration: none;' href="../index.php">Health In Hand</a>
                    </h1>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div style="float:right ; margin-right:10px ;margin-top:20px" class="nav-menu">
                            <ul>
                                <li style="float:left;list-style-type: none;"><a href="changePass.php?lang=ar">العربية</a></li>
                                <li style="float:left;list-style-type: none;">|</li>
                                <li style="float:left;list-style-type: none;"><a href="changePass.php?lang=en">English</a></li>
                            </ul>
                        </div>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $admin['first_name'] . ' ' . $admin['last_name'] ?></span>
                                <img class="img-profile rounded-circle" src="../<?php echo $admin['img'] ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../index.php">
                                    <i class="fas fa-home fa-sm fa-fw mr-2 text-gray-400"></i>
                                    <?php echo $lang['home'] ?>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="changePass.php?email=<?php echo $_SESSION['email'] ?>&person=admin">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    <?php echo $lang['changePassword'] ?>
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="#">
                                    <a class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        <input type="submit" value='<?php echo $lang['logout'] ?>' name="logout" class="button_log">
                                    </a>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <div class="card card-4">
                        <div class="card-body">
                            <h2 class="title"><?php echo $lang['changePassword'] ?></h2>
                            <form id='addAdmin' method="POST">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input class="input--style-4" type="password" id='password' name="password" placeholder="<?php echo $lang['password'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input class="input--style-4" type="password" id='con_password' name="con_password" placeholder="<?php echo $lang['changePassword'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="email" value="<?php echo $_GET['email'] ?>">
                                <input type="hidden" name="person" value="<?php echo $_GET['person'] ?>">
                                <div class="p-t-15">
                                    <button class="btn btn--radius-2 btn--blue" type="submit"><?php echo $lang['change'] ?></button>
                                </div>
                                <div id='error' class="col-6 mt-3"></div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="mr-md-auto text-center text-md-left">
                        <div class="copyright">
                            &copy; Copyright <strong><span>AHU</span></strong>. All Rights Reserved
                        </div>
                        <div class="credits">
                            Designed by <a href="#">Health In Hand</a>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>


    <script>
        $(document).on('submit', '#addAdmin', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '../php/changePass.php',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#error').html(data);
                    $('#password').val('');
                    $('#con_password').val('');

                }
            })
        });
    </script>
</body>

</html>