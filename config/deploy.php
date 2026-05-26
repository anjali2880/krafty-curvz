<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SSH Connection Details
    |--------------------------------------------------------------------------
    | Configure via .env — never hardcode credentials here.
    */
    'host'     => env('DEPLOY_HOST', ''),
    'port'     => (int) env('DEPLOY_PORT', 22),
    'username' => env('DEPLOY_USER', ''),
    'password' => env('DEPLOY_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Project Path on Remote Server
    |--------------------------------------------------------------------------
    */
    'path' => env('DEPLOY_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | SSH Timeout (seconds)
    |--------------------------------------------------------------------------
    | Increase if composer install or migrate take a long time.
    */
    'timeout' => (int) env('DEPLOY_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Deployment Commands
    |--------------------------------------------------------------------------
    | Executed sequentially inside DEPLOY_PATH. Do NOT add npm install/build —
    | frontend assets are pre-built and committed to git.
    */
    'commands' => [
        'git pull origin main',
        'composer install --no-dev --optimize-autoloader',
        'php artisan migrate --force',
        'php artisan optimize:clear',
    ],

];
