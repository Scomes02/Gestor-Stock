<div align="center">

# 📦 ReStock

### Sistema Integral de Gestión de Stock, Ventas y E-commerce

[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)](https://developer.mozilla.org/es/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)](https://developer.mozilla.org/es/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/es/docs/Web/JavaScript)
[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Architecture](https://img.shields.io/badge/Arquitectura-MVC-lightgrey?style=flat-square)]()

</div>

---

## 📋 Descripción del proyecto

**ReStock** es una solución web integral diseñada para la administración de inventarios, control de pedidos y gestión de ventas comerciales. Desarrollada desde cero bajo el patrón de arquitectura **MVC (Modelo-Vista-Controlador)** utilizando tecnologías web nativas, la aplicación conecta en tiempo real un panel administrativo de gestión interna con una interfaz pública de cliente tipo E-commerce.

El proyecto prioriza el dominio de la lógica pura de negocio y las buenas prácticas de ingeniería de software sin depender de frameworks pesados, logrando un sistema ágil, mantenible y de fácil despliegue.

## 🎯 El problema que resuelve

En muchos comercios en crecimiento, el control de inventario se lleva en planillas desconectadas del canal de ventas: los clientes consultan por productos sin stock real, los pedidos se pierden entre mensajes y no existe un registro claro del margen de ganancia ni del historial de proveedores.

**ReStock** unifica ambos mundos en una sola plataforma, dándole a cada actor **una interfaz dedicada con las herramientas exactas que necesita**: el cliente compra sobre un catálogo sincronizado con el inventario real, y el administrador controla el flujo de aprobación de pedidos, el stock y la rentabilidad desde un panel centralizado.

## ✨ Características principales

### 👨‍💻 Módulo Administrador (Gestión del Negocio)
- **Gestión de Stock (CRUD completo):** Alta, baja, modificación y consulta de productos con control de precios y existencias.
- **Control de Flujo de Pedidos:** Evaluación, aceptación o rechazo de las órdenes de compra generadas por los clientes.
- **Gestión de Entidades:** Administración centralizada de la cartera de proveedores y del padrón de clientes registrados.
- **Dashboard Analítico:** Visualización de métricas operativas clave, historial detallado de ventas y cálculo automático de ganancias.

### 🛒 Módulo Cliente (E-commerce Público)
- **Catálogo Sincronizado:** Visualización dinámica de productos disponibles con precios y disponibilidad de stock actualizados en tiempo real.
- **Carrito de Compras:** Armado, modificación de cantidades y confirmación de pedidos de forma interactiva.
- **Seguimiento de Órdenes ("Mis Pedidos"):** Consulta en vivo del estado de cada compra realizada (**Pendiente**, **Aceptado** o **Rechazado**).

### 🔐 Seguridad y control de acceso
- Sistema de autenticación con **Registro e Inicio de Sesión (Login)** diferenciado entre clientes y administradores.
- Separación de vistas y permisos según el rol para proteger las operaciones críticas de inventario y facturación.

## 🏗️ Arquitectura y stack tecnológico

El proyecto sigue el patrón **MVC (Modelo-Vista-Controlador)** implementado en PHP nativo, garantizando una clara separación de responsabilidades entre datos, lógica y presentación:

| Capa | Tecnología | Uso |
|---|---|---|
| Backend | **PHP Puro** | Lógica de negocio, controladores, gestión de sesiones y validaciones |
| Base de datos | **MySQL** | Persistencia relacional (productos, pedidos, clientes, proveedores) |
| Frontend | **HTML5** + **CSS3** | Maquetación semántica y estilos responsivos para tienda y panel |
| Interactividad | **Vanilla JavaScript** | Gestión dinámica del carrito de compras y validaciones en el cliente |
| Arquitectura | **Patrón MVC** | Separación estructurada en Modelos, Vistas y Controladores |
| Control de versiones | **Git** + **GitHub** | Versionado del código fuente y distribución del esquema SQL |

### Decisiones de diseño destacadas

- **Implementación MVC sin frameworks:** Construcción manual de la arquitectura para mantener el control total sobre el ciclo de vida de las peticiones HTTP, optimizando el consumo de recursos en servidores locales o compartidos.
- **Sincronización entre E-commerce y Backoffice:** Tanto el catálogo público como el dashboard administrativo consumen la misma fuente de verdad en MySQL, evitando discrepancias de stock.
- **Flujo de validación de pedidos por estados:** Las compras no descuentan ni impactan de forma descontrolada; pasan por una bandeja de auditoría donde el administrador aprueba o rechaza la operación.

## 📁 Estructura del proyecto

```text
Gestor-Stock/
├── controllers/            # Controladores de flujo (Productos, Pedidos, Usuarios, Proveedores)
├── models/                 # Modelos de acceso a datos y consultas SQL
├── views/                  # Vistas separadas por módulo (Admin Dashboard y Tienda Cliente)
├── assets/                 # Hojas de estilo CSS, scripts JS e imágenes de productos
├── config/                 # Configuración de conexión a la base de datos MySQL
├── catalogo_db.sql         # Script de estructura y datos iniciales de la base de datos
├── index.php               # Punto de entrada principal de la aplicación
└── README.md               # Documentación del proyecto
```

🚀 Instalación y puesta en marcha
Requisitos previos
Entorno de servidor local (XAMPP, WAMP, Laragon o MAMP)

PHP 7.4 / 8.x

MySQL / MariaDB

Gestor de base de datos (phpMyAdmin o DBeaver)

Pasos
Bash
# 1. Clonar el repositorio dentro de la carpeta pública de tu servidor (ej. htdocs en XAMPP)
cd C:/xampp/htdocs
git clone [https://github.com/Scomes02/Gestor-Stock.git](https://github.com/Scomes02/Gestor-Stock.git)
cd Gestor-Stock
Configurá la base de datos local siguiendo estos pasos:

Iniciá los servicios de Apache y MySQL desde el panel de control de XAMPP.

Abrí phpMyAdmin (http://localhost/phpmyadmin) o DBeaver y creá una nueva base de datos para el proyecto.

Importá el archivo catalogo_db.sql ubicado en la raíz del repositorio para generar todas las tablas y relaciones.

Verificá las credenciales de conexión dentro de tu archivo de configuración de base de datos:

PHP
$host = "localhost";
$dbname = "catalogo_db";
$username = "root";
$password = "";
Una vez importada la base de datos, abrí tu navegador y accedé a:

Plaintext
http://localhost/Gestor-Stock
🧩 Flujo del sistema
Fragmento de código
flowchart LR
    A[Cliente explora<br/>el catálogo] --> B[Arma carrito y<br/>confirma pedido]
    B --> C{Administrador<br/>revisa orden}
    C -->|Aprueba pedido| D[Aceptado<br/>Impacta en ventas y ganancias]
    C -->|Cancela pedido| E[Rechazado]
    D --> F[Cliente visualiza estado<br/>en Mis Pedidos]
    E --> F

📸 Capturas de Pantalla
<img width="1852" height="940" alt="Dashboard Admin" src="https://github.com/user-attachments/assets/75a9dc8b-ac9c-433b-bfc4-e362b17ad9ab" />

<img width="1842" height="932" alt="Gestión de Stock" src="https://github.com/user-attachments/assets/4c73f3cf-26df-427d-977f-b3a47cf1bc5a" />

<img width="1865" height="939" alt="Vista Catálogo Cliente" src="https://github.com/user-attachments/assets/881734bd-4613-420c-b256-c86fdbcc9cfd" />

<img width="1847" height="939" alt="Carrito de Compras" src="https://github.com/user-attachments/assets/41aa3788-d4e3-4518-bb97-68829393ab8c" />

### 👤 Autor
**Santiago Comes** 
- 💻 GitHub: [Scomes02](https://github.com/Scomes02)
- 💼 LinkedIn: [Santiago Comes](https://www.linkedin.com/in/santiago-comes)
