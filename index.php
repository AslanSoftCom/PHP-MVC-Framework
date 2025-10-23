<?php

// Güvenlik başlıkları
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Framework başlangıcı
define('CORE_DIR', __DIR__ . '/core/');
define('APP_DIR', __DIR__ . '/app/');
define('BASE_PATH', __DIR__);

$coreClasses = ['ErrorHandler', 'Controller', 'Router', 'Request', 'View', 'Database', 'Session', 'Cookie', 'Fingerprint', 'Encryptor', 'Generate', 'Helpers', 'Upload'];

array_map(function($class) {
    require_once CORE_DIR . $class . '.php';
}, $coreClasses);

// Hata yöneticisini başlat (production'da false yapılmalı)
ErrorHandler::init(true);

// Rotaları yükle ve dispatch et
require_once __DIR__ . '/app/config/router.php';
