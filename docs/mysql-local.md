# Lexi - MySQL local opcional

Este proyecto sigue funcionando en desarrollo con `SQLite`, pero ya queda preparada una vía simple para probar `MySQL` local y acercarse al despliegue final con `RDS MySQL`.

## Qué se ha añadido

- `docker-compose.mysql.yml` levanta `MySQL 8.4`.
- El mismo compose levanta `Adminer` en `http://127.0.0.1:8080` para inspección visual.
- `.env.mysql.local.example` deja una configuración local lista para Laravel.

## Arranque mínimo

1. Levantar contenedores:

```powershell
docker compose -f docker-compose.mysql.yml up -d
```

2. Copiar valores de `.env.mysql.local.example` a tu `.env` cuando quieras probar MySQL.

3. Limpiar caché de configuración:

```powershell
php artisan config:clear
```

4. Ejecutar migraciones y seeders:

```powershell
php artisan migrate
php artisan db:seed
```

Atajo ya preparado:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mysql-smoke-test.ps1
```

## Arrancar Lexi sobre MySQL sin tocar `.env`

También queda preparado un script para levantar un segundo servidor Laravel usando variables temporales de entorno:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\serve-mysql-local.ps1
```

Por defecto sirve Lexi en `http://127.0.0.1:8001`.

## Acceso visual

- URL: `http://127.0.0.1:8080`
- Sistema: `MySQL`
- Servidor: `mysql`
- Usuario: `lexi`
- Password: `lexi`
- Base de datos: `lexi`

Si en lugar de abrir `Adminer` prefieres no depender de una herramienta externa, el propio panel admin de Lexi ya muestra muchas tablas reales desde la aplicación.

## Notas

- Esto no sustituye a `RDS`; solo sirve para probar compatibilidad local.
- Si vuelves a `SQLite`, recuerda restaurar las variables `DB_*` en `.env` y volver a limpiar configuración.
- Si `docker compose up -d` falla con `//./pipe/docker_engine`, el daemon de Docker no está arrancado todavía.
- Si `3306` ya está ocupado por otro MySQL local, ajusta el puerto publicado en `docker-compose.mysql.yml` o usa las credenciales correctas de ese servidor.