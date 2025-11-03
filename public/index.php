<?php
declare(strict_types=1);

use Core\DB;

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../app/config/config.php';
$dbConfig = require __DIR__ . '/../app/config/db.php';

if (!is_array($dbConfig)) {
    throw new RuntimeException('Database config must be an array, got: ' . gettype($dbConfig));
}

$db = DB::getInstance($dbConfig)->getConnection();

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = parse_url($uri, PHP_URL_PATH);
$uri = trim($uri, '/');
$segments = explode('/', $uri);

$module = $segments[0] !== '' ? strtolower($segments[0]) : 'dashboard';
$action = $segments[1] ?? 'index';

$module = preg_replace('/[^a-z0-9_]/', '', $module);
$action = preg_replace('/[^a-z0-9_]/', '', strtolower($action));

$params = array_slice($segments, 2);

$moduleClass = '\\Modules\\' . ucfirst($module) . '\\Logic';
$moduleFile = __DIR__ . '/../app/modules/' . $module . '/Logic.php';

if (file_exists($moduleFile)) {
    require_once $moduleFile;
    if (class_exists($moduleClass)) {
        $controller = new $moduleClass($db, $config);
        if (method_exists($controller, $action)) {
            call_user_func_array([$controller, $action], $params);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Action not found";
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Module logic class not found";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Module not found";
}
