<?php
// Generar y comprobar token CSRF
session_start();

function generarTokenCSRF(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function comprobarTokenCSRF(string $tokenFormulario): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $tokenFormulario);
}



//Sanitización básica

function limpiarTexto(string $valor): string
{
    $valor = trim($valor);
    $valor = stripslashes($valor);
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}



//Validación completa de CIF (lado servidor)

function validarCIF(string $cif): bool
{
    $cif = strtoupper(trim($cif));

    // Patrón básico CIF/NIF empresa
    if (!preg_match('/^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/', $cif)) {
        return false;
    }

    $letraInicial = $cif[0];
    $numeros = substr($cif, 1, 7);
    $control = $cif[8];

    $sumaPares = 0;
    $sumaImpares = 0;

    for ($i = 0; $i < 7; $i++) {
        $digito = (int)$numeros[$i];
        if (($i + 1) % 2 === 0) {
            // posición par (2,4,6)
            $sumaPares += $digito;
        } else {
            // posición impar (1,3,5,7)
            $doble = $digito * 2;
            $sumaImpares += (int)floor($doble / 10) + ($doble % 10);
        }
    }

    $sumaTotal = $sumaPares + $sumaImpares;
    $unidad = $sumaTotal % 10;
    $digitoControl = ($unidad === 0) ? 0 : 10 - $unidad;

    $controlNumerico = (string)$digitoControl;
    $controlLetra = 'JABCDEFGHI'[$digitoControl];

    // Tipos de entidades según letra inicial
    if (in_array($letraInicial, ['A', 'B', 'E', 'H'])) {
        // Debe ser numérico
        return $control === $controlNumerico;
    } elseif (in_array($letraInicial, ['K', 'P', 'Q', 'S', 'N', 'W', 'R'])) {
        // Debe ser letra
        return $control === $controlLetra;
    } else {
        // Puede ser o número o letra
        return $control === $controlNumerico || $control === $controlLetra;
    }
}
