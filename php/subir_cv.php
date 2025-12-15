<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
session_start();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {
    $file = $_FILES['cv'];
    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf', 'doc', 'docx'];
        if (!in_array(strtolower($ext), $allowed)) die("Formato no permitido.");

        $newName = uniqid() . "." . $ext;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/../assets/img/cvs/' . $newName);

        $stmt = $pdo->prepare("UPDATE candidatos SET cv=? WHERE id=?");
        $stmt->execute([$newName, $_SESSION['usuario']['id']]);

        echo "CV subido correctamente.";
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "Archivo no recibido.";
}
