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

require_once "languages/" . 'article_' . $_SESSION['lang'] . ".php";
include 'googleTranslate/traslate.php';
if ($_SESSION['lang'] == 'en') {
    $from = "ar";
    $to = "en";
} else {
    $from = "en";
    $to = "ar";
}


if (isset($_GET['article'])) {
  $articleId = $_GET['article'];
  $stmt = $con->prepare("select * from article where id = ? ");
  $stmt->execute([$articleId]);
  if ($stmt->rowCount() == 0) {
    header("Location:index.php");
  } else {
    $row = $stmt->fetch();
  }
} else {
  $articleId = 2;
  $stmt = $con->prepare("select * from article where id = ? ");
  $stmt->execute([$articleId]);
  if ($stmt->rowCount() == 0) {
    header("Location:index.php");
  } else {
    $row = $stmt->fetch();
  }
}

$rowPopular = $con->prepare("select * from article where   id not between 2 and 12 and bodypart = ?");
$rowPopular->execute([$row['bodypart']]);
$rowPopular = $rowPopular->fetchAll(PDO::FETCH_ASSOC);

$bodyParts = $con->prepare("select * from article where   id  between 2 and 12 and bodypart != ?");
$bodyParts->execute([$row['bodypart']]);
$bodyParts = $bodyParts->fetchAll(PDO::FETCH_ASSOC);


$doctorPost = $con->prepare("select * from article where  articleId = ? and hide  = 1");
$doctorPost->execute([$articleId]);
$doctorPost = $doctorPost->fetchAll(PDO::FETCH_ASSOC);


if (isset($_SESSION['email'])) {
  $person = $_SESSION['person'];
  $stmt = $con->prepare("SELECT * from $person where email = ?");
  $stmt->execute([$_SESSION['email']]);
  $rowPerson = $stmt->fetch();

  if ($person == 'doctor') {
    $stmt = $con->prepare("SELECT * from dept where id = ?");
    $stmt->execute([$rowPerson['deptno']]);
    $department = $stmt->fetch();
    $department = $department['id'];
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
  <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">


  <title>article</title>
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
                <img src="<?php echo $rowPerson['img'] ?>" width="40" height="40" class="rounded-circle">
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                <?php if ($_SESSION['person'] != 'admin') {
                  if ($_SESSION['person'] == 'doctor') { ?>
                    <a class="dropdown-item" href="questionPage.php?dept=<?php echo $department ?>"><?php echo $lang['question'] ?></a>
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

  <div class="page-wrapper">
    <div class="content clearfix">
      <div class="main-content-wrapper">
        <div class="main-content single">
          <h1 class="post-title">
            <?php
            if ($_SESSION['lang'] == 'en') {
              echo $row['title'];
            } else {
              echo $row['title_ar'];
            }
            ?></h1>
          <div class="post-content">
            <?php
            if ($_SESSION['lang'] == 'en') {
              echo "<p>" . $row['content'] . "</p>";
            } else {
              echo "<p>" . $row['content_ar'] . "</p>";
            }
            ?>
          </div>
          <?php
          if ($row['reasons'] != null) {
            if ($_SESSION['lang'] == 'en') {
              echo '<h2 style="text-align: left;" class="post-title">Reasons</h2>
                  <div class="post-content">';
              echo "<p>" . $row['reasons'] . " .</p>
                </div>";
            } else {
              echo '<h2 style="text-align: left;" class="post-title">الاسباب</h2>
              <div class="post-content">';
              echo "<p>" . $row['reasons_ar'] . " .</p>
            </div>";
            }
          }
          if ($row['syndrome'] != null) {
            if ($_SESSION['lang'] == 'en') {
              echo '<h2 style="text-align: left;" class="post-title">Syndrome</h2>
                  <div class="post-content">';
              echo "<p>" . $row['syndrome'] . " .</p> 
                </div> ";
            } else {
              echo '<h2 style="text-align: left;" class="post-title">أعراض</h2>
                  <div class="post-content">';
              echo "<p>" . $row['syndrome_ar'] . " .</p> 
                </div> ";
            }
          }
          if ($row['treatment'] != null) {
            if ($_SESSION['lang'] == 'en') {
              echo '<h2 style="text-align: left;" class="post-title">Treatment</h2>
                  <div class="post-content">';
              echo "<p>" . $row['treatment'] . " .</p> 
                </div> ";
            } else {
              echo '<h2 style="text-align: left;" class="post-title">علاج</h2>
              <div class="post-content">';
              echo "<p>" . $row['treatment_ar'] . " .</p> 
            </div> ";
            }
          }
          ?><br>
          <div style='background-color:gray ; height:40px ; line-height:40px ; color:white ;text-align:center;'><?php echo $lang['refrences'] ?></div>
          <?php if (isset($_SESSION['email']) && $_SESSION['person'] == 'doctor') { ?>
            <div class="container-fluid rounded">
              <div class="row px-5">
                <div class="col-sm-6">
                  <div>
                    <h3><?php echo $lang['add'] ?> </h3>
                    <p class="text-secondary"><?php echo $lang['contact'] ?></p>
                  </div>
                </div>
                <div style="width:100%">
                  <form method="post" id="sendArticle" class="rounded msg-form">
                    <div class="form-group">
                      <label for="msg" class="h6"><?php echo $lang['message'] ?></label>
                      <textarea name="message" id='message' cols="10" rows="5" class="form-control bg-light" placeholder="<?php echo $lang['message'] ?>"></textarea>
                    </div>
                    <div class="form-group d-flex justify-content-end">
                      <input type="submit" name="sendMessage" class="btn btn-primary text-white" value="<?php echo $lang['sendMessage'] ?>">
                      <input type="hidden" name="email" value="<?php echo $_SESSION['email'] ?>">
                      <input type="hidden" name="person" value="<?php echo $_SESSION['person'] ?>">
                      <input type="hidden" name="articleId" value="<?php echo $articleId ?>">
                    </div>
                  </form>
                </div>
                <div id='messageContactUS' style="width:100%"> </div>
              </div>
            </div>
          <?php } ?>
          <div>
            <?php
            if (isset($_SESSION['person']) && $_SESSION['person'] == 'admin') {
              echo '<br>';
            }
            foreach ($doctorPost as $row) {
              $stmt =  $con->prepare("select * from doctor where id = ?");
              $stmt->execute([$row['doctor']]);;
              $doctor = $stmt->fetch();
              if($doctor['verify_license'] == 1){
                $icon = '<i class="fa fa-check-circle"></i>';
              }else{
                $icon = '<i class="fa fa-ban"></i>';
              }
            ?>
              <div class="bg-white p-2">
                <div class="d-flex flex-row user-info">
                  <img class="rounded-circle" src="<?php echo $doctor['img'] ?>" width="40">
                  <div class="d-flex flex-column justify-content-start ml-2">
                    <a style="text-decoration: none;" href="doctorProfile.php?email=<?php echo $doctor['email'] ?>">
                      <span class="d-block font-weight-bold name"><?php echo $doctor['first_name'] . ' ' . $doctor['last_name'] . ' ' . $icon ?></span>
                    </a>
                    <span class="date text-black-50"><?php echo $row['date_share'] ?></span>
                  </div>
                </div>
                <div style='float:right'>
                  <?php if (isset($_SESSION['person']) && ($_SESSION['person'] == 'admin' || $_SESSION['email'] == $doctor['email'])) { ?>
                    <i style='cursor:pointer' id='post-<?php echo $row['id'] ?>' onclick="deletePost(<?php echo $row['id'] ?>)" class="far fa-trash-alt"></i>
                  <?php } ?>
                  <?php if (!(isset($_SESSION['person']) && $_SESSION['person'] == 'admin')) {
                    if (isset($_SESSION['person'])) {
                  ?>
                      <i style='cursor:pointer' id='complaint-<?php echo $row['id'] ?>' onclick="complaint(<?php echo $row['id'] ?>)" class="fa fa-flag"></i>
                  <?php }
                  } ?>
                </div>
                <div class="mt-2">
                  <p class="comment-text"><?php echo translate($row['postDoctor'] ,$from , $to) ?></p>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <!-- // Main Content -->
      <div class="sidebar single">
        <div class="section popular">
          <h2 class="section-title"><?php echo $lang['popular'] ?></h2>
          <?php
          foreach ($rowPopular as $arr) {
            if ($_SESSION['lang'] == 'en') {
            echo '<div class="post clearfix">
                          <img src="' . $arr["img"] . '" alt="">
                          <a href="article.php?article=' . $arr["id"] . '" class="title">
                            <h4>' . $arr["title"] . '</h4>
                          </a>
                        </div>';
            }else{
              echo '<div class="post clearfix">
              <img src="' . $arr["img"] . '" alt="">
              <a href="article.php?article=' . $arr["id"] . '" class="title">
                <h4>' . $arr["title_ar"] . '</h4>
              </a>
            </div>';
            }
          }
          ?>
        </div>

        <div class="section topics">
          <h2 class="section-title"><?php echo $lang['bodyParts'] ?></h2>
          <ul>
            <?php
            foreach ($bodyParts as $ar) {
              if ($_SESSION['lang'] == 'en') {
              echo '<li><a href="article.php?article=' . $ar["id"] . '">' . $ar['title'] . '</a></li>';
              }else{
                echo '<li><a href="article.php?article=' . $ar["id"] . '">' . $ar['title_ar'] . '</a></li>';
              }
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $lang['areYouSure'] ?></h4>
      </div>
      <div class="modal-body">
        <form method='POST' id='deletePost'>
          <button type='submit' id='deletePostArticle' class="button"><?php echo $lang['delete'] ?></button>
          <button type='button' id='closeAlert' class="button"><?php echo $lang['close'] ?></button>
          <input type='hidden' id='idPostArticle' name='idPostArticle'>
        </form>
      </div>
    </div>
  </div>
  <div id="complaint" class="modal">
    <div class="modal-content">
      <div class="modal-body">
        <form method='POST' id='complaintForm'>
          <input type="text" id='inputComplaint' class="form-control form-control-lg" name="compalintContent" placeholder="<?php echo $lang['complaint'] ?>"><br>
          <div id='alertDone' class="alert alert-success" role="alert" style='display:none'><?php echo $lang['success'] ?></div>
          <div id='alertError' class="alert alert-danger" role="alert" style='display:none'><?php echo $lang['error'] ?></div>
          <button type='submit' id='sendComplaint' class="button"><?php echo $lang['send'] ?></button>
          <button type='button' id='closeAlertCompaint' class="button"><?php echo $lang['close'] ?></button>
          <input type='hidden' id='idArticleForComlaint' name='idArticleForComlaint'>
        </form>
      </div>
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
  <script src="js/article.js"></script>


</body>

</html>