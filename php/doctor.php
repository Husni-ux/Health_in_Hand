<?php
include 'connect.php';
session_start();

if(isset($_POST['id'])){
    $id= $_POST['id'];
    $stmt = $con->prepare("SELECT * FROM doctor where deptno = ?");
    $stmt->execute([$id]);
    $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($arr);
}

if(isset($_POST['userEmail'])){
    $email= $_POST['userEmail'];
    $stmt = $con->prepare("SELECT * FROM user where email = ?");
    $stmt->execute([$email]);
    $stmt = $stmt->fetch();
    $id = $stmt['id'];
    $current_date = Date("Y-m-d");
    // $stmt = $con->prepare("SELECT *  FROM appointment where user = ?");
    // $stmt->execute([$id]);
    $stmt = $con->prepare("select appointment.id , appointment.date_booking , appointment.sTime , appointment.eTime , 
    appointment.message , doctor.first_name ,  doctor.last_name from appointment LEFT JOIN doctor on 
    doctor.id = appointment.doctor where user = ? and date_booking > ?");
    $stmt->execute([$id , $current_date]);
    $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($arr);
}

if(isset($_POST['appointmentId'])){
    $id= $_POST['appointmentId'];
    $stmt = $con->prepare("delete from appointment where id = ?");
    $stmt->execute([$id]);
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
        echo '<div id="error" class="alert alert-success" role="alert">تم الحذف بنجاح</div>';
    } else {
        echo '<div id="error" class="alert alert-success" role="alert">The deletion was successful</div>';
    }
    //echo $id;
}

if(isset($_POST['appointmentIdToDelete'])){
    $id= $_POST['appointmentIdToDelete'];
    $stmt = $con->prepare("select *  from appointment where id = ?");
    $stmt->execute([$id]);
    $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($arr);
}

if(isset($_POST['doctorId'])){
    $id= $_POST['doctorId'];
    $stmt = $con->prepare("select *  from doctor where id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    echo $row['first_name'] . ' ' . $row['last_name'] ;
    //echo 'saeb';
}

if(isset($_POST['doctorEmail'])){
    $email= $_POST['doctorEmail'];
    $stmt = $con->prepare("select *  from doctor where email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    $current_date = Date('Y-m-d');
    $stmt = $con->prepare("select *  from appointment where doctor = ? and date_booking > ?");
    $stmt->execute([$row['id'] , $current_date]);
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
    //echo $_POST['doctorEmail'];
}

?>
