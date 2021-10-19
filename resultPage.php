<?php
session_start();
include 'php/connect.php';
include 'googleTranslate/traslate.php';


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

require_once "languages/" . 'result_' . $_SESSION['lang'] . ".php";

$resultsPerPage = 5;
$numberOfResultsPost = 0;
$numberOfResultsDoctor = 0;
$numberOfResultsDieases = 0;

if (!isset($_GET['page'])) {
    $page = 1;
} else if (isset($_GET['page'])) {
    $page = $_GET['page'];
}

if (isset($_GET["search"])) {
        $condition = '';
        $query = explode(" ", $_GET["search"]);
        $len = strlen($_GET['search']);
        if($len <= 1){
            header("location:index.php");
        }
        foreach ($query as $text) {
            $text2 = translate($text , $from , $to);// translate
            if (strlen($text) > 1)
                $condition .="content LIKE '%$text%' OR content LIKE '%$text2%' OR ";
        }
        $condition = substr($condition, 0, -4);
        $sql_post = "SELECT * FROM post WHERE " . $condition;
        $result = $con->prepare($sql_post);
        $result->execute();
        $numberOfResultsPost = $result->rowCount();

        $sql_post = "SELECT * FROM post WHERE " . $condition . "LIMIT " . $resultsPerPage . " OFFSET " . ($page - 1) * $resultsPerPage;
        $result = $con->prepare($sql_post);
        $result->execute();

        // doctor
        $condition = '';
        foreach ($query as $text) {
            $text2 = translate($text , $from , $to);// translate
            if (strlen($text) > 1)
                $condition .= "first_name LIKE '%$text%' OR first_name LIKE '%$text2%' OR last_name LIKE '%$text%' OR last_name LIKE '%$text2%' OR ";
        }

        $condition = substr($condition, 0, -4);
        $sql_doctor = "SELECT * FROM doctor WHERE " . $condition;
        $result_doctor = $con->prepare($sql_doctor);
        $result_doctor->execute();
        $numberOfResultsDoctor = $result_doctor->rowCount();

        $sql_doctor = "SELECT * FROM doctor WHERE " . $condition . "LIMIT " . $resultsPerPage . " OFFSET " . ($page - 1) * $resultsPerPage;
        $result_doctor = $con->prepare($sql_doctor);
        $result_doctor->execute();

        // disease
        $condition = '';
        foreach ($query as $text) {
            $text2 = translate($text , $from , $to);// translate
            if (strlen($text) > 1)
                $condition .= "title LIKE '%$text%' OR content LIKE '%$text%' OR title LIKE '%$text2%' OR content LIKE '%$text2%' OR 
                title_ar LIKE '%$text%' OR content_ar LIKE '%$text%' OR title_ar LIKE '%$text2%' OR content_ar LIKE '%$text2%' OR ";
        }
        $condition = substr($condition, 0, -4);
        $sql_diseases = "SELECT * FROM article WHERE " . $condition;
        $result_diseases = $con->prepare($sql_diseases);
        $result_diseases->execute();
        $numberOfResultsDieases = $result_diseases->rowCount();

        $sql_diseases = "SELECT * FROM article WHERE " . $condition . "LIMIT " . $resultsPerPage . " OFFSET " . ($page - 1) * $resultsPerPage;
        $result_diseases = $con->prepare($sql_diseases);
        $result_diseases->execute();
   // }
} else {
    header('location:index.php');
}

$numberOfResults = $numberOfResultsDieases + $numberOfResultsDoctor + $numberOfResultsPost;

$totalPagesPost = ceil($numberOfResultsPost / $resultsPerPage);
$totalPagesDoctor = ceil($numberOfResultsDoctor / $resultsPerPage);
$totalPagesDieases = ceil($numberOfResultsDieases / $resultsPerPage);

if (isset($_SESSION['email'])) {
    $person = $_SESSION['person'];
    $stmt = $con->prepare("SELECT * from $person where email = ?");
    $stmt->execute([$_SESSION['email']]);
    $row = $stmt->fetch();
    if($person == 'doctor'){
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="img/HIH2.jpg" rel="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <link rel="stylesheet" href="style/style3.css">
    <link rel="stylesheet" href="style/style10.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>

<body>

    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <h1 class="logo mr-auto">
            <a style='text-decoration: none;' href="index.php">Health In Hand</a>
        </h1>
        <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>
            <form style="margin-right: 40px;" method="POST" class="form-inline my-2 my-lg-0">
                <?php if (isset($_SESSION['email'])) { ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo $row['img'] ?>" width="40" height="40" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <?php if ($_SESSION['person'] != 'admin') { ?>
                                    <?php if ($_SESSION['person'] == 'doctor') { ?>
                                        <a class="dropdown-item" href="questionPage.php?dept=<?php echo $dept ?>"><?php echo $lang['question'] ?></a>
                                    <?php } ?>
                                    <a class="dropdown-item" href="<?php echo $person ?>Profile.php"><?php echo $lang['profile'] ?></a>
                                    <a class="dropdown-item" href="editProfile.php"><?php echo $lang['editProfile'] ?></a>
                                <?php } else { ?>
                                    <a class="dropdown-item" href="editProfile.php"><?php echo $lang['dashboard'] ?></a>
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
    <br>
    <div class="container bootstrap snippets bootdey">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <h2>
                            <?php echo $lang['numRes'] ?> <span class="text-navy"><?php echo $numberOfResults ?></span>
                        </h2>
                        <div class="search-form">
                            <form method="post">
                                <div class="input-group">
                                    <input type="text" placeholder="<?php echo $lang['search'] ?>" name="searchBox" class="form-control input-lg">
                                    <div class="input-group-btn">
                                        <button class="btn btn-lg btn-primary" type="submit" name="search" style='margin-left:10px'>
                                            <?php echo $lang['search'] ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="btn-group btn-group-justified">
                                <button onclick='clickToShow("post")' class="btn btn-primary"><?php echo $lang['posts'] ?></button>
                                <button onclick='clickToShow("doctor")' class="btn btn-primary"><?php echo $lang['doctors'] ?></button>
                                <button onclick='clickToShow("disease")' class="btn btn-primary"><?php echo $lang['diseases'] ?></button>
                            </div>
                        </div>
                        <!-- start post section  -->
                        <div class='post'>
                            <?php
                            if (isset($result) && $result->rowcount() > 0) {
                                foreach ($result as $res) {
                                    $comments = $con->prepare('select * from comment where post = ?');
                                    $comments->execute([$res['id']]);
                                    $allComment = $comments->fetch();

                                    $maxRating  = 0;
                                    $bestComment = '';
                                    foreach ($comments as $comment) {
                                        $ratingInfo =  $con->prepare('select count(id) as rateCount from rating_info where comment  = ? and typeMovement = "up"');
                                        $ratingInfo->execute([$comment['id']]);
                                        $ratingInfo = $ratingInfo->fetch();
                                        if ($ratingInfo['rateCount'] > $maxRating) {
                                            $bestComment = $comment['content'];
                                            $maxRating = $ratingInfo['rateCount'];
                                        }
                                    }
                                    if ($maxRating == 0) {
                                        if (is_array($allComment)) {
                                            $bestComment = $allComment['content'];
                                        }
                                    }
                            ?>
                                    <div class="hr-line-dashed"></div>
                                    <div class="search-result">
                                        <h5><a href='postPage.php?post=<?php echo $res['id'] ?>'><?php echo translate( $res['content'] , $from , $to) ; ?></a></h5>
                                        <h6 class="search-link" style='margin-top:15px  '> <?php echo translate( $bestComment , $from , $to) //$bestComment ?> </h6>
                                    </div>


                            <?php
                                }
                            } else {
                                if ($_SESSION['lang'] == 'en') {
                                    echo '<label>Data not Found</label>';
                                } else {
                                    echo '<label>لا يوجد نتائج</label>';
                                }
                            }
                            ?>
                            <div class="hr-line-dashed"></div>
                            <div class="text-center">
                                <?php if ($totalPagesPost >= 1) { ?>
                                    <div class="btn-group">
                                        <?php if (isset($_GET['page']) && $_GET['page'] != 1) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php echo ($_GET['page'] - 1) ?>&search=<?php echo ($_GET['search']) ?>&sql=post'><i class="fas fa-chevron-left"></i></a>
                                        <?php } ?>
                                        <?php
                                        for ($count = 1; $count <= $totalPagesPost; ++$count) {
                                            if ($page == $count) {
                                                echo '<a  style="color:black;" class="btn btn-white" href="resultPage.php?page=' . $count . '&search=' . $_GET["search"] . '&sql=post ">' . $count . '</a> ';
                                            } else {
                                                echo '<a class="btn btn-white" href="resultPage.php?page=' . $count . '&search=' . $_GET["search"] . '&sql=post ">' . $count . '</a> ';
                                            }
                                        }
                                        if ((isset($_GET['page']) && $_GET['page'] != $totalPagesPost)) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php echo ($_GET['page'] + 1) ?>&search=<?php echo ($_GET['search']) ?>&sql=post'><i class="fas fa-chevron-right"></i></a>
                                        <?php } elseif (isset($_GET['page']) && $_GET['page'] == $totalPagesPost) {
                                        } elseif (($count - 1) > 1) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php if (isset($_GET['page'])) echo ($_GET['page'] + 1);
                                                                                                else echo '2' ?>&search=<?php echo ($_GET['search']) ?>&sql=post'><i class="fas fa-chevron-right"></i></a>
                                        <?php
                                        } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- end post section  -->
                        <!-- doctor section  -->
                        <div id='doctor'>
                            <?php
                            if (isset($result_doctor) && $result_doctor->rowcount() > 0) {
                                foreach ($result_doctor as $result) {
                                    echo '<div class="hr-line-dashed"></div> ';
                                    if ($result['verify_license'] == 1) {
                                        $icon = '<i class="fa fa-check-circle"></i>';
                                    } else {
                                        $icon = '<i class="fa fa-ban"></i>';
                                    }
                            ?>
                                    <div class="col-lg-6">
                                        <div class="member d-flex align-items-start">
                                            <div class="pic"><img style='border-radius: 50%; max-height: 200px; max-width: 200px;' src="<?php echo $result['img'] ?>" class="img-fluid" alt=""></div>
                                            <div class="member-info" style='margin-top:50px ; margin-left:10px'>
                                                <a style="text-decoration:none" href="doctorProfile.php?email=<?php echo $result['email'] ?>">
                                                    <h4 style="cursor:pointer">Dr <?php echo $result['first_name'] . ' ' . $result['last_name'] . ' ' . $icon ?></h4>
                                                </a>
                                                <span>
                                                    <?php
                                                    $deptName = $con->prepare("select * from dept where id = ?");
                                                    $deptName->execute([$result['deptno']]);
                                                    $deptName = $deptName->fetch();
                                                    if ($_SESSION['lang'] == 'en') {
                                                        echo $deptName['name'];
                                                    } else {
                                                        echo $deptName['name_ar'];
                                                    }
                                                    ?>
                                                </span>
                                                <p><?php echo  $result['bio'] ?></p>
                                            </div>
                                        </div>
                                    </div>

                            <?php }
                            } else {
                                if ($_SESSION['lang'] == 'en') {
                                    echo '<label>Data not Found</label>';
                                } else {
                                    echo '<label>لا يوجد نتائج</label>';
                                }
                            } ?>
                            <div class="hr-line-dashed"></div>
                            <div class="text-center">
                                <?php if ($totalPagesDoctor >= 1) { ?>
                                    <div class="btn-group">
                                        <?php if (isset($_GET['page']) && $_GET['page'] != 1) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php echo ($_GET['page'] - 1) ?>&search=<?php echo ($_GET['search']) ?>&sql=doctor'><i class="fas fa-chevron-left"></i></a>
                                        <?php } ?>
                                        <?php
                                        for ($count = 1; $count <= $totalPagesDoctor; ++$count) {
                                            if ($page == $count) {
                                                echo '<a  style="color:black;" class="btn btn-white" href="resultPage.php?page=' . $count . '&search=' . $_GET["search"] . '&sql=doctor ">' . $count . '</a> ';
                                            } else {
                                                echo '<a class="btn btn-white" href="resultPage.php?page=' . $count . '&search=' . $_GET["search"] . '&sql=doctor ">' . $count . '</a> ';
                                            }
                                        }
                                        if ((isset($_GET['page']) && $_GET['page'] != $totalPagesDoctor)) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php echo ($_GET['page'] + 1) ?>&search=<?php echo ($_GET['search']) ?>&sql=doctor'><i class="fas fa-chevron-right"></i></a>
                                        <?php } elseif (isset($_GET['page']) && $_GET['page'] == $totalPagesDoctor) {
                                        } elseif (($count - 1) > 1) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php if (isset($_GET['page'])) echo ($_GET['page'] + 1);
                                                                                                else echo '2' ?>&search=<?php echo ($_GET['search']) ?>&sql=doctor'><i class="fas fa-chevron-right"></i></a>
                                        <?php
                                        } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- end section doctor -->

                        <!-- start section disease -->
                        <div id='disease'>
                            <?php
                            if (isset($result_diseases) && $result_diseases->rowcount() > 0) {
                                foreach ($result_diseases as $res) {
                            ?>
                                    <div class="hr-line-dashed"></div>
                                    <div class="search-result">
                                        <h5><a style='text-decoration:none' href="article.php?article=<?php echo $res['id'] ?>"><?php if($_SESSION['lang'] == "en") echo $res['title']; else echo $res['title_ar'] ?></a></h5>
                                    </div>
                            <?php
                                }
                            } else {
                                if ($_SESSION['lang'] == 'en') {
                                    echo '<label>Data not Found</label>';
                                } else {
                                    echo '<label>لا يوجد نتائج</label>';
                                }
                            }
                            ?>
                            <div class="hr-line-dashed"></div>
                            <div class="text-center">
                                <?php if ($totalPagesDieases >= 1) { ?>
                                    <div class="btn-group">
                                        <?php if (isset($_GET['page']) && $_GET['page'] != 1) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php echo ($_GET['page'] - 1) ?>&search=<?php echo ($_GET['search']) ?>&sql=disease'><i class="fas fa-chevron-left"></i></a>
                                        <?php } ?>
                                        <?php
                                        for ($count = 1; $count <= $totalPagesDieases; ++$count) {
                                            if ($page == $count) {
                                                echo '<a  style="color:black;" class="btn btn-white" href="resultPage.php?page=' . $count . '&search=' . $_GET["search"] . '&sql=disease ">' . $count . '</a> ';
                                            } else {
                                                echo '<a class="btn btn-white" href="resultPage.php?page=' . $count . '&search=' . $_GET["search"] . '&sql=disease ">' . $count . '</a> ';
                                            }
                                        }
                                        if ((isset($_GET['page']) && $_GET['page'] != $totalPagesDieases)) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php echo ($_GET['page'] + 1) ?>&search=<?php echo ($_GET['search']) ?>&sql=disease'><i class="fas fa-chevron-right"></i></a>
                                        <?php } elseif (isset($_GET['page']) && $_GET['page'] == $totalPagesDieases) {
                                        } elseif (($count - 1) > 1) { ?>
                                            <a class="btn btn-white" href='resultPage.php?page=<?php if (isset($_GET['page'])) echo ($_GET['page'] + 1);
                                                                                                else echo '2' ?>&search=<?php echo ($_GET['search']) ?>&sql=disease'><i class="fas fa-chevron-right"></i></a>
                                        <?php
                                        } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- end section disease -->
                    </div>
                    <br>
                    <div class="post">
                        <form method='post' id='share_post'>
                            <div class="d-flex flex-row align-items-start">
                                <?php if (isset($_SESSION['email'])) { ?>
                                <?php } ?>
                                <div style='width:100%'>
                                    <textarea id="summernote" name='content'></textarea>
                                    <input type="hidden" name="image" id='image'>
                                </div>
                            </div>
                            <div class="mt-2 text-right">
                                <div class="btn btn-primary btn-sm shadow-none">
                                    <select name="department" id="dept" class="btn-primary btn-sm shadow-none">
                                        <option value="0"><?php echo $lang['selectDepartment'] ?></option>
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
                                </div><br>
                                <?php
                                if (isset($_SESSION['email'])) {
                                    if ($_SESSION['person'] == 'user') {
                                ?>
                                        <button style='margin-top:10px;' name='share' class="btn btn-primary btn-sm shadow-none" type="submit"><?php echo $lang['postQuestion'] ?></button>
                                    <?php } else {
                                    ?>
                                        <a style='margin-top:10px;color:white' class="btn btn-primary btn-sm shadow-none" onclick="modalLogin()"><?php echo $lang['postQuestion'] ?></a>
                                    <?php
                                    }
                                } else { ?>
                                    <a style='margin-top:10px;color:white' class="btn btn-primary btn-sm shadow-none" onclick="modalLogin()"><?php echo $lang['postQuestion'] ?></a>
                                <?php  } ?>
                                <button style='margin-top:10px;' class="btn btn-outline-primary btn-sm ml-1 shadow-none" type="button"><?php echo $lang['cancel'] ?></button>
                            </div>
                        </form>
                        <div style='margin-top:10px;' id='error'></div>
                    </div>
                </div>
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
        </footer>
    </div>
    <input type='hidden' id='sql' value='<?php if (isset($_GET['sql'])) echo $_GET['sql']; ?>'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="js/ajax.js"></script>
    <script src="js/result.js"></script>
    <script>
        function modalLogin() {
            var close = document.getElementById("closeModal");
            var modal = document.getElementById(`commentModal`);
            modal.style.display = "block";
            close.onclick = function() {
                modal.style.display = "none";
            }
        }
    </script>



</body>

</html>