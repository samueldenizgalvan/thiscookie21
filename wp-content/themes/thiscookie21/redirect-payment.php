<?php
/**
 * Redirigir a la pasarela de pago TPV
 */

require_once(__DIR__ . '/../../../wp-load.php');
require_once(__DIR__ . '/redsys-tpv.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método no permitido');
}

// DEBUG: Ver qué datos están llegando
$logFile = __DIR__ . '/orders/redirect-log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - POST recibido:\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);

// Recibir datos del formulario
$orderId = isset($_POST['orderId']) ? sanitize_text_field($_POST['orderId']) : '';
$total = isset($_POST['total']) ? floatval($_POST['total']) : 0;
$customerName = isset($_POST['customerName']) ? sanitize_text_field($_POST['customerName']) : '';
$customerEmail = isset($_POST['customerEmail']) ? sanitize_email($_POST['customerEmail']) : '';

file_put_contents($logFile, "Procesados: orderId=$orderId, total=$total, name=$customerName, email=$customerEmail\n\n", FILE_APPEND);

if (!$orderId || $total <= 0) {
    file_put_contents($logFile, "ERROR: Datos inválidos - orderId vacío o total <= 0\n\n", FILE_APPEND);
    die('Datos del pedido inválidos - OrderID: ' . $orderId . ' - Total: ' . $total);
}

// Crear instancia de Redsys en modo TEST
$redsys = new RedsysTPV(true);

// Generar datos de pago usando el método de la clase
$paymentData = $redsys->generatePaymentForm($orderId, $total);

// DEBUG: Ver los parámetros generados
file_put_contents($logFile, "Payment Data generado:\n" . print_r($paymentData, true) . "\n\n", FILE_APPEND);

// Decodificar y ver los parámetros del merchant
$merchantParams = json_decode(base64_decode($paymentData['Ds_MerchantParameters']), true);
file_put_contents($logFile, "Merchant Parameters decodificados:\n" . print_r($merchantParams, true) . "\n\n", FILE_APPEND);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirigiendo a pago...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f5f0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .loader {
            text-align: center;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #8B4513;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader">
        <div class="spinner"></div>
        <p>Redirigiendo al pago seguro...</p>
        <p><small>Pedido: <?php echo htmlspecialchars($orderId); ?></small></p>
        <p><small>Total: <?php echo number_format($total, 2); ?>€</small></p>
    </div>

    <!-- Formulario automático para enviar al TPV -->
    <form id="paymentForm" action="<?php echo $paymentData['url']; ?>" method="POST">
        <input type="hidden" name="Ds_SignatureVersion" value="<?php echo $paymentData['Ds_SignatureVersion']; ?>">
        <input type="hidden" name="Ds_MerchantParameters" value="<?php echo $paymentData['Ds_MerchantParameters']; ?>">
        <input type="hidden" name="Ds_Signature" value="<?php echo $paymentData['Ds_Signature']; ?>">
    </form>

    <script>
        // Enviar formulario automáticamente después de 1 segundo
        setTimeout(function() {
            document.getElementById('paymentForm').submit();
        }, 1000);
    </script>
</body>
</html>
