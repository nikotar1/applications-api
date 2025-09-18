<?php

use Dotenv\Dotenv;

$root = dirname(__DIR__);
if (is_file($root.'/.env')) {
    $dotenv = Dotenv::createImmutable($root);
    $dotenv->safeLoad();
}
