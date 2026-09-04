# Changelog

Todas las modificaciones notables de **Catalogo de Proyectos** se documentan en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y el proyecto sigue la convención de [Commits Convencionales](https://www.conventionalcommits.org/es/v1.0.0/).

---

## [18fd9b7] - 2026-09-04

### Added
- **Login captura el nombre del usuario** (`feat`): el formulario de login
  (`resources/views/auth/login.blade.php`) ahora incluye un campo obligatorio
  **"Nombre"** que se guarda en la sesión.
- **Header muestra al usuario logueado** (`feat`): el bloque de usuario en
  `resources/views/proyectos/layouts/app.blade.php` muestra el **nombre real**
  ingresado + la **inicial** del nombre en el avatar + el rol "Director"
  (reemplaza al botón "+ Nuevo proyecto" del header).

### Changed
- `app/Http/Controllers/AuthController.php`: la validación de login ahora exige
  `nombre` (required|string|max:255) y lo persiste en la sesión
  (`session('nombre')`).
- `tests/Feature/AuthFlowTest.php`: se actualizó el test de login para enviar y
  verificar el nombre.

### Files
- `app/Http/Controllers/AuthController.php`
- `resources/views/auth/login.blade.php`
- `resources/views/proyectos/layouts/app.blade.php`
- `tests/Feature/AuthFlowTest.php`

---

## [797b588] - 2026-09-04

### Added
- **Documentación de historias de usuario y modelo de datos** (`docs`):
  el `README.md` pasó de ser el scaffold estándar de Laravel a documentar el
  proyecto real, incluyendo:
  - Las **7 Historias de Usuario** (US-01 a US-07) con su módulo, texto de
    historia y criterios de aceptación/usabilidad.
  - La **implementación de cada historia** y las decisiones de alcance
    consensuadas (login mock sin OAuth, form único sin wizard, estado +
    archivado unificados, versión como texto libre).
  - El **modelo de datos** completo: tablas `ministerio_secretarias`,
    `tecnologias`, `proyectos` y `proyecto_componentes` con sus campos, tipos
    y relaciones, más los datos sembrados (16 áreas, 19 tecnologías).
  - Requisitos/instalación, resumen de arquitectura y suite de tests.

### Files
- `README.md`

---

## [14a3445] - 2026-09-04

### Added
- **Commit inicial del proyecto** (`feat`): Catalogo de Proyectos sobre
  Laravel 13 / PHP 8.3 con las 7 historias de usuario implementadas:

  - **US-01 Inicio**: autenticación **mock** (SSO simulado) con botones
    Google/Microsoft, middlewares `auth.mock` y `auth.mock.landmark`
    (`AuthController`, `EnsureLoggedIn`, `RedirectIfAuthenticated`).
  - **US-02 Listados**: listado del portfolio con filtros rápidos (Área,
    Estado, Trimestre), búsqueda, orden por última actualización y paginación.
  - **US-03 ABM**: alta de proyectos con componentes 1..N dinámicos
    (`ProyectoController@store`, `proyecto_componentes`) y endpoint
    `GET /areas` para el desplegable dependiente de Área Solicitante.
  - **US-04 ABM**: edición y **baja lógica** (archivar = `estado =
    'archivado'`, no borra el registro), con modal de confirmación.
  - **US-05 Estados**: cambio de estado desde el listado (validado) +
    notificaciones al equipo esbozadas.
  - **US-06 Dashboard**: panel con Chart.js (CDN) — torta por repartición y
    barras por estado.
  - **US-07 Reportes**: exportación PDF con `barryvdh/laravel-dompdf`.

  Incluye el **modelo de datos** reconstruido desde el Excel fuente:
  `proyectos` (+ campo `estado` unificado), `proyecto_componentes`, y las
  tablas de referencia `ministerio_secretarias` y `tecnologias` con sus
  seeders. Además del resto del scaffold de Laravel (diseño Equipoba con Vite).