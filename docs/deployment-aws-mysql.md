# Lexi - Despliegue objetivo AWS

Objetivo de referencia para el proyecto:

- Aplicación Laravel en `AWS EC2`
- `Apache + PHP` en la instancia
- Base de datos `MySQL` en `AWS RDS`

## Estructura recomendada

1. `EC2` para servir Laravel.
2. `RDS MySQL` para la base de datos.
3. Variables de entorno en el servidor basadas en `.env.mysql.example`.
4. Migraciones y seeders ejecutados con Laravel, no manualmente.

## Flujo mínimo de despliegue

1. Subir código a la instancia.
2. Ejecutar `composer install --no-dev --optimize-autoloader`.
3. Crear `.env` desde `.env.mysql.example`.
4. Configurar `APP_KEY` con `php artisan key:generate --force`.
5. Ejecutar `php artisan migrate --force`.
6. Si necesitas contenido inicial, ejecutar `php artisan db:seed --force`.
7. Ejecutar `php artisan config:cache`, `php artisan route:cache` y `php artisan view:cache`.

## Base de datos

No usar `phpMyAdmin` como base de datos.

`phpMyAdmin` sería solo una herramienta opcional de inspección para MySQL. La base real en este escenario es `RDS MySQL`.

## Desarrollo local

- `SQLite` sigue siendo válido para desarrollo rápido.
- Conviene probar también con `MySQL` antes del despliegue para detectar diferencias de compatibilidad.
- Para eso se ha dejado preparado `docker-compose.mysql.yml`, `.env.mysql.local.example` y la guía `docs/mysql-local.md`.