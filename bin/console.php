#!/usr/bin/env php
<?php

use BracyCLI\Commands\BalancedBracesCommand;
use Symfony\Component\Console\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    $app = new Application('Bracy CLI app', '1.0.0');

    $app->add(new BalancedBracesCommand());

    $app->run();
} catch (\Throwable $e) {
    echo sprintf('The app failed to run.' . PHP_EOL . "%s", $e->getMessage());
}
