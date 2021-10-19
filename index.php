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

if ($_SESSION['lang'] == 'en') {
  $from = "ar";
  $to = "en";
} else {
  $from = "en";
  $to = "ar";
}

require_once "languages/" . $_SESSION['lang'] . ".php";
include 'googleTranslate/traslate.php';
include "php/connect.php";
$bestDoctors = $con->prepare("select * from doctor order by num_comment desc limit 4");
$bestDoctors->execute();
$bestDoctors = $bestDoctors->fetchAll(PDO::FETCH_ASSOC);

$stmt = $con->prepare("select * from doctor");
$stmt->execute();
$marker = $stmt->fetchAll(PDO::FETCH_ASSOC);

$article = $con->prepare("select * from article  limit 5");
$article->execute();
$article = $article->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['logout'])) {
  session_destroy();
  header("location:index.php");
}

if (isset($_SESSION['email'])) {
  $userEmail = $_SESSION['email'];
  $page = $_SESSION['person'];

  if ($page == 'user') {

    $user = $con->prepare("select * from user where email = ?");
    $user->execute([$_SESSION['email']]);
    $user = $user->fetch();
    $img = $user['img'];
  } else {
    if ($page == 'doctor') {
      $doctor = $con->prepare("select * from doctor where email = ?");
      $doctor->execute([$_SESSION['email']]);
      $doctor = $doctor->fetch();

      $dept = $con->prepare("select * from dept where id = ?");
      $dept->execute([$doctor['deptno']]);
      $dept = $dept->fetch();

      $img = $doctor['img'];
    } else {
      $admin = $con->prepare("select * from admin where email = ?");
      $admin->execute([$_SESSION['email']]);
      $admin = $admin->fetch();
      $img = $admin['img'];
    }
  }
} else {
  $userEmail = 0;
}
function articleContent($article)
{
  $content = $article;
  $letterContent = strlen($content);
  if ($letterContent <= 500) {
    $letterContent = ($letterContent / 1) - 1;
  } elseif ($letterContent > 1000 && $letterContent <= 2000) {
    $letterContent = $letterContent / 2;
  } elseif ($letterContent > 2000 && $letterContent <= 3000) {
    $letterContent = $letterContent / 3;
  } elseif ($letterContent > 3000 && $letterContent <= 4000) {
    $letterContent = $letterContent / 4;
  } else {
    $letterContent = $letterContent / 5;
  }
  for ($i = 0; $i <= $letterContent; $i++)
    echo $content[$i];
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
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
  <title>Health In Hand - Index</title>
  <meta content="" name="descriptison">
  <meta content="" name="keywords">
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <link href="img/HIH2.jpg" rel="icon">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="https://allyoucan.cloud/cdn/icofont/1.0.1/icofont.css" integrity="sha384-jbCTJB16Q17718YM9U22iJkhuGbS0Gd2LjaWb4YJEZToOPmnKDjySVa323U+W7Fv" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="style/style.css" rel="stylesheet">
  <style>

  </style>
</head>

<body>
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center">
      <h1 class="logo mr-auto"><a href="index.php">H I H
        </a></h1>
      <nav class="navbar navbar-expand-lg navbar-light ">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <form method="POST" class="form-inline my-2 my-lg-0">
            <input style="width:300px" name="searchBox" id="searchBox" class="form-control mr-sm-2" type="search" placeholder="<?php echo $lang['search'] ?>" aria-label="Search">
            <button name="search" class="btn btn-outline-success my-2 my-sm-0" type="submit"><?php echo $lang['search'] ?></button>
            <ul class="navbar-nav">
              <?php
              if (!isset($_SESSION['email'])) {
              ?>
                <li class="nav-item active">
                  <a class="nav-link" href="login.php" id="sigin-in"><?php echo $lang['login'] ?></a>
                </li>
              <?php
              } else {
              ?>
                <li class="nav-item active">
                  <div class="navbar-collapse" id="navbar-list-4">
                    <ul class="navbar-nav">
                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <img src="<?php echo  $img ?>" width="40" height="40" class="rounded-circle">
                        </a>
                        <div class="dropdown-menu" aria-la belledby="navbarDropdownMenuLink">
                          <?php if (isset($_SESSION['email']) && $_SESSION['person'] != 'admin') {
                            if (isset($_SESSION['email']) && $page == 'doctor') { ?>
                              <a class="dropdown-item" href="questionPage.php?dept=<?php echo $dept['id'] ?>"><?php echo $lang['question'] ?></a>
                            <?php } ?>
                            <a class="dropdown-item" href="<?php echo $page ?>Profile.php"><?php echo $lang['profile'] ?></a>
                            <a class="dropdown-item" href="editProfile.php"> <?php echo $lang['editProfile'] ?></a>
                          <?php } else { ?>
                            <a class="dropdown-item" href="admin/user.php"><?php echo $lang['dashboard'] ?></a>
                          <?php } ?>
                          <input class="dropdown-item" type="submit" name="logout" value="<?php echo $lang['logout'] ?>">
                        </div>
                      </li>
                    </ul>
                  </div>
                </li>
              <?php } ?>
            </ul>
          </form>
        </div>
      </nav>
      <a href="#appointment" class="appointment-btn scrollto"><?php echo $lang['appointment'] ?></a>
    </div>
    <div style="float:right ; margin-right:10  px" class="nav-menu">
      <ul>
        <li style="float:left"><a href="index.php?lang=ar">العربية</a></li>
        <li style="float:left">|</li>
        <li style="float:left"><a href="index.php?lang=en">English</a></li>
      </ul>
    </div>
    <div id="nav2" class="container d-flex align-items-center">
      <nav class="nav-menu  d-lg-block">
        <ul>
          <li><a href="index.php"><?php echo $lang['home'] ?></a></li>
          <li><a href="#cQuestion"><?php echo $lang['commonQuestion'] ?></a></li>
          <li><a href="#article"><?php echo $lang['article'] ?></a></li>
          <li><a href="#doctors"><?php echo $lang['doctor'] ?></a></li>
          <li><a href="#contatUs"><?php echo $lang['contact'] ?></a></li>
          <li><a href="bodyparts.php"><?php echo $lang['bodypart'] ?></a></li>
        </ul>
      </nav>
    </div>

  </header>
  <!-- ======= Cursor  Section ======= -->
  <section>
    <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
        <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img style='max-height:700px' src="img/cursol/5.jpg" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block">
            <p class="animated fadeInLeft " style="animation-delay: 2s"><b><i>"<?php echo $lang['quotation1'] ?>"</i></b></p>
          </div>
        </div>
        <div class="carousel-item">
          <img style='max-height:700px' src="img/cursol/2.jpg" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block">
            <p class="animate fadeInLeft " style="animation-delay:2s"><b><i>"<?php echo $lang['quotation2'] ?>"</i> </b></p>
          </div>
        </div>
        <div class="carousel-item">
          <img style='max-height:700px' src="img/cursol/3.jpg" class="d-block w-100" alt="...">
          <div class="carousel-caption d-none d-md-block">
            <p class="animated fadeInLeft " style="animation-delay:2s"><b><i>"<?php echo $lang['quotation3'] ?>"</i></b></p>
          </div>
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </section>
  <main id="main">
    <!-- ======= Common Questions Section ======= -->
    <section id="cQuestion" style="background:#fff;" class="faq section-bg">
      <div class="container">
        <div class="section-title">
          <h2><?php echo $lang['commonQuestion'] ?></h2>
          <p></p>
        </div>
        <div class="faq-list">
          <ul>
            <?php
            $commonQuestion = $con->prepare("select * from post order by num_search desc limit 5");
            $commonQuestion->execute();
            $commonQuestion = $commonQuestion->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="faq-list">
              <ul>
                <li data-aos="fade-up">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" class="collapse" href="#faq-list-1">
                    <?php
                    echo translate($commonQuestion[0]['content'], $from, $to);
                    ?>
                    <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-1" class="collapse show" data-parent=".faq-list">
                    <?php
                    $comment = $con->prepare("select * from comment where post = ?");
                    $comment->execute([$commonQuestion[0]['id']]);
                    foreach ($comment as $ar) { ?>
                      <p> <?php echo translate($ar['content'], $from, $to)  ?> </p>
                      <hr>
                    <?php } ?>
                  </div>
                </li>
                <li data-aos="fade-up">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-2" class="collapsed">
                    <?php
                    echo translate($commonQuestion[1]['content'], $from, $to);
                    ?>
                    <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-2" class="collapse" data-parent=".faq-list">
                    <?php
                    $comment = $con->prepare("select * from comment where post = ?");
                    $comment->execute([$commonQuestion[1]['id']]);
                    foreach ($comment as $ar) { ?>
                      <p> <?php echo translate($ar['content'], $from, $to)  ?> </p>
                      <hr>
                    <?php } ?>
                  </div>
                </li>
                <li data-aos="fade-up">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-3" class="collapsed">
                    <?php
                    echo translate($commonQuestion[2]['content'], $from, $to);

                    ?>
                    <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-3" class="collapse" data-parent=".faq-list">
                    <?php
                    $comment = $con->prepare("select * from comment where post = ?");
                    $comment->execute([$commonQuestion[2]['id']]);
                    foreach ($comment as $ar) { ?>
                      <p> <?php echo translate($ar['content'], $from, $to)  ?> </p>
                      <hr>
                    <?php } ?>
                  </div>
                </li>

                <li data-aos="fade-up" data-aos-delay="300">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-4" class="collapsed">
                    <?php
                    echo translate($commonQuestion[3]['content'], $from, $to);
                    ?>
                    <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-4" class="collapse" data-parent=".faq-list">
                    <?php
                    $comment = $con->prepare("select * from comment where post = ?");
                    $comment->execute([$commonQuestion[3]['id']]);
                    foreach ($comment as $ar) { ?>
                      <p> <?php echo translate($ar['content'], $from, $to)  ?> </p>
                      <hr>
                    <?php } ?>
                  </div>
                </li>
                <li data-aos="fade-up" data-aos-delay="400">
                  <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-5" class="collapsed">
                    <?php
                    echo translate($commonQuestion[4]['content'], $from, $to);
                    ?>
                    <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                  <div id="faq-list-5" class="collapse" data-parent=".faq-list">
                    <?php
                    $comment = $con->prepare("select * from comment where post = ?");
                    $comment->execute([$commonQuestion[4]['id']]);
                    foreach ($comment as $ar) { ?>
                      <p> <?php echo translate($ar['content'], $from, $to)  ?> </p>
                      <hr>
                    <?php } ?>
                  </div>
                </li>
              </ul>
            </div>
        </div>
    </section><!-- End Frequently Asked Questions Section -->
    <hr>
    <!-- ======= Appointment Section ======= -->
    <section id="appointment" class="appointment" section-bg style="background:#fff;">
      <div class="container">
        <div class="section-title">
          <h2><?php echo $lang['appointment2'] ?></h2>
          <p></p>
        </div>
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
                  <label class="required"><?php echo $lang['department'] ?></label>
                  <select name="department" id="dept" class="form-control">
                    <option value="0"><?php echo $lang['selectDept'] ?></option>
                    <?php
                    $stmt = $con->prepare("select * from dept");
                    $stmt->execute();
                    if ($_SESSION['lang'] == 'en') {
                      foreach ($stmt as $ar) {
                        echo '<option value="' . $ar["id"] . '">' . $ar['name'] . '</option>';
                      }
                    } else {
                      foreach ($stmt as $ar) {
                        echo '<option value="' . $ar["id"] . '">' . $ar['name_ar'] . '</option>';
                      }
                    }
                    ?>
                  </select>
                </div>
                <div class="col form-group">
                  <label class="required"><?php echo $lang['doctor'] ?></label>
                  <select class="form-control" name="doctor" id="doctor" class="form-control">
                    <option value="0"><?php echo $lang['selectDoctor'] ?></option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col form-group">
                  <label><?php echo $lang['timeAvailable'] ?></label>
                  <input id="time" class="form-control" disabled>
                </div>
              </div>
              <div class="form-row">
                <div class="col form-group">
                  <label class="required"><?php echo $lang['date'] ?></label>
                  <input name="dateAppointment" class="form-control date-input" type="text" id="date" title="Date" required>
                </div>
                <div class="col form-group">
                  <label><?php echo $lang['description'] ?></label>
                  <input class="form-control" type="text" id="description" name="message">
                </div>
              </div>
              <div class="form-row">
                <div class="col form-group">
                  <label class="required"><?php echo $lang['sTime'] ?></label>
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
                      <a style="color:white" class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModalSignUp"><?php echo $lang['makeAppointment'] ?></a>
                    <?php } ?>
                  <?php } else { ?>
                    <a style="color:white" class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModal"><?php echo $lang['makeAppointment'] ?></a>
                  <?php } ?>
                </div>
              </div>
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
                  <th scope="col" class="text-center align-middle"><?php echo $lang['doctor'] ?></th>
                  <th scope="col" class="text-center align-middle"><?php echo $lang['sTime'] ?></th>
                  <th scope="col" class="text-center align-middle"><?php echo $lang['delete'] ?></th>
                </tr>
              </thead>
              <input id='userEmail' type='hidden' value='<?php echo $userEmail ?>'>
            </table>
            <div class="row">
              <div class="col">
                <h3><?php echo $lang['dateDoctor'] ?> </h3>
              </div>
            </div>
            <table class="table table-bordered table-hover table-striped table-sm" id="appointment_list_doctor">
              <thead class="thead-dark">
                <tr>
                  <th scope="col" class="text-center align-middle"><?php echo $lang['date'] ?></th>
                  <th scope="col" class="text-center align-middle"><?php echo $lang['sTime'] ?></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </section><!-- End Appointment Section -->
    <hr>
    <!-- ======= Departments Section ======= -->
    <section id="article" class="departments">
      <div class="container">
        <div class="section-title">
          <h2><?php echo $lang['article'] ?></h2>
          <p><?php echo $lang['articleDesc'] ?> </p>
        </div>
        <div class="row">
          <div class="col-lg-3">
            <ul class="nav nav-tabs flex-column">
              <li class="nav-item">
                <a class="nav-link active show" data-toggle="tab" href="#tab-1">
                  <?php
                  if ($_SESSION['lang'] == 'en') {
                    echo $article[0]['title'];
                  } else {
                    echo $article[0]['title_ar'];
                  }
                  ?></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-2">
                  <?php
                  if ($_SESSION['lang'] == 'en') {
                    echo $article[1]['title'];
                  } else {
                    echo $article[1]['title_ar'];
                  }
                  ?></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-3">
                  <?php
                  if ($_SESSION['lang'] == 'en') {
                    echo $article[2]['title'];
                  } else {
                    echo $article[2]['title_ar'];
                  }
                  ?>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-4"><?php
                                                                    if ($_SESSION['lang'] == 'en') {
                                                                      echo $article[3]['title'];
                                                                    } else {
                                                                      echo $article[3]['title_ar'];
                                                                    }
                                                                    ?></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-5"><?php
                                                                    if ($_SESSION['lang'] == 'en') {
                                                                      echo $article[4]['title'];
                                                                    } else {
                                                                      echo $article[4]['title_ar'];
                                                                    }
                                                                    ?></a>
              </li>
            </ul>
          </div>
          <div class="col-lg-9 mt-4 mt-lg-0">
            <div class="tab-content">
              <div class="tab-pane active show" id="tab-1">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3><?php
                        if ($_SESSION['lang'] == 'en') {
                          echo $article[0]['title'];
                        } else {
                          echo $article[0]['title_ar'];
                        }
                        ?>
                    </h3>
                    <p class="font-italic">
                      <?php
                      if ($_SESSION['lang'] == 'en') {
                        articleContent($article[0]['content']);
                      } else {
                        articleContent($article[0]['content_ar']);
                      }
                      ?>
                    </p>
                    <a href="article.php?article=<?php echo $article[0]['id'] ?>"><?php echo $lang['readMore'] ?> ...</a>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-2">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>
                      <?php
                      if ($_SESSION['lang'] == 'en') {
                        echo $article[1]['title'];
                      } else {
                        echo $article[1]['title_ar'];
                      }
                      ?>
                    </h3>
                    <p class="font-italic">
                      <?php
                      if ($_SESSION['lang'] == 'en') {
                        articleContent($article[1]['content']);
                      } else {
                        articleContent($article[1]['content_ar']);
                      }
                      ?>
                    </p>
                    <a href="article.php?article=<?php echo $article[1]['id'] ?>"><?php echo $lang['readMore'] ?> ...</a>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-3">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3><?php
                        if ($_SESSION['lang'] == 'en') {
                          echo $article[2]['title'];
                        } else {
                          echo $article[2]['title_ar'];
                        }
                        ?></h3>
                    <p class="font-italic">
                      <?php
                      if ($_SESSION['lang'] == 'en') {
                        articleContent($article[2]['content']);
                      } else {
                        articleContent($article[2]['content_ar']);
                      }
                      ?>
                    </p>
                    <a href="article.php?article=<?php echo $article[2]['id'] ?>"> <?php echo $lang['readMore'] ?> ...</a>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-4">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3><?php
                        if ($_SESSION['lang'] == 'en') {
                          echo $article[3]['title'];
                        } else {
                          echo $article[3]['title_ar'];
                        }
                        ?></h3>
                    <p class="font-italic">
                      <?php
                      if ($_SESSION['lang'] == 'en') {
                        articleContent($article[3]['content']);
                      } else {
                        articleContent($article[3]['content_ar']);
                      }
                      ?>
                    </p>
                    <a href="article.php?article=<?php echo $article[3]['id'] ?>"> <?php echo $lang['readMore'] ?> ...</a>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab-5">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3><?php
                        if ($_SESSION['lang'] == 'en') {
                          echo $article[4]['title'];
                        } else {
                          echo $article[4]['title_ar'];
                        }
                        ?></h3>
                    <p class="font-italic">
                      <?php
                      if ($_SESSION['lang'] == 'en') {
                        articleContent($article[4]['content']);
                      } else {
                        articleContent($article[4]['content_ar']);
                      }
                      ?>
                    </p>
                    <a href="article.php?article=<?php echo $article[4]['id'] ?>"> <?php echo $lang['readMore'] ?> ...</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Departments Section -->
    <hr>
    <!-- ======= Doctors Section ======= -->
    <section id="doctors" class="doctors">
      <div class="container">
        <div class="section-title">
          <h2><?php echo $lang['doctor2'] ?></h2>
          <p><?php echo $lang['doctorDesc'] ?> </p>
        </div>
        <div class="row">
          <?php
          if ($bestDoctors[0]['verify_license'] == 1) {
            $icon = '<i class="fa fa-check-circle"></i>';
          } else {
            $icon = '<i class="fa fa-ban"></i>';
          }
          ?>
          <div class="col-lg-6">
            <div class="member d-flex align-items-start">
              <div class="pic"><img src="<?php echo $bestDoctors['0']['img'] ?>" class="img-fluid" alt=""></div>
              <div class="member-info">
                <a href="doctorProfile.php?email=<?php echo $bestDoctors[0]['email'] ?>">
                  <h4>Dr <?php echo $bestDoctors['0']['first_name'] . ' ' . $bestDoctors['0']['last_name'] . ' ' . $icon ?></h4>
                </a>
                <span>
                  <?php
                  $deptName = $con->prepare("select * from dept where id = ?");
                  $deptName->execute([$bestDoctors['0']['deptno']]);
                  $deptName = $deptName->fetch();
                  if ($_SESSION['lang'] == 'en') {
                    echo $deptName['name'];
                  } else {
                    echo $deptName['name_ar'];
                  }
                  ?>
                </span>
                <span>
                  <?php echo translate("number of comments :", $from, $to) . $bestDoctors[0]['num_comment']  ?>
                </span>
                <p><?php echo $bestDoctors[0]['bio']; ?></p>
              </div>
            </div>
          </div>
          <?php
          if ($bestDoctors[1]['verify_license'] == 1) {
            $icon = '<i class="fa fa-check-circle"></i>';
          } else {
            $icon = '<i class="fa fa-ban"></i>';
          }
          ?>
          <div class="col-lg-6 mt-4 mt-lg-0">
            <div class="member d-flex align-items-start">
              <div class="pic"><img src="<?php echo $bestDoctors['1']['img'] ?>" class="img-fluid" alt=""></div>
              <div class="member-info">
                <a href="doctorProfile.php?email=<?php echo $bestDoctors[1]['email'] ?>">
                  <h4>Dr <?php echo $bestDoctors['1']['first_name'] . ' ' . $bestDoctors['1']['last_name'] . ' ' . $icon ?></h4>
                </a>
                <span>
                  <?php
                  $deptName = $con->prepare("select * from dept where id = ?");
                  $deptName->execute([$bestDoctors['1']['deptno']]);
                  $deptName = $deptName->fetch();
                  if ($_SESSION['lang'] == 'en') {
                    echo $deptName['name'];
                  } else {
                    echo $deptName['name_ar'];
                  }
                  ?>
                </span>
                <span>
                  <?php echo translate("number of comments :", $from, $to) . $bestDoctors[1]['num_comment']  ?>
                </span>
                <p><?php echo $bestDoctors[1]['bio']; ?></p>
              </div>
            </div>
          </div>
          <?php
          if ($bestDoctors[2]['verify_license'] == 1) {
            $icon = '<i class="fa fa-check-circle"></i>';
          } else {
            $icon = '<i class="fa fa-ban"></i>';
          }
          ?>
          <div class="col-lg-6 mt-4">
            <div class="member d-flex align-items-start">
              <div class="pic"><img src="<?php echo $bestDoctors['2']['img'] ?>" class="img-fluid" alt=""></div>
              <div class="member-info">
                <a href="doctorProfile.php?email=<?php echo $bestDoctors[2]['email'] ?>">
                  <h4>Dr <?php echo $bestDoctors['2']['first_name'] . ' ' . $bestDoctors['2']['last_name']  . ' ' . $icon ?></h4>
                </a>
                <span>
                  <?php
                  $deptName = $con->prepare("select * from dept where id = ?");
                  $deptName->execute([$bestDoctors['2']['deptno']]);
                  $deptName = $deptName->fetch();
                  if ($_SESSION['lang'] == 'en') {
                    echo $deptName['name'];
                  } else {
                    echo $deptName['name_ar'];
                  }
                  ?>
                </span>
                <span>
                  <?php echo translate("number of comments :", $from, $to) . $bestDoctors[2]['num_comment']  ?>
                </span>
                <p><?php echo $bestDoctors[2]['bio']; ?></p>
              </div>
            </div>
          </div>
          <?php
          if ($bestDoctors[3]['verify_license'] == 1) {
            $icon = '<i class="fa fa-check-circle"></i>';
          } else {
            $icon = '<i class="fa fa-ban"></i>';
          }
          ?>
          <div class="col-lg-6 mt-4">
            <div class="member d-flex align-items-start">
              <div class="pic"><img src="<?php echo $bestDoctors['3']['img'] ?>" class="img-fluid" alt=""></div>
              <div class="member-info">
                <a href="doctorProfile.php?email=<?php echo $bestDoctors[3]['email'] ?>">
                  <h4>Dr <?php echo $bestDoctors['3']['first_name'] . ' ' . $bestDoctors['3']['last_name'] . ' ' . $icon ?></h4>
                </a>
                <span>
                  <?php
                  $deptName = $con->prepare("select * from dept where id = ?");
                  $deptName->execute([$bestDoctors['3']['deptno']]);
                  $deptName = $deptName->fetch();
                  if ($_SESSION['lang'] == 'en') {
                    echo $deptName['name'];
                  } else {
                    echo $deptName['name_ar'];
                  }
                  ?>
                </span>
                <span>
                  <?php echo translate("number of comments :", $from, $to) . $bestDoctors[3]['num_comment']  ?>
                </span>
                <p><?php echo $bestDoctors[3]['bio']; ?></p>
              </div>
            </div>
          </div>
        </div>
    </section><!-- End Doctors Section -->
    <hr>
    <!-- section contact us  -->
    <div id="contatUs" class="container-fluid rounded">
      <div class="row px-5">
        <div class="col-sm-6">
          <div>
            <h3> <?php echo $lang['contactUS'] ?> </h3>
            <p class="text-secondary"> <?php echo $lang['contactUsDesc'] ?></p>
          </div>
        </div>
        <?php if (!isset($_SESSION['email'])) { ?>
          <div class="col-sm-6 pad">
            <form method="post" id="contactUs" class="rounded msg-form">
              <div class="form-group">
                <label for="name" class="h6"><?php echo $lang['yourName'] ?></label>
                <div class="input-group border rounded">
                  <div class="input-group-addon px-2 pt-1">
                    <i class="fa fa-user text-primary"></i>
                  </div>
                  <input id='name' type="text" name="name" class="form-control border-0" required>
                </div>
              </div>
              <div class="form-group">
                <label for="name" class="h6"><?php echo $lang['email'] ?></label>
                <div class="input-group border rounded">
                  <div class="input-group-addon px-2 pt-1">
                    <i class="fa fa-envelope text-primary"></i>
                  </div>
                  <input id='email' type="email" name="email" class="form-control border-0" required>
                </div>
              </div>
              <div class="form-group">
                <label for="msg" class="h6"><?php echo $lang['message'] ?></label>
                <textarea name="message" id='message1' cols="10" rows="5" class="form-control bg-light" placeholder="Message" required></textarea>
              </div>
              <div class="form-group d-flex justify-content-end">
                <input type="submit" name="sendMessage" class="btn btn-primary text-white" value="<?php echo $lang['sendMessage'] ?> ">
              </div>
            </form>
          </div>
        <?php } else { ?>
          <div style="width:100%">
            <form method="post" id="contactUs" class="rounded msg-form">
              <div class="form-group">
                <label for="msg" class="h6"><?php echo $lang['message'] ?></label>
                <textarea name="message" id='message2' cols="10" rows="5" class="form-control bg-light" placeholder="Message" required></textarea>
              </div>
              <div class="form-group d-flex justify-content-end">
                <?php if ($_SESSION['person'] != 'admin') { ?>
                  <input type="submit" name="sendMessage" class="btn btn-primary text-white" value="<?php echo $lang['sendMessage'] ?> ">
                <?php } else { ?>
                  <a class="btn btn-primary text-white" data-toggle="modal" data-target="#myModalSignUp"><?php echo $lang['sendMessage'] ?> </a>
                <?php } ?> <input type="hidden" name="email" value="<?php echo $_SESSION['email'] ?>">
                <input type="hidden" name="person" value="<?php echo $_SESSION['person'] ?>">
              </div>
            </form>
          </div>
        <?php } ?>
        <div id='messageContactUS' style="width:100%"> </div>
      </div>
    </div>
    <hr>
    <!--  end esction contact us    -->
    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container">
        <div class="section-title">
          <h2><?php echo $lang['doctorNearYou'] ?></h2>
          <p> <?php echo $lang['doctorNearYouDesc'] ?> </p>
        </div>
      </div>
      <div style="border:0; width: 100%; height: 350px;">
        <div id="map"></div>
      </div>
    </section><!-- End Contact Section -->
  </main><!-- End #main -->
  <hr>
  <!-- ======= Footer ======= -->
  <div class="footer">
    <footer id="footer" style='background-color:white'>
      <div class="container d-md-flex py-4">
        <div class="mr-md-auto text-center text-md-left">
          <div class="copyright">
            &copy; Copyright <strong><span>AHU</span></strong>. All Rights Reserved
          </div>
          <div class="credits">
            Designed by <a href="index.php">Health In Hand</a>
          </div>
        </div>
      </div>
    </footer><!-- End Footer -->

    <input id="lang" type="hidden" name="session" value="<?php if (isset($_SESSION['lang'])) echo $_SESSION['lang'];
                                                          else echo 'en' ?>">
    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
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
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['close'] ?></button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="myModalSignUp" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
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
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['close'] ?></button>
          </div>
        </div>
      </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/ajax.js"></script>
    <script src="js/appointment.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBh7OMnNSzZzlU3IcVSGPbXddwNR5X7NgY&callback=initMap" async defer></script>
    <script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js'></script>
    <script type='text/javascript' src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>

    <script>
      //maps
      let map;
      infoObj = [];
      window.onload = function() {
        initMap();
      }

      function initMap() {
        navigator.geolocation.getCurrentPosition(position => {
          pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          map = new google.maps.Map(document.getElementById("map"), {
            center: pos,
            zoom: 13,
          });
          var features = [
            <?php foreach ($marker as $m) { ?> {
                position: new google.maps.LatLng(<?php echo $m['lat'] ?>, <?php echo $m['lng'] ?>),
              },
            <?php
            } ?>
          ];
          var infoWindowContent = [
            <?php foreach ($marker as $row) {
              $deptName = $con->prepare("select * from dept where id = ?");
              $deptName->execute([$row['deptno']]);
              $deptName = $deptName->fetch();

              if ($row['verify_license'] == 1) {
                $icon = '<i class="fa fa-check-circle"></i>';
              } else {
                $icon = '<i class="fa fa-ban"></i>';
              }

              $fname = $row['first_name'];
              $lname = $row['last_name'];
              $email = $row['email'];
              if ($_SESSION['lang'] == 'en') {
                $dept = $deptName['name'];
              } else {
                $dept = $deptName['name_ar'];
              }



            ?>[`<div class="info_content">
                <a href="doctorProfile.php?email=<?php echo $email ?>"><h4>Dr <?php echo  $fname . ' ' . $lname . ' ' . $icon; ?></h4></a>
                <h5><?php echo $dept ?></h5>
              </div>`],
            <?php } ?>
          ];
          //addMarkerInfo();
          for (let i = 0; i < features.length; i++) {
            var contentString = '<h3>' + infoWindowContent[i] + '</h3> ';
            const marker = new google.maps.Marker({
              position: features[i].position,
              map: map,
            });
            const infowindow = new google.maps.InfoWindow({
              content: contentString,
            });
            marker.addListener("click", () => {
              closeOtherInfo();
              infowindow.open(map, marker);
              infoObj[0] = infowindow;
            });
          }
        });
      }

      function closeOtherInfo() {
        if (infoObj.length > 0) {
          infoObj[0].set("marker", null);
          infoObj[0].close();
          infoObj[0].length = 0;
        }
      }
    </script>

</body>

</html>