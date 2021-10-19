<style>
    .alert {
        padding: 20px;
        background-color: #2E8B57;
        color: white;
    }
</style>
<?php
session_start();
include "connect.php";
$id = $_POST['id'];
$email = $_POST['email'];
$you = $_POST['you'];
$pass = $_POST['password'];
$repass = $_POST['repassword'];

if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $pass)) {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo "<div class='alert alert-warning' role='alert'>يجب أن تحتوي كلمة المرور على أرقام وأحرف وأحرف خاصة ،
        ولا يقل عن 8 أحرف. </div>";
    } else {
        echo "<div class='alert alert-warning' role='alert'>Password must contain numbers, letters and special characters, 
        and it is not less than 8 characters. </div>";
    }

} else {
    if ($pass == $repass) {
        $pass = md5($pass);
        if ($you == "user") {
            $update = $con->prepare("update user set password = ? where id = ? AND email = ?");
            $update->execute([$pass, $id, $email]);
            if ($update) {
?>
                <script>
                    window.location.href = "passwordChangeDone.php";
                </script>
            <?php
            }
        } elseif ($you == "doctor") {
            $update = $con->prepare("update doctor set password = ? where id = ? AND email = ?");
            $update->execute([$pass, $id, $email]);
            if ($update) {
            ?>
                <script>
                    window.location.href = "passwordChangeDone.php";
                </script>
<?php
            }
        }
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo '<div id="error" class="alert alert-info" role="alert">كلمة المرور ليست مشابهة</div>';
        } else {
            echo '<div id="error" class="alert alert-info" role="alert">password is not similar</div>';
        }
    }
}


?>