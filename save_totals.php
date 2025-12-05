<?php
session_start();
if (isset($_POST['total1']) && isset($_POST['total2'])) {
    $_SESSION['totals']['1'] = (int)$_POST['total1'];
    $_SESSION['totals']['2'] = (int)$_POST['total2'];
}
?>