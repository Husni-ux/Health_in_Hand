<?php

include 'connect.php';
session_start();
$content = strip_tags($_POST['message']);
$email =  $_POST['email'];
$articleId = $_POST['articleId'];
$currentDate = date('Y-m-d');
$doctor = $con->prepare("select * from doctor where email = ?");
$doctor->execute([$email]);
$doctor = $doctor->fetch();
$doctorId =  $doctor['id'];
if ($content != null) {

    $stmt = $con->prepare("insert into article (doctor , postDoctor , date_share , articleId) values( ? , ? , ? , ?)");
    if ($stmt->execute([$doctorId, $content, $currentDate, $articleId])) {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo '<div class="alert alert-success" role="alert">
            سيتم مراجعته من قبل الإدارة       
             </div>';
        } else {
            echo '<div class="alert alert-success" role="alert">
            It will be reviewed by the administration
        </div>';
        }
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo "<div class='alert alert-danger' role='alert'>
            هناك خطأ
        </div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>
            There's a mistake
        </div>";
        }
    }
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo "<div class='alert alert-danger' role='alert'>
        لا يجب أن تكون فارغة
    </div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>
        It does not have to be empty
    </div>";
    }
}
