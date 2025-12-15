<?php
session_start();
unset($_SESSION['admin_login']);
unset($_SESSION['admin_nombre']);
header('Location: index.php');
exit;
?>
