<?php
include 'connect.php';

if(isset($_POST['id'])){
    $id= $_POST['id'];
    $current_date = Date('Y-m-d');
    $stmt = $con->prepare("SELECT * FROM appointment where doctor = ? and date_booking > ?");
    $stmt->execute([$id , $current_date]);
    $stmt2 = $con->prepare("SELECT * FROM doctor where id = ? ");
    $stmt2->execute([$id]);
    $arr2 = $stmt2->fetch();
    $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sTime = $arr2['sTime'];
    $eTime = $arr2['eTime'];
    $arr[0]['sTimeDoctor'] = $sTime;
    $arr[0]['eTimeDoctor'] = $eTime;
    echo json_encode($arr);
}

?>
