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

$admin = $con->prepare("select * from admin where email = ? ");
$admin->execute([$_SESSION['email']]);
$admin = $admin->fetch();

$stmt = $con->prepare("select * from doctor ");
$stmt->execute();
$doctor = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['logout'])) {
    session_destroy();
    header("location:../index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="../img/HIH2.jpg" rel="icon">
    <title>Admin - Doctor</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/doctor.css" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-text mx-3"> <?php echo $lang['admin'] ?> </div>
            </a>
            <hr class="sidebar-divider">
            <li class="nav-item ">
                <a class="nav-link" href="user.php">
                    <i class="fas fa-users"></i>
                    <span><?php echo $lang['users'] ?></span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="doctor.php">
                    <i class="fas fa-user-md"></i>
                    <span><?php echo $lang['doctors'] ?> </span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="report.php">
                    <i class="fas fa-flag"></i>
                    <span><?php echo $lang['reports'] ?> </span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contactUs.php">
                    <i class="fas fa-address-book"></i>
                    <span><?php echo $lang['contactUs'] ?> </span></a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="verificationRequest.php">
                    <i class="fas fa-check-square"></i>
                    <span><?php echo $lang['verificationRequests'] ?> </span></a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="registerAdmin.php">
                    <i class="fas fa-user-plus"></i>
                    <span><?php echo $lang['addAdmin'] ?> </span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="commentUser.php">
                <i class="fas fa-comments"></i>
                <span><?php echo translate('Deleted Comments ',$from,$to) ?> </span></a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="postUser.php">
                <i class="far fa-clipboard"></i>
                <span><?php echo translate('Deleted Posts ',$from,$to) ?> </span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="logo mr-auto">
                        <a style='text-decoration: none;' href="../index.php">Health In Hand</a>
                    </h1>
                    <ul class="navbar-nav ml-auto">
                    <div style="float:right ; margin-right:10px ;margin-top:20px" class="nav-menu">
                        <ul>
                            <li style="float:left;list-style-type: none;"><a href="doctor.php?lang=ar">العربية</a></li>
                            <li style="float:left;list-style-type: none;">|</li>
                            <li style="float:left;list-style-type: none;"><a href="doctor.php?lang=en">English</a></li>
                        </ul>
                         </div>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $admin['first_name'] . ' ' . $admin['last_name'] ?></span>
                                <img class="img-profile rounded-circle" src="../<?php echo $admin['img'] ?>">
                            </a>
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
                                        <input type="submit" value='<?php echo $lang['logout'] ?> ' name="logout" class="button_log">
                                    </a>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800"><?php echo $lang['tables'] ?> </h1>
                    <p class="mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><?php echo $lang['dataTables'] . ' ' . $lang['doctors'] ?> </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <input class="form-control" id="myInput" type="text" placeholder="<?php echo $lang['search'] ?> ..."> <br>
                                <table class="table table-bordered" id="table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Name</th>
                                            <th>email</th>
                                            <th>gender</th>
                                            <th>verified</th>
                                            <th>License number</th>
                                            <th>License img</th>
                                            <th>Verify license</th>
                                            <th>birth date</th>
                                            <th>edit</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>id</th>
                                            <th>Name</th>
                                            <th>email</th>
                                            <th>gender</th>
                                            <th>verified</th>
                                            <th>License number</th>
                                            <th>License img</th>
                                            <th>Verify license</th>
                                            <th>birth date</th>
                                            <th>edit</th>
                                        </tr>
                                    </tfoot>
                                    <?php
                                    $i = 0;
                                    foreach ($doctor as $row) {
                                        $i++;
                                    ?>
                                        <tbody id="row-<?php echo $row['id'] ?>">
                                            <tr>
                                                <td><?php echo $row['id'] ?></td>
                                                <td><?php echo $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                                <th><?php echo $row['email'] ?></th>
                                                <th><?php echo $row['gender'] ?></th>
                                                <th><?php if ($row['verified'] == 1) echo 'True';
                                                    else echo 'False' ?></th>
                                                <th><?php echo $row['license_number'] ?></th>
                                                <?php if ($row['license_img'] != null) { ?>
                                                    <th><img onclick="clickImg(<?php echo $i ?>)" id="img-<?php echo $i ?>" style="height:100px;width:100px" src="../<?php echo $row['license_img'] ?>"></th>
                                                <?php } else { ?>
                                                    <th><?php echo $lang['noImage'] ?> </th>
                                                <?php } ?>
                                                <th><?php if ($row['verify_license'] == 1) echo 'True';
                                                    else echo 'False' ?></th>
                                                <th><?php echo $row['birth'] ?></th>
                                                <th>
                                                    <a style="text-decoration:none" href="changePass.php?email=<?php echo $row['email'] ?>&person=doctor"><i style='cursor:pointer' class="fas fa-edit" title='Edit Information'></i> </a>
                                                    <i style='cursor:pointer' id="doctor-<?php echo $row['id'] ?>" onclick="deleteDoctor(<?php echo $row['id'] ?>)" title='Remove Doctor' class="fas fa-user-minus"></i>
                                                </th>
                                            </tr>

                                        </tbody>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
        </div>
    </div>

    <!-- Delete User Modal-->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">are you sure</h4>
            </div>
            <div class="modal-body">
                <form method='POST' id='deleteUser'>
                    <button type='submit' id='delDoctor' class="button">Delete</button>
                    <button type='button' id='closeAlert' class="button">Close</button>
                    <input type='hidden' id='idDoctor' name='idDoctor'>
                </form>
            </div>
        </div>
    </div>
    <!-- The Modal Image  -->
    <div id="myModal1" class="modal">
        <span class='close' id="close">&times;</span>
        <img class="modal-content" id="img">
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="../js/admin_doctor.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>