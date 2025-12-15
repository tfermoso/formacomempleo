<?php
session_start();

function requireLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../login.php");
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['usuario']['is_admin']) && $_SESSION['usuario']['is_admin'] == 1;
}
