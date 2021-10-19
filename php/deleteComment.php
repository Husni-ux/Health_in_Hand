 <?php
    include 'connect.php';
    session_start();
    $idComment = $_POST['idComment'];
    if($_SESSION['person'] == 'doctor'){
        $stmt = $con->prepare('update doctor set num_comment = num_comment - 1 where email = ?');
        $stmt->execute([$_SESSION['email']]);    
    }
    $stmt = $con->prepare('DELETE FROM comment WHERE id = ?;');
    $stmt->execute([$idComment]);
    
    
?>