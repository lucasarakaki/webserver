<?php
// Autoload
require __DIR__.'/vendor/autoload.php';

// Functions
require __DIR__.'/functions/helper.php';

// Load dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
