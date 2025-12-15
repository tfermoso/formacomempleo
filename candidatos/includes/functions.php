<?php

function getCandidato(mysqli $conn, int $id) {
    $stmt = $conn->prepare("
        SELECT nombre, apellidos 
        FROM candidatos 
        WHERE id = ? AND deleted_at IS NULL
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getOfertasInscritas(mysqli $conn, int $idcandidato) {
    $sql = "
        SELECT 
            o.id,
            o.titulo,
            oc.estado AS estado_inscripcion,
            oc.fecha_inscripcion,
            o.descripcion,
            o.requisitos,
            o.funciones,
            o.salario_min,
            o.salario_max,
            o.tipo_contrato,
            o.jornada,
            o.ubicacion,
            o.fecha_incorporacion,
            o.fecha_publicacion,
            e.nombre AS empresa
        FROM ofertas o
        INNER JOIN ofertas_candidatos oc ON o.id = oc.idoferta
        INNER JOIN empresas e ON o.idempresa = e.id
        WHERE oc.idcandidato = ?
          AND o.deleted_at IS NULL
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idcandidato);
    $stmt->execute();
    return $stmt->get_result();
}

function getOfertasNoInscritas(mysqli $conn, int $idcandidato) {
    $sql = "
        SELECT 
            o.id,
            o.titulo,
            o.descripcion,
            o.requisitos,
            o.funciones,
            o.salario_min,
            o.salario_max,
            o.tipo_contrato,
            o.jornada,
            o.ubicacion,
            o.fecha_incorporacion,
            o.fecha_publicacion,
            e.nombre AS empresa
        FROM ofertas o
        INNER JOIN empresas e ON o.idempresa = e.id
        WHERE o.deleted_at IS NULL
          AND o.estado = 'abierta'
          AND o.id NOT IN (
                SELECT idoferta 
                FROM ofertas_candidatos 
                WHERE idcandidato = ?
          )
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idcandidato);
    $stmt->execute();
    return $stmt->get_result();
}

function eliminarInscripcion(mysqli $conn, int $idoferta, int $idcandidato): bool {
    $stmt = $conn->prepare("
        DELETE FROM ofertas_candidatos 
        WHERE idoferta = ? AND idcandidato = ?
    ");
    $stmt->bind_param("ii", $idoferta, $idcandidato);
    return $stmt->execute();
}

function inscribirseEnOferta(mysqli $conn, int $idoferta, int $idcandidato): bool {
    $stmt = $conn->prepare("
        INSERT INTO ofertas_candidatos (idoferta, idcandidato, fecha_inscripcion, estado)
        VALUES (?, ?, NOW(), 'pendiente')
    ");
    $stmt->bind_param("ii", $idoferta, $idcandidato);
    return $stmt->execute();
}

?>
<?php

function getCandidatoCompleto($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM candidatos WHERE id = ? AND deleted_at IS NULL");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function actualizarCandidato(mysqli $conn, int $id, array $data) {
    $sql = "
        UPDATE candidatos SET
            nombre = ?,
            apellidos = ?,
            telefono = ?,
            email = ?,
            linkedin = ?,
            web = ?,
            cp = ?,
            ciudad = ?,
            provincia = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssissi",
        $data['nombre'],
        $data['apellidos'],
        $data['telefono'],
        $data['email'],
        $data['linkedin'],
        $data['web'],
        $data['cp'],
        $data['ciudad'],
        $data['provincia'],
        $id
    );

    return $stmt->execute();
}

function subirArchivo($file, $destino, $permitidos) {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $permitidos)) return false;

    $nombreFinal = uniqid() . "." . $ext;
    $ruta = $destino . $nombreFinal;

    if (move_uploaded_file($file['tmp_name'], $ruta)) {
        return $nombreFinal;
    }

    return false;
}

function conectarBD() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $conexion->set_charset("utf8mb4");
    return $conexion;
}

?>
