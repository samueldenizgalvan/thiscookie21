<?php
/**
 * Guardar pedidos en JSON y enviar emails
 */

// Cargar WordPress
require_once(__DIR__ . '/../../../wp-load.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Validar email
if (empty($data['customerEmail']) || !filter_var($data['customerEmail'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

// Generar ID único
$orderId = 'ORDER-' . date('Ymd') . '-' . uniqid();
$orderDate = date('Y-m-d H:i:s');

// Preparar pedido
$order = [
    'id' => $orderId,
    'date' => $orderDate,
    'status' => 'pending',
    'customer' => [
        'name' => $data['customerName'],
        'phone' => $data['customerPhone'],
        'email' => $data['customerEmail']
    ],
    'deliveryDate' => $data['deliveryDate'],
    'pickupLocation' => $data['pickupLocation'],
    'pickupTime' => $data['pickupTime'] ?? '',
    'items' => $data['items'],
    'total' => $data['total']
];

// Guardar en JSON
$ordersDir = __DIR__ . '/orders/pending/';
if (!file_exists($ordersDir)) {
    mkdir($ordersDir, 0755, true);
}

$filename = $ordersDir . $orderId . '.json';
file_put_contents($filename, json_encode($order, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Registro mensual
$monthYear = date('Y-m');
$historyDir = __DIR__ . '/orders/history/';
if (!file_exists($historyDir)) {
    mkdir($historyDir, 0755, true);
}

$monthFile = $historyDir . 'monthly-' . $monthYear . '.json';
$monthlyData = [];
if (file_exists($monthFile)) {
    $monthlyData = json_decode(file_get_contents($monthFile), true) ?: [];
}
$monthlyData[] = $order;
file_put_contents($monthFile, json_encode($monthlyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// NO enviar email aquí - se enviará después del pago exitoso
// El email se envía desde tpv-success.php

echo json_encode([
    'success' => true,
    'message' => 'Pedido guardado',
    'orderId' => $orderId
]);
