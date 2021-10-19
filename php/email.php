<style>
    .alert {
        padding: 20px;
        background-color: #2E8B57;
        color: white;
    }
</style>
<?php
include 'connect.php';
include 'messageEmail.php';
include 'mail.php';
session_start();

$email = $_POST['email'];
$row = null;
$select = $con->prepare("select * from user where email = ?");
$select->execute([$email]);

$selectDoc = $con->prepare("select * from doctor where email = ?");
$selectDoc->execute([$email]);

$you = null;
if ($select->rowcount() == 1) {
    $you = "user";
    $row = $select->fetch(PDO::FETCH_ASSOC);
} elseif ($selectDoc->rowcount() == 1) {
    $you = "doctor";
    $row = $selectDoc->fetch(PDO::FETCH_ASSOC);
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo '<div id="error" class="alert alert-info" role="alert">البريد الإلكتروني غير موجود</div>';
    } else {
        echo '<div id="error" class="alert alert-info" role="alert">email is not exist</div>';
    }
}

if ($you != null) {
    $id = $row['id'];
    $to = $email;
    $subject = "Password recover";
    $message = message($to, $id, $email, $you);

    sendMail($to, $subject, $message);
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo '<div class="alert">تحقق من بريدك الالكتروني</div>';
    } else {
        echo '<div class="alert">check your email</div>';
    }
}


?>