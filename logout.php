<?php
session_start();
$_SESSION = [];       // hapus semua data session di memori
session_destroy();    // hancurkan session di server
header('location: login.php');
exit;
?>
