<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;

abstract class Command
{
    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    abstract public function handle(Message $message, array $args): void;

    /**
     * @return string
     */
    abstract public function trigger(): string;

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 0;
    }
}
