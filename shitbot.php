<?php

require __DIR__.'/vendor/autoload.php';

use ShitwareLtd\Shitbot\Shitbot;

(Dotenv\Dotenv::createImmutable(__DIR__))->load();

Shitbot::run();
