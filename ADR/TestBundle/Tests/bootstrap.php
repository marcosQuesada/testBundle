<?php
$file = __DIR__.'/../../../../../../app/bootstrap.php.cache';
$autoload = require_once $file;

/*
$file = __DIR__.'/../../../../../autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite (composer install --dev).');
}

$autoload = require_once $file;*/