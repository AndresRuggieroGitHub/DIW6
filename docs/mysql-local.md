# Lexi - MySQL local opcional

Este proyecto sigue funcionando en desarrollo con `SQLite`, pero ya queda preparada una vía simple para probar `MySQL` local y acercarse al despliegue final con `RDS MySQL`.

## Qué se ha añadido

- `docker-compose.mysql.yml` levanta `MySQL 8.4`.
- El mismo compose levanta `Adminer` en `http://127.0.0.1:8080` para inspección visual.
- `.env.mysql.local.example` deja una configuración local lista para Laravel.

## Arranque mínimo

1. Tener un MySQL/MariaDB local disponible.

Con Docker:

```powershell
docker compose -f docker-compose.mysql.yml up -d
```

Con XAMPP/MariaDB ya arrancado no hace falta Docker.

2. Bootstrap completo de la base MySQL sin tocar `.env`:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-mysql-local.ps1
```

Con XAMPP y `root` sin password:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-mysql-local.ps1 -DbUser root -EmptyPassword
```

3. Copiar valores de `.env.mysql.local.example` a tu `.env` solo si quieres que MySQL pase a ser tu entorno por defecto.

4. Limpiar caché de configuración:

```powershell
php artisan config:clear
```

5. Ejecutar migraciones y seeders manualmente solo si no usas el script de bootstrap:

```powershell
php artisan migrate
php artisan db:seed
```

Atajo ya preparado:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mysql-smoke-test.ps1
```

Ese smoke test ahora también comprueba el admin inicial y los módulos de `billing` y `ai`.

Si usas XAMPP con `root` y sin password:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\mysql-smoke-test.ps1 -DbUser root -EmptyPassword
```

## Arrancar Lexi sobre MySQL sin tocar `.env`

También queda preparado un script para levantar un segundo servidor Laravel usando variables temporales de entorno:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\serve-mysql-local.ps1
```

Por defecto sirve Lexi en `http://127.0.0.1:8001`.

Con XAMPP y `root` sin password:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\serve-mysql-local.ps1 -DbUser root -EmptyPassword
```

## Error frecuente: `10061` o sin escucha en `3306`

Si `bootstrap-mysql-local.ps1` o `switch-env-to-mysql-local.ps1` fallan diciendo que no hay ningun servidor MySQL/MariaDB escuchando en `127.0.0.1:3306`, el problema no es Laravel: el daemon de MySQL no esta arrancado.

Opciones tipicas:

- iniciar MySQL desde XAMPP
- levantar `docker compose -f docker-compose.mysql.yml up -d`
- usar otro host o puerto con `-DbHost` y `-DbPort`

Flujo recomendado si quieres trabajar ya proyectando todo a MySQL:

1. `bootstrap-mysql-local.ps1`
2. `mysql-smoke-test.ps1`
3. `serve-mysql-local.ps1`

## Pasar `.env` a MySQL local

Si quieres que MySQL deje de ser solo un servidor alternativo y pase a ser el entorno principal del proyecto, puedes conmutar `.env` con backup automático:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\switch-env-to-mysql-local.ps1 -DbUser root -EmptyPassword -Bootstrap
```

Ese script:

- guarda una copia inicial en `.env.sqlite.backup`
- cambia `DB_CONNECTION` y `DB_*` en `.env`
- limpia configuración
- opcionalmente migra y siembra MySQL si usas `-Bootstrap`

Para volver al flujo SQLite local:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\switch-env-to-sqlite.ps1
```

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