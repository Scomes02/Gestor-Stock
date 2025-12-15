<?php
session_start();
include('../catalogo-conexion/conexion.php'); 

$mensaje_error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail']);
    $clave = $_POST['Clave'];

    $query = $conexion->prepare("SELECT id, nombre, mail, Clave, admin FROM usuarios WHERE mail = ?");
    
    if ($query === false) {
        $mensaje_error = 'Error interno del sistema.';
    } else {
        $query->bind_param("s", $mail);
        $query->execute();
        $resultado = $query->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($clave, $usuario['Clave'])) {
                $_SESSION['usuario'] = [
                    'id' => $usuario['id'],
                    'nombre' => $usuario['nombre'],
                    'mail' => $usuario['mail'],
                    'admin' => $usuario['admin']
                ]; 
                header('Location: ../catalogo-productos/vista_cliente.php');
                exit();
            } else {
                $mensaje_error = 'Contraseña incorrecta.';
            }
        } else {
            $mensaje_error = 'El usuario no existe.';
        }
        $query->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Iniciar Sesión - Capibara Store</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --azul-capibara: #0B173D;
            --dorado-capibara: #B78F0D;
        }
        body {
            background: linear-gradient(135deg, #0B173D 0%, #1a2a5a 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            border-radius: 15px;
            background-color: white;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h3 {
            color: var(--azul-capibara);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-primary {
            background-color: var(--azul-capibara);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: var(--dorado-capibara);
            transform: scale(1.02);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .register-text {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <img src="/img/Proyecto_nuevo.ico" alt="Logo" width="50" class="mb-2">
        <h3>Bienvenido</h3>
        <p class="text-muted">Ingresa a tu panel de gestión</p>
    </div>
    
    <?php if ($mensaje_error): ?>
        <div class="alert alert-danger py-2 mb-4" role="alert" style="font-size: 0.85rem;">
            ⚠️ <?php echo htmlspecialchars($mensaje_error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php"> 
        <div class="mb-3">
            <label for="mail" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control form-control-lg" id="mail" name="mail" placeholder="usuario@capibara.com" required>
        </div>
        <div class="mb-4">
            <label for="clave" class="form-label">Contraseña</label>
            <input type="password" class="form-control form-control-lg" id="clave" name="Clave" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">Iniciar Sesión</button>
    </form>

    <div class="register-text">
        <span class="text-muted">¿No tienes acceso?</span><br>
        <a href="registro.php" class="text-decoration-none fw-bold" style="color: var(--dorado-capibara);">Solicita tu cuenta aquí</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>