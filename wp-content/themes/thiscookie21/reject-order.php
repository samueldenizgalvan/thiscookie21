<?php
/**
 * Rechazar Pedido y Enviar Email de Cancelación
 */

// Cargar WordPress
require_once(__DIR__ . '/../../../wp-load.php');

// Verificar que sea administrador
if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url('/admin-pedidos/'));
    exit;
}

// Verificar que llegue el orderId
if (!isset($_POST['orderId'])) {
    wp_redirect(home_url('/admin-pedidos/'));
    exit;
}

$orderId = sanitize_text_field($_POST['orderId']);
$pendingFile = __DIR__ . '/orders/pending/' . $orderId . '.json';

// Verificar que el archivo existe
if (!file_exists($pendingFile)) {
    wp_redirect(home_url('/admin-pedidos/?error=not_found'));
    exit;
}

// Cargar el pedido
$order = json_decode(file_get_contents($pendingFile), true);

// Actualizar estado a rechazado y añadir fecha de rechazo
$order['status'] = 'rejected';
$order['rejectedDate'] = date('Y-m-d H:i:s');

// Guardar en historial mensual
$monthYear = date('Y-m');
$historyFile = __DIR__ . '/orders/history/monthly-' . $monthYear . '.json';

$history = [];
if (file_exists($historyFile)) {
    $history = json_decode(file_get_contents($historyFile), true);
    if (!is_array($history)) {
        $history = [];
    }
}

// Añadir pedido al historial
$history[] = $order;
file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Eliminar de pendientes
unlink($pendingFile);

// Enviar email de rechazo al cliente
send_order_rejection_email($order);

// Redirigir de vuelta al panel con mensaje de éxito
wp_redirect(home_url('/admin-pedidos/?rejected=true'));
exit;

/**
 * Enviar email de rechazo de pedido
 */
function send_order_rejection_email($order) {
    // Extraer datos del cliente
    $customerName = '';
    $customerEmail = '';
    
    if (isset($order['customer'])) {
        $customerName = $order['customer']['name'] ?? '';
        $customerEmail = $order['customer']['email'] ?? '';
    } else {
        $customerName = $order['customerName'] ?? '';
        $customerEmail = $order['customerEmail'] ?? '';
    }
    
    if (empty($customerEmail)) {
        error_log('No se puede enviar email de rechazo: email vacío');
        return false;
    }
    
    // Configuración de PHPMailer
    require_once(ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php');
    require_once(ABSPATH . 'wp-includes/PHPMailer/SMTP.php');
    require_once(ABSPATH . 'wp-includes/PHPMailer/Exception.php');
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configuración SMTP de SendGrid
        $mail->isSMTP();
        $mail->Host = 'smtp.sendgrid.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'apikey';
        $mail->Password = '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';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Remitente y destinatario
        $mail->setFrom('pedidosthiscookie21@gmail.com', 'ThisCookie21');
        $mail->addAddress($customerEmail, $customerName);
        
        // Contenido del email
        $mail->isHTML(true);
        $mail->Subject = '❌ Pedido No Disponible - ThisCookie21';
        
        $orderDate = new DateTime($order['deliveryDate']);
        $formattedDate = $orderDate->format('d/m/Y');
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #fff; padding: 30px; border: 2px solid #dc3545; border-top: none; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 0.9em; }
                .info-box { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0; border-radius: 5px; }
                .btn { display: inline-block; background: #8B4513; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1 style="margin: 0;">❌ Pedido No Disponible</h1>
                </div>
                <div class="content">
                    <p>Hola <strong>' . htmlspecialchars($customerName) . '</strong>,</p>
                    
                    <p>Lamentamos informarte que <strong>no podemos confirmar tu pedido</strong> para la fecha solicitada.</p>
                    
                    <div class="info-box">
                        <strong>📦 Número de pedido:</strong> ' . htmlspecialchars($order['id']) . '<br>
                        <strong>📅 Fecha solicitada:</strong> ' . $formattedDate . '<br>
                        <strong>💰 Importe:</strong> ' . number_format($order['total'], 2, ',', '.') . '€
                    </div>
                    
                    <p><strong>Motivo:</strong> No disponemos de stock suficiente o no podemos atender la fecha solicitada.</p>
                    
                    <p>🔄 <strong>¿Qué puedes hacer?</strong></p>
                    <ul>
                        <li>Realizar un nuevo pedido para otra fecha</li>
                        <li>Contactarnos para buscar una alternativa</li>
                    </ul>
                    
                    <p>💳 <strong>Reembolso:</strong> Si realizaste el pago, te devolveremos el importe en los próximos 3-5 días laborables.</p>
                    
                    <p>Sentimos las molestias. Esperamos poder atenderte en otra ocasión.</p>
                    
                    <a href="' . home_url() . '" class="btn">🍪 Hacer Nuevo Pedido</a>
                </div>
                <div class="footer">
                    <p>ThisCookie21 - Galletas Artesanales</p>
                    <p>📧 pedidosthiscookie21@gmail.com</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        $mail->send();
        error_log('Email de rechazo enviado a: ' . $customerEmail);
        return true;
        
    } catch (Exception $e) {
        error_log('Error al enviar email de rechazo: ' . $mail->ErrorInfo);
        return false;
    }
}
