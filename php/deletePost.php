<?php
include 'connect.php';


if (isset($_POST['idPostArticle'])) {
    $idPost =  $_POST['idPostArticle'];
    $stmt = $con->prepare('DELETE FROM article WHERE id = ?;');
    $stmt->execute([$idPost]);
} else {
    $idPost = $_POST['idPost'];
    $imgPost = $con->prepare('select img from post WHERE id = ?;');
    $imgPost->execute([$idPost]);
    $imgPost = $imgPost->fetch();

    $allImage = $con->prepare('select * from post WHERE img = ?;');
    $allImage->execute([$imgPost['img']]);
    if ($allImage->rowCount() <= 1) {
        if ($imgPost['img'] != null || $imgPost['img'] != '')
            unlink('../' . $imgPost['img']);
    }
    $stmt = $con->prepare('DELETE FROM post WHERE id = ?;');
    $stmt->execute([$idPost]);
}
