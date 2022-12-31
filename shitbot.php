<?php

require __DIR__.'/vendor/autoload.php';

use ShitwareLtd\Shitbot\Shitbot;

(Dotenv\Dotenv::createImmutable(__DIR__))->load();

$opt = getopt(
    short_options: '',
    long_options: ['install']
);


Shitbot::run(asInstall: isset($opt['install']));
