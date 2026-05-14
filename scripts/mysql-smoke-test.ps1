$ErrorActionPreference = 'Stop'

Set-Location "$PSScriptRoot\.."

$env:APP_ENV = 'local'
$env:DB_CONNECTION = 'mysql'
$env:DB_HOST = '127.0.0.1'
$env:DB_PORT = '3306'
$env:DB_DATABASE = 'lexi'
$env:DB_USERNAME = 'lexi'
$env:DB_PASSWORD = 'lexi'

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan tinker --execute="dump([
    'users' => DB::table('users')->count(),
    'words' => DB::table('words')->count(),
    'translations' => DB::table('translations')->count(),
    'categories' => DB::table('categories')->count(),
]);"