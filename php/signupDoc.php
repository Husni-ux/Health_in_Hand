<style>
    .alert {
        padding: 20px;
        background-color: #f44336;
        color: white;
    }
</style>
<?php
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$satck = array();
$stack[] = "";
session_start();
$fname = strip_tags($_POST['fname']);
$lname = strip_tags($_POST['lname']);
$email = strip_tags($_POST['email']);
$password = strip_tags($_POST['password']);
$repassword = strip_tags($_POST['repassword']);
$birth = $_POST['birth'];
$page = $_POST['page'];
$gender = $_POST['gender'];
$phone = strip_tags($_POST['phone']);
if (isset($_FILES['license_img'])) {
    $filename = $_FILES["license_img"]["name"];
    $tempname = $_FILES["license_img"]["tmp_name"];
    $folder = "img/license" . $filename;
    move_uploaded_file($tempname, '../' . $folder);
}

if (isset($_POST['license_number'])) {
    $license_number = strip_tags($_POST['license_number']);
} else {
    $license_number = null;
}
$specialization = $_POST['specialization'];
$lat =  $_POST['lat'];
$lng =  $_POST['lng'];

if($lat == null){
    $lat = 31.945368 ;
    $lng = 35.928371 ;
}

$dob = new DateTime($birth);
$now = new DateTime();
$difference = $now->diff($dob);
$age = $difference->y;



 if ($age >= 22 && $age < 41) {
     if (is_numeric($phone)) {
         if ($specialization != "0") {

             if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
                 if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                     echo "<div class='alert alert-warning' role='alert'>يجب أن تحتوي كلمة المرور على أرقام وأحرف وأحرف خاصة ،
                     ولا يقل عن 8 أحرف. </div>";
                 } else {
                     echo "<div class='alert alert-warning' role='alert'>Password must contain numbers, letters and special characters, 
                     and it is not less than 8 characters. </div>";
                 }

                 $i = 1;
             } else {
                 if ($password == $repassword) {
                     include 'connect.php';
                     $key = generateRandomString();
                     $password = md5($password);

                     $check = $con->prepare("select * from doctor where email = ?");
                     $check->execute([$email]);
                     $row = $check->rowcount();

                     $checkUser = $con->prepare("select * from user where email = ?");
                     $checkUser->execute([$email]);
                     $rowUser = $checkUser->rowcount();

                     if ($row == 0 && $rowUser == 0) {
                        $insert = $con->prepare("insert into doctor (first_name , last_name, email , password , gender , birth ,vkey ,	license_number , license_img ,  phone ,deptno , lat, lng)  VALUES (? , ? , ? , ? , ? , ?,  ? , ? , ? , ? , ? , ? , ?) ");
                        $insert->execute([$fname, $lname, $email, $password, $gender, $birth, $key, $license_number, $folder, $phone, $specialization, $lat, $lng]);

                        if ($insert) {
                            $to = $email;
                            $subject = "Email verification";
                            include 'messageDoc.php';
                            include 'mail.php';
                            $message = mes($to, $key, $page);

                            if (sendMail($to, $subject, $message))
?>
                            <script>
                                location.href = "thxMessage.php"
                            </script>
<?php

                   }
                } else {
                    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                        $stack[] = "البريد الإلكتروني موجود";
                    } else {
                        $stack[] = "email is exist";
                    }
                }
            } else {
                if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                    $stack[] = "كلمة المرور ليست مشابهة";
                } else {
                    $stack[] = "password is not similar";
                }
            }
        }
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            $stack[] =  "حدد التخصص";
        } else {
            $stack[] =  "select a major";
        }
    }
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        $stack[] = "ادخل رقم الهاتف";
    } else {
        $stack[] = "set phone number";
    }
}
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        $stack[] = "يجب أن يكون العمر أكبر من 18 عامًا و اقل من 70 عاما";
    } else {
        $stack[] = "Be over 18 years old and less than 70 years old";
    }
}

foreach ($stack as $ar) {
    if ($ar == "")
        continue;
    echo '<div class="alert"> ' . $ar . '  </div>';
}

?>