<style>
    .alert {
        padding: 20px;
        background-color: red;
        color: white;
        margin-top: 10px;
    }
</style>
<?php
// include 'connect.php';
// $error = array();
// $error[] = "";
// session_start();
// $dateAppointment = $_POST['dateAppointment'];
// $department = $_POST['department'];
// $doctor = $_POST['doctor'];
// $message = $_POST['message'];
// $sTime = $_POST['sTime'];
// $eTime =  date('H:i', strtotime(' +20 minutes', strtotime($sTime)));
// $currentDate = date("d/m/Y");

// if (isset($_SESSION['email'])) {
//     $userEmail = $_SESSION['email'];
//     if ($dateAppointment != NULL) {
//         if ($dateAppointment >= $currentDate) {
//             $dateAppointment = strtr($dateAppointment, '/', '-');
//             $dateAppointment =  date('Y-m-d', strtotime($dateAppointment));
//             $unixTimestamp = strtotime($dateAppointment);            
//             $dayOfWeek = date("l", $unixTimestamp);
            
//             if($dayOfWeek != "Friday" || $dayOfWeek != "Saturday"){

//             }
//             if ($department != 0) {
//                 if ($doctor != 0) {
//                     if ($sTime != $eTime) {
//                         $getUserId = $con->prepare("select id from user where email = ?");
//                         $getUserId->execute([$userEmail]);
//                         $getUserId = $getUserId->fetch();
//                         $userId = $getUserId['id'];

//                         $getUserAppointment = $con->prepare("select * from appointment where user = ?");
//                         $getUserAppointment->execute([$userId]);

//                         $getDoctorAppointment = $con->prepare("select * from appointment where doctor = ?");
//                         $getDoctorAppointment->execute([$doctor]);

//                         $doctorData = $con->prepare("select * from doctor where id = ?");
//                         $doctorData->execute([$doctor]);
//                         $doctorData = $doctorData->fetch();

//                         /* to sure the user dont have date in same time */
//                         $flage = 0;
//                         $sTime =  date('H:i:s', strtotime($sTime));
//                         $eTime =  date('H:i:s', strtotime($eTime));
//                         foreach ($getUserAppointment as $ar) {

//                             if ($dateAppointment == $ar['date_booking']) {
//                                 if (
//                                     $sTime > $ar['sTime'] && $sTime < $ar['eTime'] ||
//                                     $eTime > $ar['sTime'] && $eTime < $ar['eTime'] ||
//                                     $sTime < $ar['sTime'] && $eTime >= $ar['eTime'] ||
//                                     $sTime <= $ar['sTime'] && $eTime > $ar['eTime'] ||
//                                     $sTime == $ar['sTime'] && $eTime == $ar['eTime']
//                                 ) {

//                                     $flage = 1;
//                                 }
//                             }
//                         }
//                         /* to sure the doctor dont have date in same time */
//                         $flage2 = 0;
//                         foreach ($getDoctorAppointment as $ar) {
//                             if ($dateAppointment == $ar['date_booking']) {
//                                 if (
//                                     $sTime > $ar['sTime'] && $sTime < $ar['eTime'] ||
//                                     $eTime > $ar['sTime'] && $eTime < $ar['eTime'] ||
//                                     $sTime < $ar['sTime'] && $eTime >= $ar['eTime'] ||
//                                     $sTime <= $ar['sTime'] && $eTime > $ar['eTime'] ||
//                                     $sTime == $ar['sTime'] && $eTime == $ar['eTime']
//                                 ) {

//                                     $flage2 = 1;
//                                 }
//                             }
//                         }
//                         $flage3 = 0;
//                         if (
//                             $sTime < $doctorData['sTime']  || $sTime >= $doctorData['eTime'] ||
//                             $eTime <= $doctorData['sTime']  || $eTime >= $doctorData['eTime']
//                         ) {
//                             $flage3 = 1;
//                         }
//                         $flage4 = 0;
//                         $currentDate = date('Y-m-d');
//                         if (
//                             $dateAppointment < $currentDate
//                         ) {
//                             $flage4 = 1;
//                         }
//                         //insert
//                         if ($flage == 1) {
//                             if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                                 $error[] = 'لديك موعد في نفس التاريخ';
//                             } else {
//                                 $error[] = 'you have an appointment in a same date';
//                             }
//                         } elseif ($flage4 == 1) {
//                             if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                                 $error[] = "يجب أن يكون الموعد بعد تاريخ اليوم";
//                             } else {
//                                 $error[] = "The appointment must be after today's date";
//                             }
//                         } elseif ($flage3 == 1) {
//                             if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                                 $error[] = 'العيادة مغلقة في هذا الوقت';
//                             } else {
//                                 $error[] = 'The clinic is closed at this time';
//                             }
//                         } elseif ($flage2 == 1) {
//                             if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                                 $error[] = 'الطبيب لديه موعد في نفس التاريخ';
//                             } else {
//                                 $error[] = 'Doctor have an appointment in a same date';
//                             }
//                         } else {
//                             $insert = $con->prepare("insert into appointment (doctor , user , date_booking , message , sTime , eTime) values (? , ? , ? , ? , ? , ?)");
//                             $insert->execute([$doctor, $userId, $dateAppointment, $message, $sTime, $eTime]);
//                             if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                                 echo '<div id="error" class="alert alert-info" role="alert" style="background:green;">كانت عملية الحجز ناجحة</div>';
//                             } else {
//                                 echo '<div id="error" class="alert alert-info" role="alert" style="background:green;">The reservation process was successful</div>';
//                             }
//                         }
//                     } else {
//                         if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                             $error[] = "تاريخ البدء مشابه لتاريخ الانتهاء";
//                         } else {
//                             $error[] = "The start date is similar to the end date";
//                         }
//                     }//
//                 } else {
//                     if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                         $error[] = "اختر طبيب";
//                     } else {
//                         $error[] = "Select Doctor";
//                     }
//                 }//
//             } else {
//                 if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                     $error[] = "اختر قسم";
//                 } else {
//                     $error[] = "Select Department";
//                 }
//             }
//         } else {
//             if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//                 $error[] = "يجب أن يكون التاريخ بعد تاريخ اليوم";
//             } else {
//                 $error[] = "The date should be after today's date";
//             }
//         }
//     } else {
//         if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//             $error[] = "اختر التاريخ";
//         } else {
//             $error[] = "select a date";
//         }
//     }
// } else {
//     if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
//         $error[] = "يجب أن يكون تسجيل الدخول";
//     } else {
//         $error[] = "should be login";
//     }
// }

// foreach ($error as $ar) {
//     if ($ar == "")
//         continue;
//     echo '<div id="error" class="alert alert-info" role="alert">' . $ar . '</div>';
// }

?>

<style>
    .alert {
        padding: 20px;
        background-color: red;
        color: white;
        margin-top: 10px;
    }
</style>
<?php
include 'connect.php';
$error = array();
$error[] = "";
session_start();
$dateAppointment = $_POST['dateAppointment'];
$department = $_POST['department'];
$doctor = $_POST['doctor'];
$message = $_POST['message'];
$sTime = $_POST['sTime'];
$eTime =  date('H:i', strtotime(' +20 minutes', strtotime($sTime)));
$currentDate = date("d/m/Y");

if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    if ($dateAppointment != NULL) {
        if ($dateAppointment >= $currentDate) {
            $dateAppointment = strtr($dateAppointment, '/', '-');
            $dateAppointment =  date('Y-m-d', strtotime($dateAppointment));
            $unixTimestamp = strtotime($dateAppointment);            
            $dayOfWeek = date("l", $unixTimestamp);
            if($dayOfWeek != "Friday" && $dayOfWeek != "Saturday"){
            if ($department != 0) {
                if ($doctor != 0) {
                    if ($sTime != $eTime) {
                        $getUserId = $con->prepare("select id from user where email = ?");
                        $getUserId->execute([$userEmail]);
                        $getUserId = $getUserId->fetch();
                        $userId = $getUserId['id'];

                        $getUserAppointment = $con->prepare("select * from appointment where user = ?");
                        $getUserAppointment->execute([$userId]);

                        $getDoctorAppointment = $con->prepare("select * from appointment where doctor = ?");
                        $getDoctorAppointment->execute([$doctor]);

                        $doctorData = $con->prepare("select * from doctor where id = ?");
                        $doctorData->execute([$doctor]);
                        $doctorData = $doctorData->fetch();

                        /* to sure the user dont have date in same time */
                        $flage = 0;
                        $sTime =  date('H:i:s', strtotime($sTime));
                        $eTime =  date('H:i:s', strtotime($eTime));
                        foreach ($getUserAppointment as $ar) {

                            if ($dateAppointment == $ar['date_booking']) {
                                if (
                                    $sTime > $ar['sTime'] && $sTime < $ar['eTime'] ||
                                    $eTime > $ar['sTime'] && $eTime < $ar['eTime'] ||
                                    $sTime < $ar['sTime'] && $eTime >= $ar['eTime'] ||
                                    $sTime <= $ar['sTime'] && $eTime > $ar['eTime'] ||
                                    $sTime == $ar['sTime'] && $eTime == $ar['eTime']
                                ) {

                                    $flage = 1;
                                }
                            }
                        }
                        /* to sure the doctor dont have date in same time */
                        $flage2 = 0;
                        foreach ($getDoctorAppointment as $ar) {
                            if ($dateAppointment == $ar['date_booking']) {
                                if (
                                    $sTime > $ar['sTime'] && $sTime < $ar['eTime'] ||
                                    $eTime > $ar['sTime'] && $eTime < $ar['eTime'] ||
                                    $sTime < $ar['sTime'] && $eTime >= $ar['eTime'] ||
                                    $sTime <= $ar['sTime'] && $eTime > $ar['eTime'] ||
                                    $sTime == $ar['sTime'] && $eTime == $ar['eTime']
                                ) {

                                    $flage2 = 1;
                                }
                            }
                        }
                        $flage3 = 0;
                        if (
                            $sTime < $doctorData['sTime']  || $sTime >= $doctorData['eTime'] ||
                            $eTime <= $doctorData['sTime']  || $eTime >= $doctorData['eTime']
                        ) {
                            $flage3 = 1;
                        }
                        $flage4 = 0;
                        $currentDate = date('Y-m-d');
                        if (
                            $dateAppointment < $currentDate
                        ) {
                            $flage4 = 1;
                        }
                        //insert
                        if ($flage == 1) {
                            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                                $error[] = 'لديك موعد في نفس التاريخ';
                            } else {
                                $error[] = 'you have an appointment in a same date';
                            }
                        } elseif ($flage4 == 1) {
                            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                                $error[] = "يجب أن يكون الموعد بعد تاريخ اليوم";
                            } else {
                                $error[] = "The appointment must be after today's date";
                            }
                        } elseif ($flage3 == 1) {
                            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                                $error[] = 'العيادة مغلقة في هذا الوقت';
                            } else {
                                $error[] = 'The clinic is closed at this time';
                            }
                        } elseif ($flage2 == 1) {
                            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                                $error[] = 'الطبيب لديه موعد في نفس التاريخ';
                            } else {
                                $error[] = 'Doctor have an appointment in a same date';
                            }
                        } else {
                            $insert = $con->prepare("insert into appointment (doctor , user , date_booking , message , sTime , eTime) values (? , ? , ? , ? , ? , ?)");
                            $insert->execute([$doctor, $userId, $dateAppointment, $message, $sTime, $eTime]);
                            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                                echo '<div id="error" class="alert alert-info" role="alert" style="background:green;">كانت عملية الحجز ناجحة</div>';
                            } else {
                                echo '<div id="error" class="alert alert-info" role="alert" style="background:green;">The reservation process was successful</div>';
                            }
                        }
                    } else {
                        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                            $error[] = "تاريخ البدء مشابه لتاريخ الانتهاء";
                        } else {
                            $error[] = "The start date is similar to the end date";
                        }
                    }//
                } else {
                    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                        $error[] = "اختر طبيب";
                    } else {
                        $error[] = "Select Doctor";
                    }
                }//
            } else {
                if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                    $error[] = "اختر قسم";
                } else {
                    $error[] = "Select Department";
                }
            }
        }else{
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                $error[] = "العيادة مغلقة في هذا اليوم";
            } else {
                $error[] = "The clinic is closed on this day";
            }
        }
        } else {
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                $error[] = "يجب أن يكون التاريخ بعد تاريخ اليوم";
            } else {
                $error[] = "The date should be after today's date";
            }
        }
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            $error[] = "اختر التاريخ";
        } else {
            $error[] = "select a date";
        }
    }
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        $error[] = "يجب أن يكون تسجيل الدخول";
    } else {
        $error[] = "should be login";
    }
}

foreach ($error as $ar) {
    if ($ar == "")
        continue;
    echo '<div id="error" class="alert alert-info" role="alert">' . $ar . '</div>';
}

?>