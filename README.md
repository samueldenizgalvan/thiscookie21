# ThisCookie21 - Sitio Web WordPress

Sitio web completo para la tienda de galletas artesanales ThisCookie21 con sistema de pedidos online, pasarela de pago BBVA y confirmaciones automáticas por WhatsApp.

## 🎯 Características Principales

- ✅ Catálogo completo de 30 galletas con precios
- 🛒 Carrito de compras con persistencia
- 💳 Pasarela de pago BBVA TPV Virtual (Redsys)
- 📱 Confirmaciones automáticas por WhatsApp con código QR
- � Panel de administración de pedidos
- 🗓️ Sistema de recogida con ubicaciones dinámicas
- 📈 Estadísticas de ventas mensuales
- 💼 100% Responsive (móvil, tablet, escritorio)

## 🚀 INICIO RÁPIDO

### 💻 Desarrollo Local (Windows)


**Opción B** - Comando manual:
```powershell
npm install
npm start
```

#### 3️⃣ Conectar WhatsApp
1. Abre en navegador: **http://localhost:3000**
2. Escanea el código QR con WhatsApp
3. ¡Listo! Las confirmaciones se enviarán automáticamente

📖 **Guía completa local**: `whatsapp-bot/INICIO-RAPIDO.md`

---

### 🌐 Despliegue en Producción (VPS)

#### Opción A: Instalación Automática (30 minutos)

```bash
# 1. Conectar al VPS
ssh root@TU_IP_VPS

# 2. Descargar y ejecutar script
wget https://raw.githubusercontent.com/TU_REPO/install-vps.sh
chmod +x install-vps.sh
./install-vps.sh
```

#### Opción B: Instalación Manual

Sigue la guía paso a paso en: **`DESPLIEGUE-VPS.md`**

#### Guía Rápida de Despliegue

Ver: **`DESPLIEGUE-RAPIDO.md`** - Resumen ejecutivo en 30 minutos

📖 **Documentación completa de producción**:
- `DESPLIEGUE-VPS.md` - Guía detallada (15 pasos)
- `DESPLIEGUE-RAPIDO.md` - Resumen rápido
- `install-vps.sh` - Script de instalación automática
- `.env.example` - Configuración de variables de entorno

## 📦 Instalación WordPress

### Opción 1: Local by Flywheel (Recomendado)

1. Descarga [Local by Flywheel](https://localwp.com/)
2. Crea un nuevo sitio llamado "thiscookie21"
3. El tema ya está en: `wp-content/themes/thiscookie21/`
4. Activa el tema desde WordPress (Apariencia > Temas)

## 📁 Estructura del Proyecto

```
thiscookie21/
├── wp-content/themes/thiscookie21/
│   ├── style.css                    # Estilos principales
│   ├── index.php                    # Catálogo + Carrito + Checkout
│   ├── functions.php                # Panel admin + WordPress hooks
│   ├── redsys-tpv.php              # Clase TPV BBVA
│   ├── process-payment.php          # Procesamiento de pagos
│   ├── tpv-success.php             # Página pago exitoso
│   ├── tpv-error.php               # Página error de pago
│   ├── tpv-notification.php        # Webhook del banco
│   ├── get-orders.php              # API obtener pedidos
│   ├── get-stats.php               # API estadísticas
│   ├── complete-order.php          # API completar pedido
│   ├── delete-order.php            # API eliminar pedido
│   ├── orders/
│   │   ├── pending/                # Pedidos pendientes
│   │   ├── completed/              # Pedidos completados
│   │   └── history/                # Histórico mensual
│   └── whatsapp-bot/               # 🆕 Bot de WhatsApp
│       ├── whatsapp-bot.js         # Servidor Node.js
│       ├── package.json            # Dependencias
│       ├── INICIAR-BOT.bat         # Inicio rápido
│       ├── GUIA-COMPLETA.md        # Guía detallada
│       ├── test-whatsapp.html      # Prueba manual
│       └── README.md               # Documentación técnica
```

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
Editar `process-payment.php` línea ~55:
```php
$redsys = new RedsysTPV(false); // false = producción
```

## 📱 Sistema WhatsApp

### Funcionamiento
1. Cliente completa pedido y paga
2. Pago confirmado → Automáticamente envía WhatsApp
3. Mensaje incluye: pedido completo, fecha, dirección, horario

### Tecnología
- **whatsapp-web.js** - Control de WhatsApp Web
- **Puppeteer** - Automatización navegador
- **Express.js** - Servidor API REST
- **QR Code** - Autenticación una sola vez

### Endpoints API
```
GET  http://localhost:3000/api/status              # Estado conexión
GET  http://localhost:3000/api/qr                  # Código QR
POST http://localhost:3000/api/send-confirmation   # Enviar pedido
POST http://localhost:3000/api/send-message        # Mensaje custom
```

## 🗓️ Sistema de Recogida

### Días y Ubicaciones
- **Miércoles**: Arucas (7:30-13:00 / 17:00-20:00)
- **Viernes, Sábado, Domingo**: Las Palmas (13:00-18:00)
- **Lunes, Martes, Jueves**: Bloqueados

### Validación
JavaScript bloquea días no permitidos automáticamente en el calendario.

## 📊 Panel de Administración

Acceso: **WordPress Admin → Pedidos 🍪**

### Funciones
- ✅ Ver pedidos pendientes/completados
- 📈 Estadísticas del mes actual
- 📅 Histórico de ventas
- ✅ Marcar pedidos como completados
- 🗑️ Eliminar pedidos
- 📱 Ver estado confirmación WhatsApp

## 🍪 Catálogo de Galletas (30 productos)

El sitio muestra todas las galletas organizadas por precio:

- **2,90€** (8): Choconueces, Brownie, Brownie Naranja, Coco, Limón, Dátiles, Arándano, Gofio
- **3,50€** (6): Kinder®, 3 Chocolates, Red Velvet, Carrot Cake, Black and White, Te Matcha
- **3,90€** (9): Oreo® Rellena, Lotus® Rellena, Cheese Cake, Cinnamon Roll, Black and White Rellena, Nutella® Rellena, Cocomilky, Milkybar Rellena, Pumpkin Cake
- **4,20€** (3): Manihoney Rellena, Turrón Blend Rellena, Reese's
- **4,90€** (1): Happy Hippo® Rellena
- **5,90€** (1): Pistacho Rellena

## 🔧 Personalización

### Cambiar Datos del TPV
Editar `redsys-tpv.php`:
```php
private $merchantCode = 'TU_CODIGO';
private $terminal = 'TU_TERMINAL';
private $secretKey = 'TU_CLAVE_SECRETA';
```

### Modificar Mensaje WhatsApp
Editar `whatsapp-bot/whatsapp-bot.js` línea ~83:
```javascript
let message = `🍪 *PEDIDO CONFIRMADO - ThisCookie21*\n\n`;
// ... personalizar aquí
```

### Agregar Fotos de Galletas
Cuando tengas las fotos, reemplazar en `index.php`:
```html
<div class="cookie-image">📸 Foto Próximamente</div>
```
Por:
```html
<img src="ruta/imagen.jpg" alt="Galleta">
```

### Modificar Colores
Archivo `style.css`:
- Color principal: `#8B4513` (marrón chocolate)
- Color secundario: `#D2691E` (marrón claro)
- Color acento: `#28a745` (verde éxito)

## 📱 Responsive Design

El sitio está optimizado para:
- 📱 **Móviles** (< 480px): Grid 2 columnas
- 📱 **Tablets** (480px - 768px): Grid 3 columnas
- 💻 **Desktop** (> 768px): Grid 4 columnas

## ⚠️ Requisitos del Sistema

### WordPress
- WordPress 5.0+
- PHP 7.4+
- Permisos escritura en `/orders/`

### WhatsApp Bot
- Node.js 14+
- NPM o Yarn
- Puerto 3000 disponible
- Google Chrome (para Puppeteer)

## 🐛 Solución de Problemas

### WhatsApp no envía mensajes
1. Verificar que el bot esté corriendo: `http://localhost:3000`
2. Ver si muestra "✅ Conectado"
3. Revisar console del navegador para errores CORS

### Error de pago
1. Verificar credenciales del TPV en `redsys-tpv.php`
2. Comprobar que sea entorno TEST
3. Usar tarjeta de prueba: 4548812049400004

### Carrito no aparece
1. Abrir DevTools (F12) → Console
2. Verificar errores JavaScript
3. Comprobar que `localStorage` esté habilitado

### Pedidos no se guardan
1. Verificar permisos carpeta `/orders/` (755)
2. Comprobar espacio en disco
3. Revisar logs de PHP

## 📚 Documentación Adicional

- **WhatsApp Bot**: `whatsapp-bot/GUIA-COMPLETA.md`
- **Sistema Pedidos**: `SISTEMA-PEDIDOS.md`
- **API Endpoints**: `whatsapp-bot/README.md`

## 🔐 Seguridad

- ✅ Validación de pagos con firma HMAC SHA256
- ✅ Sanitización de datos de formulario
- ✅ Protección carpeta `/orders/` con .htaccess
- ✅ CORS configurado para API endpoints
- ⚠️ **IMPORTANTE**: Cambiar credenciales TPV en producción

## 🚀 Pasar a Producción

### 1. Configurar TPV Producción
```php
// process-payment.php
$redsys = new RedsysTPV(false); // false = producción
```

### 2. Actualizar Credenciales BBVA
Editar `redsys-tpv.php` con datos reales del banco

### 3. Configurar Servidor WhatsApp
- Usar PM2 o similar para mantener bot corriendo 24/7
- Configurar auto-inicio en reinicio del servidor

### 4. Backup Automático
- Configurar backup diario de carpeta `/orders/`
- Considerar migrar a base de datos MySQL

## 📞 Soporte

Para cualquier duda o modificación del sistema:
- Revisar documentación completa en `/whatsapp-bot/`
- Verificar logs del bot en consola del servidor
- Comprobar errores PHP en WordPress debug log

## Responsive Design

El sitio está optimizado para:
- 📱 Móviles (menos de 480px)
- 📱 Tablets (480px - 768px)
- 💻 Desktop (más de 768px)

## Soporte

Para cualquier duda o modificación, contacta con el desarrollador.

---

© 2025 ThisCookie21 - Galletas Artesanales Hechas con Amor 🍪
