# Lexi

Aplicación web de aprendizaje de idiomas basada en Laravel, manteniendo el aspecto del prototipo original y sus URLs terminadas en `.html`.

## Estado actual

Lexi ya funciona como aplicación web real:

- Laravel 12 gestiona rutas, autenticación, sesión, permisos y APIs.
- SQLite se usa como base de datos de desarrollo.
- El frontend conserva el prototipo original, pero ya reutiliza layouts Blade y endpoints reales.
- Auth, perfil, biblioteca y panel admin ya están migrados a Blade.

## Funcionalidades ya implementadas

- Login, registro, logout y recuperación de contraseña con sesión real.
- Perfil persistido con idioma nativo bloqueado y nombre editable.
- Biblioteca de vocabulario persistida en base de datos.
- Catálogo base de biblioteca servido desde backend con fallback al HTML estático.
- Colecciones de usuario persistidas en base de datos.
- Registro de intentos de ejercicios en backend.
- Progreso calculado desde endpoints reales.
- Rutas privadas protegidas con `auth`.
- Rutas de administración protegidas por rol `admin`.

## Arquitectura actual

### Routing y renderizado

- Las páginas principales del prototipo siguen existiendo como archivos `.html` en la raíz.
- Laravel resuelve primero las vistas Blade en [routes/web.php](routes/web.php) y, si no existen, cae al `.html` raíz para conservar URLs y migrar de forma incremental.
- Las páginas privadas y administrativas principales ya usan vistas reales en `resources/views`.

### Frontend

- `public/style.css` y `public/js/script.js` son la fuente de verdad de estilos y comportamiento.
- Los archivos raíz `style.css` y `js/script.js` quedan como wrappers de compatibilidad para no romper el prototipo.
- `biblioteca.html` ya mezcla render estático heredado con catálogo cargado desde `/api/library/state` cuando la base de datos tiene contenido.

### Base de datos

- Driver actual en desarrollo: `sqlite`.
- Fichero actual: `database/database.sqlite`.
- Las migraciones están aplicadas.
- `php artisan db:seed` crea el usuario admin inicial y un catálogo base de vocabulario para la biblioteca.
- `admin-words.html` ya permite ver visualmente filas reales de `words`, `translations`, `categories` y la conexión activa.
- La configuración vive en `.env` y [config/database.php](config/database.php).

## Dirección de despliegue recomendada

- App Laravel en `AWS EC2` con `Apache + PHP`.
- Base de datos en `AWS RDS MySQL`.
- `SQLite` queda como opción cómoda de desarrollo local, no como destino final de producción.
- Plantilla de entorno MySQL: `.env.mysql.example`.
- Plantilla local para pruebas MySQL: `.env.mysql.local.example`.
- Compose local opcional: `docker-compose.mysql.yml`.
- Guía corta de referencia: `docs/deployment-aws-mysql.md`.
- Guía local MySQL: `docs/mysql-local.md`.

## ¿Hace falta pasar a phpMyAdmin?

No.

`phpMyAdmin` no es una base de datos; es solo una interfaz para administrar MySQL o MariaDB. El proyecto puede funcionar perfectamente sin `phpMyAdmin`.

Ahora mismo SQLite es una elección correcta para desarrollo porque:

- reduce complejidad local
- no requiere instalar un servidor de base de datos
- permite avanzar rápido en modelo de datos y lógica

## Cuándo conviene migrar a MySQL o MariaDB

Para un proyecto real desplegado, sí suele ser recomendable pasar a MySQL/MariaDB o PostgreSQL cuando quieras:

- despliegue multiusuario real
- backups y administración más cómodos
- hosting PHP tradicional
- separar aplicación y base de datos en servicios distintos

## Cómo pasar de SQLite a MySQL/MariaDB

1. Crear una base de datos nueva en MySQL o MariaDB.
2. Cambiar en `.env` los valores `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.
3. Ejecutar `php artisan config:clear`.
4. Ejecutar `php artisan migrate`.
5. Si hace falta contenido inicial, preparar seeders y ejecutar `php artisan db:seed`.

Ejemplo mínimo para entorno MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lexi
DB_USERNAME=root
DB_PASSWORD=
```

Lo correcto es migrar por Laravel y sus migraciones, no mover tablas manualmente desde `phpMyAdmin`.

## Opción útil para ver MySQL de forma visual

Si quieres acercarte al escenario real y además ver la base de datos de forma visual, ahora tienes una opción local simple:

1. Levantar `MySQL` y `Adminer` con `docker-compose.mysql.yml`.
2. Usar `.env.mysql.local.example` como base para tu `.env`.
3. Ejecutar `php artisan migrate` y `php artisan db:seed`.

Referencia rápida: `docs/mysql-local.md`.

Scripts útiles ya preparados:

- `scripts/mysql-smoke-test.ps1` para migrar, sembrar y comprobar conteos sobre MySQL local.
- `scripts/serve-mysql-local.ps1` para arrancar Lexi en otro puerto usando MySQL sin tocar `.env`.

## ¿Hay que pasar todo a Blade?

No de golpe.

La estrategia correcta aquí es progresiva:

- mantener HTML estático donde solo hay presentación y JS
- pasar a Blade las superficies que dependen de sesión, permisos o datos del servidor
- extraer después layouts compartidos para header, drawer y footer

La prioridad técnica no es “todo a Blade” por sí solo, sino:

- una sola fuente de verdad para sesión y permisos
- una sola fuente de verdad para los assets
- menos duplicidad de layout
- menos lógica crítica en páginas estáticas

## Trabajo técnico recomendado a continuación

1. Sustituir el bloque masivo de cards estáticas de biblioteca por render 100% desde base de datos.
2. Seguir conectando `app`, `ejercicios` y `progreso` a datos reales donde aún dependan de mockups del DOM.
3. Reducir más deuda de JavaScript heredado en `public/js/script.js`, separando mejor catálogo, progreso y carrito.
4. Preparar seeders/editorial workflow más amplio para catálogo real y administración de palabras.
5. Planificar paso a MySQL/MariaDB o PostgreSQL para producción.
6. Probar al menos una vez el proyecto sobre MySQL local antes del despliegue a RDS.

## Ejecución local

1. Instalar dependencias con `composer install`.
2. Asegurar que existe `database/database.sqlite`.
3. Configurar `.env`.
4. Ejecutar `php artisan migrate`.
5. Ejecutar `php artisan db:seed`.
6. Levantar el servidor con `php artisan serve`.

## Credenciales de desarrollo

- Admin inicial: `admin@lexi.app`
- Password: `password`

## Repositorio

- GitHub: https://github.com/AndresRuggieroGitHub/DIW6

## Autor

Andres Ruggiero
