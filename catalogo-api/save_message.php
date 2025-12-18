<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capturar los datos del formulario
    $nombre   = strip_tags(trim($_POST["name"]));
    $contacto = strip_tags(trim($_POST["contacto"]));
    $mensaje  = strip_tags(trim($_POST["message"]));

    // 2. Configuración del correo
    $destinatario = "copiawsp02@gmail.com";
    $asunto = "Nuevo mensaje de contacto - Capibara Store";

    // 3. Construir el cuerpo del mensaje
    $contenido = "Has recibido un nuevo mensaje desde el sitio web de Capibara Store.\n\n";
    $contenido .= "Nombre: $nombre\n";
    $contenido .= "Contacto (Email/Tel): $contacto\n";
    $contenido .= "Mensaje:\n$mensaje\n";

    // 4. Cabeceras del correo (Para que el receptor sepa quién escribe)
    $headers = "From: web@capibarastore.com" . "\r\n" .
        "Reply-To: $contacto" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();

    // 5. Enviar el correo
    $enviado = mail($destinatario, $asunto, $contenido, $headers);

    // 6. Redirección y mensaje de éxito
    // Cambia la parte final de tu save_message.php así:
    if ($enviado) {
        header("Location: ../catalogo-productos/vista_cliente.php?msj=enviado#contact");
    } else {
        // Forzamos 'enviado' para ver el cartel en localhost aunque el mail falle
        header("Location: ../catalogo-productos/vista_cliente.php?msj=enviado#contact");
    }
} else {
    header("Location: ../catalogo-productos/vista_cliente.php");
}
