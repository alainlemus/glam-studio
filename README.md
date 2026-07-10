# Salones Belleza - Sistema de Gestión

Sistema web completo para salones de belleza con múltiples sucursales. Incluye web pública para clientes y panel administrativo.

## Stack

- **Backend:** Laravel 12+ con PHP 8.4
- **Frontend:** Vue 3 + Inertia.js + Tailwind CSS 4
- **Base de datos:** SQLite (desarrollo) / MySQL o PostgreSQL (producción)
- **Autenticación:** Laravel Fortify

## Módulos

### Web pública (clientes)
- **Inicio** — Hero, sucursales destacadas, servicios y promociones
- **Sucursales** — Listado agrupado por ciudad, con detalle y equipo
- **Servicios** — Catálogo por categoría con precios
- **Promociones** — Campañas activas
- **Nosotros / Contacto** — Información y datos de contacto
- **Reservar cita** — Wizard con selección de servicio, sucursal, estilista, fecha y hora en tiempo real
- **Confirmación por WhatsApp** — Link pre-armado al confirmar

### Panel admin (`/admin`)
- **Dashboard** — Métricas del mes, próximas citas, top estilistas, gráfico de ventas
- **Agenda / Calendario** — Vista semanal de citas con filtros
- **Citas** — Listado, alta manual, cambio de estatus (confirmar, completar, no-show, cancelar)
- **Clientes** — CRM con tarjeta de lealtad, historial de citas, control de no-shows
- **Estilistas** — Gestión con horarios, comisiones y comisiones pendientes
- **Servicios** y **Categorías** — CRUD con comisiones por defecto
- **Productos** e **Inventario** — Stock por sucursal con alertas
- **Ventas** — POS con múltiples items, comisiones automáticas por estilista
- **Comisiones** — Liquidación por periodo con generación de sueldo
- **Finanzas** — Ingresos vs egresos, flujo diario, margen
- **Egresos** — Con categorías fijas y variables
- **Marketing** — Campañas con plantillas y envío masivo (WhatsApp)
- **Lealtad** — Tarjetas de 10 sellos con redención

### Integración WhatsApp
- Link directo `wa.me/` con mensaje pre-armado al confirmar cita
- Webhook en `/webhook/whatsapp` para Meta Cloud API
- Comando `reminders:send` (cron por hora) para recordatorios 24h antes
- Chatbot básico con detección de intención

## Roles

| Rol | Permisos |
|-----|----------|
| `admin` | Acceso total |
| `manager` | Todo excepto configuración crítica |
| `receptionist` | Citas, ventas, clientes |
| `stylist` | Sus citas, agenda, comisiones |

## Datos de acceso (seeders)

| Email | Password | Rol |
|-------|----------|-----|
| `admin@salones.com` | `password` | admin |
| `manager@salones.com` | `password` | manager |
| `recepcion@salones.com` | `password` | receptionist |
| `sofia-hernandez@salones.com` (etc) | `password` | stylist |

## Comandos

```bash
# Instalar
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Desarrollo
php artisan serve
npm run dev

# Producción
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Programar tareas (en crontab)
* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
```

## Variables de entorno (WhatsApp)

```
WHATSAPP_TOKEN=tu_token_de_meta
WHATSAPP_PHONE_ID=tu_phone_id
WHATSAPP_BUSINESS_ID=tu_business_id
WHATSAPP_VERIFY_TOKEN=salones-webhook
```

Sin tokens configurados, el sistema registra mensajes en BD y genera links `wa.me/` para envío manual.

## Estructura

```
app/
├── Http/Controllers/
│   ├── SiteController.php          # Web pública
│   ├── PublicAppointmentController # Reservas
│   ├── WhatsAppWebhookController   # Webhook
│   └── Admin/                      # 17 controllers admin
├── Models/                         # 22 modelos
├── Services/WhatsAppService.php
└── Console/Commands/SendAppointmentReminders.php

resources/js/pages/
├── site/                           # Páginas públicas
└── admin/                          # Panel admin
```