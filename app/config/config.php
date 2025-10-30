<?php

require_once __DIR__ . '/env.php';

return [
    'app_env' => getenv('APP_ENV'),
    'app_debug' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),
    'app_url' => getenv('APP_URL'),
    'session_cookie_name' => getenv('SESSION_COOKIE_NAME'),
];
