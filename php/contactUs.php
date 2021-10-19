<?php

include 'connect.php';
session_start();
$email =  $_POST['email'];
$message =  strip_tags($_POST['message']);
$currentDate = date("Y-m-d");
if ($message != null) {
    if (isset($_POST['name'])) {
        $name =  $_POST['name'];
    } else {
        $person = $_POST['person'];
        $stmt = $con->prepare("select * from $person where email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        $name = $row['first_name'] . ' ' . $row['last_name'];
    }
    $stmt = $con->prepare("insert into contactUs (email , message , name ,date_contact) values (? , ? , ? , ?)");
    if ($stmt->execute([$email, $message, $name, $currentDate])) {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo '<div class="alert alert-success" role="alert">
            تمت بنجاح
        </div>';
        } else {
            echo '<div class="alert alert-success" role="alert">
                    success
                </div>';
        }
    } else {
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
            echo '<div class="alert alert-danger" role="alert">
                    يوجد خطأ
                </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
                    error
                </div>';
        }
    }
} else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo '<div class="alert alert-danger" role="alert">
                الرساله فارغه
            </div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">
                message is empty
            </div>';
    }
}
