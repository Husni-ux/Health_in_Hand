<?php
include '../php/connect.php';
session_start();

$i = 0;
$password = $_POST['password'];
$con_password = $_POST['con_password'];
$email = $_POST['email'];
$person = $_POST['person'];

if ($password != null) {
    if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo "<div class='alert alert-warning' role='alert'>يجب أن تحتوي كلمة المرور على أرقام وأحرف وأحرف خاصة ،
            ولا يقل عن 8 أحرف. </div>";        } else {
            echo "<div class='alert alert-warning' role='alert'>Password must contain numbers, letters and special characters, 
            and it is not less than 8 characters. </div>";        }

        $i = 1;
    } else {
        if ($password == $con_password) {
            $password = md5($con_password);
            $stmt = $con->prepare("UPDATE $person SET password = '$password' where email= ?");
            if($stmt->execute([$email])){
                if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                    echo "<div class='alert alert-success' role='alert'>نجاح. </div>";    
                } else {
                    echo "<div class='alert alert-success' role='alert'>Success. </div>";    
                }
            }
        } else {
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                echo "<div class='alert alert-warning' role='alert'>كلمة المرور غير متطابقة. </div>";
            } else {
                echo "<div class='alert alert-warning' role='alert'>password is not match. </div>";
            }
            $i = 1;
        }
    }
}
