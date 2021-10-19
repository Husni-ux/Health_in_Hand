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

require_once "languages/" . 'post_' . $_SESSION['lang'] . ".php";

include 'googleTranslate/traslate.php';
if ($_SESSION['lang'] == 'en') {
    $from = "ar";
    $to = "en";
} else {
    $from = "en";
    $to = "ar";
}

if (isset($_GET["post"])) {
    $postId =  $_GET['post'];


    $is_page_refreshed = (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0');

    if (!$is_page_refreshed) {
        $updateSearch = $con->prepare("update post set num_search = num_search + 1 where id  =?");
        $updateSearch->execute([$postId]);
    }

    $post = $con->prepare("select user.first_name , user.last_name , user.img , user.email,
    post.content , post.date_share , post.img as postImg
    from user LEFT JOIN post on post.user = user.id WHERE post.id  = ? order by post.date_share desc");
    $post->execute([$postId]);
    if ($post->rowCount() == 0) {
        header("location:index.php");
    }
    $post = $post->fetch();

    $comments = $con->prepare("select comment.content , comment.date_share , doctor.verify_license 
      , CASE WHEN doctor.first_name IS not NULL THEN doctor.email ELSE user.email END as email 
      , CASE WHEN doctor.first_name IS not NULL THEN 'doctor' ELSE 'user' END as his_comment ,
      CASE WHEN doctor.first_name IS not NULL THEN doctor.first_name ELSE user.first_name END as first_name,
      CASE WHEN doctor.last_name IS not NULL THEN doctor.last_name ELSE user.last_name END as last_name , 
      CASE WHEN doctor.img IS not NULL THEN doctor.img ELSE user.img END as img , comment.id
      FROM comment left join user on user.id = comment.user 
      left join doctor on doctor.id = comment.doctor where post = ? order by date_share asc; 
    ");
    $comments->execute([$_GET["post"]]);
} else {
    header('location:index.php');
}

$showIcon = false;
if (isset($_SESSION['email'])) {
    // user Data
    $person = $_SESSION['person'];
    $profilePage = $con->prepare("select * from $person where email = ?");
    $profilePage->execute([$_SESSION['email']]);
    if ($profilePage->rowCount() == 0)
        header('location:index.php');
    $profilePage = $profilePage->fetch();
    $imgProfile = $profilePage['img'];

    $profilePageId = $profilePage['id'];

    $showIcon = true;
    $deleteIcon = true;
    if ($person == 'doctor') {
        $dept = $con->prepare("select * from dept where id = ?");
        $dept->execute([$profilePage['deptno']]);
        $dept = $dept->fetch();
        $dept = $dept['id'];
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("location:index.php");
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="img/HIH2.jpg" rel="icon">
    <link rel="stylesheet" href="style/style8.css">
    <style>
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
                                <img src="<?php echo $imgProfile ?>" width="40" height="40" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <?php if ($_SESSION['person'] != 'admin') { ?>
                                    <?php if ($_SESSION['person'] == 'doctor') { ?>
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
    <div class="card">
        <div class="list-group">
            <div class="container mt-5">
                <div class="d-flex justify-content-center row">
                    <div class="col-md-8">
                        <div class="d-flex flex-column comment-section" id="myGroup">
                            <div class="bg-white p-2">
                                <div class="d-flex flex-row user-info">
                                    <img class="rounded-circle" src="<?php echo $post['img'] ?>" width="40">
                                    <div class="d-flex flex-column justify-content-start ml-2">
                                        <a style="text-decoration: none;" href="userProfile.php?email=<?php echo $post['email'] ?>">
                                            <span class="d-block font-weight-bold name"><?php echo $post['first_name'] . ' ' . $post['last_name'] ?></span>
                                        </a>
                                        <span class="date text-black-50"><?php echo $post['date_share'] ?></span>
                                    </div>
                                </div>
                                <div style='float:right'>
                                    <?php if (isset($_SESSION['person']) && $_SESSION['person'] == 'admin') { ?>
                                        <i style='cursor:pointer' id='post' onclick="deletePost(<?php echo $postId ?>)" class="far fa-trash-alt"></i>
                                    <?php }
                                    if (isset($_SESSION['email']) && $_SESSION['person'] != 'admin') { ?>
                                        <i style='cursor:pointer' id='complaint1' onclick="complaint(<?php echo $postId ?>)" class="fa fa-flag"></i>
                                    <?php } ?>
                                </div>
                                <div style='margin-top:10px ;max-height:300px ; max-width: 300px; margin-bottom:10px;cursor:pointer'>
                                    <img onclick='clickImg()' id="myImg" src="<?php echo $post['postImg'] ?>" style="width:100%;max-width:300px;max-height:300px">
                                </div>
                                <div class="mt-2">
                                    <p class="comment-text"><?php echo translate($post['content'], $from, $to) ?></p>
                                </div>
                            </div>
                            <div class="bg-white p-2">
                                <div class="d-flex flex-row fs-12">
                                    <div onclick="HideShow()" class="like p-2 cursor action-collapse">
                                        <i class="far fa-comment"></i>
                                        <span style='cursor: pointer' class="ml-1"><?php echo $lang['comment'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div id="collapse" class="bg-light p-2 collapse" data-parent="#myGroup">
                                <div id='counter'>

                                    <?php
                                    foreach ($comments as $comment) {
                                        if ($comment['his_comment'] == 'doctor') {

                                            if ($comment['verify_license'] == 1) {
                                                $icon = '<i class="fa fa-check-circle"></i>';
                                            } else {
                                                $icon = '<i class="fa fa-ban"></i>';
                                            }
                                        } else {
                                            $icon = '';
                                        }
                                    ?>
                                        <div id="comment<?php echo $comment['id'] ?>">
                                            <div class="bg-white p-2">
                                                <div class="d-flex flex-row user-info">
                                                    <img class="rounded-circle" src="<?php echo $comment['img'] ?>" width="40">
                                                    <div class="d-flex flex-column justify-content-start ml-2">
                                                        <a style='text-decoration: none' href='<?php echo $comment['his_comment'] ?>Profile.php?email=<?php echo $comment['email'] ?>'>
                                                            <span class="d-block font-weight-bold name"><?php echo $comment['first_name'] . ' ' . $comment['last_name'] . ' ' . $icon ?></span>
                                                        </a>
                                                        <span class="date text-black-50"><?php echo  substr($comment['date_share'], 0, -3) ?></span>
                                                    </div>
                                                    <form method='POST' id='rateComment'>
                                                        <div id=movement-<?php echo $comment['id'] ?>>

                                                            <?php
                                                            $his_comment = $comment['his_comment'];

                                                            $rateCommentUp = $con->prepare("select * ,  count(*) as countRate from rating_info where comment = ? and typeMovement = ?");
                                                            $rateCommentUp->execute([$comment['id'], 'up']);

                                                            $rateCommentDown = $con->prepare("select * , count(*) as countRate from rating_info where comment = ? and typeMovement = ?");
                                                            $rateCommentDown->execute([$comment['id'], 'down']);

                                                            $rateCommentUp = $rateCommentUp->fetch();
                                                            $rateCommentDown = $rateCommentDown->fetch();
                                                            if (isset($_SESSION['email']) && $_SESSION['person'] != 'admin') {
                                                                $rateOnThisComment = $con->prepare("select * from rating_info where comment = ? and $person = ?");
                                                                $rateOnThisComment->execute([$comment['id'], $profilePageId]);

                                                                $rateOnThisComment = $rateOnThisComment->fetch();
                                                            }
                                                            if (isset($_SESSION['email'])) {
                                                                if ($_SESSION['person'] != 'admin') {

                                                                    if ($_SESSION['email'] != $comment['email']) {
                                                                        if (is_array($rateOnThisComment) && $rateOnThisComment['typeMovement'] == 'up') { ?>
                                                                            <button style='color:blue' onclick='movementType(<?php echo  $comment["id"] ?> , "up")' class="fa fa-chevron-up rate"></button>
                                                                            <?php echo $rateCommentUp['countRate'] ?>
                                                                            <button onclick='movementType(<?php echo  $comment["id"] ?> , "down")' class="fa fa-chevron-down rate"></button>
                                                                            <?php echo $rateCommentDown['countRate'] ?>
                                                                        <?php } elseif (is_array($rateOnThisComment) && $rateOnThisComment['typeMovement'] == 'down') { ?>
                                                                            <button onclick='movementType(<?php echo  $comment["id"] ?> , "up")' class="fa fa-chevron-up rate"></button>
                                                                            <?php echo $rateCommentUp['countRate'] ?>
                                                                            <button style='color:blue' onclick='movementType(<?php echo  $comment["id"] ?> , "down")' class="fa fa-chevron-down rate"></button>
                                                                            <?php echo $rateCommentDown['countRate'] ?>
                                                                        <?php } else { ?>
                                                                            <button onclick='movementType(<?php echo  $comment["id"] ?> , "up")' class="fa fa-chevron-up rate"></button>
                                                                            <?php echo $rateCommentUp['countRate'] ?>
                                                                            <button onclick='movementType(<?php echo  $comment["id"] ?> , "down")' class="fa fa-chevron-down rate"></button>
                                                                            <?php echo $rateCommentDown['countRate'] ?>
                                                                        <?php }
                                                                    } else {  ?>
                                                                        <a class="fa fa-chevron-up" onclick="modalRate()"></a>
                                                                        <?php echo $rateCommentUp['countRate']; ?>
                                                                        <a class="fa fa-chevron-down" onclick="modalRate()"></a>
                                                                    <?php echo $rateCommentDown['countRate'];
                                                                    }
                                                                } else { ?>
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
                                                            }
                                                            ?>

                                                        </div>
                                                        <input type='hidden' name='commentId' value='<?php echo $comment['id'] ?>'>
                                                        <input id='movementType-<?php echo $comment['id'] ?>' type='hidden' name='moveMent' value='s'>
                                                    </form>
                                                </div>
                                                <div style='float:right'>
                                                    <?php
                                                    if (isset($_SESSION['email']) && ($comment['email'] == $_SESSION['email'] || $_SESSION['person'] == 'admin')) { ?>
                                                        <i style='cursor:pointer' id='comment-<?php echo $comment['id'] ?>' onclick="areYouSureComment(<?php echo $comment['id'] ?>)" data-placement="top" title="delete comment" class="fa fa-trash"></i>
                                                    <?php }
                                                    if (isset($_SESSION['email']) && ($comment['email'] == $_SESSION['email'] && $_SESSION['person'] == 'doctor')) { ?>
                                                        <i onclick="edit('<?php echo $comment['id'] ?>' , 'content_comment' , '<?php echo translate($comment['content'], $from, $to) ?>'  )" class="fas fa-edit"></i>
                                                    <?php } ?>
                                                </div>
                                                <div class="mt-2">
                                                    <p class="comment-text"><?php echo translate($comment['content'], $from, $to) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <form method='post' id='share_comment'>
                                    <input type='hidden' name='post' value='<?php echo $postId ?>'>
                                    <input type='hidden' name='id' value='<?php echo $profilePageId ?>'>
                                    <input type='hidden' name='Iam' value='<?php echo $person ?>'>
                                    <input type='hidden' name='commentId' id="commentUpdate" value=''>
                                    <div id="comm-1"></div>
                                    <div class="d-flex flex-row align-items-start">
                                        <?php if ($showIcon) { ?>
                                            <img class="rounded-circle" src="<?php echo $imgProfile ?>" width="40">
                                        <?php } ?>
                                        <textarea id='content_comment' name='content' class="form-control ml-1 shadow-none textarea"></textarea>
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
                                        <button onclick="HideShow()" class="btn btn-outline-primary btn-sm ml-1 shadow-none" type="button"><?php echo $lang['cancel'] ?></button>
                                    </div>
                                </form>
                            </div>
                            <!-- The Modal Image -->
                            <div id="myModal" class="modal">
                                <span class="close">&times;</span>
                                <img class="modal-content" id="img01">
                            </div>
                            <!-- The Modal Delete Psot  -->
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
                            <!-- The Modal Delete Comment -->
                            <div id="deleteModalComment" class="modal">
                                <span class="close">&times;</span>
                                <div id="caption"></div>
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
                            <!-- The Modal complaint dept post -->
                            <div id="complaint" class="modal">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <form method='POST' id='complaintForm'>
                                            <input type="text" id='inputComplaint' class="form-control form-control-lg" name="compalintContent" placeholder="<?php echo $lang['yourComplaint'] ?>"><br>
                                            <div id='alertDone' class="alert alert-success" role="alert" style='display:none'><?php echo $lang['success'] ?></div>
                                            <div id='alertError' class="alert alert-danger" role="alert" style='display:none'><?php echo $lang['error'] ?></div>
                                            <button type='submit' id='sendComplaint' class="button"><?php echo $lang['send'] ?></button>
                                            <button type='button' id='closeAlertCompaint' class="button"><?php echo $lang['close'] ?></button>
                                            <input type='hidden' id='idPostForComlaint' name='idPostForComlaint'>
                                        </form>
                                    </div>
                                </div>
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
                        </div>
                    </div>
                </div>
                <hr>
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
                    $message = "It must be from a user account";
                } else {
                    $message = "يجب ان يكون من حساب مستخدم";
                }
                ?>
                <h4 class="modal-title"><?php echo $message ?></h4>
            </div>
            <div class="modal-footer">
                <button id="closeModal2" type="button" class="btn btn-default" data-dismiss="modal"><?php echo $lang['close'] ?></button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.15.0/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="js/postpage.js"></script>
    <script>

    </script>
</body>

</html>