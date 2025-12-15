<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="/img/Proyecto_nuevo.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Registro - Capibara Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --azul-capibara: #0B173D;
            --dorado-capibara: #B78F0D;
        }
        body {
            background: linear-gradient(135deg, #0B173D 0%, #1a2a5a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h2 {
            color: var(--azul-capibara);
            font-weight: 700;
            margin-top: 10px;
        }
        .btn-success {
            background-color: var(--azul-capibara);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-success:hover {
            background-color: #1a2a5a;
            transform: translateY(-2px);
        }
        .form-control:focus {
            border-color: var(--dorado-capibara);
            box-shadow: 0 0 0 0.25 mil rgba(183, 143, 13, 0.25);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <img src="/img/Proyecto_nuevo.ico" alt="Logo" width="60">
            <h2>Crea tu cuenta</h2>
            <p class="text-muted">Únete a la comunidad de Capibara Store</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="../catalogo-api/registrarse.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre de Usuario</label>
                <input type="text" class="form-control" name="usuario" placeholder="Ej: santi_capibara" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Correo Electrónico</label>
                <input type="email" class="form-control" name="correo" placeholder="correo@ejemplo.com" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Clave</label>
                    <input type="password" class="form-control" name="clave" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Repetir Clave</label>
                    <input type="password" class="form-control" name="rclave" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100 mb-3">Registrarse</button>
            <div class="d-flex justify-content-between align-items-center">
                <a href="../catalogo-productos/vista_cliente.php" class="text-decoration-none text-muted small">← Volver al inicio</a>
                <a href="login.php" class="text-decoration-none small fw-bold" style="color: var(--dorado-capibara);">¿Ya tienes cuenta?</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>