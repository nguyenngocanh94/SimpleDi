#!/usr/bin/env php
<?php

// Find and initialize Composer
use SimpleDi\Generator\AutoGenerator;

$files = array(
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
);

$found = false;
foreach ($files as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

if (!class_exists('Composer\Autoload\ClassLoader', false)) {
    die(
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
}

$PROJECT = getenv('PROJECT');
if(empty($PROJECT)) {
    die("Set PROJECT by: PROJECT=application ./vendor/ericsnguyen/simple-di/bin/generate.\n");
}

$FILE = getenv('FILE');
if(empty($FILE)) {
    die("Set FILE by: FILE=proxy/registry.php ./vendor/ericsnguyen/simple-di/bin/generate.\n");
}

$generator = new AutoGenerator($PROJECT,$FILE);
$generator->write();
?>
