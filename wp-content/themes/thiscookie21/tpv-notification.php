<?php
/**
 * Notificación del TPV (webhook)
 * Actualiza el estado del pedido según la respuesta del banco
 */

require_once(__DIR__ . '/redsys-tpv.php');
require_once(__DIR__ . '/../../../wp-load.php');
require_once(__DIR__ . '/email-functions.php');

// Obtener datos del POST
$version = $_POST['Ds_SignatureVersion'] ?? '';
$parameters = $_POST['Ds_MerchantParameters'] ?? '';
$signature = $_POST['Ds_Signature'] ?? '';

// Log para debugging
$logFile = __DIR__ . '/orders/tpv-log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Notificación recibida\n", FILE_APPEND);
file_put_contents($logFile, "Parameters: $parameters\n", FILE_APPEND);
file_put_contents($logFile, "Signature: $signature\n\n", FILE_APPEND);

// Verificar firma
$redsys = new RedsysTPV(true);
if (!$redsys->verifySignature($parameters, $signature)) {
    file_put_contents($logFile, "ERROR: Firma inválida\n\n", FILE_APPEND);
    http_response_code(400);
    exit;
}

// Decodificar parámetros
$decodedParams = json_decode(base64_decode($parameters), true);

$orderNumber = $decodedParams['Ds_Order'] ?? '';
$response = $decodedParams['Ds_Response'] ?? '';
$amount = $decodedParams['Ds_Amount'] ?? '';
$authorisationCode = $decodedParams['Ds_AuthorisationCode'] ?? '';

file_put_contents($logFile, "Order: $orderNumber, Response: $response, Amount: $amount\n\n", FILE_APPEND);

// Buscar el pedido
$orderId = 'ORDER-' . $orderNumber;
$pendingFile = __DIR__ . '/orders/pending/' . $orderId . '.json';

if (file_exists($pendingFile)) {
    $order = json_decode(file_get_contents($pendingFile), true);
    
    // Verificar si el pago fue exitoso (códigos 0000-0099 son exitosos)
    $responseCode = intval($response);
    
    if ($responseCode >= 0 && $responseCode <= 99) {
        // Pago exitoso
        $order['status'] = 'pending';
        $order['paymentStatus'] = 'paid';
        $order['paymentDate'] = date('Y-m-d H:i:s');
        $order['authorisationCode'] = $authorisationCode;

        // Guardar actualización
        file_put_contents($pendingFile, json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        file_put_contents($logFile, "SUCCESS: Pedido $orderId marcado como pagado\n\n", FILE_APPEND);

        // 🎯 ENVIAR EMAIL DE CONFIRMACIÓN DESPUÉS DEL PAGO EXITOSO
        try {
            $emailSent = send_order_confirmation_email($order);
            if ($emailSent) {
                file_put_contents($logFile, "SUCCESS: Email de confirmación enviado a {$order['customerEmail']}\n\n", FILE_APPEND);
            } else {
                file_put_contents($logFile, "ERROR: No se pudo enviar el email de confirmación\n\n", FILE_APPEND);
            }
        } catch (Exception $e) {
            file_put_contents($logFile, "ERROR al enviar email: " . $e->getMessage() . "\n\n", FILE_APPEND);
        }
    } else {
        // Pago fallido
        $order['status'] = 'payment_failed';
        $order['paymentStatus'] = 'failed';
        $order['paymentErrorCode'] = $response;
        
        file_put_contents($pendingFile, json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        file_put_contents($logFile, "ERROR: Pago fallido para $orderId. Código: $response\n\n", FILE_APPEND);
    }
}

http_response_code(200);
echo "OK";
