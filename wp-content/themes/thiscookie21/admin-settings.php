<?php
/*
Template Name: Admin - Ajustes
*/

// Verificar autenticación
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    wp_redirect(home_url('/login/'));
    exit;
}

$config_file = get_template_directory() . '/orders/config/settings.json';

// Procesar guardado de configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $settings = [
        'email_from' => sanitize_email($_POST['email_from']),
        'email_from_name' => sanitize_text_field($_POST['email_from_name']),
        'sendgrid_api_key' => sanitize_text_field($_POST['sendgrid_api_key']),
        'whatsapp_phone' => sanitize_text_field($_POST['whatsapp_phone']),
        'redsys_merchant' => sanitize_text_field($_POST['redsys_merchant']),
        'redsys_terminal' => sanitize_text_field($_POST['redsys_terminal']),
        'redsys_secret' => sanitize_text_field($_POST['redsys_secret']),
        'redsys_test_mode' => isset($_POST['redsys_test_mode']) ? true : false,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($config_file, json_encode($settings, JSON_PRETTY_PRINT));
    $success_message = "Configuración guardada correctamente";
}

// Cargar configuración actual
$default_settings = [
    'email_from' => 'pedidosthiscookie21@gmail.com',
    'email_from_name' => 'ThisCookie21',
    'sendgrid_api_key' => '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',
    'whatsapp_phone' => '34620805760',
    'redsys_merchant' => '368734406',
    'redsys_terminal' => '001',
    'redsys_secret' => 'sq7HjrUOBfKmC576ILgskD5srU870gJ7',
    'redsys_test_mode' => false
];

$settings = file_exists($config_file) 
    ? json_decode(file_get_contents($config_file), true) 
    : $default_settings;

get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes - ThisCookie21</title>
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
            max-width: 900px;
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

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-orders {
            background: #007bff;
            color: white;
        }

        .btn-orders:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .btn-products {
            background: #28a745;
            color: white;
        }

        .btn-products:hover {
            background: #218838;
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

        .settings-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .form-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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
        .form-group input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group small {
            display: block;
            color: #888;
            margin-top: 5px;
            font-size: 12px;
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

        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .btn-save:hover {
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
                justify-content: stretch;
            }

            .btn {
                flex: 1;
                justify-content: center;
                font-size: 12px;
                padding: 8px 12px;
            }

            .settings-card {
                padding: 20px;
            }

            .form-section h2 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Ajustes del Sistema</h1>
            <div class="nav-buttons">
                <a href="<?php echo home_url('/admin-pedidos/'); ?>" class="btn btn-orders">📦 Pedidos</a>
                <a href="<?php echo home_url('/productos/'); ?>" class="btn btn-products">🍪 Productos</a>
                <a href="<?php echo home_url('/'); ?>" class="btn btn-back">🏠 Inicio</a>
                <a href="<?php echo home_url('/login/?logout=1'); ?>" class="btn btn-logout">🚪 Salir</a>
            </div>
        </div>

        <div class="settings-card">
            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    ✓ <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Configuración de Email -->
                <div class="form-section">
                    <h2>📧 Configuración de Email</h2>
                    
                    <div class="form-group">
                        <label for="email_from">Email Remitente</label>
                        <input type="email" id="email_from" name="email_from" 
                               value="<?php echo esc_attr($settings['email_from']); ?>" required>
                        <small>Email desde el que se enviarán las notificaciones</small>
                    </div>

                    <div class="form-group">
                        <label for="email_from_name">Nombre Remitente</label>
                        <input type="text" id="email_from_name" name="email_from_name" 
                               value="<?php echo esc_attr($settings['email_from_name']); ?>" required>
                        <small>Nombre que aparecerá como remitente</small>
                    </div>

                    <div class="form-group">
                        <label for="sendgrid_api_key">SendGrid API Key</label>
                        <input type="text" id="sendgrid_api_key" name="sendgrid_api_key" 
                               value="<?php echo esc_attr($settings['sendgrid_api_key']); ?>" required>
                        <small>Clave API de SendGrid para envío de emails</small>
                    </div>
                </div>

                <!-- Configuración de WhatsApp -->
                <div class="form-section">
                    <h2>📱 Configuración de WhatsApp</h2>
                    
                    <div class="form-group">
                        <label for="whatsapp_phone">Número de WhatsApp</label>
                        <input type="text" id="whatsapp_phone" name="whatsapp_phone" 
                               value="<?php echo esc_attr($settings['whatsapp_phone']); ?>" required>
                        <small>Formato: código país + número (ej: 34620805760)</small>
                    </div>
                </div>

                <!-- Configuración de Redsys -->
                <div class="form-section">
                    <h2>💳 Configuración de Pasarela de Pago (Redsys)</h2>
                    
                    <div class="form-group">
                        <label for="redsys_merchant">Merchant Code (FUC)</label>
                        <input type="text" id="redsys_merchant" name="redsys_merchant" 
                               value="<?php echo esc_attr($settings['redsys_merchant']); ?>" required>
                        <small>Código de comercio proporcionado por el banco</small>
                    </div>

                    <div class="form-group">
                        <label for="redsys_terminal">Terminal</label>
                        <input type="text" id="redsys_terminal" name="redsys_terminal" 
                               value="<?php echo esc_attr($settings['redsys_terminal']); ?>" required>
                        <small>Número de terminal (generalmente 001)</small>
                    </div>

                    <div class="form-group">
                        <label for="redsys_secret">Clave Secreta</label>
                        <input type="text" id="redsys_secret" name="redsys_secret" 
                               value="<?php echo esc_attr($settings['redsys_secret']); ?>" required>
                        <small>Clave secreta para firma de operaciones</small>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="redsys_test_mode" name="redsys_test_mode" 
                                   <?php echo $settings['redsys_test_mode'] ? 'checked' : ''; ?>>
                            <label for="redsys_test_mode" style="margin-bottom: 0;">Modo Prueba (Test)</label>
                        </div>
                        <small>Activar para usar el entorno de pruebas de Redsys</small>
                    </div>
                </div>

                <button type="submit" name="save_settings" class="btn-save">
                    💾 Guardar Configuración
                </button>
            </form>
        </div>
    </div>
</body>
</html>

<?php get_footer(); ?>
