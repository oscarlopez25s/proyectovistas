<?php
header("Content-Type: application/json; charset=utf-8");
require_once "conexion.php";
try {
    // Recibir los datos enviados desde Registro.html
    $datos = json_decode(file_get_contents("php://input"), true);
    $usuario = trim($datos["usuario"] ?? "");
    $password = $datos["password"] ?? "";
    $apoyos = $datos["apoyos"] ?? [];
    // Validar los campos
    if ($usuario === "" || $password === "") {
        throw new Exception("Completa todos los campos.");
    }
    if (!is_array($apoyos)) {
        throw new Exception("Los apoyos seleccionados no son válidos.");
    }
    // Validar que no exista el usuario
    $consulta = $conexion->prepare(
        "SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?"
    );
    $consulta->execute([$usuario]);
    if ($consulta->fetch()) {
        throw new Exception("Este nombre de usuario ya está registrado.");
    }
    // Comprobar los identificadores de apoyo
    $apoyos = array_unique(array_map("intval", $apoyos));
    $permitidos = [1, 2, 3, 4, 5, 6, 7];
    foreach ($apoyos as $id) {
        if (!in_array($id, $permitidos, true)) {
            throw new Exception("Se seleccionó un apoyo no válido.");
        }
    }
    // Si selecciona Ninguna, guardar solamente esa opción
    if (in_array(7, $apoyos, true)) {
        $apoyos = [7];
    }
    // Proteger la contraseña
    $contrasenaHash = password_hash($password, PASSWORD_DEFAULT);
    // Iniciar transacción
    $conexion->beginTransaction();
    // Registrar usuario
    $insertarUsuario = $conexion->prepare(
        "INSERT INTO usuarios (nombre_usuario, contrasena)
         VALUES (?, ?)"
    );
    $insertarUsuario->execute([$usuario, $contrasenaHash]);
    // Obtener el ID del usuario registrado
    $idUsuario = $conexion->lastInsertId();
    // Registrar los apoyos seleccionados
    if (!empty($apoyos)) {
        $insertarApoyo = $conexion->prepare(
            "INSERT INTO usuario_apoyos (id_usuario, id_apoyo)
             VALUES (?, ?)"
        );
        foreach ($apoyos as $idApoyo) {
            $insertarApoyo->execute([$idUsuario, $idApoyo]);
        }
    }
    // Confirmar los cambios
    $conexion->commit();
    echo json_encode([
        "success" => true,
        "mensaje" => "¡Registro exitoso!"
    ]);
} catch (Exception $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "mensaje" => $e->getMessage()
    ]);
}
?>
