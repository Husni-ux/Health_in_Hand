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
$message = $_POST['message'];
$sTime = $_POST['sTime'];
$eTime =  date('H:i', strtotime(' +20 minutes', strtotime($sTime)));
$currentDate = date("d/m/Y");
$doctorEmail = $_POST['doctorEmail'];


if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    if ($dateAppointment != NULL) {
        if ($dateAppointment >= $currentDate) {
            $dateAppointment = strtr($dateAppointment, '/', '-');
            $dateAppointment =  date('Y-m-d', strtotime($dateAppointment));
            $unixTimestamp = strtotime($dateAppointment);            
            $dayOfWeek = date("l", $unixTimestamp);
            if($dayOfWeek != "Friday" && $dayOfWeek != "Saturday"){

            if ($sTime != $eTime) {
                $getUserId = $con->prepare("select id from user where email = ?");
                $getUserId->execute([$userEmail]);
                $getUserId = $getUserId->fetch();
                $userId = $getUserId['id'];

                $getUserAppointment = $con->prepare("select * from appointment where user = ?");
                $getUserAppointment->execute([$userId]);

                $doctorData = $con->prepare("select * from doctor where email = ?");
                $doctorData->execute([$doctorEmail]);
                $doctorData = $doctorData->fetch();
                $doctor = $doctorData['id'];

                $getDoctorAppointment = $con->prepare("select * from appointment where doctor = ?");
                $getDoctorAppointment->execute([$doctor]);

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
                $currentDate = date('Y-m-d');
                $flage4 = 0;
                if (
                    $dateAppointment < $currentDate
                ) {
                    $flage4 = 1;
                }
                //insert
                if ($flage == 1) {
                    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                        $error[] = '???????? ???????? ???? ?????? ??????????????';
                    } else {
                        $error[] = 'you have an appointment in a same date';
                    }
                } elseif ($flage4 == 1) {
                    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                        $error[] = "?????? ???? ???????? ???????????? ?????? ?????????? ??????????";
                    } else {
                        $error[] = "The appointment must be after today's date";
                    }
                } elseif ($flage3 == 1) {
                    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                        $error[] = '?????????????? ?????????? ???? ?????? ??????????';
                    } else {
                        $error[] = 'The clinic is closed at this time';
                    }
                } elseif ($flage2 == 1) {
                    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                        $error[] = '???????????? ???????? ???????? ???? ?????? ??????????????';
                    } else {
                        $error[] = 'Doctor have an appointment in a same date';
                    }
                } else {
                    $insert = $con->prepare("insert into appointment (doctor , user , date_booking , message , sTime , eTime) values (? , ? , ? , ? , ? , ?)");
                    $insert->execute([$doctor, $userId, $dateAppointment, $message, $sTime, $eTime]);
                    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                        echo '<div id="error" class="alert alert-info" role="alert" style="background:green;">???????? ?????????? ?????????? ??????????</div>';
                    } else {
                        echo '<div id="error" class="alert alert-info" role="alert" style="background:green;">The reservation process was successful</div>';
                    }
                }
            } else {
                if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                    $error[] = "?????????? ?????????? ?????????? ???????????? ????????????????";
                } else {
                    $error[] = "The start date is similar to the end date";
                }
            }
        }else{
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                $error[] = "?????????????? ?????????? ???? ?????? ??????????";
            } else {
                $error[] = "The clinic is closed on this day";
            }
        }
        } else {
            if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
                $error[] = "?????? ???? ???????? ?????????????? ?????? ?????????? ??????????";
            } else {
                $error[] = "The date should be after today's date";
            }
        }
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            $error[] = "?????? ??????????????";
        } else {
            $error[] = "select a date";
        }
    }
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        $error[] = "?????? ???? ???????? ?????????? ????????????";
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