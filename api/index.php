<?php

// Ensure writable directories in /tmp for Vercel serverless environment
$tmpDirs = [
    '/tmp/views',
    '/tmp/sessions',
    '/tmp/cache',
    '/tmp/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Prevent Symfony/Laravel from treating /api as base URL prefix
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Forward Vercel serverless requests to Laravel public index
require __DIR__ . '/../public/index.php';
