<?php
session_start();

if (isset($_GET['msg'])) {
    $_SESSION['fastmoney_last'] = $_GET['msg'];
    exit;
}

echo $_SESSION['fastmoney_last'] ?? "";
