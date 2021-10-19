<style>
.alert {
  padding: 20px;
  background-color: #f44336;
  color: white;
}
</style>
<?php
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
session_start();
$satck = array();
$stack[] = "";
$fname = strip_tags($_POST['fname']);
$lname = strip_tags($_POST['lname']);
$email = strip_tags($_POST['email']);
$password = strip_tags($_POST['password']);
$repassword = strip_tags($_POST['repassword']);
$fname = strip_tags($_POST['fname']);
$fname = strip_tags($_POST['fname']);
$birth = $_POST['brith'];
$page = $_POST['page'];
$gender = $_POST['gender'];

$dob = new DateTime($birth);
$now = new DateTime();
$difference = $now->diff($dob);
$age = $difference->y;

if($age >= 18){
    if ( ! preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password )){
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            $error[] = 'لديك موعد في نفس التاريخ';
        } else {
            $error[] = 'you have an appointment in a same date';
        }
        echo "<div class='alert alert-warning' role='alert'>Password must contain numbers, letters and special characters, 
        and it is not less than 8 characters. </div>";
        $i = 1;
    }else{
        if($password == $repassword){
            include 'connect.php';
            
            $key = generateRandomString();
            $password = md5($password);
           
            $cheack = $con->prepare("select * from user where email = ?");
            $cheack->execute([$email]);
            $row = $cheack->rowcount();
    
            $cheackDoc = $con->prepare("select * from doctor where email = ?");
            $cheackDoc->execute([$email]);
            $rowDoc = $cheackDoc->rowcount();
            
            if($row == 0 && $rowDoc == 0){
                $insert = $con->prepare("insert into user (first_name , last_name, email , password , gender , birth ,vkey )  VALUES (? , ? , ? , ? , ? , ? , ?) ");
                $insert->execute([$fname , $lname , $email , $password , $gender , $birth ,$key]);
            
                if($insert){
                        $to = $email;
                        $subject = "Email verification";
                        include 'messageUser.php';
                        include 'mail.php';
                        $message = mes($to , $key , $page); 
            
                        if(sendMail($to , $subject , $message ))
                ?>
                    <script>
                        location.href = "thxMessage.php"
                    </script>
                  <?php  
                    }
        
            }else { 
                if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                    $stack[] = "هذا البريد الإلكتروني موجود بالفعل";
                } else {
                    $stack[] = "This email already exists";
                }
            }
        
        }
        else
            {
                if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                    $stack[] ="كلمة المرور ليست مشابهة";
                } else {
                    $stack[] ="password is not similar";
                }
            
            }
    }
    }else{
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            $stack[] = "يجب أن يكون العمر أكبر من 18 عامًا";
        } else {
            $stack[] = "The age must be over 18";
        }
    }



foreach($stack as $ar){
    if($ar == "")
        continue;
    echo '<div class="alert"> '.$ar.'  </div>';
}




?>