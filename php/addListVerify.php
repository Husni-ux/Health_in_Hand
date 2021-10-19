<?php 
    include 'connect.php';
    $stmt = $con->prepare('update doctor set verify_license	 = 1 where id = ?');
    $stmt->execute([$_POST['idDoctor']]);
?>