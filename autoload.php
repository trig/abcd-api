<?php

if (!defined('PROJECT_DIR')) {
    define('PROJECT_DIR', __DIR__);
}

// PSR-4 autoloader
spl_autoload_register(function ($className) {
    $classFile = PROJECT_DIR . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    if(!file_exists($classFile)){
        throw new \RuntimeException("Unable to load class {$className}");
    }
    include_once $classFile;
});