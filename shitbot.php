<?php

include __DIR__.'/vendor/autoload.php';

use ShitwareLtd\Shitbot\ShitbotProvider;

(Dotenv\Dotenv::createImmutable(__DIR__))->load();

ShitbotProvider::boot();
