<?php
session_start();
$_SESSION = []; // vaciar array de sesión
session_destroy();
header("Location: login.php");
exit;
