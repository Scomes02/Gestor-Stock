<?php
session_start();
include '../catalogo-conexion/conexion.php';

// 1. Verificación de seguridad
if (!isset($_SESSION['usuario']['id'])) {
    die(json_encode(['error' => 'Sesión no válida.']));
}

// 2. Validación de campos requeridos (usando $_POST)
$campos_requeridos = ['nombre', 'precio_costo', 'precio_venta', 'cantidad', 'id_proveedor', 'categoria'];
foreach ($campos_requeridos as $campo) {
    if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
        die(json_encode(['error' => "Datos incompletos: falta el campo '$campo'"]));
    }
}

// 3. Captura y saneamiento de datos (DEFINICIÓN DE VARIABLES)
$id_usuario = $_SESSION['usuario']['id'];
$nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
$precio_costo = floatval($_POST['precio_costo']);
$precio_venta = floatval($_POST['precio_venta']);
$cantidad = intval($_POST['cantidad']);
$id_proveedor = intval($_POST['id_proveedor']);
$id_editar = isset($_POST['id_editar']) ? intval($_POST['id_editar']) : null;

// Lógica de categoría dinámica
$categoria = ($_POST['categoria'] === 'NUEVA') ? mysqli_real_escape_string($conexion, $_POST['nueva_categoria']) : mysqli_real_escape_string($conexion, $_POST['categoria']);

// 4. Manejo de Imagen
$ruta_db = "fotos_productos/default.png"; // Imagen base por defecto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $nombre_archivo = time() . "_" . $_FILES['foto']['name'];
    $ruta_destino = "../fotos_productos/" . $nombre_archivo;

    if (!is_dir('../fotos_productos/')) {
        mkdir('../fotos_productos/', 0777, true);
    }

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
        $ruta_db = "fotos_productos/" . $nombre_archivo;
    }
}

// 5. CASO A: ACTUALIZACIÓN DIRECTA (Si el usuario pulsó "Editar")
if ($id_editar) {
    // Si no se subió imagen nueva, recuperamos la que ya tenía para no perderla
    if ($ruta_db === "fotos_productos/default.png") {
        $resImg = $conexion->query("SELECT imagen FROM productos WHERE id = $id_editar");
        $actual = $resImg->fetch_assoc();
        $ruta_db = $actual['imagen'] ?? $ruta_db;
    }

    $sqlUpd = "UPDATE productos SET nombre=?, precio_costo=?, precio_venta=?, cantidad=?, id_proveedor=?, categoria=?, imagen=? WHERE id=?";
    $stmtUpd = $conexion->prepare($sqlUpd);
    $stmtUpd->bind_param("sddiissi", $nombre, $precio_costo, $precio_venta, $cantidad, $id_proveedor, $categoria, $ruta_db, $id_editar);

    if ($stmtUpd->execute()) {
        echo json_encode(['mensaje' => 'Producto actualizado correctamente']);
    } else {
        echo json_encode(['error' => 'Error al actualizar: ' . $conexion->error]);
    }
    $conexion->close();
    exit; // Finalizamos aquí si fue una edición
}

// 6. CASO B: CREACIÓN O ACTUALIZACIÓN POR NOMBRE (Si el usuario pulsó "Guardar")
$stmt_check = $conexion->prepare("SELECT id, cantidad, imagen FROM productos WHERE nombre = ?");
$stmt_check->bind_param("s", $nombre);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Si el producto existe por nombre, sumamos el stock
    $fila = $result->fetch_assoc();
    $nueva_cantidad = $fila['cantidad'] + $cantidad;
    $id_existente = $fila['id'];

    if ($ruta_db === "fotos_productos/default.png") {
        $ruta_db = $fila['imagen'];
    }

    $update = "UPDATE productos SET precio_costo=?, precio_venta=?, cantidad=?, id_usuario=?, categoria=?, imagen=? WHERE id=?";
    $stmt_upd = $conexion->prepare($update);
    $stmt_upd->bind_param("ddiissi", $precio_costo, $precio_venta, $nueva_cantidad, $id_usuario, $categoria, $ruta_db, $id_existente);

    if ($stmt_upd->execute()) {
        echo json_encode(['mensaje' => 'Stock y datos actualizados correctamente']);
    } else {
        echo json_encode(['error' => 'Error al actualizar: ' . $conexion->error]);
    }
} else {
    // Producto totalmente nuevo
    $insert = "INSERT INTO productos (nombre, precio_costo, precio_venta, cantidad, id_proveedor, id_usuario, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_ins = $conexion->prepare($insert);
    $stmt_ins->bind_param("sddiiiss", $nombre, $precio_costo, $precio_venta, $cantidad, $id_proveedor, $id_usuario, $categoria, $ruta_db);

    if ($stmt_ins->execute()) {
        echo json_encode(['mensaje' => 'Producto registrado con éxito']);
    } else {
        echo json_encode(['error' => 'Error al agregar: ' . $conexion->error]);
    }
}

$conexion->close();
?>