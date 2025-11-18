<?php
/**
 * Panel de Administrador - Ver Pedidos
 * Template Name: Admin - Pedidos
 */

// Verificar que el usuario esté autenticado como administrador
if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(home_url('/login/'));
    exit;
}

// Función helper para obtener datos del cliente (compatible con estructura antigua y nueva)
function get_customer_data($order, $field) {
    // Estructura nueva: $order['customer']['name']
    if (isset($order['customer']) && isset($order['customer'][$field])) {
        return $order['customer'][$field];
    }
    // Estructura antigua: $order['customerName']
    $oldField = 'customer' . ucfirst($field);
    if (isset($order[$oldField])) {
        return $order[$oldField];
    }
    return 'N/A';
}

get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Pedidos - ThisCookie21</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #8B4513;
            border-bottom: 3px solid #D2691E;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
        }
        .tab {
            padding: 12px 24px;
            background: #f5f5f5;
            border: none;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
            color: #666;
            transition: all 0.3s;
        }
        .tab.active {
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .orders-table th {
            background: #8B4513;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .orders-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .orders-table tr:hover {
            background: #f9f5f0;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .order-details {
            font-size: 0.9rem;
            color: #666;
        }
        .btn-view {
            background: #8B4513;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-view:hover {
            background: #D2691E;
        }
        .btn-confirm {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-confirm:hover {
            background: #20c997;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-reject:hover {
            background: #c82333;
        }
        .search-box {
            margin-bottom: 20px;
            padding: 12px;
            width: 100%;
            max-width: 400px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .filter-group {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .filter-group select {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .order-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .order-modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 2rem;
            cursor: pointer;
            color: #999;
        }
        .modal-close:hover {
            color: #333;
        }
        .product-list {
            list-style: none;
            padding: 0;
        }
        .product-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        /* RESPONSIVE para móvil */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .admin-container {
                padding: 15px;
            }
            h1 {
                font-size: 1.5rem;
                padding-bottom: 10px;
            }
            .stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .stat-card {
                padding: 15px;
            }
            .stat-card h3 {
                font-size: 1.5rem;
            }
            .stat-card p {
                font-size: 0.85rem;
            }
            .tabs {
                flex-direction: column;
                gap: 5px;
            }
            .tab {
                padding: 10px;
                font-size: 0.9rem;
            }
            
            /* Convertir tabla a diseño de tarjetas en móvil */
            .orders-table {
                border: 0;
            }
            .orders-table thead {
                display: none;
            }
            .orders-table tbody {
                display: block;
            }
            .orders-table tr {
                display: block;
                margin-bottom: 15px;
                border: 2px solid #ddd;
                border-radius: 8px;
                background: white;
                padding: 10px;
            }
            .orders-table td {
                display: block;
                text-align: left;
                padding: 8px 5px;
                border: none;
                font-size: 0.9rem;
            }
            .orders-table td:before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                color: #8B4513;
                margin-bottom: 5px;
            }
            
            .btn-view,
            .btn-confirm,
            .btn-reject {
                padding: 8px 12px;
                font-size: 0.85rem;
                margin: 5px 5px 5px 0;
                display: inline-block;
                width: auto;
            }
            .search-box {
                font-size: 0.9rem;
            }
            .modal-content {
                max-width: 95%;
                padding: 20px;
            }
            .order-details {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .stat-card h3 {
                font-size: 1.2rem;
            }
            .orders-table {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
            <h1 style="margin: 0;">🍪 Panel de Pedidos - ThisCookie21</h1>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="<?php echo home_url('/productos/'); ?>" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;">
                    🍪 Productos
                </a>
                <a href="<?php echo home_url('/ajustes/'); ?>" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;">
                    ⚙️ Ajustes
                </a>
                <a href="<?php echo home_url('/'); ?>" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;">
                    🏠 Inicio
                </a>
                <a href="<?php echo home_url('/login/?logout=1'); ?>" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;">
                    🚪 Salir
                </a>
            </div>
        </div>

        <?php
        // Cargar pedidos pendientes (SOLO los que están PAGADOS pero NO confirmados NI rechazados)
        $pendingDir = __DIR__ . '/orders/pending/';
        $pendingOrders = [];
        $allPendingFiles = [];
        
        if (is_dir($pendingDir)) {
            $files = glob($pendingDir . '*.json');
            foreach ($files as $file) {
                $order = json_decode(file_get_contents($file), true);
                $allPendingFiles[] = $order; // Guardar todos para "Todos los pedidos"
                
                // FILTRO: Solo mostrar en "Pendientes" si está pagado y no confirmado ni rechazado
                $paymentStatus = $order['paymentStatus'] ?? '';
                $orderStatus = $order['status'] ?? 'pending';
                if ($paymentStatus === 'paid' && $orderStatus !== 'confirmed' && $orderStatus !== 'rejected') {
                    $pendingOrders[] = $order;
                }
            }
        }

        // Cargar historial de pedidos del mes actual (CONFIRMADOS y RECHAZADOS)
        $monthYear = date('Y-m');
        $monthFile = __DIR__ . '/orders/history/monthly-' . $monthYear . '.json';
        $historyOrders = [];
        $confirmedOrders = [];
        
        if (file_exists($monthFile)) {
            $historyOrders = json_decode(file_get_contents($monthFile), true);
            if (!is_array($historyOrders)) {
                $historyOrders = [];
            }
            // Filtrar solo confirmados para la pestaña "Confirmados"
            $confirmedOrders = array_filter($historyOrders, function($o) {
                return ($o['status'] ?? '') === 'confirmed';
            });
        }

        // TODOS los pedidos (pendientes sin pagar + pagados + confirmados + rechazados)
        $allOrders = array_merge($allPendingFiles, $historyOrders);

        // Estadísticas
        $totalRevenue = array_reduce($confirmedOrders, function($sum, $o) {
            return $sum + floatval($o['total']);
        }, 0);
        ?>

        <div class="stats">
            <div class="stat-card">
                <h3><?php echo count($pendingOrders); ?></h3>
                <p>Pendientes de Confirmar</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($allOrders); ?></h3>
                <p>Total Este Mes</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($confirmedOrders); ?></h3>
                <p>Pedidos Confirmados</p>
            </div>
            <div class="stat-card">
                <h3><?php echo number_format($totalRevenue, 2); ?>€</h3>
                <p>Ingresos del Mes</p>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="showTab('pending')">📦 Pendientes</button>
            <button class="tab" onclick="showTab('all')">📋 Todos los Pedidos</button>
            <button class="tab" onclick="showTab('confirmed')">✅ Confirmados</button>
        </div>

        <!-- Tab: Pedidos Pendientes de Confirmar -->
        <div id="tab-pending" class="tab-content active">
            <input type="text" class="search-box" id="search-pending" placeholder="🔍 Buscar por nombre, email o ID..." onkeyup="filterOrders('pending')">
            
            <?php if (empty($pendingOrders)): ?>
                <div class="empty-state">
                    <h3>No hay pedidos pendientes de confirmar</h3>
                    <p>Los pedidos pagados aparecerán aquí esperando tu confirmación</p>
                </div>
            <?php else: ?>
                <table class="orders-table" id="table-pending">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>ID Pedido</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Recogida</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($pendingOrders) as $order): ?>
                        <tr>
                            <td data-label="Fecha"><?php echo date('d/m/Y H:i', strtotime($order['date'])); ?></td>
                            <td data-label="ID Pedido"><code><?php echo $order['id']; ?></code></td>
                            <td data-label="Cliente">
                                <strong><?php echo get_customer_data($order, 'name'); ?></strong><br>
                                <span class="order-details">
                                    📧 <?php echo get_customer_data($order, 'email'); ?><br>
                                    📱 <?php echo get_customer_data($order, 'phone'); ?>
                                </span>
                            </td>
                            <td data-label="Productos">
                                <ul style="margin: 0; padding-left: 15px; font-size: 0.85rem;">
                                    <?php foreach ($order['items'] as $item): ?>
                                    <li><?php echo esc_html($item['name']); ?> x<?php echo $item['quantity']; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td data-label="Recogida">
                                <strong><?php echo date('d/m/Y', strtotime($order['deliveryDate'])); ?></strong><br>
                                <span class="order-details"><?php echo $order['pickupLocation']; ?></span>
                            </td>
                            <td data-label="Total"><strong><?php echo number_format($order['total'], 2); ?>€</strong></td>
                            <td data-label="Acciones">
                                <button class="btn-view" onclick="showOrderDetails(<?php echo htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8'); ?>)">
                                    Ver Detalles
                                </button>
                                <form method="POST" action="<?php echo get_template_directory_uri(); ?>/confirm-order.php" style="display: inline-block; margin-left: 5px;">
                                    <input type="hidden" name="orderId" value="<?php echo $order['id']; ?>">
                                    <button type="submit" class="btn-confirm" onclick="return confirm('¿Confirmar este pedido y enviar email al cliente?')">
                                        ✅ Confirmar
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo get_template_directory_uri(); ?>/reject-order.php" style="display: inline-block; margin-left: 5px;">
                                    <input type="hidden" name="orderId" value="<?php echo $order['id']; ?>">
                                    <button type="submit" class="btn-reject" onclick="return confirm('¿Rechazar este pedido y notificar al cliente?')">
                                        ❌ Rechazar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Tab: Todos los Pedidos -->
        <div id="tab-all" class="tab-content">
            <input type="text" class="search-box" id="search-all" placeholder="🔍 Buscar..." onkeyup="filterOrders('all')">
            
            <?php if (empty($allOrders)): ?>
                <div class="empty-state">
                    <h3>No hay pedidos este mes</h3>
                </div>
            <?php else: ?>
                <table class="orders-table" id="table-all">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>ID Pedido</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado Pago</th>
                            <th>Estado Pedido</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($allOrders) as $order): ?>
                        <tr>
                            <td data-label="Fecha"><?php echo date('d/m/Y H:i', strtotime($order['date'])); ?></td>
                            <td data-label="ID Pedido"><code><?php echo $order['id']; ?></code></td>
                            <td data-label="Cliente"><strong><?php echo get_customer_data($order, 'name'); ?></strong></td>
                            <td data-label="Total"><strong><?php echo number_format($order['total'], 2); ?>€</strong></td>
                            <td data-label="Estado Pago">
                                <?php
                                $paymentStatus = $order['paymentStatus'] ?? 'unpaid';
                                $paymentLabels = [
                                    'paid' => '✅ Pagado',
                                    'unpaid' => '❌ No Pagado',
                                    'cancelled' => '🚫 Cancelado'
                                ];
                                $paymentClass = 'status-' . $paymentStatus;
                                ?>
                                <span class="status-badge <?php echo $paymentClass; ?>">
                                    <?php echo $paymentLabels[$paymentStatus] ?? '❓ Desconocido'; ?>
                                </span>
                            </td>
                            <td data-label="Estado Pedido">
                                <?php
                                $orderStatus = $order['status'] ?? 'pending';
                                $statusLabels = [
                                    'confirmed' => '✅ Confirmado',
                                    'rejected' => '❌ Rechazado',
                                    'pending' => '⏳ Pendiente'
                                ];
                                $statusClass = $orderStatus === 'confirmed' ? 'status-paid' : ($orderStatus === 'rejected' ? 'status-failed' : 'status-pending');
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusLabels[$orderStatus] ?? '❓ Desconocido'; ?>
                                </span>
                            </td>
                            <td data-label="Acciones">
                                <button class="btn-view" onclick="showOrderDetails(<?php echo htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8'); ?>)">
                                    Ver Detalles
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Tab: Solo Confirmados -->
        <div id="tab-confirmed" class="tab-content">
            <input type="text" class="search-box" id="search-confirmed" placeholder="🔍 Buscar..." onkeyup="filterOrders('confirmed')">
            
            <?php if (empty($confirmedOrders)): ?>
                <div class="empty-state">
                    <h3>No hay pedidos confirmados aún</h3>
                    <p>Los pedidos confirmados aparecerán aquí</p>
                </div>
            <?php else: ?>
                <table class="orders-table" id="table-confirmed">
                    <thead>
                        <tr>
                            <th>Fecha Confirmación</th>
                            <th>ID Pedido</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Recogida</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($confirmedOrders) as $order): ?>
                        <tr>
                            <td data-label="Fecha Confirmación"><?php echo date('d/m/Y H:i', strtotime($order['confirmedDate'] ?? $order['date'])); ?></td>
                            <td data-label="ID Pedido"><code><?php echo $order['id']; ?></code></td>
                            <td data-label="Cliente">
                                <strong><?php echo get_customer_data($order, 'name'); ?></strong><br>
                                <span class="order-details">
                                    📧 <?php echo get_customer_data($order, 'email'); ?><br>
                                    📱 <?php echo get_customer_data($order, 'phone'); ?>
                                </span>
                            </td>
                            <td data-label="Productos">
                                <ul style="margin: 0; padding-left: 15px; font-size: 0.85rem;">
                                    <?php foreach ($order['items'] as $item): ?>
                                    <li><?php echo esc_html($item['name']); ?> x<?php echo $item['quantity']; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td data-label="Recogida">
                                <strong><?php echo date('d/m/Y', strtotime($order['deliveryDate'])); ?></strong><br>
                                <span class="order-details"><?php echo $order['pickupLocation']; ?></span>
                            </td>
                            <td data-label="Total"><strong><?php echo number_format($order['total'], 2); ?>€</strong></td>
                            <td data-label="Acciones">
                                <button class="btn-view" onclick="showOrderDetails(<?php echo htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8'); ?>)">
                                    Ver Detalles
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para ver detalles del pedido -->
    <div id="orderModal" class="order-modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todos los tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Mostrar el tab seleccionado
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function filterOrders(tableId) {
            const searchValue = document.getElementById('search-' + tableId).value.toLowerCase();
            const table = document.getElementById('table-' + tableId);
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            }
        }

        function showOrderDetails(order) {
            const modal = document.getElementById('orderModal');
            const modalBody = document.getElementById('modalBody');
            
            let productsHtml = '<ul class="product-list">';
            order.items.forEach(item => {
                productsHtml += `
                    <li>
                        <span><strong>${item.name}</strong> (x${item.quantity})</span>
                        <span>${(item.price * item.quantity).toFixed(2)}€</span>
                    </li>
                `;
            });
            productsHtml += '</ul>';
            
            const customerName = order.customer?.name || order.customerName || 'N/A';
            const customerEmail = order.customer?.email || order.customerEmail || 'N/A';
            const customerPhone = order.customer?.phone || order.customerPhone || 'N/A';
            const isPaid = order.paymentStatus === 'paid';
            const authCode = order.authorisationCode || 'N/A';
            
            modalBody.innerHTML = `
                <h2>Detalles del Pedido</h2>
                <p><strong>ID:</strong> ${order.id}</p>
                <p><strong>Fecha:</strong> ${new Date(order.date).toLocaleString('es-ES')}</p>
                <hr>
                <h3>Cliente</h3>
                <p><strong>Nombre:</strong> ${customerName}</p>
                <p><strong>Email:</strong> ${customerEmail}</p>
                <p><strong>Teléfono:</strong> ${customerPhone}</p>
                <hr>
                <h3>Recogida</h3>
                <p><strong>Fecha:</strong> ${new Date(order.deliveryDate).toLocaleDateString('es-ES')}</p>
                <p><strong>Lugar:</strong> ${order.pickupLocation}</p>
                <hr>
                <h3>Productos</h3>
                ${productsHtml}
                <hr>
                <h3 style="text-align: right;">Total: ${order.total}€</h3>
                ${isPaid ? `
                    <p style="color: green;"><strong>✅ PAGADO</strong></p>
                    <p><small>Código de autorización: ${authCode}</small></p>
                ` : ''}
            `;
            
            modal.classList.add('active');
        }

        function closeModal() {
            document.getElementById('orderModal').classList.remove('active');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Auto-refresh cada 60 segundos
        setTimeout(function() {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
