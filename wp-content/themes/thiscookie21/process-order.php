<?php
/**
 * Procesar pedido - Guardar y enviar email
 */

// Permitir CORS para desarrollo local
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Si es OPTIONS, terminar aquí
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Cargar WordPress
require_once('../../../../../wp-load.php');

// Obtener datos del pedido
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Validar datos requeridos
$required_fields = ['nombre', 'telefono', 'email', 'fecha_recogida', 'lugar_recogida', 'productos', 'total'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Campo requerido: $field"]);
        exit;
    }
}

// Generar ID único del pedido
$order_id = 'ORDER-' . date('Ymd') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);

// Crear estructura del pedido
$order = [
    'id' => $order_id,
    'fecha_pedido' => date('Y-m-d H:i:s'),
    'estado' => 'pending',
    'cliente' => [
        'nombre' => sanitize_text_field($data['nombre']),
        'telefono' => sanitize_text_field($data['telefono']),
        'email' => sanitize_email($data['email'])
    ],
    'recogida' => [
        'fecha' => sanitize_text_field($data['fecha_recogida']),
        'lugar' => sanitize_text_field($data['lugar_recogida']),
        'horario' => sanitize_text_field($data['horario_recogida'] ?? '')
    ],
    'productos' => $data['productos'],
    'total' => floatval($data['total']),
    'pago' => [
        'metodo' => 'TPV',
        'estado' => 'pendiente',
        'referencia' => ''
    ]
];

// Guardar pedido en archivo JSON
$orders_dir = get_template_directory() . '/orders/pending';
if (!file_exists($orders_dir)) {
    mkdir($orders_dir, 0755, true);
}

$order_file = $orders_dir . '/' . $order_id . '.json';
file_put_contents($order_file, json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Enviar email de confirmación al cliente
$email_sent = false;
try {
    $email_sent = send_order_confirmation_email($order);
} catch (Exception $e) {
    error_log('Error al enviar email: ' . $e->getMessage());
}

// Responder con éxito
echo json_encode([
    'success' => true,
    'message' => 'Pedido creado correctamente',
    'order_id' => $order_id,
    'email_sent' => $email_sent
]);
exit;
