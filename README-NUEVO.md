# ThisCookie21 - Sitio Web WordPress

Sitio web completo para la tienda de galletas artesanales ThisCookie21 con sistema de pedidos online, pasarela de pago BBVA y **confirmaciones automáticas por Email**.

## 🎯 Características Principales

- ✅ Catálogo completo de 30 galletas con precios
- 🛒 Carrito de compras con persistencia
- 💳 Pasarela de pago BBVA TPV Virtual (Redsys)
- 📧 **Confirmaciones automáticas por Email** (HTML profesionales)
- 📊 Panel de administración de pedidos
- 🗓️ Sistema de recogida con ubicaciones dinámicas
- 📈 Estadísticas de ventas mensuales
- 💼 100% Responsive (móvil, tablet, escritorio)

## 🚀 INICIO RÁPIDO - Sistema de Emails

### 📧 Configuración (15 minutos)

#### 1️⃣ Configurar Gmail SMTP

1. **Crear cuenta Gmail** para pedidos (ej: `pedidosthiscookie21@gmail.com`)
2. **Activar verificación en 2 pasos**: https://myaccount.google.com/security
3. **Generar contraseña de aplicación**: https://myaccount.google.com/apppasswords
4. **Copiar contraseña** de 16 caracteres

#### 2️⃣ Actualizar Credenciales

Edita: `wp-content/themes/thiscookie21/functions.php`

```php
$phpmailer->Username = 'pedidosthiscookie21@gmail.com';  // Tu email
$phpmailer->Password = 'abcd efgh ijkl mnop';  // Contraseña de aplicación
```

#### 3️⃣ Probar

- Haz un pedido de prueba
- Verifica que recibes 2 emails:
  - ✅ Confirmación al cliente
  - ✅ Notificación al admin

📖 **Guía completa**: `CONFIGURAR-GMAIL-SMTP.md`  
📖 **Resumen sistema**: `RESUMEN-SISTEMA-EMAIL.md`

---

## 📧 Emails que se Envían

### 1️⃣ Al Cliente (automático)
- 🍪 Header con logo
- 📋 Detalles del pedido
- 📅 Fecha y lugar de recogida
- 🛒 Lista de productos
- 💰 Total del pedido
- ✅ Confirmación de pago

### 2️⃣ Panel de Administración (automático)
Los pedidos aparecen en: **WordPress Admin → Pedidos 🍪**

**Nota**: NO se envía email al admin para evitar spam. Solo aparece en el panel.

### 3️⃣ Del Banco (automático)
- Confirmación de pago de Redsys/BBVA

---

## 📦 Instalación WordPress

### Desarrollo Local
1. Instalar **Local by Flywheel** o XAMPP
2. Crear sitio WordPress
3. Copiar tema a `wp-content/themes/thiscookie21/`
4. Activar tema
5. Configurar Gmail SMTP

### Producción (VPS)

**Requisitos VPS** (más económico que antes):
- RAM: 2GB (antes 4GB)
- CPU: 1 core
- Disco: 20GB
- SO: Ubuntu 22.04
- **Coste**: ~5€/mes

**Stack necesario**:
- Nginx
- MySQL
- PHP 8.1+
- SSL (Let's Encrypt)
- **NO necesita**: Node.js, PM2, Puppeteer

**Instalación rápida**:
```bash
# Stack LEMP
apt install nginx mysql-server php8.1-fpm php8.1-mysql php8.1-curl \
    php8.1-gd php8.1-mbstring php8.1-xml php8.1-zip -y

# WordPress
cd /var/www
wget https://wordpress.org/latest.tar.gz
tar -xzf latest.tar.gz

# SSL
apt install certbot python3-certbot-nginx -y
certbot --nginx -d tudominio.com
```

📖 **Guía completa**: `DESPLIEGUE-VPS.md`

---

## 📁 Estructura del Proyecto

```
thiscookie21/
├── wp-content/themes/thiscookie21/
│   ├── style.css                    # Estilos principales
│   ├── index.php                    # Catálogo de galletas
│   ├── functions.php                # Configuración SMTP + Admin
│   ├── email-functions.php          # 🆕 Sistema de emails
│   ├── redsys-tpv.php              # Clase TPV BBVA
│   ├── process-payment.php          # Procesamiento de pagos
│   ├── tpv-success.php             # Página pago exitoso
│   ├── tpv-error.php               # Página error de pago
│   ├── tpv-notification.php        # Webhook del banco
│   └── orders/
│       ├── pending/                # Pedidos pendientes
│       ├── completed/              # Pedidos completados
│       └── history/                # Histórico mensual
│
└── Documentación/
    ├── README.md                    # Este archivo
    ├── RESUMEN-SISTEMA-EMAIL.md     # 🆕 Sistema de emails
    ├── CONFIGURAR-GMAIL-SMTP.md     # 🆕 Configuración Gmail
    ├── MIGRACION-EMAIL.md           # 🆕 Migración de WhatsApp
    ├── DESPLIEGUE-VPS.md            # Despliegue en VPS
    └── CHECKLIST-DESPLIEGUE.md      # Checklist completo
```

---

## 🍪 Catálogo de Galletas (30 productos)

- **2,90€** (8): Choconueces, Brownie, Brownie Naranja, Coco, Limón, Dátiles, Arándano, Gofio
- **3,50€** (6): Kinder®, 3 Chocolates, Red Velvet, Carrot Cake, Black and White, Te Matcha
- **3,90€** (9): Oreo® Rellena, Lotus® Rellena, Cheese Cake, Cinnamon Roll, Black and White Rellena, Nutella® Rellena, Cocomilky, Milkybar Rellena, Pumpkin Cake
- **4,20€** (3): Manihoney Rellena, Turrón Blend Rellena, Reese's
- **4,90€** (1): Happy Hippo® Rellena
- **5,90€** (1): Pistacho Rellena

---

## 💳 Pasarela de Pago BBVA

### Entorno de Prueba (Actual)
- Merchant: 368734406
- Terminal: 001
- URL: https://sis-t.redsys.es:25443/sis/realizarPago

### Tarjetas de Prueba
- ✅ **Exitosa**: 4548812049400004
- ❌ **Rechazada**: 4548810000000003
- Fecha: Cualquier futura
- CVV: 123

### Activar Producción
Editar `process-payment.php`:
```php
$redsys = new RedsysTPV(false); // false = producción
```

---

## ⚖️ Comparativa: WhatsApp vs Email

| Aspecto | WhatsApp Bot | Email (Actual) |
|---------|--------------|----------------|
| **Complejidad** | Alta | Baja |
| **Servidores** | 2 | 1 |
| **RAM necesaria** | 4GB | 2GB |
| **Coste VPS** | ~10€/mes | ~5€/mes |
| **Configuración** | 1 hora | 15 minutos |
| **Mantenimiento** | Difícil | Fácil |
| **Fiabilidad** | 85% | 99%+ |
| **Profesionalismo** | Informal | Formal |

---

## 📈 Costos Mensuales

### Desarrollo:
- **Gmail SMTP**: GRATIS (500 emails/día)

### Producción:
- **VPS** (2GB): 4.50€/mes (Hetzner)
- **Dominio**: ~10€/año = 0.83€/mes
- **SSL**: GRATIS (Let's Encrypt)
- **Email**: GRATIS (Gmail) o SendGrid (100/día gratis)
- **Total**: ~5.33€/mes

---

## 🔧 Personalización

### Cambiar Datos del TPV
Editar `redsys-tpv.php`:
```php
private $merchantCode = 'TU_CODIGO';
private $terminal = 'TU_TERMINAL';
private $secretKey = 'TU_CLAVE_SECRETA';
```

### Personalizar Emails
Editar `email-functions.php`:
- Cambiar colores: Buscar `#8B4513`, `#D2691E`
- Añadir logo: Añadir `<img src="...">` en header
- Modificar textos: Editar mensajes en funciones

### Añadir Fotos de Galletas
Cuando tengas fotos, reemplazar en `index.php`:
```html
<div class="cookie-image">📸 Foto Próximamente</div>
```
Por:
```html
<img src="ruta/imagen.jpg" alt="Galleta">
```

---

## 📊 Panel de Administración

Acceso: **WordPress Admin → Pedidos 🍪**

Funciones:
- ✅ Ver pedidos pendientes/completados
- 📈 Estadísticas del mes actual
- 📅 Histórico de ventas
- ✅ Marcar pedidos como completados
- 🗑️ Eliminar pedidos

---

## 🐛 Solución de Problemas

### Emails no llegan
1. Verificar credenciales Gmail en `functions.php`
2. Verificar contraseña de aplicación correcta
3. Revisar carpeta SPAM
4. Verificar límite 500 emails/día no excedido

### Error de pago
1. Verificar credenciales TPV en `redsys-tpv.php`
2. Comprobar entorno TEST
3. Usar tarjeta de prueba: 4548812049400004

### Carrito no aparece
1. Abrir DevTools (F12) → Console
2. Verificar errores JavaScript
3. Comprobar localStorage habilitado

---

## 📚 Documentación Completa

### Sistema de Emails:
- 📧 **`RESUMEN-SISTEMA-EMAIL.md`** - Visión general
- 🔧 **`CONFIGURAR-GMAIL-SMTP.md`** - Configuración paso a paso
- 🔄 **`MIGRACION-EMAIL.md`** - Migración de WhatsApp a Email
- 📝 **`email-functions.php`** - Código de funciones

### Despliegue:
- 🚀 **`DESPLIEGUE-VPS.md`** - Guía completa de producción
- ✅ **`CHECKLIST-DESPLIEGUE.md`** - Checklist detallado

---

## ✅ Checklist de Inicio

### Desarrollo Local:
- [ ] WordPress instalado (Local/XAMPP)
- [ ] Tema activado
- [ ] Cuenta Gmail creada
- [ ] Verificación 2 pasos activada
- [ ] Contraseña de aplicación generada
- [ ] `functions.php` actualizado con credenciales
- [ ] Email de admin configurado en WordPress
- [ ] Pedido de prueba realizado
- [ ] Emails recibidos correctamente

### Producción:
- [ ] VPS contratado (2GB RAM)
- [ ] Dominio comprado y DNS configurado
- [ ] Stack LEMP instalado
- [ ] WordPress instalado
- [ ] Tema subido y activado
- [ ] SSL configurado
- [ ] TPV modo producción
- [ ] Gmail configurado
- [ ] Sistema probado end-to-end

---

## 🎯 Próximos Pasos

1. **Ahora**: Configurar Gmail SMTP siguiendo `CONFIGURAR-GMAIL-SMTP.md`
2. **Luego**: Probar sistema completo en local
3. **Después**: Cuando esté listo → Despliegue en VPS

---

## 📞 Soporte

Para dudas sobre configuración, consulta la documentación correspondiente:
- Emails → `CONFIGURAR-GMAIL-SMTP.md`
- Despliegue → `DESPLIEGUE-VPS.md`
- Sistema general → `RESUMEN-SISTEMA-EMAIL.md`

---

🍪 **ThisCookie21 - Galletas Artesanales con Sistema de Pedidos Online**

© 2025 ThisCookie21 - Hecho con ❤️
