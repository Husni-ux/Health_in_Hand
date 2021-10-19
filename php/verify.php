<?php
$error = null;
session_start();
if(isset($_GET['key']) && isset($_GET['page'])){

    include 'connect.php';
    $key = $_GET['key'];
    $page = $_GET['page'];

        
    if($page == "user"){
        $result = $con->prepare("select verified from user where verified = 0 and vkey = '$key' limit 1");
        $result->execute();

        if($result->rowcount() == 1){
            $update = $con->prepare("update user set verified = 1 where vkey = '$key'");
            $update->execute();
            if($update){
        ?>
            <script>
                location.href = "..\\index.php";
            </script>
        <?php 
            }else
            {
                echo "no success";
            }
        }else
        {
            $error = "email is exist";
        }
    }elseif($page == "doctor"){
        $result = $con->prepare("select verified from doctor where verified = 0 and vkey = '$key'");
        $result->execute();

        if($result->rowcount() == 1){
            $update = $con->prepare("update doctor set verified = 1 where vkey = '$key' limit 1");
            $update->execute();
            if($update){
        ?>
            <script>
                location.href = "..\\index.php";
            </script>
        <?php 
            }else
            {
                if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                    echo "خطأ";
                } else {
                    echo "error";
                }
            }
        }else
        {
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                echo "البريد الإلكتروني موجود";
            } else {
                echo "email is exist";
            }
        }
    }
        
}


?>
