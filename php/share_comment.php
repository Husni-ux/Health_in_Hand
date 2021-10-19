<?php
include 'connect.php';

$Iam =  $_POST['Iam'];
$id =  $_POST['id'];
$post =  $_POST['post'];
$content =  strip_tags($_POST['content']);
$current_date = date("Y-m-d h:i:sa");
$commentId =  $_POST['commentId'];

if (isset($_POST['flage'])) {
    $flage =  $_POST['flage']; // i
}
if (isset($_POST['dept'])) {
    $dept = $_POST['dept']; 
}

if (isset($_POST['flageIJ'])) {
    $flage3 = $_POST['flageIJ']; // i j
}

if ($content != null) {
    $content = str_replace("&nbsp;", " ", $content);
    if ($commentId == null) {
        if ($Iam == 'user') {
            $insert = $con->prepare('INSERT INTO comment (user , post , content , date_share) VALUES (?,?,?,?);');
        } else {
            $insert = $con->prepare('INSERT INTO comment (doctor , post , content , date_share) VALUES (?,?,?,?);');
            $update = $con->prepare('update doctor set num_comment  = num_comment + 1 where id = ?');
            $update->execute([$id]);
        }
        $insert->execute([$id, $post, $content, $current_date]);

    } else {
        $update = $con->prepare('update comment set content  = ? , date_share = ?  where id = ?');
        $update->execute([$content, $current_date,$commentId]);

    }
}

       $stmt = $con->prepare('select comment.content , comment.date_share , doctor.verify_license
     , CASE WHEN doctor.first_name IS not NULL THEN doctor.email ELSE user.email END as email
     , CASE WHEN doctor.first_name IS not NULL THEN "doctor" ELSE "user"  END as his_comment ,
      CASE WHEN doctor.first_name IS not NULL THEN doctor.first_name ELSE user.first_name END as first_name,
       CASE WHEN doctor.last_name IS not NULL THEN doctor.last_name ELSE user.last_name END as last_name ,
       CASE WHEN doctor.img IS not NULL THEN doctor.img ELSE user.img END as img , comment.id
       FROM comment left join user on user.id = comment.user
       left join doctor on doctor.id = comment.doctor where post = ? and content =? and date_share = ? order by date_share asc;');
     $stmt->execute([$post , $content , $current_date]);
     $arr  = $stmt->fetchAll(PDO::FETCH_ASSOC);

     if(isset($_POST['flage'])){
        $arr[0]['flage'] = $flage;
     }
     if(isset($_POST['flageIJ'])){
        $arr[0]['ij'] = $flage3;
     }
    //  if(isset($_POST['flage2'])){
    //     $arr[0]['j'] = $flage2;
    //  }
     if(isset($_POST['dept'])){
        $arr[0]['dept'] = $dept;
     }

    if($content != null)
        echo json_encode($arr);
    else echo 'saeb';


