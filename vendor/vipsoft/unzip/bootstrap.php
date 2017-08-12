<?php
function autoload($class)
{
    $file = strpos($class, '\\') !== false
        ? str_replace('\\', '/', $class)
        : str_replace('_', '/', $class);

    $path = __DIR__ . '/src/' . $file . '.php';

    if (file_exists($path)) {
        include_once $path;
    }
}

spl_autoload_register('autoload');
