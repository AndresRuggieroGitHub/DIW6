param(
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbName = 'lexi',
    [string]$DbUser = 'lexi',
    [string]$DbPassword = 'lexi',
    [switch]$EmptyPassword
)

$ErrorActionPreference = 'Stop'

Set-Location "$PSScriptRoot\.."

$resolvedPassword = if ($EmptyPassword.IsPresent) { '' } else { $DbPassword }

$env:APP_ENV = 'local'
$env:DB_CONNECTION = 'mysql'
$env:DB_HOST = $DbHost
$env:DB_PORT = $DbPort
$env:DB_DATABASE = $DbName
$env:DB_USERNAME = $DbUser
$env:DB_PASSWORD = $resolvedPassword

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan tinker --execute="dump([
    'users' => DB::table('users')->count(),
    'words' => DB::table('words')->count(),
    'translations' => DB::table('translations')->count(),
    'categories' => DB::table('categories')->count(),
]);"