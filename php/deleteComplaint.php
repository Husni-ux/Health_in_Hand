<?php
    include 'connect.php';

    $stmt = $con  ->prepare("delete from complaints where id = ?");
    $stmt->execute([$_POST['idComplaint']]);
?>