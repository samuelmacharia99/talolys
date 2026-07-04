<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Serve static assets from project root when using PHP's built-in server
if (php_sapi_name() === 'cli-server') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $projectRoot = dirname(__DIR__, 2);
    $assetPath = $projectRoot . $uri;

    if ($uri !== '/' && is_file($assetPath)) {
        $mimeTypes = [
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'json' => 'application/json',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
            'ico'  => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2'=> 'font/woff2',
            'ttf'  => 'font/ttf',
            'eot'  => 'application/vnd.ms-fontobject',
            'otf'  => 'font/otf',
            'map'  => 'application/json',
            'webp' => 'image/webp',
            'mp4'  => 'video/mp4',
            'pdf'  => 'application/pdf',
            'php'  => 'text/plain',
        ];
        $ext = strtolower(pathinfo($assetPath, PATHINFO_EXTENSION));
        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
        }
        readfile($assetPath);
        return;
    }
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
