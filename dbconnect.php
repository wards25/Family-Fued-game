<?php
$database = 'family_feud';
date_default_timezone_set("Asia/Manila");

    $conn = mysqli_connect('localhost', 'root', '', $database);
    if (!$conn){
        die("Database Connection Failed" . mysqli_error($conn));
    }
    // select database
    $select_db = mysqli_select_db($conn, $database);
    if (!$select_db){
        header("Refresh:0; url=index.php");
        die("Database Selection Failed" . mysqli_error($conn));
    }
?>