param(
    [int]$Port = 8001
)

$ErrorActionPreference = 'Stop'

Set-Location "$PSScriptRoot\.."

$env:APP_ENV = 'local'
$env:APP_URL = "http://127.0.0.1:$Port"
$env:DB_CONNECTION = 'mysql'
$env:DB_HOST = '127.0.0.1'
$env:DB_PORT = '3306'
$env:DB_DATABASE = 'lexi'
$env:DB_USERNAME = 'lexi'
$env:DB_PASSWORD = 'lexi'

php artisan serve --host=127.0.0.1 --port=$Port