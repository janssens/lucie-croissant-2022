#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Command\NationBuilderEventCommand;

//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();

$application = new Application();
$application->add(new NationBuilderEventCommand());

$application->run();
