# 📦 ReStock - Sistema de Gestión de Stock y Ventas

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## 📄 Descripción

**ReStock** es una solución web integral diseñada para la administración de inventarios y ventas. Desarrollada bajo el patrón de arquitectura **MVC (Modelo-Vista-Controlador)**, la aplicación conecta un panel administrativo robusto con una interfaz de cliente intuitiva tipo E-commerce.

### ✨ Características Principales

El sistema está dividido en dos módulos interconectados:

**👨‍💻 Panel de Administrador (Gestión del Negocio)**
* **Gestión de Stock:** CRUD completo (Crear, Leer, Actualizar, Borrar) de productos.
* **Control de Flujo:** Aceptación o rechazo de pedidos realizados por los clientes.
* **Gestión de Entidades:** Administración de la cartera de proveedores y clientes.
* **Dashboard Analítico:** Visualización de métricas clave, historial de ventas y cálculo automático de ganancias.

**🛒 Vista Cliente (E-commerce Público)**
* **Autenticación:** Sistema seguro de Registro e Inicio de Sesión (Login).
* **Catálogo:** Visualización de productos disponibles con precios y stock actualizados.
* **Carrito de Compras:** Armado y gestión de pedidos en tiempo real.
* **Seguimiento:** Sección "Mis Pedidos" para consultar el estado de la compra (Pendiente / Aceptado / Rechazado).

---

## 🛠️ Tecnologías Utilizadas

Este proyecto fue construido utilizando tecnologías web estándar sin frameworks pesados, enfocándose en la lógica pura y las buenas prácticas de programación:

* **Frontend:** HTML5, CSS3 y Vanilla JavaScript.
* **Backend:** PHP puro (encargado de la lógica de negocio, validaciones y controladores).
* **Base de Datos:** MySQL (Diseño estructurado y relacional).
* **Arquitectura:** Patrón MVC (Modelo-Vista-Controlador) para la separación de responsabilidades.

---

## ▶️ Instalación y Despliegue Local

Sigue estos pasos para correr el proyecto en tu máquina local (requiere un entorno de servidor como XAMPP, WAMP o MAMP):

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/Scomes02/Gestor-Stock.git](https://github.com/Scomes02/Gestor-Stock.git)
Mover al servidor local:
Coloca la carpeta clonada dentro del directorio público de tu servidor web (por ejemplo, en la carpeta htdocs si usas XAMPP).

Configurar la Base de Datos:

Abre phpMyAdmin (o tu gestor SQL preferido como DBeaver).

Crea una nueva base de datos.

Importa el archivo catalogo_db.sql que se encuentra en la carpeta raíz de este proyecto.

Ejecutar:
Abre tu navegador web y navega a la ruta local del proyecto. Por ejemplo:
http://localhost/Gestor-Stock

📸 Capturas de Pantalla
<img width="1852" height="940" alt="Dashboard Admin" src="https://github.com/user-attachments/assets/75a9dc8b-ac9c-433b-bfc4-e362b17ad9ab" />

<img width="1842" height="932" alt="Gestión de Stock" src="https://github.com/user-attachments/assets/4c73f3cf-26df-427d-977f-b3a47cf1bc5a" />

<img width="1865" height="939" alt="Vista Catálogo Cliente" src="https://github.com/user-attachments/assets/881734bd-4613-420c-b256-c86fdbcc9cfd" />

<img width="1847" height="939" alt="Carrito de Compras" src="https://github.com/user-attachments/assets/41aa3788-d4e3-4518-bb97-68829393ab8c" />

👤 Autor
Santiago Comes

💼 LinkedIn: Visitar mi perfil

💻 GitHub: @Scomes02
