<?php
if(empty($_FILES['file']))
{
    exit();
}
$filename = $_FILES["file"]["name"]; 
$tempname = $_FILES["file"]["tmp_name"];	
$folder = "img/post/".$filename;  
move_uploaded_file($tempname,'../'. $folder);
echo $folder;

?>