<?php
session_start();
require_once "../conexion.php"; // archivo con $pdo

// Verificar que el admin esté logueado
if (!isset($_SESSION['admin_login'])) {
    header('Location: login.php');
    exit;
}

// Función para manejar la activación de pestañas
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'empresas';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="includes/styles.css">
    
</head>
<body>

<h1>Dashboard Administrador</h1>

<nav>
    <a href="?tab=empresas" class="<?= $activeTab === 'empresas' ? 'active' : '' ?>">Empresas</a>
    <a href="?tab=ofertas" class="<?= $activeTab === 'ofertas' ? 'active' : '' ?>">Ofertas</a>
    <a href="?tab=candidatos" class="<?= $activeTab === 'candidatos' ? 'active' : '' ?>">Candidatos</a>
    <a href="?tab=usuarios" class="<?= $activeTab === 'usuarios' ? 'active' : '' ?>">Usuarios</a>
    <a href="logout.php">Cerrar Sesión</a>
</nav>

<?php
// ===================== Empresas =====================
if ($activeTab === 'empresas') {
    echo '<h2>Empresas</h2>';
    echo '<a href="includes/empresas/crear_empresa.php" class="actions a create">➕ Crear Empresa</a><br><br>';

    $stmt = $pdo->query("SELECT * FROM empresas");
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($empresas) {
        echo '<table><tr>
            <th>ID</th><th>CIF</th><th>Nombre</th><th>Teléfono</th><th>Email Contacto</th><th>Acciones</th>
        </tr>';
        foreach ($empresas as $e) {
            echo '<tr>
                <td>'.$e['id'].'</td>
                <td>'.$e['cif'].'</td>
                <td>'.$e['nombre'].'</td>
                <td>'.$e['telefono'].'</td>
                <td>'.$e['email_contacto'].'</td>
                <td class="actions">
                    <a href="includes/empresas/editar_empresa.php?id='.$e['id'].'" class="edit">✏ Editar</a>
                    <a href="includes/empresas/borrar_empresa.php?id='.$e['id'].'" class="delete" onclick="return confirm(\'¿Seguro que quieres eliminar esta empresa?\')">🗑 Borrar</a>
                </td>
            </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No hay empresas registradas.</p>';
    }
}

// ===================== Ofertas =====================
if ($activeTab === 'ofertas') {
    echo '<h2>Ofertas</h2>';
    echo '<a href="includes/ofertas/crear_oferta.php" class="actions a create">➕ Crear Oferta</a><br><br>';

    $stmt = $pdo->query("SELECT ofertas.*, empresas.nombre AS empresa_nombre, sectores.nombre AS sector_nombre, modalidad.nombre AS modalidad_nombre
                         FROM ofertas
                         JOIN empresas ON ofertas.idempresa = empresas.id
                         JOIN sectores ON ofertas.idsector = sectores.id
                         JOIN modalidad ON ofertas.idmodalidad = modalidad.id");
    $ofertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($ofertas) {
        echo '<table><tr>
            <th>ID</th><th>Título</th><th>Empresa</th><th>Sector</th><th>Modalidad</th><th>Acciones</th>
        </tr>';
        foreach ($ofertas as $o) {
            echo '<tr>
                <td>'.$o['id'].'</td>
                <td>'.$o['titulo'].'</td>
                <td>'.$o['empresa_nombre'].'</td>
                <td>'.$o['sector_nombre'].'</td>
                <td>'.$o['modalidad_nombre'].'</td>
                <td class="actions">
                    <a href="includes/ofertas/editar_oferta.php?id='.$o['id'].'" class="edit">✏ Editar</a>
                    <a href="includes/ofertas/borrar_oferta.php?id='.$o['id'].'" class="delete" onclick="return confirm(\'¿Seguro que quieres eliminar esta oferta?\')">🗑 Borrar</a>
                </td>
            </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No hay ofertas registradas.</p>';
    }
}

// ===================== Candidatos =====================
if ($activeTab === 'candidatos') {
    echo '<h2>Candidatos</h2>';
    echo '<a href="includes/candidatos/crear_candidato.php" class="actions a create">➕ Crear Candidato</a><br><br>';

    $stmt = $pdo->query("SELECT * FROM candidatos");
    $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($candidatos) {
        echo '<table><tr>
            <th>ID</th><th>Nombre</th><th>Apellidos</th><th>Email</th><th>Teléfono</th><th>Acciones</th>
        </tr>';
        foreach ($candidatos as $c) {
            echo '<tr>
                <td>'.$c['id'].'</td>
                <td>'.$c['nombre'].'</td>
                <td>'.$c['apellidos'].'</td>
                <td>'.$c['email'].'</td>
                <td>'.$c['telefono'].'</td>
                <td class="actions">
                    <a href="includes/candidatos/editar_candidato.php?id='.$c['id'].'" class="edit">✏ Editar</a>
                    <a href="includes/candidatos/borrar_candidato.php?id='.$c['id'].'" class="delete" onclick="return confirm(\'¿Seguro que quieres eliminar este candidato?\')">🗑 Borrar</a>
                </td>
            </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No hay candidatos registrados.</p>';
    }
}

// ===================== Usuarios =====================
if ($activeTab === 'usuarios') {
    echo '<h2>Usuarios</h2>';
    echo '<a href="includes/usuarios/crear_usuario.php" class="actions a create">➕ Crear Usuario</a><br><br>';

    $stmt = $pdo->query("SELECT usuarios.*, empresas.nombre AS empresa_nombre FROM usuarios LEFT JOIN empresas ON usuarios.idempresa = empresas.id");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($usuarios) {
        echo '<table><tr>
            <th>ID</th><th>Nombre</th><th>Apellidos</th><th>Email</th><th>Empresa</th><th>Admin</th><th>Acciones</th>
        </tr>';
        foreach ($usuarios as $u) {
            echo '<tr>
                <td>'.$u['id'].'</td>
                <td>'.$u['nombre'].'</td>
                <td>'.$u['apellidos'].'</td>
                <td>'.$u['email'].'</td>
                <td>'.$u['empresa_nombre'].'</td>
                <td>'.($u['is_admin'] ? 'Sí' : 'No').'</td>
                <td class="actions">
                    <a href="includes/usuarios/editar_usuario.php?id='.$u['id'].'" class="edit">✏ Editar</a>
                    <a href="includes/usuarios/borrar_usuario.php?id='.$u['id'].'" class="delete" onclick="return confirm(\'¿Seguro que quieres eliminar este usuario?\')">🗑 Borrar</a>
                </td>
            </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No hay usuarios registrados.</p>';
    }
}
?>

</body>
</html>
