<!doctype html>
<html lang="ru">
    <body>
        <!--<form id ="inputfile" action="astar.php" method="post" enctype="multipart/form-data">-->
        <!--  Select image to upload:-->
        <!--  <input type="file" name="fileToUpload" id="fileToUpload">-->
        <!--  <input type="submit" value="Upload Image" name="submit">-->
        <!--</form>-->
        <input type="file" onchange="readFile(this)">
        <script type="text/javascript" src="astar.js"></script>
        <?php include 'astar_code.php';?>
    </body>
    <style>
   body{
    background-color: Grey;
   } 
   </style>
</html>