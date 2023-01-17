<?php

define('SHITBOT_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

use ShitwareLtd\Shitbot\Shitbot;
use Dotenv\Dotenv;

(Dotenv::createImmutable(__DIR__))->load();

$opt = getopt(
    short_options: '',
    long_options: ['install']
);

Shitbot::run(asInstall: isset($opt['install']));
