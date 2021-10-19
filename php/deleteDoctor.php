<?php 
    include 'connect.php';
    $idDoctor = $_POST['idDoctor'];
    $stmt = $con->prepare("delete from doctor where id = ?");
    $stmt->execute([$idDoctor]);
?>