/**
 * tienda.js - Capibara Store (Versión Final Unificada)
 * - Carrito Persistente
 * - Filtro Inteligente
 * - Checkout y Envío de Pedido con Método de Pago
 */

// 1. PERSISTENCIA Y VARIABLES GLOBALES
let carrito = JSON.parse(localStorage.getItem('capibara_cart')) || [];
const cartIcon = document.getElementById('cartIcon');
const cartDropdown = document.getElementById('cartDropdown');
const cartItemsContainer = document.getElementById('cartItems');
const totalPagarElement = document.getElementById('total-pagar');
const contadorProductos = document.getElementById('contador-productos');

document.addEventListener('DOMContentLoaded', () => {
    actualizarVistaCarrito();
    
    // 2. GESTIÓN DE INTERFAZ DEL CARRITO
    
    // Abrir/Cerrar carrito al tocar el icono
    cartIcon?.addEventListener('click', (e) => {
        e.stopPropagation();
        cartDropdown.classList.toggle('hidden');
    });

    // Cerrar carrito al hacer clic fuera del dropdown
    document.addEventListener('click', (e) => {
        if (!cartDropdown.contains(e.target) && !cartIcon.contains(e.target)) {
            cartDropdown.classList.add('hidden');
        }
    });

    // 3. LÓGICA DE AGREGAR PRODUCTOS
    document.addEventListener('click', e => {
        const btnAdd = e.target.closest('.btn-add-cart');
        if (btnAdd) {
            const producto = {
                id: btnAdd.dataset.id,
                nombre: btnAdd.dataset.nombre,
                precio: parseFloat(btnAdd.dataset.precio),
                img: btnAdd.dataset.img,
                cantidad: 1
            };

            const index = carrito.findIndex(p => p.id === producto.id);
            if (index !== -1) {
                carrito[index].cantidad++;
            } else {
                carrito.push(producto);
            }
            guardarYActualizar();
            cartDropdown.classList.remove('hidden'); // Mostrar carrito al agregar
        }
    });

    // 4. LÓGICA DE ELIMINAR PRODUCTOS
    cartItemsContainer?.addEventListener('click', e => {
        const btnDelete = e.target.closest('.btn-delete');
        if (btnDelete) {
            const id = btnDelete.dataset.id;
            carrito = carrito.filter(p => p.id !== id);
            guardarYActualizar();
        }
    });

    // 5. SISTEMA DE FILTRADO INTELIGENTE (NOMBRE + CATEGORÍA)
    
    function filtrarProductos() {
        const termino = document.getElementById('search-bar').value.toLowerCase();
        const categoriaSeleccionada = document.getElementById('category-filter').value;
        const productos = document.querySelectorAll('.product-item');

        productos.forEach(item => {
            const nombre = item.querySelector('.product-title').innerText.toLowerCase();
            const catItem = item.dataset.category; 
            
            const coincideNombre = nombre.includes(termino);
            const coincideCat = (categoriaSeleccionada === 'all' || catItem === categoriaSeleccionada);

            // Mostrar solo si cumple ambas condiciones
            item.style.display = (coincideNombre && coincideCat) ? 'block' : 'none';
        });

        // Ocultar secciones de encabezado vacías tras el filtro
        document.querySelectorAll('.container-header').forEach(header => {
            // Navegamos al div row de productos, que está después del hr
            const rowProductos = header.nextElementSibling.nextElementSibling; 
            if (!rowProductos) return; 

            const tieneVisibles = Array.from(rowProductos.querySelectorAll('.product-item'))
                                       .some(p => p.style.display !== 'none');
            
            header.style.display = tieneVisibles ? 'block' : 'none';
            header.nextElementSibling.style.display = tieneVisibles ? 'block' : 'none'; // Ocultar el <hr>
        });
    }

    // Escuchar escritura en buscador
    document.getElementById('search-bar')?.addEventListener('input', filtrarProductos);

    // Escuchar cambio en el selector de categoría
    document.getElementById('category-filter')?.addEventListener('change', filtrarProductos);

    // 6. FLUJO DE CHECKOUT (MODAL Y GUARDADO DE PEDIDO)
    
    // Abrir Modal
    document.getElementById('btnPagar')?.addEventListener('click', () => {
        if (carrito.length === 0) return alert("Tu carrito está vacío.");
        
        // Resetear modal a paso 1
        document.getElementById('step-payment').classList.remove('d-none');
        document.getElementById('step-success').classList.add('d-none');
        
        const modal = new bootstrap.Modal(document.getElementById('modalCheckout'));
        modal.show();
    });

    // Selección de método de pago y envío de datos
    document.querySelectorAll('.btn-pay-option').forEach(boton => {
        boton.addEventListener('click', function() {
            const metodo = this.getAttribute('data-method');
            
            // VERIFICACIÓN CLAVE: Aseguramos que el carrito no esté vacío
            if (carrito.length === 0) {
                alert("Tu carrito está vacío. Por favor, agrega productos antes de pagar.");
                return; 
            }

            // Creamos el payload que incluye el carrito y el método de pago
            const payload = {
                carrito: carrito,
                metodo_pago: metodo 
            };

            // Enviamos el objeto completo al servidor (Ruta corregida con ../)
            fetch('../catalogo-api/guardar_pedido.php', {
                method: 'POST',
                body: JSON.stringify(payload), 
                headers: {'Content-Type': 'application/json'}
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const msj = `Perfecto, ya has realizado tu pedido con éxito. Por favor, acércate a nuestra sucursal para retirar tu pedido abonando con <b>${metodo}</b>. ¡Muchas gracias por elegir Capibara Store!`;
                    
                    document.getElementById('mensaje-final').innerHTML = msj;

                    // Transición de pasos en el modal
                    document.getElementById('step-payment').classList.add('d-none');
                    document.getElementById('step-success').classList.remove('d-none');

                    vaciarCarrito();
                } else {
                    // Muestra el error detallado de PHP/SQL si existe
                    alert("Error al guardar el pedido: " + (data.error || "Error desconocido."));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert("Error de conexión al guardar el pedido.");
            });
        });
    });
});

// --- FUNCIONES AUXILIARES ---

function guardarYActualizar() {
    localStorage.setItem('capibara_cart', JSON.stringify(carrito));
    actualizarVistaCarrito();
}

function actualizarVistaCarrito() {
    if (!cartItemsContainer) return;
    
    cartItemsContainer.innerHTML = '';
    let total = 0;
    let totalCant = 0;

    if (carrito.length === 0) {
        cartItemsContainer.innerHTML = '<p class="text-center text-muted py-4">El carrito está vacío</p>';
    } else {
        carrito.forEach(p => {
            total += (p.precio * p.cantidad);
            totalCant += p.cantidad;

            cartItemsContainer.innerHTML += `
                <div class="cart-item d-flex align-items-center gap-2">
                    <img src="${p.img}" alt="${p.nombre}">
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-bold text-truncate" style="max-width: 140px;">${p.nombre}</p>
                        <small class="text-muted">${p.cantidad} x $${p.precio.toFixed(2)}</small>
                    </div>
                    <button class="btn btn-sm text-danger btn-delete border-0" data-id="${p.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });
    }

    if (totalPagarElement) totalPagarElement.innerText = `$${total.toFixed(2)}`;
    if (contadorProductos) contadorProductos.innerText = totalCant;
}

function vaciarCarrito() {
    carrito = [];
    localStorage.removeItem('capibara_cart');
    actualizarVistaCarrito();
}