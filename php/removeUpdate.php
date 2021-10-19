<?php 
    include 'connect.php';
    $stmt =$con->prepare("delete from  article  where id = ?");
    $stmt->execute([$_POST['idArticleUpdate']]);
?>