<?php
/**
 * Página de confirmación de pago exitoso
 */

// Cargar WordPress
require_once(__DIR__ . '/../../../wp-load.php');

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso - ThisCookie21</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f5f0;
            margin: 0;
            padding: 20px;
        }
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            line-height: 1.6;
            margin: 15px 0;
        }
        .order-number {
            background: #f9f5f0;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
            color: #8B4513;
        }
        .btn-home {
            display: inline-block;
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            font-weight: bold;
        }
        .btn-home:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <?php
    // Cargar funciones de email
    require_once(__DIR__ . '/email-functions.php');
    
    // Obtener el número de pedido de la URL
    $orderNumber = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
    
    // Si hay un pedido, cargarlo y enviar email
    if ($orderNumber) {
        $orderFile = __DIR__ . '/orders/pending/' . $orderNumber . '.json';
        
        if (file_exists($orderFile)) {
            $order = json_decode(file_get_contents($orderFile), true);
            
            // Marcar como pagado si aún no lo está
            if (!isset($order['paymentStatus']) || $order['paymentStatus'] !== 'paid') {
                $order['paymentStatus'] = 'paid';
                $order['paymentDate'] = date('Y-m-d H:i:s');
                $order['status'] = 'pending'; // Pendiente de recoger
                
                // Guardar el pedido actualizado
                file_put_contents($orderFile, json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                
                // 🎯 ENVIAR EMAIL DE FACTURA (NO CONFIRMACIÓN)
                try {
                    $emailSent = send_order_invoice_email($order);
                    $emailStatus = $emailSent ? 'enviado' : 'no enviado';
                } catch (Exception $e) {
                    $emailStatus = 'error: ' . $e->getMessage();
                }
            }
        }
    }
    ?>
    <script>
        // Limpiar el carrito después de un pago exitoso
        window.addEventListener('DOMContentLoaded', function() {
            localStorage.removeItem('cart');
            console.log('Carrito limpiado después de pago exitoso');
        });
    </script>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1>¡Pago Realizado con Éxito!</h1>
        <p>Hemos recibido tu pago correctamente.</p>
        <?php if ($orderNumber): ?>
        <div class="order-number">
            📦 Número de pedido: <?php echo $orderNumber; ?>
        </div>
        <?php endif; ?>
        <p><strong>⏳ Tu pedido está PENDIENTE de confirmación</strong></p>
        <p><strong>✉️ Te hemos enviado una factura por email</strong> con los detalles de tu pago.</p>
        <p>Revisaremos tu pedido y <strong>te enviaremos otro email de confirmación</strong> con la fecha y lugar de recogida en breve.</p>
        <p style="font-size: 0.9em; color: #666;">Por favor, revisa tu bandeja de entrada (y la carpeta de spam si no lo encuentras).</p>
        <a href="<?php echo home_url(); ?>" class="btn-home">🍪 Volver al Inicio</a>
    </div>
</body>
</html>
