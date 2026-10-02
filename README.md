# Taller JEJ

Aplicación web para la administración de clientes, servicios e inventario en un taller de motocicletas. Reescritura del proyecto de grado original (Blade) usando un stack más moderno: Laravel + Inertia.js + Vue 3.

> Proyecto de grado — Tecnología en Desarrollo de Software, Corporación Universitaria para el Desarrollo Empresarial y Social Misión Paz.

## Capturas de pantalla

### Dashboard

Resumen de la actividad del taller: servicios por estado, ingresos del mes, repuestos en bajo stock y carga de trabajo de los mecánicos.

![Dashboard](docs/dashboard.png)

### Calendario de disponibilidad

Cada barra va desde el ingreso hasta la entrega del servicio, con el color de su estado.

![Calendario](docs/calendario.png)

### Servicios

Órdenes de trabajo con su mecánico, estado y costo total.

![Servicios](docs/servicios.png)

### Facturas

Facturación con número consecutivo, estado de pago y descarga en PDF.

![Facturas](docs/facturas.png)

### Clientes

![Clientes](docs/clientes.png)

## Stack tecnológico

- **Backend:** Laravel 13 (PHP 8.5)
- **Frontend:** Inertia.js + Vue 3
- **Base de datos:** PostgreSQL 18
- **Testing:** Pest
- **Autorización:** Policies + Form Requests (sin middleware de rol genérico)

## Funcionalidades

- Autenticación propia (login con límite de intentos, sin paquete de scaffolding)
- Roles de usuario: **admin**, **recepcionista**, **mecánico**
- CRUD completo de:
    - **Clientes**
    - **Motocicletas** (asociadas a un cliente)
    - **Repuestos** (con alerta de bajo stock)
    - **Servicios** (órdenes de trabajo con descuento automático de inventario y cálculo de costos)
    - **Usuarios** (solo administradores, con eliminación lógica y bloqueo de auto-eliminación)
- Eliminación lógica (soft deletes) en todos los módulos
- Un mecánico solo ve los servicios que tiene asignados; puede recibir permiso especial para crear servicios
- Suite de pruebas automatizadas con Pest (42 tests / 105 aserciones)

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/TU-USUARIO/taller-motos-vue.git
cd taller-motos-vue

# 2. Instalar dependencias de PHP
composer install

# 3. Instalar dependencias de JavaScript
npm install

# 4. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar la base de datos en .env
# DB_CONNECTION=pgsql
# DB_DATABASE=taller_motos_vue
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_password

# 6. Ejecutar migraciones
php artisan migrate

# 7. (Opcional) Poblar con datos de prueba
php artisan db:seed
```

## Ejecutar el proyecto

Necesitas dos terminales abiertas al mismo tiempo:

```bash
# Terminal 1: servidor de Laravel
php artisan serve

# Terminal 2: compilación de assets (Vite)
npm run dev
```

Accede en `http://localhost:8000`.

**Usuario de prueba (admin):**

- Correo: `admin@tallermotos.com`
- Contraseña: `12345678`

## Ejecutar los tests

```bash
php artisan test
```

## Autor

**Erwin Adolfo Grizales Buitrago**
Tecnología en Desarrollo de Software
Director de tesis: Wilder Ordoñez — Codirector: Jaime Andrés Jirón
