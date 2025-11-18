<?php
/**
 * ThisCookie21 Theme Functions
 */

// Incluir funciones de email
require_once(get_template_directory() . '/email-functions.php');

// Configurar SMTP para envío de emails
add_action('phpmailer_init', 'thiscookie21_configure_smtp');

function thiscookie21_configure_smtp($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.sendgrid.net';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 587;
    $phpmailer->Username = 'apikey';
    $phpmailer->Password = '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';
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->From = 'pedidosthiscookie21@gmail.com';
    $phpmailer->FromName = 'ThisCookie21 - Galletas Artesanales';
}

// Cargar estilos del tema
function thiscookie21_enqueue_styles() {
    wp_enqueue_style('thiscookie21-style', get_stylesheet_uri(), array(), '1.0');
}
add_action('wp_enqueue_scripts', 'thiscookie21_enqueue_styles');

// Soporte para características del tema
function thiscookie21_setup() {
    // Título del sitio dinámico
    add_theme_support('title-tag');
    
    // Soporte para imágenes destacadas
    add_theme_support('post-thumbnails');
    
    // Soporte HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Soporte para logo personalizado
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
}
add_action('after_setup_theme', 'thiscookie21_setup');

// Configuración del sitio
function thiscookie21_customize_register($wp_customize) {
    // Sección de configuración de galletas
    $wp_customize->add_section('thiscookie21_settings', array(
        'title'    => __('Configuración de ThisCookie21', 'thiscookie21'),
        'priority' => 30,
    ));
}
add_action('customize_register', 'thiscookie21_customize_register');

// Crear página de administración de pedidos automáticamente
function thiscookie21_create_admin_page() {
    // Crear página de pedidos
    $page = get_page_by_path('admin-pedidos');
    if (!$page) {
        $page_data = array(
            'post_title'    => 'Panel de Pedidos',
            'post_name'     => 'admin-pedidos',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
            'page_template' => 'admin-orders.php'
        );
        wp_insert_post($page_data);
    }
    
    // Crear página de login
    $loginPage = get_page_by_path('login');
    if (!$loginPage) {
        $login_data = array(
            'post_title'    => 'Login',
            'post_name'     => 'login',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
            'page_template' => 'page-login.php'
        );
        wp_insert_post($login_data);
    }
}
add_action('after_switch_theme', 'thiscookie21_create_admin_page');// Agregar enlace de pedidos en el menú de administración de WordPress
function thiscookie21_admin_menu() {
    add_menu_page(
        'Pedidos ThisCookie21',           // Título de la página
        'Pedidos 🍪',                      // Título del menú
        'manage_options',                  // Capacidad requerida
        'thiscookie21-orders',             // Slug del menú
        'thiscookie21_orders_page',        // Función callback
        'dashicons-cart',                  // Icono
        25                                 // Posición
    );
}
add_action('admin_menu', 'thiscookie21_admin_menu');

// Función que muestra la página de pedidos en el admin de WordPress
function thiscookie21_orders_page() {
    // Redirigir a la página pública del panel (solo accesible por admin)
    echo '<script>window.location.href = "' . home_url('/admin-pedidos/') . '";</script>';
    echo '<p>Redirigiendo al panel de pedidos...</p>';
}

