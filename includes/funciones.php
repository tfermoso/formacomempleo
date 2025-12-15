<?php
// includes/funciones.php
if (session_status() === PHP_SESSION_NONE) session_start();

function limpiar($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, "UTF-8");
}

function redir($url) {
    header("Location: $url");
    exit;
}

// Simple CSRF
function csrf_token() {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function csrf_check($token) {
    return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}

function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validar_password($pw) {
    return strlen($pw) >= 6; // regla mínima, cámbiala si quieres
}
