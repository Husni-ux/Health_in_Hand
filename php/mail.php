<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../mail/vendor/autoload.php";
function sendMail($to ,$subject ,$message)
{
  $mail = new PHPMailer();
  $mail->IsSMTP();
  // $mail->Mailer = "smtp";
  // $mail->SMTPDebug  = 1;
  $mail->SMTPAuth   = TRUE;
  $mail->SMTPSecure = "tls";
  $mail->Port       = 587;
  $mail->Host       = "smtp.gmail.com";
  $mail->Username   = "healthinhand8@gmail.com";
  $mail->Password   = "6269231@";
  $mail->IsHTML(true);
  $mail->AddAddress($to, "recipient-name");
  $mail->SetFrom("healthinhand8@gmail.com", "Health In Hand");
  $mail->Subject = $subject ;
  $content = $message ;
  $mail->MsgHTML($content);
  $mail->Send();
    
}
?>
