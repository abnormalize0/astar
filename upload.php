<?php
session_start();
unset($_SESSION["file"]);
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
} else {
    echo "Sorry, there was an error uploading your file.";
}
setcookie("state", "0", time() + 3600);
setcookie("file", $_FILES["fileToUpload"]["name"], time() + 3600);

$url = 'astar.php'; 
while (ob_get_status()) 
{
    ob_end_clean();
}
header( "Location: $url" );

?>