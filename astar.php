<!doctype html>
<html lang="ru">
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="astar.css">
        
    </head>
    <body>
        <div class="big">Нахождение пути с помощью алгоритма A*</div>
        <div class="small">Для начала работы загрузите файл содержащий двумерный массив с лабиринтом. Столбцы в нем должны быть разделены единичными пробелами, а строки единичными переносами строк. 
        Затем, кликами мыши по соответствующим ячейкам выберите точки, между которыми должен будет проложен путь.</div><br><br>
        
        <form class='hidden' action="upload.php" method="post" enctype="multipart/form-data">
          <input type="file" name="fileToUpload" id="fileToUpload" onchange="document.getElementById('fileform').click();">
          <input type="submit" value="Upload Image" name="submit" id="fileform">
        </form>
        
        <label for="fileToUpload" class="button">Загрузить файл</label><br><br><br>
        <script type="text/javascript" src="astar.js"></script>
        <?php include 'astar_code.php';?>
    </body>
</html>