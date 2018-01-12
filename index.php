<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Test</title>
    </head>
    <body>
        <form name="upload" action="upload.php" method="POST" enctype="multipart/form-data">
                Select image to upload: <input type="file" name="image[]" multiple>
                <input type="submit" name="upload" value="upload">
        </form>
    </body>
</html>
