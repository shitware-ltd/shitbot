<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

class Admin extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!!!!';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        if (! Helpers::isOwner($message)) {
            return;
        }

        $matched = match ($args[0] ?? false) {
            'rest' => $this->rest(),
            'wakeup' => $this->wakeup(),
            default => false,
        };

        if ($matched) {
            $message->react('a:verified:903877054271979522');
        }
    }

    /**
     * @return bool
     */
    private function rest(): bool
    {
        Shitbot::paused(true);

        return true;
    }

    /**
     * @return bool
     */
    private function wakeup(): bool
    {
        Shitbot::paused(false);

        return true;
    }
}
