<?php 
    include 'connect.php';
    $stmt =$con->prepare("update article set hide = 1 where id = ?");
    $stmt->execute([$_POST['idArticleUpdate']]);
?>