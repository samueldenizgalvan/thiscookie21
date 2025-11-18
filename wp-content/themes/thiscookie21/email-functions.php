<?php
/**
 * EMAIL 1: Factura/Recibo después del pago
 * Se envía automáticamente cuando el pago es exitoso
 * El pedido queda PENDIENTE de confirmación del administrador
 */

function send_order_invoice_email($order) {
    // Configuración del email
    $customerEmail = isset($order['customer']['email']) ? $order['customer']['email'] : $order['customerEmail'];
    $customerName = isset($order['customer']['name']) ? $order['customer']['name'] : $order['customerName'];
    
    $to = $customerEmail;
    $subject = '🧾 Factura de Pago - Pedido Pendiente de Confirmación - ThisCookie21 #' . $order['id'];
    
    // Construir el mensaje HTML
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
                color: white;
                padding: 30px;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
            }
            .content {
                background: #fff;
                padding: 30px;
                border: 1px solid #ddd;
            }
            .warning-box {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
            }
            .order-info {
                background: #f9f5f0;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
            }
            .order-info h2 {
                color: #8B4513;
                margin-top: 0;
            }
            .product-list {
                list-style: none;
                padding: 0;
            }
            .product-list li {
                padding: 10px 0;
                border-bottom: 1px solid #eee;
            }
            .total {
                background: #8B4513;
                color: white;
                padding: 15px;
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer {
                background: #f5f5f5;
                padding: 20px;
                text-align: center;
                border-radius: 0 0 10px 10px;
                color: #666;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>🍪 ThisCookie21</h1>
            <p>Factura de Pago Recibido</p>
        </div>
        
        <div class="content">
            <div class="warning-box">
                <p style="margin: 0; font-weight: bold;">⏳ Tu pedido está PENDIENTE de confirmación</p>
                <p style="margin: 10px 0 0 0;">Hemos recibido tu pago correctamente. Revisaremos tu pedido y te enviaremos un email de confirmación en breve con todos los detalles.</p>
            </div>
            
            <div class="order-info">
                <h2>🧾 Datos de la Factura</h2>
                <p><strong>Número de pedido:</strong> ' . $order['id'] . '</p>
                <p><strong>Fecha del pago:</strong> ' . date('d/m/Y H:i', strtotime($order['date'])) . '</p>
                <p><strong>Cliente:</strong> ' . htmlspecialchars($customerName) . '</p>
                <p><strong>Estado:</strong> <span style="color: #ff8c00;">⏳ Pendiente de confirmación</span></p>
            </div>
            
            <h2>🛒 Productos</h2>
            <ul class="product-list">';
    
    foreach ($order['items'] as $item) {
        $message .= '
                <li>
                    <strong>' . htmlspecialchars($item['name']) . '</strong> × ' . $item['quantity'] . '
                    <span style="float: right;">' . number_format($item['price'], 2) . '€</span>
                </li>';
    }
    
    $message .= '
            </ul>
            
            <div class="total">
                💰 TOTAL PAGADO: ' . number_format($order['total'], 2) . '€
            </div>
            
            <p style="text-align: center; color: #28a745; font-weight: bold;">
                ✅ Pago recibido correctamente
            </p>
            
            <p><strong>Próximos pasos:</strong></p>
            <ol>
                <li>Revisaremos tu pedido</li>
                <li>Te enviaremos un email de confirmación con la fecha y lugar de recogida</li>
                <li>Prepararemos tus galletas con cariño 🍪</li>
            </ol>
            
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
        </div>
        
        <div class="footer">
            <p><strong>ThisCookie21 - Galletas Artesanales</strong></p>
            <p>Hechas con amor 🍪</p>
        </div>
    </body>
    </html>
    ';
    
    // Headers para HTML
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ThisCookie21 <pedidosthiscookie21@gmail.com>'
    );
    
    // Enviar email
    $sent = wp_mail($to, $subject, $message, $headers);
    
    return $sent;
}

/**
 * EMAIL 2: Confirmación definitiva del pedido
 * Se envía manualmente cuando el administrador confirma el pedido desde el panel
 */

function send_order_confirmation_email($order) {
    // Configuración del email
    $customerEmail = isset($order['customer']['email']) ? $order['customer']['email'] : $order['customerEmail'];
    $customerName = isset($order['customer']['name']) ? $order['customer']['name'] : $order['customerName'];
    $customerPhone = isset($order['customer']['phone']) ? $order['customer']['phone'] : $order['customerPhone'];
    
    $to = $customerEmail;
    $subject = '✅ PEDIDO CONFIRMADO - ThisCookie21 #' . $order['id'];
    
    // Construir el mensaje HTML
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .header {
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                color: white;
                padding: 30px;
                text-align: center;
                border-radius: 10px 10px 0 0;
            }
            .header h1 {
                margin: 0;
                font-size: 28px;
            }
            .content {
                background: #fff;
                padding: 30px;
                border: 1px solid #ddd;
            }
            .success-box {
                background: #d4edda;
                border-left: 4px solid #28a745;
                padding: 15px;
                margin: 20px 0;
            }
            .order-info {
                background: #f9f5f0;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
            }
            .order-info h2 {
                color: #8B4513;
                margin-top: 0;
            }
            .product-list {
                list-style: none;
                padding: 0;
            }
            .product-list li {
                padding: 10px 0;
                border-bottom: 1px solid #eee;
            }
            .total {
                background: #8B4513;
                color: white;
                padding: 15px;
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                border-radius: 5px;
                margin: 20px 0;
            }
            .pickup-info {
                background: #e7f3ff;
                padding: 20px;
                border-left: 4px solid #0066cc;
                margin: 20px 0;
            }
            .footer {
                background: #f5f5f5;
                padding: 20px;
                text-align: center;
                border-radius: 0 0 10px 10px;
                color: #666;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>🍪 ThisCookie21</h1>
            <p>¡Tu pedido ha sido CONFIRMADO!</p>
        </div>
        
        <div class="content">
            <div class="success-box">
                <p style="margin: 0; font-weight: bold; font-size: 18px;">✅ ¡PEDIDO CONFIRMADO!</p>
                <p style="margin: 10px 0 0 0;">Tu pedido ha sido aceptado y estamos preparando tus deliciosas galletas artesanales con todo el amor.</p>
            </div>
            
            <div class="order-info">
                <h2>📋 Detalles del Pedido</h2>
                <p><strong>Número de pedido:</strong> ' . $order['id'] . '</p>
                <p><strong>Fecha del pedido:</strong> ' . date('d/m/Y H:i', strtotime($order['date'])) . '</p>
                <p><strong>Cliente:</strong> ' . htmlspecialchars($customerName) . '</p>
                <p><strong>Teléfono:</strong> ' . htmlspecialchars($customerPhone) . '</p>
                <p><strong>Estado:</strong> <span style="color: #28a745;">✅ Confirmado</span></p>
            </div>
            
            <div class="pickup-info">
                <h2>📅 INFORMACIÓN DE RECOGIDA</h2>
                <p><strong>⏰ Fecha de recogida:</strong> ' . date('d/m/Y', strtotime($order['deliveryDate'])) . '</p>
                <p><strong>📍 Lugar de recogida:</strong></p>
                <p style="font-size: 16px;">' . nl2br(htmlspecialchars($order['pickupLocation'])) . '</p>
                <p><strong>🕐 Horario:</strong> ' . htmlspecialchars($order['pickupTime']) . '</p>
            </div>
            
            <h2>🛒 Productos</h2>
            <ul class="product-list">';
    
    foreach ($order['items'] as $item) {
        $message .= '
                <li>
                    <strong>' . htmlspecialchars($item['name']) . '</strong> × ' . $item['quantity'] . '
                    <span style="float: right;">' . number_format($item['price'], 2) . '€</span>
                </li>';
    }
    
    $message .= '
            </ul>
            
            <div class="total">
                💰 TOTAL: ' . number_format($order['total'], 2) . '€ (PAGADO)
            </div>
            
            <p style="text-align: center; font-size: 16px; margin: 30px 0;">
                <strong>🎉 ¡Nos vemos en la recogida!</strong><br>
                Tus galletas estarán listas y esperándote.
            </p>
            
            <p><strong>Recuerda:</strong></p>
            <ul>
                <li>Puntualidad: recoge tu pedido en el horario indicado</li>
                <li>Lleva contigo el número de pedido: <strong>' . $order['id'] . '</strong></li>
                <li>Si tienes algún problema, contáctanos lo antes posible</li>
            </ul>
            
            <p>¡Gracias por confiar en ThisCookie21! 🍪❤️</p>
        </div>
        
        <div class="footer">
            <p><strong>ThisCookie21 - Galletas Artesanales</strong></p>
            <p>Hechas con amor 🍪</p>
        </div>
    </body>
    </html>
    ';
    
    // Headers para HTML
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ThisCookie21 <pedidosthiscookie21@gmail.com>'
    );
    
    // Enviar email
    $sent = wp_mail($to, $subject, $message, $headers);
    
    return $sent;
}

// Función de notificación al admin (DESACTIVADA - Solo panel de admin)
// Los pedidos aparecen automáticamente en: WordPress Admin → Pedidos 🍪
function send_admin_notification_email($order) {
    // Esta función está desactivada intencionalmente
    // El administrador verá los pedidos en el panel de WordPress
    return true;
    
    /* CÓDIGO COMENTADO - Por si lo quieres activar en el futuro
function send_admin_notification_email_DESACTIVADA($order) {
    $admin_email = get_option('admin_email');
    $subject = '🔔 Nuevo Pedido - ThisCookie21 #' . $order['id'];
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .alert { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background: #8B4513; color: white; }
        </style>
    </head>
    <body>
        <h1>🔔 Nuevo Pedido Recibido</h1>
        
        <div class="alert">
            <strong>⚠️ Acción requerida:</strong> Preparar pedido para el ' . date('d/m/Y', strtotime($order['deliveryDate'])) . '
        </div>
        
        <table>
            <tr>
                <th>Campo</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td><strong>Número de pedido:</strong></td>
                <td>' . $order['id'] . '</td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong></td>
                <td>' . htmlspecialchars($order['customer']['name']) . '</td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong></td>
                <td>' . htmlspecialchars($order['customer']['phone']) . '</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>' . htmlspecialchars($order['customer']['email']) . '</td>
            </tr>
            <tr>
                <td><strong>Fecha de recogida:</strong></td>
                <td>' . date('d/m/Y', strtotime($order['deliveryDate'])) . '</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td><strong>' . number_format($order['total'], 2) . '€</strong></td>
            </tr>
        </table>
        
        <h2>🛒 Productos</h2>
        <ul>';
    
    foreach ($order['items'] as $item) {
        $message .= '<li>' . htmlspecialchars($item['name']) . ' × ' . $item['quantity'] . ' - ' . number_format($item['price'], 2) . '€</li>';
    }
    
    $message .= '
        </ul>
        
        <p><a href="' . admin_url('admin.php?page=thiscookie21-orders') . '" 
           style="background: #8B4513; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
           Ver en Panel de Administración
        </a></p>
    </body>
    </html>
    ';
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Sistema ThisCookie21 <sistema@thiscookie21.com>'
    );
    
    wp_mail($admin_email, $subject, $message, $headers);
    */
}
