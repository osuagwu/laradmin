<?php
//namespace BethelChika\IsThisFake;
/**
 *  This file is included by a register method of a service provider has the '$this' object that can be used here. 
 *  As this file is included, it is essenially part of the service provider register method, so do not do anything you would not do in a register method of a service provider here.
 */
spl_autoload_register(function ($class) {
    $prefix='BethelChika\\IsThisFake\\';
    $base_dir = __DIR__ . '/src/';
    $class = ltrim($class, '\\');
    
     // does the class use the namespace prefix?
     $len = strlen($prefix);
     if (strncmp($prefix, $class, $len) !== 0) {
         // no, move to the next registered autoloader
         return;
     }
         // get the relative class name
    $relative_class = substr($class, $len);
    
    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
    
});

$this->app->register(\BethelChika\IsThisFake\IsThisFakeServiceProvider::class);
