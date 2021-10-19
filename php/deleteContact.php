<?php
    include 'connect.php';
    $stmt = $con->prepare("delete from contactUs where id = ?");
    $stmt->execute([$_POST['idContact']]);
?>