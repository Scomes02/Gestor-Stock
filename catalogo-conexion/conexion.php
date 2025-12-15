<?php
// catalogo-conexion/conexion.php
// Versión segura: lee credenciales desde variables de entorno o .env (si existe).
// Mantener el orden original: solo se modificó la forma de cargar credenciales.
// Si no se encuentran variables de entorno, se usan valores por defecto (los que estaban).
// NOTA: Recomendado crear un archivo .env en la raíz del proyecto (no lo subas a git).
// Ejemplo de .env: ver ../.env.example

// Cargar .env simple si existe (formato KEY=VAL por línea)
$env_path = __DIR__ . '/../.env';
if (file_exists($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($k,$v) = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        if (!getenv($k)) putenv("$k=$v");
        $_ENV[$k] = $v;
        $_SERVER[$k] = $v;
    }
}

// Leer variables (prioriza variables de entorno)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$usuario = getenv('DB_USER') ?: 'root';
$contrasena = getenv('DB_PASS') ?: 'Comes.1016';
$base_datos = getenv('DB_NAME') ?: 'catalogo_db';
$puerto = intval(getenv('DB_PORT') ?: 3306);

// Conexión usando mysqli con manejo de errores
$conexion = new mysqli($host, $usuario, $contrasena, $base_datos, $puerto);

if ($conexion->connect_error) {
    // Mensaje claro para desarrollo; en producción devolver genérico y logear el detalle.
    die("❌ Error de conexión: " . $conexion->connect_error);
}

// Configurar conjunto de caracteres por defecto
$conexion->set_charset('utf8mb4');

?>
