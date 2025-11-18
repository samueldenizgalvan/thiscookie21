<?php
/**
 * Template Name: Login Admin
 * Página de login para acceder al panel de pedidos
 */

// Si ya está logueado, redirigir al panel
if (is_user_logged_in() && current_user_can('administrator')) {
    wp_redirect(home_url('/admin-pedidos/'));
    exit;
}

// Procesar login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = sanitize_text_field($_POST['username']);
    $password = $_POST['password'];
    
    $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true
    );
    
    $user = wp_signon($creds, false);
    
    if (is_wp_error($user)) {
        $error = 'Usuario o contraseña incorrectos';
    } else {
        if (current_user_can('administrator')) {
            wp_redirect(home_url('/admin-pedidos/'));
            exit;
        } else {
            wp_logout();
            $error = 'No tienes permisos de administrador';
        }
    }
}

get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ThisCookie21</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 100%;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #8B4513;
            margin: 0;
            font-size: 2rem;
        }
        .logo p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #8B4513;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: scale(1.02);
        }
        .back-home {
            text-align: center;
            margin-top: 20px;
        }
        .back-home a {
            color: #8B4513;
            text-decoration: none;
        }
        .back-home a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🍪 ThisCookie21</h1>
            <p>Panel de Administración</p>
        </div>

        <?php if ($error): ?>
            <div class="error">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" name="username" id="username" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required autocomplete="current-password">
            </div>

            <button type="submit" name="login_submit" class="btn-login">
                Iniciar Sesión
            </button>
        </form>

        <div class="back-home">
            <a href="<?php echo home_url(); ?>">← Volver a la tienda</a>
        </div>
    </div>
</body>
</html>

<?php get_footer(); ?>
