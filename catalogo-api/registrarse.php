<?php
// Habilitar reporte de errores para depuración (Quitar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include_once('../catalogo-conexion/conexion.php'); //

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Captura y limpieza de datos
    $usuario = trim($_POST['usuario'] ?? '');
    $correo  = trim($_POST['correo'] ?? '');
    $clave   = $_POST['clave'] ?? '';
    $rclave  = $_POST['rclave'] ?? '';

    // 2. Validaciones básicas de seguridad
    if (empty($usuario) || empty($correo) || empty($clave)) {
        header("Location: registro.php?error=Todos los campos son obligatorios");
        exit;
    }

    if ($clave !== $rclave) {
        header("Location: registro.php?error=Las contraseñas no coinciden");
        exit;
    }

    try {
        // 3. Verificar si el usuario o correo ya existen
        // Según tu login.php, la tabla es 'usuarios' y las columnas son 'nombre' y 'mail'
        $check = $conexion->prepare("SELECT id FROM usuarios WHERE nombre = ? OR mail = ?");
        $check->bind_param("ss", $usuario, $correo);
        $check->execute();
        $resultado = $check->get_result();

        if ($resultado->num_rows > 0) {
            header("Location: registro.php?error=El usuario o correo ya están registrados");
            exit;
        }
        $check->close();

        // 4. Encriptar la contraseña
        $claveHash = password_hash($clave, PASSWORD_BCRYPT);

        // 5. Insertar nuevo usuario (Asumiendo admin = 0 por defecto)
        // Columnas inferidas de tu login.php: nombre, mail, Clave, admin
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, mail, Clave, admin) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("sss", $usuario, $correo, $claveHash);

        if ($stmt->execute()) {
            // Registro exitoso: redirigir al login con mensaje de éxito
            header("Location: login.php?mensaje=Registro exitoso. Ahora puedes iniciar sesión.");
            exit;
        } else {
            throw new Exception("Error al ejecutar la inserción: " . $stmt->error);
        }

    } catch (Exception $e) {
        // Captura errores de SQL o del sistema y los envía de vuelta
        header("Location: registro.php?error=" . urlencode($e->getMessage()));
        exit;
    } finally {
        if (isset($stmt)) $stmt->close();
        $conexion->close();
    }
} else {
    // Si no es POST, redirigir al formulario
    header("Location: registro.php");
    exit;
}