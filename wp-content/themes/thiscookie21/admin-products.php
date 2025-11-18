<?php
/*
Template Name: Admin - Productos
*/

// Verificar autenticación
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    wp_redirect(home_url('/login/'));
    exit;
}

$products_file = get_template_directory() . '/orders/config/products.json';

// Crear archivo si no existe
if (!file_exists($products_file)) {
    $default_products = [
        [
            'id' => 1,
            'name' => 'Cookie Clásica',
            'price' => 2.90,
            'image' => 'cookie-clasica.jpg',
            'description' => 'Cookie clásica con chips de chocolate',
            'stock' => 100,
            'active' => true
        ]
    ];
    file_put_contents($products_file, json_encode($default_products, JSON_PRETTY_PRINT));
}

// Procesar acciones
$message = '';
$message_type = '';

// Añadir producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $products = json_decode(file_get_contents($products_file), true);
    
    $new_id = empty($products) ? 1 : max(array_column($products, 'id')) + 1;
    
    $new_product = [
        'id' => $new_id,
        'name' => sanitize_text_field($_POST['name']),
        'price' => floatval($_POST['price']),
        'image' => sanitize_text_field($_POST['image']),
        'description' => sanitize_textarea_field($_POST['description']),
        'stock' => intval($_POST['stock']),
        'active' => isset($_POST['active'])
    ];
    
    $products[] = $new_product;
    file_put_contents($products_file, json_encode($products, JSON_PRETTY_PRINT));
    
    $message = "Producto añadido correctamente";
    $message_type = "success";
}

// Editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $products = json_decode(file_get_contents($products_file), true);
    $product_id = intval($_POST['product_id']);
    
    foreach ($products as &$product) {
        if ($product['id'] === $product_id) {
            $product['name'] = sanitize_text_field($_POST['name']);
            $product['price'] = floatval($_POST['price']);
            $product['image'] = sanitize_text_field($_POST['image']);
            $product['description'] = sanitize_textarea_field($_POST['description']);
            $product['stock'] = intval($_POST['stock']);
            $product['active'] = isset($_POST['active']);
            break;
        }
    }
    
    file_put_contents($products_file, json_encode($products, JSON_PRETTY_PRINT));
    
    $message = "Producto actualizado correctamente";
    $message_type = "success";
}

// Eliminar producto
if (isset($_GET['delete'])) {
    $products = json_decode(file_get_contents($products_file), true);
    $product_id = intval($_GET['delete']);
    
    $products = array_filter($products, function($product) use ($product_id) {
        return $product['id'] !== $product_id;
    });
    
    file_put_contents($products_file, json_encode(array_values($products), JSON_PRETTY_PRINT));
    
    $message = "Producto eliminado correctamente";
    $message_type = "success";
}

// Cargar productos
$products = json_decode(file_get_contents($products_file), true);

get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - ThisCookie21</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-orders {
            background: #007bff;
            color: white;
        }

        .btn-orders:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .btn-settings {
            background: #6c757d;
            color: white;
        }

        .btn-settings:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-logout {
            background: #dc3545;
            color: white;
        }

        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-add {
            background: #28a745;
            color: white;
        }

        .btn-add:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .products-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .products-header h2 {
            color: #333;
            font-size: 22px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
            position: relative;
        }

        .product-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .product-card.inactive {
            opacity: 0.6;
            background: #f8f9fa;
        }

        .product-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .product-image {
            width: 100%;
            height: 150px;
            background: #f0f0f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 48px;
        }

        .product-name {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
        }

        .product-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .product-stock {
            font-size: 12px;
            color: #888;
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }

        .btn-edit {
            flex: 1;
            background: #ffc107;
            color: #000;
            padding: 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-edit:hover {
            background: #e0a800;
        }

        .btn-delete {
            flex: 1;
            background: #dc3545;
            color: white;
            padding: 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: #333;
            font-size: 22px;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .nav-buttons {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
                font-size: 12px;
                padding: 8px 12px;
            }

            .products-card {
                padding: 20px;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍪 Gestión de Productos</h1>
            <div class="nav-buttons">
                <a href="<?php echo home_url('/admin-pedidos/'); ?>" class="btn btn-orders">📦 Pedidos</a>
                <a href="<?php echo home_url('/ajustes/'); ?>" class="btn btn-settings">⚙️ Ajustes</a>
                <a href="<?php echo home_url('/'); ?>" class="btn btn-back">🏠 Inicio</a>
                <a href="<?php echo home_url('/login/?logout=1'); ?>" class="btn btn-logout">🚪 Salir</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="products-card">
            <div class="products-header">
                <h2>Catálogo de Productos (<?php echo count($products); ?>)</h2>
                <button class="btn btn-add" onclick="openAddModal()">➕ Añadir Producto</button>
            </div>

            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card <?php echo !$product['active'] ? 'inactive' : ''; ?>">
                        <span class="product-status <?php echo $product['active'] ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo $product['active'] ? '✓ Activo' : '✗ Inactivo'; ?>
                        </span>
                        
                        <div class="product-image">🍪</div>
                        
                        <div class="product-name"><?php echo esc_html($product['name']); ?></div>
                        <div class="product-price"><?php echo number_format($product['price'], 2); ?>€</div>
                        <div class="product-description"><?php echo esc_html($product['description']); ?></div>
                        <div class="product-stock">Stock: <?php echo $product['stock']; ?> unidades</div>
                        
                        <div class="product-actions">
                            <button class="btn-edit" onclick='openEditModal(<?php echo json_encode($product); ?>)'>
                                ✏️ Editar
                            </button>
                            <button class="btn-delete" onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo esc_js($product['name']); ?>')">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal Añadir Producto -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>➕ Añadir Producto</h2>
                <button class="btn-close" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="add_name">Nombre del Producto *</label>
                    <input type="text" id="add_name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="add_price">Precio (€) *</label>
                    <input type="number" id="add_price" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="add_image">Imagen (nombre del archivo)</label>
                    <input type="text" id="add_image" name="image" placeholder="cookie-clasica.jpg">
                </div>

                <div class="form-group">
                    <label for="add_description">Descripción</label>
                    <textarea id="add_description" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="add_stock">Stock (unidades) *</label>
                    <input type="number" id="add_stock" name="stock" min="0" value="100" required>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="add_active" name="active" checked>
                        <label for="add_active" style="margin-bottom: 0;">Producto activo</label>
                    </div>
                </div>

                <button type="submit" name="add_product" class="btn-submit">💾 Guardar Producto</button>
            </form>
        </div>
    </div>

    <!-- Modal Editar Producto -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Editar Producto</h2>
                <button class="btn-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <input type="hidden" id="edit_product_id" name="product_id">
                
                <div class="form-group">
                    <label for="edit_name">Nombre del Producto *</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="edit_price">Precio (€) *</label>
                    <input type="number" id="edit_price" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="edit_image">Imagen (nombre del archivo)</label>
                    <input type="text" id="edit_image" name="image">
                </div>

                <div class="form-group">
                    <label for="edit_description">Descripción</label>
                    <textarea id="edit_description" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_stock">Stock (unidades) *</label>
                    <input type="number" id="edit_stock" name="stock" min="0" required>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="edit_active" name="active">
                        <label for="edit_active" style="margin-bottom: 0;">Producto activo</label>
                    </div>
                </div>

                <button type="submit" name="edit_product" class="btn-submit">💾 Actualizar Producto</button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        function openEditModal(product) {
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_image').value = product.image;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_stock').value = product.stock;
            document.getElementById('edit_active').checked = product.active;
            
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        function confirmDelete(id, name) {
            if (confirm('¿Estás seguro de que quieres eliminar el producto "' + name + '"?')) {
                window.location.href = '?delete=' + id;
            }
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>

<?php get_footer(); ?>
