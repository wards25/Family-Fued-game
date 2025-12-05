<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
</head>

<body>
    <form action="upload_process.php" method="post" enctype="multipart/form-data">
        <input type="file" name="questions_text" id="questions_text" placeholder="Enter questions text" accept=".csv" required>
        <br>
        <br>
        <button type="submit">Upload</button>
    </form>
</body>

</html>