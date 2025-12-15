<?php
if (!isset($titulo)) { $titulo = "Formac Empleo"; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= $titulo ?></title>
<link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<header class="main-header">
    <h1>Formac Empleo</h1>
</header>

</body>
<style>
.main-header {
    background: linear-gradient(90deg,
        #17C964,   /* verde */
        #22A699,   /* turquesa */
        #357ABD,   /* azul */
        #5A4FCF    /* violeta */
    );
    color: white;
    padding: 20px;
    text-align: center;
}


.center-buttons {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  margin-top: 50px;
}

.center-buttons a {
  background-color: #007BFF; /* Azul Bootstrap */
  color: white;
  padding: 10px 20px;
  text-decoration: none;
  border-radius: 5px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.center-buttons a:hover {
  background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
}



/* Estilo del formulario */
.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

/* Inputs */
.card input[type="email"],
.card input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0 20px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* Botón */
.btn {
    background-color: #004aad;
    color: white;
    padding: 12px;
    width: 100%;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
}

.btn:hover {
    background-color: #00357e;
}

/* Enlace de registro */
.card p a {
    color: #004aad;
    text-decoration: none;
}

.card p a:hover {
    text-decoration: underline;
}


body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:url(../foto/El-empleo-en-personas-con-discapacidad-auditiva.jpg)no-repeat center center fixed;
            background-size: cover;
            opacity: 0.3;
            /* Ajusta la transparencia aquí (0.0 a 1.0) */
            z-index: -1;
        }




</style>

<main class="container">
