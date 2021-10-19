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

require_once "languages/" . 'profile_' . $_SESSION['lang'] . ".php";
include 'googleTranslate/traslate.php';
if ($_SESSION['lang'] == 'en') {
    $from = "ar";
    $to = "en";
} else {
    $from = "en";
    $to = "ar";
}

$me = ' ';

if (isset($_GET['email'])) {
    $doctor = $con->prepare("select * from doctor where email = ?");
    $doctor->execute([$_GET['email']]);
    if ($doctor->rowCount() == 0)
        header('location:index.php');
    $doctor = $doctor->fetch();
    // get department name 
    $dept = $con->prepare("select * from dept where id = ?");
    $dept->execute([$doctor['deptno']]);
    $dept = $dept->fetch();

    if (isset($_SESSION['email'])) {
        $email_session = $_SESSION['email'];
        $me  = $_SESSION['person'];
        if ($_GET['email'] == $email_session) {
            header('location:doctorProfile.php');
        } else {
            $personPage = $con->prepare("select * from $me where email = ?");
            $personPage->execute([$_SESSION['email']]);
            $person = $personPage->fetch();
            $img = $person['img'];
            $personProfileId = $person['id'];

            if ($me == 'doctor') {
                $deptDoctor = $con->prepare("select * from dept where id = ?");
                $deptDoctor->execute([$person['deptno']]);
                $deptDoctor = $deptDoctor->fetch();
                $deptDoctor = $deptDoctor['id'];
            }
        }
    }
    $sessEmail = $doctor['email'];

} elseif (isset($_SESSION['email'])) {

    $me = $_SESSION['person'];
    // Doctor Data
    $doctor_sess = $con->prepare("select * from doctor where email = ?");
    $doctor_sess->execute([$_SESSION['email']]);
    if ($doctor_sess->rowCount() == 0)
        header('location:index.php');
    $doctor = $doctor_sess->fetch();
    // get dept name
    $dept = $con->prepare("select * from dept where id = ?");
    $dept->execute([$doctor['deptno']]);
    $dept = $dept->fetch();
    $deptDoctor  = $dept['id'];

    $img = $doctor['img'];
    $current_date = Date('Y-m-d');
    $personProfileId = $doctor['id'];
    // Appointment Data
    $Appointment = $con->prepare("select appointment.id , appointment.date_booking , appointment.sTime , appointment.eTime , 
        appointment.message , user.first_name , user.last_name from appointment LEFT JOIN user on
         user.id = appointment.user where doctor = ? and date_booking > ?");
    $Appointment->execute([$doctor['id'], $current_date]);

    $sessEmail = $doctor['email'];
} else {
    header('location:index.php');
}
///////////////////////////////
$allCommentId = $con->prepare('select * from comment where doctor = ?');
$allCommentId->execute([$doctor['id']]);

$allPost = $con->prepare('select DISTINCT post.content , post.date_share , post.id , 
        user.first_name , user.last_name , user.img, user.email,
        post.img as postImg from post
        LEFT JOIN user on user.id = post.user
        LEFT JOIN comment on post.id = comment.post
        LEFT JOIN doctor on doctor.id = comment.doctor
        WHERE comment.doctor  = ? order by date_share desc
    ');
$allPost->execute([$doctor['id']]);
/////////////////////////////////
if (isset($_GET['email'])) {
    $doctorEmail = $_GET['email'];
} else $doctorEmail = null;


if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    $showIcon = true;
} else {
    $userEmail = null;
    $showIcon = false;
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location:index.php");
}

if (isset($_POST['search'])) {
    if (!empty($_POST["searchBox"])) {
        header("location:resultPage.php?search=" . $_POST["searchBox"]);
    }
}

if ($doctor['verify_license'] == 1) {
    $icon = '<i class="fa fa-check-circle"></i>';
} else {
    $icon = '<i class="fa fa-ban"></i>';
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Doctor</title>
    <link href="img/HIH2.jpg" rel="icon">
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style8.css">
    <style>
        input[type="checkbox"] {
            -webkit-appearance: none;
        }

        .check {
            position: relative;
            display: block;
            width: 50px;
            height: 20px;
            background-color: #272121;
            cursor: pointer;
            border-radius: 20px;
            transition: ease-in .5s
        }

        .check::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 2px;
            left: 2px;
            background-color: #fff;
            border-radius: 50%;
            transition: ease-in .5s
        }

        input[type="checkbox"]:checked~.check {
            background-color: red;
            /* box-shadow: 0 0 0 1200px # 272   */
        }

        input[type="checkbox"]:checked~.check::after {
            background-color: #272121;
            transform: translateX(30px)
        }

        .rate {
            background-color: Transparent;
            background-repeat: no-repeat;
            border: none;
            cursor: pointer;
            overflow: hidden;
            outline: none;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <h1 class="logo mr-auto">
            <a style='text-decoration: none;' href="index.php">Health In Hand</a>
        </h1>
        <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>
            <form style="margin-right: 40px;" method="POST" class="form-inline my-2 my-lg-0">
                <input id="searchBox" class="form-control mr-sm-2" type="search" placeholder="<?php echo $lang['search'] ?>" aria-label="Search" name="searchBox">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit" name="search"><?php echo $lang['search'] ?></button>
                <?php if (isset($_SESSION['email'])) { ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo $img ?>" width="40" height="40" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <?php if ($_SESSION['person'] != 'admin') {
                                    if ($_SESSION['person'] == 'doctor') { ?>
                                        <a class="dropdown-item" href="questionPage.php?dept=<?php echo $deptDoctor ?>"><?php echo $lang['question'] ?></a>
                                    <?php } ?>
                                    <a class="dropdown-item" href="<?php echo $me ?>Profile.php"><?php echo $lang['profile'] ?></a>
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
    <div class="row">
        <div class="leftcolumn">
            <div class="card">
                <h2><?php echo $lang['pic'] ?></h2>
                <div class="fakeimg"><img style="width:100%;max-height:300px" src="<?php echo $doctor['img'] ?>"></div>
                <h3 class="ID">Dr <?php echo $doctor['first_name'] . ' ' . $doctor['last_name'] . ' ' . $icon; ?></h3>
            </div>
            <!-- Doctor Data -->
            <div class="card">
                <h2><?php echo $lang['info'] ?></h2>
                <ul class="list-group list-group-flush info">
                    <li class="list-group-item"><span><?php echo $lang['email'] ?></span>| <?php echo $doctor['email'] ?></li>
                    <li class="list-group-item"><span><?php echo $lang['major'] ?></span>| <?php if ($_SESSION['lang'] == 'en') {
                                                                                                echo $dept['name'];
                                                                                            } else {
                                                                                                echo $dept['name_ar'];
                                                                                            } ?></li>
                    <li class="list-group-item"><span><?php echo $lang['phone'] ?></span>| <?php echo  '0' . $doctor['phone'] ?></li>
                    <?php if ($doctor['scout_doctor'] != null) { ?>
                        <li class="list-group-item"><span><?php echo $lang['scoutDoctor'] ?></span>| <?php echo  $doctor['scout_doctor'] ?> JD</li>
                    <?php } ?>
                    <li class="list-group-item"><span><?php echo $lang['country'] ?></span>| <?php if ($doctor['country'] == null) echo '---';
                                                                                                else echo translate($doctor['country'],$from,$to) ;  ?></li>
                </ul>
            </div>
        </div>
        <div class="rightcolumn">
            <?php if (isset($_SESSION['email']) && $_SESSION['email'] == $sessEmail) { ?>
                <div class="card">
                    <h2><?php echo $lang['reservations'] ?></h2>
                    <table class="table table-hover table-dark">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col"><?php echo $lang['patientName'] ?></th>
                                <th scope="col"><?php echo $lang['date'] ?></th>
                                <th scope="col"><?php echo $lang['description'] ?></th>
                                <th scope="col"><?php echo $lang['startTime'] ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($Appointment as $ar) {
                            ?>
                                <tr>
                                    <th scope="row"><?php echo ++$i ?></th>
                                    <td><?php echo $ar['first_name'] . ' ' . $ar['last_name'] ?></td>
                                    <td><?php echo $ar['date_booking'] ?></td>
                                    <td><?php echo $ar['message'] ?></td>
                                    <td><?php echo substr($ar['sTime'], 0, -3) ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php
            } else {
            ?>
                <div class="card">
                    <div class="container">
                        <br>
                        <div class="row">
                            <div class="calendar col-md-8 offset-md-2">
                                <div>
                                    <div class="card-header bg-primary">
                                        <ul>
                                            <li id="month" class="text-white text-uppercase text-center">
                                            </li>
                                            <li id="year" class="text-white text-uppercase text-center">
                                            </li>
                                        </ul>
                                    </div>
                                    <table class="table calendar table-bordered table-responsive-sm" id="calendar">
                                        <thead>
                                            <tr class="weekdays bg-dark">
                                                <th scope="col" class="text-white text-center">Mo</th>
                                                <th scope="col" class="text-white text-center">Tu</th>
                                                <th scope="col" class="text-white text-center">We</th>
                                                <th scope="col" class="text-white text-center">Th</th>
                                                <th scope="col" class="text-white text-center">Fr</th>
                                                <th scope="col" class="text-white text-center">Sa</th>
                                                <th scope="col" class="text-white text-center">Su</th>
                                            </tr>
                                        </thead>
                                        <tbody class="days bg-light" id="days"></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="row">
                            <div class="col offset-md-1">
                                <form id="form_create_appointment" method="POST">
                                    <div class="form-row">
                                        <div class="col form-group">
                                            <label class="required"><?php echo $lang['date'] ?></label>
                                            <input name="dateAppointment" class="form-control date-input" type="text" id="date" data-trigger="hover" data-toggle="popover" title="Date" data-content="You can select any date from today clicking on the number in the calendar" required>
                                        </div>
                                        <div class="col form-group">
                                            <label><?php echo $lang['description'] ?></label>
                                            <input class="form-control" type="text" id="description" name="message">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col form-group">
                                            <label><?php echo $lang['timeAvailable'] ?></label>
                                            <input id="time" class="form-control" disabled value="<?php echo substr($doctor['sTime'], 0, -3) . '-' .  substr($doctor['eTime'], 0, -3) ?>">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col form-group">
                                            <label class="required"><?php echo $lang['startTime'] ?></label>
                                            <input class="form-control time-input" type="text" id="start_time" name="sTime" required>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col form-group">
                                            <button type="button" class="btn btn-warning btn-block" id="clear" onclick="clear_input()"><?php echo $lang['clearForm'] ?></button>
                                        </div>
                                        <div class="col form-group">
                                            <?php if (isset($_SESSION['email'])) {
                                                if ($_SESSION['person'] == 'user') { ?>
                                                    <input id="submit" type="submit" value="<?php echo $lang['makeAppointment'] ?>" class="btn btn-primary btn-block" name="submit">
                                                <?php } else { ?>
                                                    <a style="color:white" class="btn btn-primary btn-block" onclick="modalAdmin()"  ><?php echo $lang['makeAppointment'] ?></a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <a style="color:white" class="btn btn-primary btn-block" onclick="modalLogin()"><?php echo $lang['makeAppointment'] ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <input name='doctorEmail' id='doctorEmail' type='hidden' value='<?php echo $doctorEmail ?>'>
                                    <input id='userEmail' name='userEmail' type='hidden' value='<?php echo $userEmail ?>'>
                                    <div class="col form-group">
                                        <div id='error'></div>
                                    </div>
                                </form>
                            </div>
                            <div class="col offset-md-1">
                                <div class="row">
                                    <div class="col">
                                        <h3><?php echo $lang['myAppointments'] ?></h3>
                                    </div>
                                </div>
                                <table class="table table-bordered table-hover table-striped table-sm" id="appointment_list">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col" class="text-center align-middle"><?php echo $lang['date'] ?></th>
                                            <th scope="col" class="text-center align-middle"><?php echo $lang['description'] ?></th>
                                            <th scope="col" class="text-center align-middle"><?php echo $lang['doctorName'] ?></th>
                                            <th scope="col" class="text-center align-middle"><?php echo $lang['startTime'] ?></th>
                                            <th scope="col" class="text-center align-middle"><?php echo $lang['delete'] ?></th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="row">
                                    <div class="col">
                                        <h3><?php echo $lang['dateDoctor'] ?></h3>
                                    </div>
                                </div>
                                <table class="table table-bordered table-hover table-striped table-sm" id="appointment_list_doctor">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col" class="text-center align-middle"><?php echo $lang['date'] ?></th>
                                            <th scope="col" class="text-center align-middle"><?php echo $lang['startTime'] ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="card">
                <div id='mycomment' class="list-group">
                    <?php
                    $i = 0;
                    $j = 0;
                    foreach ($allPost as $ar) {
                        $comment = $con->prepare("select comment.content , comment.date_share , doctor.verify_license 
              , CASE WHEN doctor.first_name IS not NULL THEN doctor.email ELSE user.email END as email 
              , CASE WHEN doctor.first_name IS not NULL THEN 'doctor' ELSE 'user' END as his_comment ,
               CASE WHEN doctor.first_name IS not NULL THEN doctor.first_name ELSE user.first_name END as first_name,
                CASE WHEN doctor.last_name IS not NULL THEN doctor.last_name ELSE user.last_name END as last_name , 
                CASE WHEN doctor.img IS not NULL THEN doctor.img ELSE user.img END as img , comment.id
                FROM comment left join user on user.id = comment.user 
                left join doctor on doctor.id = comment.doctor where post = ? order by date_share asc; 
              ");
                        $comment->execute([$ar['id']]);
                        $postId =  $ar['id'];
                        ++$i;
                    ?>
                        <div class="container mt-5">
                            <div class="d-flex justify-content-center row">
                                <div class="col-md-8">
                                    <div class="d-flex flex-column comment-section" id="myGroup">
                                        <div class="bg-white p-2">
                                            <div class="d-flex flex-row user-info"><img class="rounded-circle" src="<?php echo $ar['img'] ?>" width="40">
                                                <div class="d-flex flex-column justify-content-start ml-2">
                                                    <a style="text-decoration: none;" href="userProfile.php?email=<?php echo $ar['email'] ?>">
                                                        <span class="d-block font-weight-bold name"><?php echo $ar['first_name'] . ' ' . $ar['last_name'] ?></span>
                                                    </a>
                                                    <span class="date text-black-50"><?php echo $ar['date_share'] ?></span>
                                                </div>
                                            </div>
                                            <div style='float:right'>
                                                <?php if (isset($_SESSION['email']) && $_SESSION['person'] != 'admin') { ?>
                                                    <i style='cursor:pointer' id='complaint-<?php echo $i ?>' onclick="complaint(<?php echo $postId ?> , <?php echo $i ?>)" class="fa fa-flag"></i>
                                                <?php } ?>
                                            </div>
                                            <div style='margin-top:10px ;max-height:300px ; max-width: 300px; margin-bottom:10px;cursor:pointer'>
                                                <img onclick='clickImg(<?php echo $i ?> , "mypost")' id="myImg-<?php echo $i ?>" src="<?php echo $ar['postImg'] ?>" style="width:100%;max-width:300px;max-height:300px">
                                            </div>
                                            <div class="mt-2">
                                                <p class="comment-text"><?php echo translate($ar['content'], $from, $to) ?></p>
                                            </div>
                                        </div>
                                        <div class="bg-white p-2">
                                            <div class="d-flex flex-row fs-12">
                                                <div onclick="HideShow(<?php echo $i ?>)" class="like p-2 cursor action-collapse">
                                                    <i class="far fa-comment"></i>
                                                    <span style='cursor: pointer' class="ml-1"><?php echo $lang['comment'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="collapse-<?php echo $i ?>" class="bg-light p-2 collapse" data-parent="#myGroup">
                                            <div id='counter-<?php echo $i ?>'>
                                                <div id="postComment<?php echo $i . '0'; ?>"></div>
                                                <?php
                                                $j = 0;
                                                foreach ($comment as $comm) {
                                                    if ($comm['his_comment'] == 'doctor') {

                                                        if ($comm['verify_license'] == 1) {
                                                            $icon = '<i class="fa fa-check-circle"></i>';
                                                        } else {
                                                            $icon = '<i class="fa fa-ban"></i>';
                                                        }
                                                    } else {
                                                        $icon = '';
                                                    }
                                                    ++$j;
                                                ?>
                                                    <div id="comment<?php echo $i . $j ?>">
                                                        <div class="bg-white p-2">
                                                            <div class="d-flex flex-row user-info">
                                                                <img class="rounded-circle" src="<?php echo $comm['img'] ?>" width="40">
                                                                <div class="d-flex flex-column justify-content-start ml-2">
                                                                    <a style='text-decoration: none' href='<?php echo $comm['his_comment'] ?>Profile.php?email=<?php echo $comm['email'] ?>'>
                                                                        <span class="d-block font-weight-bold name"><?php echo $comm['first_name'] . ' ' . $comm['last_name'] . ' ' . $icon ?></span>
                                                                    </a>
                                                                    <span class="date text-black-50"><?php echo  substr($comm['date_share'], 0, -3) ?></span>
                                                                </div>
                                                                <form method='POST' id='rateComment'>
                                                                    <div id=movement-<?php echo $comm['id'] ?>>
                                                                        <?php
                                                                        $his_comment = $comm['his_comment'];

                                                                        $rateCommentUp = $con->prepare("select * ,  count(*) as countRate from rating_info where comment = ? and typeMovement = ?");
                                                                        $rateCommentUp->execute([$comm['id'], 'up']);

                                                                        $rateCommentDown = $con->prepare("select * , count(*) as countRate from rating_info where comment = ? and typeMovement = ?");
                                                                        $rateCommentDown->execute([$comm['id'], 'down']);

                                                                        $rateCommentUp = $rateCommentUp->fetch();
                                                                        $rateCommentDown = $rateCommentDown->fetch();

                                                                        if (isset($_SESSION['email'])) {
                                                                            if ($_SESSION['person'] != 'admin') {
                                                                                if ($_SESSION['email'] != $comm['email']) {

                                                                                    $rateOnThisComment = $con->prepare("select * from rating_info where comment = ? and $me = ?");
                                                                                    $rateOnThisComment->execute([$comm['id'], $personProfileId]);

                                                                                    $rateOnThisComment = $rateOnThisComment->fetch();

                                                                                    if (is_array($rateOnThisComment) && $rateOnThisComment['typeMovement'] == 'up') {
                                                                        ?>
                                                                                        <button style='color:blue;' onclick='movementType(<?php echo  $comm["id"] ?> , "up")' class="fa fa-chevron-up rate"></button>
                                                                                        <?php echo $rateCommentUp['countRate'] ?>
                                                                                        <button onclick='movementType(<?php echo  $comm["id"] ?> , "down")' class="fa fa-chevron-down rate"></button>
                                                                                        <?php echo $rateCommentDown['countRate'] ?>
                                                                                    <?php
                                                                                    } elseif (is_array($rateOnThisComment)  && $rateOnThisComment['typeMovement'] == 'down') { ?>
                                                                                        <button onclick='movementType(<?php echo  $comm["id"] ?> , "up")' class="fa fa-chevron-up rate"></button>
                                                                                        <?php echo $rateCommentUp['countRate'] ?>
                                                                                        <button style='color:blue' onclick='movementType(<?php echo  $comm["id"] ?> , "down")' class="fa fa-chevron-down rate"></button>
                                                                                        <?php echo $rateCommentDown['countRate'] ?>
                                                                                    <?php
                                                                                    } else { ?>
                                                                                        <button onclick='movementType(<?php echo  $comm["id"] ?> , "up")' class="fa fa-chevron-up rate"></button>
                                                                                        <?php echo $rateCommentUp['countRate'] ?>
                                                                                        <button onclick='movementType(<?php echo  $comm["id"] ?> , "down")' class="fa fa-chevron-down rate"></button>
                                                                                        <?php echo $rateCommentDown['countRate'] ?>
                                                                                    <?php }
                                                                                } else {  ?>
                                                                                    <a class="fa fa-chevron-up" onclick="modalRate()"></a>
                                                                                    <?php echo $rateCommentUp['countRate']; ?>
                                                                                    <a class="fa fa-chevron-down" onclick="modalRate()"></a>
                                                                                <?php echo $rateCommentDown['countRate'];
                                                                                }
                                                                            } else {
                                                                                ?>
                                                                                <a class="fa fa-chevron-up" onclick="modalAdmin()"></a>
                                                                                <?php echo $rateCommentUp['countRate']; ?>
                                                                                <a class="fa fa-chevron-down" onclick="modalAdmin()"></a>
                                                                            <?php echo $rateCommentDown['countRate'];
                                                                            }
                                                                        } else { ?>
                                                                            <a class="fa fa-chevron-up rate" onclick="modalLogin()"></a>
                                                                            <?php echo $rateCommentUp['countRate']; ?>
                                                                            <a class="fa fa-chevron-down rate" onclick="modalLogin()"></a>
                                                                        <?php echo $rateCommentDown['countRate']; // modalRate()
                                                                        } ?>
                                                                    </div>
                                                                    <input type='hidden' name='commentId' value='<?php echo $comm['id'] ?>'>
                                                                    <input id='movementType-<?php echo $comm['id'] ?>' type='hidden' name='moveMent' value='s'>
                                                                </form>
                                                            </div>

                                                            <div style='float:right'>
                                                                <?php
                                                                if (isset($_SESSION['email']) && ($comm['email'] == $_SESSION['email'] || $_SESSION['person'] == 'admin')) { ?>
                                                                    <i style='cursor:pointer' id='comment-<?php echo $i . $j ?>' onclick="areYouSureComment(<?php echo $comm['id'] ?>,<?php echo $i . $j ?>)" data-placement="top" title="delete comment" class="fa fa-trash"></i>
                                                                <?php }
                                                                if (isset($_SESSION['email']) && ($comm['email'] == $_SESSION['email'] && $_SESSION['person'] == 'doctor')) { ?>
                                                                    <i onclick="edit('<?php echo $i . $j ?>' , 'content_comment-<?php echo $i ?>' , '<?php echo translate($comm['content'], $from, $to) ?>' , '<?php echo $i ?>' ,'<?php echo $comm['id'] ?>' )" class="fas fa-edit"></i>
                                                                <?php } ?>
                                                            </div>

                                                            <div class="mt-2">
                                                                <p class="comment-text"><?php echo translate($comm['content'], $from, $to) ?></p>
                                                            </div>

                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <form method='post' id='share_comment'>
                                                <input type='hidden' name='post' value='<?php echo $postId ?>'>
                                                <input type='hidden' name='id' value='<?php echo $personProfileId ?>'>
                                                <input type='hidden' name='Iam' value='<?php echo $me ?>'>
                                                <input type='hidden' id='flage-<?php echo $i ?>' name='flage' value='<?php echo $i ?>'>
                                                <input type='hidden' name='flageIJ' value='<?php echo  $i . $j; ?>'>
                                                <div id="comm-<?php echo $i ?>"></div>
                                                <input type='hidden' name='commentId' id="commentUpdate-<?php echo $i ?>" value=''>

                                                <div class="d-flex flex-row align-items-start">
                                                    <?php if ($showIcon) { ?>
                                                        <img class="rounded-circle" src="<?php echo $img ?>" width="40">
                                                    <?php } ?>
                                                    <textarea id='content_comment-<?php echo $i ?>' name='content' class="form-control ml-1 shadow-none textarea"></textarea>
                                                </div>
                                                <div class="mt-2 text-right">
                                                    <?php if (isset($_SESSION['email'])) {
                                                        if ($_SESSION['person'] != 'admin') { ?>
                                                            <button name='share' class="btn btn-primary btn-sm shadow-none" type="submit"><?php echo $lang['postComment'] ?></button>
                                                        <?php } else { ?>
                                                            <button class="btn btn-primary btn-sm shadow-none" onclick="modalAdmin()"><?php echo $lang['postComment'] ?></button>
                                                        <?php }
                                                    } else { ?>
                                                        <button class="btn btn-primary btn-sm shadow-none" onclick="modalLogin()"><?php echo $lang['postComment'] ?></button>
                                                    <?php } ?>

                                                    <button onclick="HideShow(<?php echo $i ?>)" class="btn btn-outline-primary btn-sm ml-1 shadow-none" type="button"><?php echo $lang['cancel'] ?></button>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- The Modal -->
                                        <div id="myModal" class="modal">
                                            <span class="close">&times;</span>
                                            <img class="modal-content" id="img01">
                                        </div>
                                        <!-- The Modal Delete Post  -->
                                        <div id="deleteModal" class="modal">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title"><?php echo $lang['areYouSure'] ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form method='POST' id='deletePost'>
                                                        <button type='submit' id='deletePost' class="button"><?php echo $lang['delete'] ?></button>
                                                        <button type='button' id='closeAlert' class="button"><?php echo $lang['close'] ?></button>
                                                        <input type='hidden' id='idPost' name='idPost'>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- The Modal Delete comment  -->
                                        <div id="deleteModalComment" class="modal">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title"><?php echo $lang['areYouSure'] ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form method='POST' id='deleteComment'>
                                                        <button type='submit' id='delete_comment' class="button"><?php echo $lang['delete'] ?></button>
                                                        <button type='button' id='closeAlertComment' class="button"><?php echo $lang['close'] ?></button>
                                                        <input type='hidden' id='idComment' name='idComment'>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- The Modal complaint post -->
                                        <div id="complaint" class="modal">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method='POST' id='complaintForm'>
                                                        <input type="text" id='inputComplaint' class="form-control form-control-lg" name="compalintContent" placeholder="<?php echo $lang['complaint'] ?>"><br>
                                                        <div id='alertDone' class="alert alert-success" role="alert" style='display:none'><?php echo $lang['success'] ?></div>
                                                        <div id='alertError' class="alert alert-danger" role="alert" style='display:none'><?php echo $lang['error'] ?></div>
                                                        <button type='submit' id='sendComplaint' class="button"><?php echo $lang['send'] ?></button>
                                                        <button type='button' id='closeAlertCompaint' class="button"><?php echo $lang['close'] ?></button>
                                                        <input type='hidden' id='idPostForComlaint' name='idPostForComlaint'>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    <br>
    <input type='hidden' id='userEmail' value='<?php if (isset($_SESSION['email'])) echo $_SESSION['email'] ?>'>
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
    <div id="commentModal" class="modal">
        <div class="modal-content">
            <div class="modal-body">
                <?php
                if ($_SESSION['lang'] == "en") {
                    $message = "You must be <a href='login.php'>login</a> on the site";
                } else {
                    $message = "يجب ان تكون <a href='login.php'>مسجل</a> في الموقع";
                }
                ?>
                <h4 class="modal-title"><?php echo $message ?></h4>
            </div>
            <div class="modal-footer">
                <button id="closeModal" type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['close'] ?></button>
            </div>
        </div>
    </div>
    <div id="commentModalSameUser" class="modal">
        <div class="modal-content">
            <div class="modal-body">
                <?php
                if ($_SESSION['lang'] == "en") {
                    $message = "you cannot rate your comment";
                } else {
                    $message = "لا يمكنك تقييم تعليقك";
                }
                ?>
                <h4 class="modal-title"><?php echo $message ?></h4>
            </div>
            <div class="modal-footer">
                <button id="closeModal1" type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['close'] ?></button>
            </div>
        </div>
    </div>
    <div id="commentModalAdmin" class="modal">
        <div class="modal-content">
            <div class="modal-body">
                <?php
                if ($_SESSION['lang'] == "en") {
                    $message = "You must be <a href='login.php'>login</a> on the site as user";
                } else {
                  $message = "يجب ان تكون <a href='login.php'>مسجل</a> في الموقع كمستخدم";
                }
                ?>
                <h4 class="modal-title"><?php echo $message ?></h4>
            </div>
            <div class="modal-footer">
                <button id="closeModal2" type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['close'] ?></button>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

    <script src="js/appointment2.js"></script>
    <script src="js/doctor.js"></script>

    <script>

    </script>

</body>

</html>