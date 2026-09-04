# Catalogo de Proyectos

Aplicación web (Laravel 13, PHP 8.3) para gestionar el **portfolio de proyectos de un organismo público**: alta, listado con filtros, edición, estados, archivado, un dashboard con gráficos y exportación de reportes en PDF.

Construido sobre dos documentos fuente:
- `CatalogoProductos.xlsx` — define el modelo de datos (campos, jerarquía de áreas, tecnologías).
- `HU Catalogo proyecto.pdf` — define las 7 historias de usuario implementadas.

---

## Requisitos

- PHP 8.3+
- Composer 2
- MariaDB 10.6+ (u otro motor MySQL compatible)
- Node.js + npm (solo para compilar assets con Vite)

Instalación:

```bash
composer install
cp .env.example .env
php artisan key:generate
# configurar las variables DB_* en .env
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

---

## Historias de Usuario

| ID | Módulo | Historia | Criterios de Aceptación y Usabilidad |
|----|--------|----------|--------------------------------------|
| **US-01** | Inicio | Como Director, quiero acceder al sistema con un inicio de sesión seguro (SSO), para proteger la información estratégica sin fricciones de acceso. | - Soporte para `Single Sign-On` (Google/Microsoft).<br>- Redirección automática al Dashboard tras el login. |
| **US-02** | Listados | Como Director, quiero ver un listado principal del portfolio de proyectos, para tener un panorama rápido de todas las iniciativas. | - La tabla debe incluir filtros rápidos (Área solicitante, Última Actualización, trimestre).<br>- Paginación para carga rápida. |
| **US-03** | ABM | Como PMO / Director, quiero dar de alta un nuevo proyecto definiendo sus parámetros base. | - Formulario de alta con campos obligatorios marcados visualmente. Campos: Nombre de Proyecto (texto), Nombre Proyecto Marca (texto), Área Solicitante (desplegable), Componentes de la solución 1 a n (Nombre, Tipo, Tecnología, Versión, Exposición a internet, Observaciones, Repositorio GitLab, URL dev, Nombre normalizado, URL OpenShift) y Estado. |
| **US-04** | ABM | Como Director, quiero editar datos o realizar la "baja lógica" (archivar) de un proyecto, para mantener el portfolio actualizado frente a cambios de negocio. | - Los proyectos archivados no se borran de la base de datos (auditoría).<br>- Requiere confirmación en un modal emergente. |
| **US-05** | Estados | Como Director, quiero cambiar el estado del proyecto (Ej: Planificación, Ejecución, Frenado, Finalizado), para reflejar su madurez actual. | - Cambio de estado mediante menú desplegable accesible desde el listado.<br>- Dispara notificaciones automáticas al equipo (esbozado). |
| **US-06** | Dashboard | Como Director, quiero visualizar un panel interactivo con la cantidad de proyectos por repartición. | - Gráficos limpios (torta y barras). |
| **US-07** | Reportes | Como Director, quiero exportar el estado actual del portfolio y dashboard en PDF, para presentarlo en las reuniones del comité directivo. | - Botón de exportación visible en el menú principal.<br>- El PDF debe respetar el formato visual del dashboard. |

### Implementación por historia

| ID | Estado | Notas de implementación |
|----|--------|-------------------------|
| US-01 | ✅ | Login **mock** (botones Google/Microsoft + "Simulación de ingreso") con middleware propio (`auth.mock`, `auth.mock.landmark`). Sin OAuth real. |
| US-02 | ✅ | Listado con búsqueda, filtros por Área/Estado/Trimestre, orden por actualización y paginación (12 por página). |
| US-03 | ✅ | Formulario único (no wizard) con componentes 1..n dinámicos y campos obligatorios marcados. |
| US-04 | ✅ | **Baja lógica**: archivar setea `estado = 'archivado'` (no borra el registro), con modal de confirmación. |
| US-05 | ✅ | Desplegable de estado en el listado. Notificaciones al equipo esbozadas (hook para evento futuro). |
| US-06 | ✅ | Dashboard con Chart.js (CDN): torta por repartición + barras por estado. |
| US-07 | ✅ | Exportación PDF con `barryvdh/laravel-dompdf`. |

> Decisiones de alcance consensuadas: se unificó "estado" y "archivado" en un único campo **`estado`** (US-04 + US-05); el alta usa **form único** (no wizard); el login es **mock sin OAuth**; la versión del componente es **texto libre**.

---

## Modelo de datos

Base de datos: `catalogo_proyectos`.

### Tablas de referencia (master data)

#### `ministerio_secretarias` — áreas/organismos (jerarquía 3 niveles)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint PK | Identificador |
| `nivel2` | string | Jerarquía general (ej. "Ministerios", "Jefatura de Gobierno") |
| `nivel3` | string | Área / ministerio (ej. "Ministerio de Salud") |
| `nivel4` | string nullable | Secretaría / dependencia (ej. "Secretaría de Transporte") |
| `timestamps` | — | `created_at` / `updated_at` |

Datos sembrados (16 filas): 4 ministerios con nivel3 `-` (**Jefatura de Gobierno**, **Vicejefatura de Gobierno**), más los ministerios de **Hacienda y Finanzas**, **Salud**, **Educación**, **Desarrollo Humano y Hábitat**, **Justicia y Seguridad**, **Infraestructura**, **Desarrollo Económico**, **Espacio Público e Higiene Urbana**, **Cultura** y **Jefatura de Gabinete de Ministros** (este último con 4 secretarías de nivel4).

#### `tecnologias` — catálogo de tecnologías

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint PK | Identificador |
| `categoria` | string | Categoría (BackEnd, Frontend, Base de datos) |
| `nombre` | string | Nombre de la tecnología (ej. Laravel, Angular, PostgreSQL) |
| `timestamps` | — | `created_at` / `updated_at` |

Datos sembrados (19 filas): BackEnd (10), Frontend (4), Base de datos (5).

### Tablas del dominio

#### `proyectos` — cabecera del portfolio

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint PK | Identificador |
| `nombre_proyecto` | string | Nombre del proyecto |
| `nombre_proyecto_marca` | string | Nombre de marca del proyecto |
| `area_solicitante_id` | bigint FK nullable → `ministerio_secretarias(id)` | Área solicitante (nivel3→nivel4) |
| `estado` | string(20) default `planificacion` | `planificacion` / `ejecucion` / `frenado` / `finalizado` / `archivado` (baja lógica) |
| `timestamps` | — | `created_at` / `updated_at` |

#### `proyecto_componentes` — componentes de la solución (relación 1..N con `proyectos`)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint PK | Identificador |
| `proyecto_id` | bigint FK → `proyectos(id)` | Proyecto padre (`cascadeOnDelete`) |
| `nombre_componente` | string | Nombre del componente |
| `tipo_componente` | string | `API` / `BACKEND` / `FRONT` / `FULLSTACK` / `Dependencia Propia (Npm, maven, etc)` / `MOBILE` / `BASE DE DATOS` |
| `tecnologia` | string | Tecnología usada |
| `version` | string | Versión (texto libre) |
| `exposicion_internet` | string | `Externo` / `Interno` / `No expuesta` |
| `observaciones` | text nullable | Notas |
| `repositorio_gitlab` | string | URI del repositorio GitLab |
| `url_dev` | string | URL del entorno de desarrollo |
| `nombre_normalizado` | string | Nombre normalizado del componente |
| `url_openshift` | string | URL de OpenShift (entorno productivo) |
| `timestamps` | — | `created_at` / `updated_at` |

> El resto de las tablas (`users`, `sessions`, `cache`, `jobs`, etc.) son las que trae Laravel por scaffolding estándar y no forman parte del dominio del portfolio.

---

## Arquitectura (resumen)

- **Rutas**: todas las rutas de la app están detrás del middleware `auth.mock` (requieren sesión), salvo `/login` (público) que usa `auth.mock.landmark`. El endpoint `GET /areas?nivel3=…` alimenta el desplegable dependiente de Área Solicitante en el form de alta/edición.
- **Controllers**: `ProyectoController` (CRUD, listado, estados, dashboard, reporte, áreas) y `AuthController` (login/logout mock).
- **Modelos**: `Proyecto`, `ProyectoComponente`, `MinisterioSecretaria`, `Tecnologia`. El modelo `Proyecto` expone `ESTADOS_ACTIVOS`, `ESTADO_ARCHIVADO`, `ESTADOS_LABELS` y los scopes `NoArchivados` / `IncluyendoArchivados`.
- **Estilos**: diseño en bloques de color (#0054e9, #8de2d6, #55b4a8, #CAF2EC, warning #ffc409) compilado con Vite. Chart.js por CDN.

---

## Tests

```bash
php artisan test --compact
```

La suite de features cubre:
- `AuthFlowTest` — US-01 (login mock, protección de rutas, logout).
- `EstadoYArchivoTest` — US-04 (archivar = baja lógica) y US-05 (cambio de estado + validación).
- `AltaCircuitoTest` — circuito completo US-03 (login → form → guardar).