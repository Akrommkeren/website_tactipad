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

// Forward Vercel serverless requests to Laravel public index
require __DIR__ . '/../public/index.php';
