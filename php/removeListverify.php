<?php 
    include 'connect.php';
    $stmt = $con->prepare('update doctor set hide = 1 where id = ?');
    $stmt->execute([$_POST['idDoctor']]);
?>