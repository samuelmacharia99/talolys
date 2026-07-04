<?php

/**
 * Laravel router for PHP's built-in development server.
 *
 * Usage: php -S 0.0.0.0:80 server.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request is for a real file, serve it directly.
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Otherwise, route through the Laravel front controller.
require_once __DIR__ . '/index.php';
