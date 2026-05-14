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
    'admin_exists' => DB::table('users')->where('email', 'admin@lexi.app')->exists(),
    'words' => DB::table('words')->count(),
    'translations' => DB::table('translations')->count(),
    'categories' => DB::table('categories')->count(),
    'plans' => DB::getSchemaBuilder()->hasTable('plans') ? DB::table('plans')->count() : 0,
    'subscriptions' => DB::getSchemaBuilder()->hasTable('subscriptions') ? DB::table('subscriptions')->count() : 0,
    'ai_generations' => DB::getSchemaBuilder()->hasTable('ai_generations') ? DB::table('ai_generations')->count() : 0,
]);"