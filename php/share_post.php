<?php
session_start();
include 'connect.php';

$stmt = $con->prepare("SELECT * FROM user where email = ?");
$stmt->execute([$_SESSION['email']]);
$row = $stmt->fetch();
$user = $row['id'];

if ($_POST['content'] != null) {
  if ($_POST['department'] != 0) {
    if ($_POST['image'] == null) {
      $img = null;
    } else {
      $img = $_POST['image'];
    }

    $content = $_POST['content'];
    $content =  htmlspecialchars_decode($content);
    $content = strip_tags($content);
    $deptno = $_POST['department'];
    $date =  date("Y-m-d");
    $insert = $con->prepare("insert into post (user , content, img , deptno,date_share) value(? , ? , ? , ? ,?)");
    $insert->execute([$user, $content, $img, $deptno, $date]);
    $stmt = $con->prepare("select * from post where user = ? and content = ?");
    $stmt->execute([$user, $content]);
    $postInfo = $stmt->fetch();
    $content = str_replace("&nbsp;", " ", $content);
    $update = $con->prepare("update post set content = ? where id = ?");
    $update->execute([$content, $postInfo['id']]);

?>
    <script>
       location.href =("postPage.php?post=<?php echo $postInfo['id'] ?>")
    </script>
<?php
  } else {
    if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
      echo
      '<div class="alert alert-danger" role="alert">
          حدد القسم
        </div>';
    } else {
      echo
      '<div class="alert alert-danger" role="alert">
        Select Department
      </div>';
    }
  }
} else {
  if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'ar') {
    echo
    '<div class="alert alert-danger" role="alert">
          اكتب سؤالك
           </div>';
  } else {
    echo
    '<div class="alert alert-danger" role="alert">
      Write Your Question
     </div>';
  }
}

?>