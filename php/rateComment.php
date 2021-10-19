<?php
    include 'connect.php';
    session_start();
    $ar = array();

    $idComment = $_POST["commentId"];
    $personEmail = $_SESSION['email'];
    $me = $_SESSION['person'];
    $moveMent = $_POST["moveMent"];

    $stmt = $con->prepare("select id from $me where email = ?");
    $stmt->execute([$personEmail]);
    $row = $stmt->fetch();

    $ratingInfio = $con->prepare("select *  from rating_info where $me = ? and comment = ?");
    $ratingInfio->execute([$row['id'] , $idComment]);

    if($ratingInfio->rowCount() > 0){
        $stmt = $con->prepare("update  rating_info set typeMovement = ? where comment = ? and $me = ?");
        $stmt->execute([$_POST['moveMent'] , $_POST['commentId'] , $row['id']]);
    }else{
        $stmt = $con->prepare("insert into rating_info ($me , comment , typeMovement) values (? , ? , ?)");
        $stmt->execute([$row['id'] , $_POST['commentId'] , $_POST['moveMent']]);
    }

    $ratingInfio = $con->prepare("select count(id) as countRate from rating_info where comment = ? and typeMovement = ?");
    $ratingInfio->execute([$idComment , "up"]);
    $ratingInfioUp = $ratingInfio->fetch();

    $ratingInfio = $con->prepare("select count(id) as countRate from rating_info where comment = ? and typeMovement = ?");
    $ratingInfio->execute([$idComment , "down"]);
    $ratingInfioDown = $ratingInfio->fetch();

    $ar[] = $idComment;
    $ar[] = $moveMent;
    $ar[] = $ratingInfioUp['countRate'];
    $ar[] = $ratingInfioDown['countRate'];

    echo json_encode($ar);

    ?>