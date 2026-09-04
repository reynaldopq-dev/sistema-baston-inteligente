# Sistema Bastón Inteligente

Sistema web de asistencia y monitoreo para el **Bastón Electrónico** destinado a personas con
discapacidad visual, desarrollado para el **Centro de No Videntes "Manuela Gandarillas"** de Cochabamba.

Proyecto Socio-Comunitario — Carrera de Sistemas Informáticos, **INCOS Cochabamba**.

---

## ¿Qué hace el sistema?

El bastón electrónico (ESP32 con sensores ultrasónicos y módulo GPS) envía su ubicación y las
alertas de emergencia al sistema web. Desde la plataforma, el personal del centro puede:

- Registrar y administrar **beneficiarios** (personas no videntes del centro).
- Registrar y asignar los **bastones** electrónicos a cada beneficiario.
- Ver la **geolocalización** en tiempo real de los bastones en un mapa.
- Recibir y atender **alertas SOS** enviadas desde el bastón.
- Administrar **usuarios del personal** y **personas responsables (tutores)** con acceso propio.
- Consultar la **auditoría** de todas las acciones realizadas en el sistema.
- Exportar reportes en **PDF** y **Excel**.

---

## Tecnologías

| Componente | Tecnología |
|---|---|
| Framework | Laravel 12 |
| Lenguaje | PHP 8.2+ |
| Base de datos | MySQL (MariaDB / XAMPP) |
| Interfaz | AdminLTE 3 + Blade + Bootstrap |
| Reportes PDF | barryvdh/laravel-dompdf |
| Reportes Excel | maatwebsite/excel |
| Assets | Vite + NPM |
| Hardware | ESP32 + sensores ultrasónicos + GPS |

---

## Módulos del sistema

```
Autenticación y roles     → login, cambio de contraseña obligatorio, control por rol
Beneficiarios             → CRUD, fotos, papelera (soft delete), reportes PDF/Excel
Bastones                  → CRUD, asignación a beneficiario, papelera, reportes PDF/Excel
Usuarios (personal)       → CRUD, roles, activación/desactivación, reportes PDF/Excel
Tutores / responsables    → CRUD, vinculación con beneficiarios
Monitoreo                 → geolocalización en mapa
Alertas SOS               → registro, detalle y cambio de estado de emergencias
Auditoría                 → bitácora de acciones del personal
```

### Roles de acceso

| Rol | Permisos |
|---|---|
| **Administrador** | Acceso total: crear, editar, eliminar, auditoría |
| **Tutor** | Monitoreo de su beneficiario y atención de alertas |
| **Personal** | Consulta de los módulos según asignación |
| **Invitado** | Solo lectura (acceso por QR para la feria de exposición) |

---

## Instalación

### Requisitos previos

- PHP 8.2 o superior
- Composer
- MySQL / MariaDB (XAMPP)
- Node.js y NPM

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/reynaldopq-dev/sistema-baston-inteligente.git
cd sistema-baston-inteligente

# 2. Instalar dependencias de PHP
composer install

# 3. Instalar dependencias de JavaScript
npm install

# 4. Copiar el archivo de configuración
cp .env.example .env

# 5. Generar la clave de la aplicación
php artisan key:generate

# 6. Configurar la base de datos en el archivo .env
#    DB_DATABASE=baston_inteligente
#    DB_USERNAME=root
#    DB_PASSWORD=

# 7. Crear las tablas y cargar los datos base
php artisan migrate --seed

# 8. Enlazar el almacenamiento de imágenes
php artisan storage:link

# 9. Compilar los assets
npm run build

# 10. Levantar el servidor
php artisan serve
```

El sistema queda disponible en `http://localhost:8000`.

> **Importante:** el archivo `.env` **no se sube al repositorio** porque contiene credenciales.
> Cada integrante debe crear el suyo a partir de `.env.example`.

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/        → Beneficiarios, Bastones, Usuarios, Tutores
│   │   ├── Auth/         → Login, recuperación y cambio de contraseña
│   │   ├── Monitoreo/    → Geolocalización
│   │   └── Sistema/      → Auditoría
│   └── Middleware/       → CheckRol, ForzarCambioPassword, SecurityHeaders
├── Models/               → Beneficiario, Baston, Usuario, Alerta, Auditoria, User
├── Observers/            → RegistraAuditoria
├── Services/             → AlertaSosService
├── Exports/              → Exportaciones a Excel
└── Console/Commands/     → RevisarSosBastones

database/migrations/      → Estructura de la base de datos
routes/web.php            → Rutas del sistema
resources/views/          → Vistas Blade (AdminLTE)
```

---

## Convención de commits

Este repositorio usa **Conventional Commits** para mantener un historial legible:

| Prefijo | Uso |
|---|---|
| `feat:` | Nueva funcionalidad |
| `fix:` | Corrección de un error |
| `refactor:` | Reorganización de código sin cambiar el comportamiento |
| `docs:` | Documentación |
| `style:` | Formato, estilos, vistas |
| `chore:` | Configuración, dependencias, tareas de mantenimiento |

Ejemplo: `feat(alertas): registrar SOS enviado desde el bastón`

---

## Equipo de desarrollo

| Integrante | Rol |
|---|---|
| Reynaldo Pérez | Desarrollo web y base de datos |
| Farit Choque | Desarrollo |
| José Espinoza | Desarrollo |

**Docente:** Ing. Raúl Vera Portanda — Gestión y Mejoramiento de la Calidad de Software

**Institución:** INCOS Cochabamba — Sistemas Informáticos

---

## Licencia

Proyecto académico desarrollado con fines educativos y de apoyo social al
Centro de No Videntes "Manuela Gandarillas".
