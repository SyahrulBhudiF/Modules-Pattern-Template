<?php

use Core\DB;

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';


$config = require_once __DIR__ . '/../app/config/config.php';

$configDb = require_once __DIR__ . '/../app/config/db.php';
$db = DB::getInstance($configDb)->getConnection();

$module = isset($_GET['module']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['module']) : 'Dashboard';
$action = isset($_GET['action']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['action']) : 'index';

$moduleClass = '\\Modules\\' . $module . '\\Logic';

$moduleFile = __DIR__ . '/../app/modules/' . $module . '/logic.php';
if (file_exists($moduleFile)) {
    require_once $moduleFile;

    if (class_exists($moduleClass)) {
        $controller = new $moduleClass($db, $config);

        if (method_exists($controller, $action)) {
            $controller->{$action}();

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
