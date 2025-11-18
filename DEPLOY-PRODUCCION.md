# 🚀 Guía de Despliegue a Producción - ThisCookie21

## ✅ Cambios realizados para producción:

### 1. TPV Redsys
- ✅ Cambiado de TEST a PRODUCCIÓN
- ✅ URL: `https://sis.redsys.es/sis/realizarPago` (sin `-t`)
- ✅ Credenciales: Las mismas (368734406 / 001)

### 2. URLs
- ✅ Todos los archivos usan `get_site_url()` (dinámico)
- ✅ No hay URLs hardcodeadas
- ✅ Funcionará automáticamente en thiscookie21.com

---

## 📋 PASO 1: Configurar Dominio

### A) DNS (En tu proveedor de dominio):

```
Tipo    Nombre    Valor               TTL
────────────────────────────────────────────
A       @         168.231.86.61       3600
A       www       168.231.86.61       3600
```

### B) Esperar propagación DNS:
- Tiempo: 1-24 horas
- Verificar: `ping thiscookie21.com`

---

## 📦 PASO 2: Preparar VPS Ubuntu

### Conectar por SSH:
```bash
ssh root@168.231.86.61
# O con tu usuario:
ssh usuario@168.231.86.61
```

### Actualizar sistema:
```bash
sudo apt update
sudo apt upgrade -y
```

### Instalar LAMP Stack:
```bash
# Apache
sudo apt install apache2 -y
sudo systemctl start apache2
sudo systemctl enable apache2

# MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation
# Responder: Y a todo, configurar contraseña root

# PHP 8.1+
sudo apt install php php-mysql php-curl php-json php-mbstring php-xml php-zip -y
sudo systemctl restart apache2

# Verificar versiones
php -v
mysql --version
apache2 -v
```

---

## 🗄️ PASO 3: Crear Base de Datos

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE thiscookie21_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'thiscookie21_user'@'localhost' IDENTIFIED BY 'TU_CONTRASEÑA_SEGURA';
GRANT ALL PRIVILEGES ON thiscookie21_db.* TO 'thiscookie21_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**⚠️ IMPORTANTE: Guarda estos datos:**
- Base de datos: `thiscookie21_db`
- Usuario: `thiscookie21_user`
- Contraseña: `[LA_QUE_PONGAS]`

---

## 📁 PASO 4: Subir archivos WordPress

### Opción A: FileZilla (GUI)
1. Descargar FileZilla Client
2. Conectar vía SFTP:
   - Host: `sftp://[IP_VPS]`
   - Usuario: tu usuario SSH
   - Puerto: 22
3. Subir carpeta completa a: `/var/www/html/`

### Opción B: SCP desde tu PC:
```powershell
# Desde PowerShell en Windows
scp -r "C:\Users\samue\Desktop\proyectos\thiscookie21\*" usuario@168.231.86.61:/var/www/html/
```

### Opción C: Git (Recomendado):
```bash
# En el VPS
cd /var/www/html
sudo rm index.html
git clone https://github.com/TU_USUARIO/thiscookie21.git .
```

---

## ⚙️ PASO 5: Configurar wp-config.php

```bash
cd /var/www/html
sudo nano wp-config.php
```

**Contenido del archivo:**
```php
<?php
define('DB_NAME', 'thiscookie21_db');
define('DB_USER', 'thiscookie21_user');
define('DB_PASSWORD', 'TU_CONTRASEÑA_SEGURA');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// Claves de seguridad (generar en: https://api.wordpress.org/secret-key/1.1/salt/)
define('AUTH_KEY',         'poner-aqui-clave-generada');
define('SECURE_AUTH_KEY',  'poner-aqui-clave-generada');
define('LOGGED_IN_KEY',    'poner-aqui-clave-generada');
define('NONCE_KEY',        'poner-aqui-clave-generada');
define('AUTH_SALT',        'poner-aqui-clave-generada');
define('SECURE_AUTH_SALT', 'poner-aqui-clave-generada');
define('LOGGED_IN_SALT',   'poner-aqui-clave-generada');
define('NONCE_SALT',       'poner-aqui-clave-generada');

$table_prefix = 'wp_';

define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

// URLs forzadas (ajustar si es necesario)
define('WP_HOME', 'https://thiscookie21.com');
define('WP_SITEURL', 'https://thiscookie21.com');

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
```

Guardar: `Ctrl+O`, Enter, `Ctrl+X`

---

## 🔒 PASO 6: Permisos correctos

```bash
# Propietario correcto
sudo chown -R www-data:www-data /var/www/html

# Permisos de archivos
sudo find /var/www/html -type f -exec chmod 644 {} \;
sudo find /var/www/html -type d -exec chmod 755 {} \;

# Directorios especiales con escritura
sudo chmod -R 775 /var/www/html/wp-content/themes/thiscookie21/orders
sudo chmod -R 775 /var/www/html/wp-content/themes/thiscookie21/config

# Crear directorios si no existen
sudo mkdir -p /var/www/html/wp-content/themes/thiscookie21/orders/pending
sudo mkdir -p /var/www/html/wp-content/themes/thiscookie21/orders/history
sudo mkdir -p /var/www/html/wp-content/themes/thiscookie21/orders/temp
sudo mkdir -p /var/www/html/wp-content/themes/thiscookie21/config
sudo chown -R www-data:www-data /var/www/html/wp-content/themes/thiscookie21/orders
sudo chown -R www-data:www-data /var/www/html/wp-content/themes/thiscookie21/config
```

---

## 🌐 PASO 7: Configurar Apache

```bash
sudo nano /etc/apache2/sites-available/thiscookie21.conf
```

**Contenido:**
```apache
<VirtualHost *:80>
    ServerName thiscookie21.com
    ServerAlias www.thiscookie21.com
    ServerAdmin pedidosthiscookie21@gmail.com
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/thiscookie21_error.log
    CustomLog ${APACHE_LOG_DIR}/thiscookie21_access.log combined
</VirtualHost>
```

**Activar configuración:**
```bash
sudo a2ensite thiscookie21.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## 🔐 PASO 8: Instalar SSL (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache -y

# Obtener certificado SSL
sudo certbot --apache -d thiscookie21.com -d www.thiscookie21.com

# Responder:
# Email: pedidosthiscookie21@gmail.com
# Aceptar términos: Y
# ¿Compartir email? N
# ¿Redirigir HTTP a HTTPS? 2 (Sí, redirigir)

# Renovación automática (ya está configurado)
sudo certbot renew --dry-run
```

---

## 📧 PASO 9: Configurar SendGrid

### SPF Record (en DNS):
```
Tipo    Nombre    Valor
────────────────────────────────────────────────────
TXT     @         v=spf1 include:sendgrid.net ~all
```

### DKIM (Opcional, mejorar entregabilidad):
1. Ir a SendGrid → Settings → Sender Authentication
2. Verificar dominio thiscookie21.com
3. Añadir registros DNS que te proporcionen

---

## ✅ PASO 10: Instalar WordPress

1. Ir a: `https://thiscookie21.com/wp-admin/install.php`
2. Seleccionar idioma: Español
3. Configurar:
   - **Título:** ThisCookie21
   - **Usuario admin:** admin (o tu preferido)
   - **Contraseña:** (generada o personalizada)
   - **Email:** pedidosthiscookie21@gmail.com
4. Hacer clic en "Instalar WordPress"

---

## 🎨 PASO 11: Activar tema

1. Ir a: Apariencia → Temas
2. Activar: **ThisCookie21**
3. Ir a: Páginas → Añadir nueva
   - Título: "Admin Pedidos"
   - Slug: `admin-pedidos`
   - Plantilla: Admin - Pedidos
   - Publicar
4. Crear página:
   - Título: "Login"
   - Slug: `login`
   - Plantilla: Login
   - Publicar

---

## 🧪 PASO 12: Probar todo

### A) Verificar páginas:
- ✅ `https://thiscookie21.com` → Catálogo de galletas
- ✅ `https://thiscookie21.com/login` → Login admin
- ✅ `https://thiscookie21.com/admin-pedidos` → Panel pedidos

### B) Hacer pedido de prueba:
1. Añadir galletas al carrito
2. Rellenar formulario
3. Pagar con tarjeta real (pequeño importe)
4. Verificar:
   - ✅ Email recibido
   - ✅ Pedido en `/admin-pedidos/`
   - ✅ Archivo JSON en `orders/pending/`

### C) Confirmar/Rechazar pedido:
1. Login en `/admin-pedidos/`
2. Ver pedido en "Pendientes"
3. Confirmar o rechazar
4. Verificar email al cliente

---

## 🔧 PASO 13: Configurar WhatsApp (Opcional)

1. Ir a: `https://thiscookie21.com/admin-pedidos`
2. En "Configuración de Notificaciones WhatsApp"
3. Ingresar tu número: `693701893`
4. Guardar
5. Hacer pedido de prueba y verificar que se abre WhatsApp

---

## 📊 Monitoreo y Mantenimiento

### Logs de errores:
```bash
# Ver logs de Apache
sudo tail -f /var/log/apache2/thiscookie21_error.log

# Ver logs de PHP
sudo tail -f /var/log/apache2/error.log
```

### Backups automáticos:
```bash
# Crear script de backup
sudo nano /root/backup-thiscookie21.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
mkdir -p $BACKUP_DIR

# Backup archivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html

# Backup base de datos
mysqldump -u root -p thiscookie21_db > $BACKUP_DIR/db_$DATE.sql

# Mantener solo últimos 7 backups
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completado: $DATE"
```

```bash
sudo chmod +x /root/backup-thiscookie21.sh

# Programar backup diario (3 AM)
sudo crontab -e
# Añadir línea:
0 3 * * * /root/backup-thiscookie21.sh
```

---

## 🚨 Solución de Problemas

### Error 500:
```bash
# Ver logs
sudo tail -100 /var/log/apache2/error.log

# Verificar permisos
sudo chown -R www-data:www-data /var/www/html
```

### Base de datos no conecta:
```bash
# Verificar MySQL
sudo systemctl status mysql
sudo systemctl restart mysql

# Probar conexión
mysql -u thiscookie21_user -p thiscookie21_db
```

### SSL no funciona:
```bash
# Renovar certificado
sudo certbot renew --force-renewal
sudo systemctl restart apache2
```

### Emails no llegan:
- Verificar registros SPF en DNS
- Revisar logs en SendGrid
- Comprobar carpeta SPAM

---

## ✅ Checklist Final

- [ ] DNS configurado y propagado
- [ ] VPS con LAMP instalado
- [ ] Base de datos creada
- [ ] Archivos subidos
- [ ] wp-config.php configurado
- [ ] Permisos correctos
- [ ] Apache configurado
- [ ] SSL instalado
- [ ] WordPress instalado
- [ ] Tema activado
- [ ] Páginas creadas
- [ ] Pedido de prueba exitoso
- [ ] Email funcionando
- [ ] WhatsApp configurado
- [ ] Backup programado

---

## 📞 Soporte

**Si algo falla:**
1. Revisar logs: `/var/log/apache2/thiscookie21_error.log`
2. Verificar permisos de directorios
3. Comprobar configuración de base de datos
4. Verificar SSL con: `https://www.ssllabs.com/ssltest/`

**Archivos importantes:**
- Configuración Apache: `/etc/apache2/sites-available/thiscookie21.conf`
- wp-config.php: `/var/www/html/wp-config.php`
- Tema: `/var/www/html/wp-content/themes/thiscookie21/`
- Pedidos: `/var/www/html/wp-content/themes/thiscookie21/orders/`

---

¡Tu web está lista para producción! 🎉🍪
