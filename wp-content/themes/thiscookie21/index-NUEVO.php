<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <h1 class="site-title">🍪 ThisCookie21</h1>
    <p class="site-description">Galletas Artesanales Hechas con Amor</p>
</header>

<main class="container">
    <section class="intro-section">
        <h2>Nuestras Deliciosas Galletas</h2>
        <p>Cada galleta está hecha a mano con los mejores ingredientes. ¡Descubre nuestros sabores!</p>
    </section>

    <section class="cookies-section">
        <?php
        $cookies_by_price = array(
            '2,90€' => array(
                array('name' => 'Choconueces', 'description' => 'Chocolate puro 52% y nueces californianas.', 'price' => 2.90),
                array('name' => 'Brownie', 'description' => 'Chocolate puro 52%.', 'price' => 2.90),
                array('name' => 'Brownie naranja', 'description' => 'Chocolate puro 52% con naranja natural.', 'price' => 2.90),
                array('name' => 'Coco', 'description' => 'Chocolate puro con leche y coco.', 'price' => 2.90),
                array('name' => 'Limón', 'description' => 'Chocolate puro blanco y limón natural.', 'price' => 2.90),
                array('name' => 'Dátiles', 'description' => 'Dátiles, chocolate blanco y nueces californianas.', 'price' => 2.90),
                array('name' => 'Arándano', 'description' => 'Chocolate puro blanco y arándano rojo deshidratado.', 'price' => 2.90),
                array('name' => 'Gofio', 'description' => 'Con gofio, nuestro sabor más canario.', 'price' => 2.90),
            ),
            '3,50€' => array(
                array('name' => '3 Chocolates', 'description' => 'Chocolates puros: negro 52%, con leche y blanco.', 'price' => 3.50),
                array('name' => 'Kinder®', 'description' => '¡La favorita de muchos!', 'price' => 3.50),
                array('name' => 'Red Velvet', 'description' => 'Con cacao, chocolate blanco rellena de frosting de queso crema.', 'price' => 3.50),
                array('name' => 'Carrot Cake', 'description' => 'Con canela, nueces californianas, zanahoria rellena de frosting de queso crema.', 'price' => 3.50),
                array('name' => 'Te matcha', 'description' => 'Con te matcha japonés ecológico y chocolate blanco.', 'price' => 3.50),
            ),
            '3,90€' => array(
                array('name' => 'Oreo® rellena', 'description' => 'Chocolate puro 52%, chocolate puro blanco y galletas Oreo, rellena con crema de cookies and cream.', 'price' => 3.90),
                array('name' => 'Lotus® rellena', 'description' => 'El inconfundible sabor de la galleta caramelizada, rellena de crema Lotus.', 'price' => 3.90),
                array('name' => 'Cheese Cake', 'description' => 'Arándano azul natural rellena de frosting de queso crema.', 'price' => 3.90),
                array('name' => 'Cinnamon roll', 'description' => 'Tu bollo preferido de cinnamon roll, pero en cookie, con glaseado suave de queso crema.', 'price' => 3.90),
                array('name' => 'Black and White', 'description' => 'Con cacao negro, chocolate blanco y chocolate con leche.', 'price' => 3.90),
                array('name' => 'Black and White rellena', 'description' => 'Con cacao negro, chocolate blanco y chocolate con leche, rellena de Nutella®.', 'price' => 3.90),
                array('name' => 'Nutella® rellena', 'description' => 'Rellena de Nutella®.', 'price' => 3.90),
                array('name' => 'Cocomilky', 'description' => 'Con coco rallado rellena de dulce de leche.', 'price' => 3.90),
                array('name' => 'Milkybar rellena', 'description' => 'Con chocolate blanco milkybar, rellena de crema de chocolate blanco.', 'price' => 3.90),
                array('name' => 'Pumpkin Cake', 'description' => 'Con calabaza y 5 especias, frosting de queso crema, caramelo salado y crumble.', 'price' => 3.90),
            ),
            '4,20€' => array(
                array('name' => 'Manihoney rellena', 'description' => 'Con cacahuetes Eagle®, chocolate con leche, rellena de crema de cacahuete Eagle®.', 'price' => 4.20),
                array('name' => 'Turrón blend rellena', 'description' => 'Con almendra tostada, rellena de crema de turrón blando, cubierta de granillo de almendra.', 'price' => 4.20),
                array('name' => 'Reese\'s', 'description' => 'Con Reese\'s pieces, coronada con chocolate con leche y Reese\'s mini cups.', 'price' => 4.20),
            ),
            '4,90€' => array(
                array('name' => 'Happy Hippo® rellena', 'description' => 'Rellena de crema Kinder y coronada con Happy Hippo.', 'price' => 4.90),
            ),
            '5,90€' => array(
                array('name' => 'Pistacho rellena', 'description' => 'Con chocolate blanco, pistacho, rellena con nuestra exclusiva crema de pistacho (la que sabe de verdad a pistacho) y coronada con más pistacho.', 'price' => 5.90),
            ),
        );

        foreach ($cookies_by_price as $price => $cookies) {
            echo '<div class="price-group">';
            echo '<div class="price-header">' . esc_html($price) . '</div>';
            echo '<div class="cookies-grid">';
            
            foreach ($cookies as $cookie) {
                $cookie_id = sanitize_title($cookie['name']);
                ?>
                <div class="cookie-card">
                    <div class="cookie-image">🍪</div>
                    <div class="cookie-content">
                        <h3><?php echo esc_html($cookie['name']); ?></h3>
                        <p><?php echo esc_html($cookie['description']); ?></p>
                        <button class="add-to-cart" onclick="addToCart('<?php echo $cookie_id; ?>', '<?php echo esc_js($cookie['name']); ?>', <?php echo $cookie['price']; ?>)">
                            Añadir al Carrito
                        </button>
                    </div>
                </div>
                <?php
            }
            
            echo '</div></div>';
        }
        ?>
    </section>

    <div id="cartIcon" class="cart-icon" onclick="toggleCart()">
        🛒 <span id="cartCount">0</span>
    </div>

    <div id="cartModal" class="cart-modal">
        <div class="cart-content">
            <span class="close-cart" onclick="toggleCart()">&times;</span>
            <h2>🛒 Tu Pedido</h2>
            
            <!-- RESUMEN DEL CARRITO -->
            <div id="cartSummary">
                <h3>Productos seleccionados:</h3>
                <div id="cartItems"></div>
                <div class="cart-total">
                    <strong>Total: </strong><span id="cartTotal">0,00€</span>
                </div>
            </div>
            
            <hr style="margin: 20px 0; border: 1px solid #ddd;">
            
            <!-- FORMULARIO DE DATOS -->
            <div class="order-form-inline">
                <h3>📋 Datos del Pedido</h3>
                <div class="form-group">
                    <label for="customerName">Nombre *</label>
                    <input type="text" id="customerName" required>
                </div>
                <div class="form-group">
                    <label for="customerEmail">Email *</label>
                    <input type="email" id="customerEmail" required>
                </div>
                <div class="form-group">
                    <label for="customerPhone">Teléfono *</label>
                    <input type="tel" id="customerPhone" required>
                </div>
                <div class="form-group">
                    <label for="deliveryDate">Fecha de recogida *</label>
                    <select id="deliveryDate" required onchange="updatePickupLocation()">
                        <option value="">Selecciona una fecha</option>
                    </select>
                </div>
                <div class="form-group" id="pickupLocationGroup" style="display:none;">
                    <label>Lugar de recogida</label>
                    <div id="pickupInfo"></div>
                </div>
                <button class="checkout-btn" id="submitOrder">Confirmar y Pagar</button>
            </div>
        </div>
    </div>

</main>

<footer class="site-footer">
    <p>&copy; <?php echo date('Y'); ?> ThisCookie21 - Todos los derechos reservados</p>
    <p>Galletas Artesanales Hechas con Amor 🍪</p>
</footer>

<script>
let cart = [];

document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('thiscookie21_cart');
    if (saved) cart = JSON.parse(saved);
    updateCart();
    generateAvailableDates();
});

function saveCart() {
    localStorage.setItem('thiscookie21_cart', JSON.stringify(cart));
}

function addToCart(id, name, price) {
    const item = cart.find(i => i.id === id);
    if (item) {
        item.quantity++;
    } else {
        cart.push({id, name, price, quantity: 1});
    }
    updateCart();
    saveCart();
    
    // Hacer visible el icono del carrito con animación
    const cartIcon = document.getElementById('cartIcon');
    cartIcon.classList.add('cart-bounce');
    setTimeout(() => cartIcon.classList.remove('cart-bounce'), 500);
}

function updateCart() {
    const count = cart.reduce((s, i) => s + i.quantity, 0);
    const total = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
    
    document.getElementById('cartCount').textContent = count;
    document.getElementById('cartTotal').textContent = total.toFixed(2).replace('.', ',') + '€';
    
    const items = document.getElementById('cartItems');
    if (cart.length === 0) {
        items.innerHTML = '<p>Tu carrito está vacío</p>';
    } else {
        items.innerHTML = cart.map(i => `
            <div class="cart-item">
                <div><strong>${i.name}</strong> - ${i.price.toFixed(2)}€</div>
                <div>
                    <button onclick="updateQuantity('${i.id}', -1)">-</button>
                    <span>${i.quantity}</span>
                    <button onclick="updateQuantity('${i.id}', 1)">+</button>
                    <button onclick="removeFromCart('${i.id}')">🗑️</button>
                </div>
            </div>
        `).join('');
    }
}

function updateQuantity(id, change) {
    const item = cart.find(i => i.id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) removeFromCart(id);
        else { updateCart(); saveCart(); }
    }
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    updateCart();
    saveCart();
}

function toggleCart() {
    const modal = document.getElementById('cartModal');
    if (modal.style.display === 'block') {
        modal.style.display = 'none';
    } else {
        if (cart.length === 0) {
            alert('Tu carrito está vacío');
            return;
        }
        modal.style.display = 'block';
    }
}

function generateAvailableDates() {
    const select = document.getElementById('deliveryDate');
    const today = new Date();
    const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    
    for (let i = 1; i <= 30; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + i);
        const day = date.getDay();
        
        if (day === 0 || day === 3 || day === 5 || day === 6) {
            const opt = document.createElement('option');
            opt.value = date.toISOString().split('T')[0];
            opt.textContent = `${days[day]} ${date.getDate()} ${months[date.getMonth()]}`;
            select.appendChild(opt);
        }
    }
}

function updatePickupLocation() {
    const date = new Date(document.getElementById('deliveryDate').value);
    const day = date.getDay();
    const info = document.getElementById('pickupInfo');
    
    if (day === 3) {
        info.innerHTML = `
            <div style="padding: 15px; background: #f9f5f0; border-radius: 10px; border-left: 4px solid #8B4513;">
                <strong style="font-size: 1.1rem; color: #8B4513;">📍 Cafetería Las Flores</strong>
                <p style="margin: 8px 0 5px 0;">Calle Francisco Gourié, 5 - Arucas</p>
                <p style="margin: 0; color: #666;"><strong>Horario:</strong> 7:30-13:00, 17:00-20:00</p>
            </div>
        `;
    } else {
        info.innerHTML = `
            <div style="padding: 15px; background: #f9f5f0; border-radius: 10px; border-left: 4px solid #8B4513;">
                <strong style="font-size: 1.1rem; color: #8B4513;">📍 Las Palmas de Gran Canaria</strong>
                <p style="margin: 8px 0 5px 0;">C/ La Naval, 36, 35008</p>
                <p style="margin: 0; color: #666;"><strong>Horario:</strong> 13:00-18:00</p>
            </div>
        `;
    }
    document.getElementById('pickupLocationGroup').style.display = 'block';
}

document.getElementById('submitOrder').addEventListener('click', function() {
    const name = document.getElementById('customerName').value;
    const email = document.getElementById('customerEmail').value;
    const phone = document.getElementById('customerPhone').value;
    const date = document.getElementById('deliveryDate').value;
    
    if (!name || !email || !phone || !date) {
        alert('Completa todos los campos');
        return;
    }
    
    const dateObj = new Date(date);
    const day = dateObj.getDay();
    const location = day === 3 ? 'Cafetería Las Flores, Calle Francisco Gourié 5, Arucas' : 'C/ La Naval, 36, 35008 Las Palmas de Gran Canaria';
    const time = day === 3 ? '7:30-13:00, 17:00-20:00' : '13:00-18:00';
    const total = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
    
    const orderData = {
        customerName: name,
        customerEmail: email,
        customerPhone: phone,
        deliveryDate: date,
        pickupLocation: location,
        pickupTime: time,
        items: cart.map(i => ({name: i.name, price: i.price, quantity: i.quantity})),
        total: total.toFixed(2)
    };
    
    // Guardar pedido primero (SIN enviar email todavía)
    fetch('<?php echo get_template_directory_uri(); ?>/save-order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(orderData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Pedido guardado, ahora redirigir a pasarela de pago
            const paymentForm = document.createElement('form');
            paymentForm.method = 'POST';
            paymentForm.action = '<?php echo get_template_directory_uri(); ?>/redirect-payment.php';
            
            const orderIdInput = document.createElement('input');
            orderIdInput.type = 'hidden';
            orderIdInput.name = 'orderId';
            orderIdInput.value = data.orderId;
            paymentForm.appendChild(orderIdInput);
            
            const totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'total';
            totalInput.value = total.toFixed(2);
            paymentForm.appendChild(totalInput);
            
            const nameInput = document.createElement('input');
            nameInput.type = 'hidden';
            nameInput.name = 'customerName';
            nameInput.value = name;
            paymentForm.appendChild(nameInput);
            
            const emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.name = 'customerEmail';
            emailInput.value = email;
            paymentForm.appendChild(emailInput);
            
            document.body.appendChild(paymentForm);
            paymentForm.submit();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(e => {
        console.error('Error:', e);
        alert('Error al enviar el pedido.');
    });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
