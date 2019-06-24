<?php

// define('FRAMEWORK_START', microtime(true));

require 'app/lib/Dev.php';
require 'app/lib/helpers.php';

use app\core\Router;

spl_autoload_register(function($class) {
    $path = str_replace('\\', '/', $class.'.php');
    if (file_exists($path)) {
        include $path;
    }
});

session_start();

$router = new Router;
$router->run();