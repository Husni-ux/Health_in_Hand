<?php 
    include 'connect.php';
    $idUser = $_POST['idUser'];
    $stmt = $con->prepare("delete from user where id = ?");
    $stmt->execute([$idUser]);
?>