<?php
/**
 * Script de instalación para crear las páginas necesarias
 * Ejecutar una sola vez desde el navegador: https://thiscookie21.com/wp-content/themes/thiscookie21/install-pages.php
 */

require_once('../../../../../wp-load.php');

if (!current_user_can('administrator')) {
    die('Acceso denegado. Debes ser administrador.');
}

// Crear página de Ajustes
$ajustes_page = array(
    'post_title'    => 'Ajustes',
    'post_name'     => 'ajustes',
    'post_status'   => 'publish',
    'post_type'     => 'page',
    'post_content'  => '',
    'page_template' => 'admin-settings.php'
);

$ajustes_id = wp_insert_post($ajustes_page);

if ($ajustes_id) {
    update_post_meta($ajustes_id, '_wp_page_template', 'admin-settings.php');
    echo "✓ Página 'Ajustes' creada correctamente (ID: $ajustes_id)<br>";
} else {
    echo "✗ Error al crear página 'Ajustes'<br>";
}

// Crear página de Productos
$productos_page = array(
    'post_title'    => 'Productos',
    'post_name'     => 'productos',
    'post_status'   => 'publish',
    'post_type'     => 'page',
    'post_content'  => '',
    'page_template' => 'admin-products.php'
);

$productos_id = wp_insert_post($productos_page);

if ($productos_id) {
    update_post_meta($productos_id, '_wp_page_template', 'admin-products.php');
    echo "✓ Página 'Productos' creada correctamente (ID: $productos_id)<br>";
} else {
    echo "✗ Error al crear página 'Productos'<br>";
}

echo "<br><strong>Instalación completada!</strong><br>";
echo "<br>Por seguridad, elimina este archivo después de ejecutarlo:<br>";
echo "<code>rm " . __FILE__ . "</code>";
echo "<br><br><a href='" . home_url('/admin-pedidos/') . "'>Ir al Panel de Administración</a>";
?>
