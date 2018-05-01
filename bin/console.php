#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BracyCLI\Commands\BalancedBracesCommand;
use Symfony\Component\Console\Application;

$app = new Application('Bracy CLI app', '1.0.0');

$app->add(new BalancedBracesCommand());

try {
    $app->run();
} catch (\Exception $e) {
    echo sprintf("The app failed to run." . PHP_EOL . "%s", $e->getMessage());
}
