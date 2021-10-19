<?php
    include 'connect.php';
    session_start();

    $person =  $_SESSION['person'];
    $personInfo = $con->prepare("select * from $person where email = ?");
    $personInfo->execute([$_SESSION['email']]);
    $personInfo = $personInfo->fetch();

    $content = strip_tags($_POST['compalintContent']);
    $date_complaint = date("Y/m/d");


    if(isset($_POST['idArticleForComlaint'])){

        $post = $_POST['idArticleForComlaint'];
        $insertComplaint = $con->prepare("insert into complaints ($person , content , articlePost , date_complaint) values(? , ? , ? , ?)");
        if($insertComplaint->execute([$personInfo['id'] , $content , $post , $date_complaint ])){
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                echo 'تم';
            } else {
                echo 'done';
            }
        }else{
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                echo 'خطأ';
            } else {
                echo 'error';
            }
        }
    }else{
        $post = $_POST['idPostForComlaint'];

        $insertComplaint = $con->prepare("insert into complaints ($person , content , post , date_complaint) values(? , ? , ? , ?)");
        if($insertComplaint->execute([$personInfo['id'] , $content , $post , $date_complaint ])){
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                echo 'تم';
            } else {
                echo 'done';
            }
        }else{
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                echo 'خطأ';
            } else {
                echo 'error';
            }
        }
    }

    // echo $_POST['idArticleForComlaint'];

?>