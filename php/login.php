<style>
    .alert {
        padding: 20px;
        background-color: #2E8B57;
        color: white;
        margin-top: 10px;
    }
</style>
<?php
include 'connect.php';
session_start();
$error = array();
$error[] = "";

function checkVer($res, $you, $con)
{
    $row = $res->fetch(PDO::FETCH_ASSOC);
    $verified = $row['verified'];

    if ($verified == 1) {
        $_SESSION['email'] = $row['email'];
        $_SESSION['person'] = $you;
?>
        <script>
            location.href = "index.php";
        </script>
    <?php
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            return "لم يتم التحقق من هذا الحساب ";
        } else {
            return "this account has not yet been verified ";
        }
    }
}
$email = $_POST['email'];
$password = $_POST['password'];
$password = md5($password);

$result = $con->prepare("select * from user where email = ? and password = ? ");
$result->execute([$email, $password]);

$resultDoc = $con->prepare("select * from doctor where email = ? and password = ? ");
$resultDoc->execute([$email, $password]);

$admin = $con->prepare("select * from admin where email = ? and password = ? ");
$admin->execute([$email, $password]);

if ($result->rowcount() != 0) {
    $error[] =  checkVer($result, "user", $con);
} elseif ($resultDoc->rowcount() != 0) {
    $error[] = checkVer($resultDoc, "doctor", $con);
} elseif ($admin->rowcount() != 0) {
    $row = $admin->fetch();
    $_SESSION['email'] = $row['email'];
    $_SESSION['person'] = "admin";
    ?>
    <script>
        location.href = "admin/user.php";
    </script>
<?php
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        $error[] = "البريد الإلكتروني أو كلمة المرور غير صالحة";
    } else {
        $error[] = "email or password is not valid";
    }
}

foreach ($error as $ar) {
    if ($ar == "")
        continue;
    echo '<div id="error" class="alert alert-info" role="alert">' . $ar . '</div>';
}
?>