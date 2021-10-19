<?php
include 'connect.php';
session_start();

$firstName = strip_tags($_POST['first_name']);
$lastName = strip_tags($_POST['last_name']);
$email = strip_tags($_POST['email']);
$password = strip_tags($_POST['password']);
$confirmPassword = strip_tags($_POST['conPassword']);

$allAdmin = $con->prepare('select * from admin where email = ?');
$allAdmin->execute([$email]);
$allUser = $con->prepare('select * from user where email = ?');
$allUser->execute([$email]);
$allDoctor = $con->prepare('select * from doctor where email = ?');
$allDoctor->execute([$email]);

$session = $con->prepare('select * from admin where email = ?');
$session->execute([$_SESSION['email']]);
$session = $session->fetch();
$adminId = $session['id'];

if ($allAdmin->rowCount() == 0 && $allUser->rowCount() == 0 && $allDoctor->rowCount() == 0) {
    if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo '<div class="alert alert-danger" role="alert">
                    يجب أن تحتوي كلمة المرور على أرقام وأحرف وأحرف خاصة ،
                    ولا يقل عن 8 أحرف.
                </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
                Password must contain numbers, letters and special characters, 
                and it is not less than 8 characters.
            </div>';
        }
    } elseif ($password == $confirmPassword) {
        $password = md5($password);
        $stmt = $con->prepare('insert into admin (first_name , last_name , email , password,adminId) values(? ,?  ,? , ?, ?)');
        if ($stmt->execute([$firstName, $lastName, $email, $password , $adminId])) {
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                echo '<div class="alert alert-success" role="alert">
                        تم التسجيل بنجاح
                    </div>';
            } else {
                echo '<div class="alert alert-success" role="alert">
                    Registration was successful
                 </div>';
            }
        }
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo '<div class="alert alert-danger" role="alert">
                    كلمة السر غير متطابقة
                 </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
                Password does not match
            </div>';
        }
    }
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo '<div class="alert alert-danger" role="alert">
                البريد الالكتروني موجود بالفعل
              </div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">
            Email already exists
          </div>';
    }
}
