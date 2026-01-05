<?php
/**
 * Конфигурация модулей приложения.
 * Этот файл подключается из `web.php` как значение ключа `modules`.
 */
return [
    'story' => [
        'class' => 'app\modules\story\Module',
        'python_api_url' => $_ENV['PYTHON_API_URL'] ?? 'http://127.0.0.1:8000',
    ],
];
