<?php
session_start();

// Centralizamos la lógica del nombre de usuario
$nombreUsuario = 'Invitado';
if (isset($_SESSION['usuario']['nombre'])) {
    $nombreUsuario = $_SESSION['usuario']['nombre'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/img/Proyecto_nuevo.ico">
    <title>CAPIBARA STORE | Calidad y Estilo</title>

    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8fa0212ec6.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="navbar-fixed">
        <div class="logo">
            <img src="../fotos index/cover.png" alt="Capibara Store Logo" class="imag">
        </div>

        <div class="audio-player">
            <audio controls>
                <source src="../fotos index/Capibara.mp4" type="audio/mp4">
            </audio>
        </div>

        <nav>
            <ul>
                <li><a href="vista_cliente.php" class="btn-grow">Inicio</a></li>
                <li><a href="tienda.php" class="btn-grow">Tienda</a></li>
                <li><a href="#contact" class="btn-grow">Contacto</a></li>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <?php if ($_SESSION['usuario']['admin'] == 1): ?>
                        <li><a href="inicio.php" class="btn-grow admin-link">Ir a Sistema</a></li>
                    <?php endif; ?>
                    <li><a href="../catalogo-api/cerrar_sesion.php" class="btn-grow logout">Cerrar Sesión (<?= htmlspecialchars($nombreUsuario) ?>)</a></li>
                <?php else: ?>
                    <li><a href="../catalogo-api/login.php" class="btn-grow">Iniciar Sesión</a></li>
                    <li><a href="../catalogo-api/registro.php" class="btn-grow">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <section class="hero">
            <div class="hero-text">
                <h1>Bienvenido a Capibara Store 👋</h1>
                <p>Somos apasionados por ofrecerte lo mejor en tendencia y calidad. En Capibara Store, nos dedicamos a proveer productos exclusivos que se adaptan a tu estilo de vida, garantizando seguridad en cada compra y una experiencia excepcional.</p>
                <a href="tienda.php" class="btn-cta">Explorar Tienda</a>
            </div>
        </section>

        <section class="novedades">
            <h2 class="section-title">Nuestras Tendencias</h2>
            <div class="grid-galeria">
                <img src="../fotos index/FOT1.webp" class="foto-grid" alt="Producto 1">
                <img src="../fotos index/FOT2.jpg" class="foto-grid" alt="Producto 2">
                <img src="../fotos index/FOT3.jpg" class="foto-grid" alt="Producto 3">
                <img src="../fotos index/FOT4.jpg" class="foto-grid" alt="Producto 4">
            </div>
        </section>

        <section class="promo-carrusel">
            <div id="conteCarrusel">
                <div class="tarjeta active">
                    <img src="../fotos index/img1.jpg" alt="Promo 1">
                    <div class="overlay-carrusel">
                        <h3>Colección Verano</h3>
                    </div>
                </div>
                <div class="tarjeta">
                    <img src="../fotos index/img2.jpg" alt="Promo 2">
                    <div class="overlay-carrusel">
                        <h3>Ofertas Especiales</h3>
                    </div>
                </div>
                <div class="tarjeta">
                    <img src="../fotos index/img3.jpg" alt="Promo 3">
                    <div class="overlay-carrusel">
                        <h3>Envíos a todo el país</h3>
                    </div>
                </div>

                <div class="controles">
                    <button class="btn-carrusel" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                    <button class="btn-carrusel" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </section>

        <section id="contact" class="contacto-section">
            <div class="contacto-container">
                <div class="contacto-info">
                    <h2>¡Contáctanos!</h2>
                    <p>¿Tienes dudas o sugerencias? Déjanos un mensaje y te responderemos en menos de 24 horas.</p>
                    <div class="seguir-redes">
                        <strong>¡No te pierdas nada!</strong>
                        <p>Síguenos para enterarte de promociones exclusivas.</p>
                    </div>
                </div>

                <div class="contacto-form">
                    <?php if (isset($_GET['msj']) && $_GET['msj'] == 'enviado'): ?>
                        <p style="color: green; font-weight: bold;">¡Mensaje enviado con éxito! Te contactaremos pronto.</p>
                    <?php endif; ?>
                    <form action="../catalogo-api/save_message.php" method="post">
                        <input type="hidden" name="page" value="index">
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" id="name" name="name" placeholder="Tu nombre..." required>
                        </div>
                        <div class="form-group">
                            <label for="contacto">Email o Teléfono</label>
                            <input type="text" id="contacto" name="contacto" placeholder="Tu contacto..." required>
                        </div>
                        <div class="form-group">
                            <label for="message">Mensaje</label>
                            <textarea name="message" id="message" rows="4" placeholder="¿En qué podemos ayudarte?" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-dark">
        <div class="footer-content">
            <p>&copy; 2024 CAPIBARA STORE - Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="https://www.instagram.com/mauricio_arias_continella?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank"><i class="fab fa-instagram"></i></a></li>
                <li><a href="https://www.tiktok.com/@germangiorgiss" target="_blank"><i class="fab fa-tiktok"></i></a></li>
                <li><a href="https://wa.me" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
            </ul>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tarjetas = document.querySelectorAll('.tarjeta');
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            let currentIndex = 0;
            let autoPlay = setInterval(showNext, 5000); // Cambio cada 5 segundos

            function updateSlide(index) {
                tarjetas.forEach(t => t.classList.remove('active'));
                tarjetas[index].classList.add('active');
            }

            function showNext() {
                currentIndex = (currentIndex + 1) % tarjetas.length;
                updateSlide(currentIndex);
            }

            function showPrev() {
                currentIndex = (currentIndex - 1 + tarjetas.length) % tarjetas.length;
                updateSlide(currentIndex);
            }

            // Detener el auto-play cuando el usuario hace clic manualmente
            function resetTimer() {
                clearInterval(autoPlay);
                autoPlay = setInterval(showNext, 5000);
            }

            nextBtn.addEventListener('click', () => {
                showNext();
                resetTimer();
            });

            prevBtn.addEventListener('click', () => {
                showPrev();
                resetTimer();
            });
        });
    </script>
</body>

</html>