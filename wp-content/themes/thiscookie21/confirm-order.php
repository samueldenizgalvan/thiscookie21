<?php
/**
 * Confirmar Pedido y Enviar Email de Confirmación
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

// Actualizar estado a confirmado y añadir fecha de confirmación
$order['status'] = 'confirmed';
$order['confirmedDate'] = date('Y-m-d H:i:s');

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
file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));

// Eliminar de pendientes
unlink($pendingFile);

// Cargar función de email
require_once(__DIR__ . '/email-functions.php');

// Enviar email de confirmación al cliente
send_order_confirmation_email($order);

// Redirigir de vuelta al panel con mensaje de éxito
wp_redirect(home_url('/admin-pedidos/?confirmed=true'));
exit;
