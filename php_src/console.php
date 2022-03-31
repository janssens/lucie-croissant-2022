#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();

$application = new Application();

foreach (glob(__DIR__.'/Command/*Command.php') as $filename) {
    include_once($filename);
    $path_info = pathinfo($filename);
    $className = $path_info['filename'];
    $application->add(new $className());
}

$application->run();
