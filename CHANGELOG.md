# Changelog

Todas las modificaciones notables de **Catalogo de Proyectos** se documentan en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y el proyecto sigue la convención de [Commits Convencionales](https://www.conventionalcommits.org/es/v1.0.0/).

---

## [03bbbb6] - 2026-09-04

### Added
- **Vista Lista en "Proyectos"** (`feat`): se agregó un toggle **Tarjetas / Lista**
  en el listado (`resources/views/proyectos/index.blade.php`). La vista Lista
  (nuevo partial `resources/views/proyectos/partials/_row.blade.php`) muestra,
  por cada proyecto en una fila, la **misma información** que la card: nombre +
  badge de estado, marca, área solicitante, n.° de componentes, selector de
  cambio de estado (o "Archivado") y acciones Ver / Editar / Archivar.
- **Preferencia de vista guardada en el navegador** (`feat`): el toggle persiste
  la elección en `localStorage` (clave `catalogo.vista`), sin recargar la página
  ni requerir backend. Por defecto muestra Tarjetas.

### Changed
- **Buscador en fila propia** (`feat`): el filtro "Buscar" de `index.blade.php`
  pasó de compartir fila con los demás filtros a ocupar **una fila completa a
  ancho de página, arriba** de los filtros generales (Área solicitante, Estado,
  Trimestre, Última actualización). El input es más alto (`py-3`) y con
  `autofocus`, de modo que el texto escrito es legible.
- **Dashboard con dos tarjetas** (`feat`): se eliminaron las tarjetas
  **"Mayor repartición"** y **"Mayor estado"** (no eran indicadores relevantes).
  Quedan **"Proyectos activos"** y **"Reparticiones"**, y la grilla pasó de
  4 columnas (`xl:grid-cols-4`) a 2 (`sm:grid-cols-2`). También se eliminó el
  cálculo huérfano de `$maxRep`/`$maxEst`.
- **Login solo con Microsoft** (`feat`): se eliminó el botón **"Ingresar con
  Google"** de `resources/views/auth/login.blade.php`; queda únicamente
  **"Ingresar con Microsoft"**, manteniendo el campo de Nombre.

### Files
- `resources/views/auth/login.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/proyectos/index.blade.php`
- `resources/views/proyectos/partials/_row.blade.php` (nuevo)

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