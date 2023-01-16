<?php

namespace ShitwareLtd\Shitbot\Commands;

use Closure;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Activity;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use ShitwareLtd\Shitbot\Support\Status;

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
            'status' => $this->status($args),
            'terminate' => $this->terminate(),
            default => false,
        };

        if ($matched) {
            $message->react('a:verified:903877054271979522')
                ->then(function () use ($matched) {
                    if (is_callable($matched)) {
                        $matched();
                    }
                });
        }
    }

    /**
     * @return bool
     */
    private function rest(): bool
    {
        Shitbot::paused(true);

        Status::set(
            status: Activity::STATUS_IDLE,
            type: Activity::TYPE_WATCHING,
            name: 'the void. 💤'
        );

        return true;
    }

    /**
     * @return bool
     */
    private function wakeup(): bool
    {
        Shitbot::paused(false);

        Status::setDefault();

        return true;
    }

    /**
     * @param  array  $args
     * @return bool
     */
    private function status(array $args): bool
    {
        $flags = array_splice(
            array: $args,
            offset: 1,
            length: 2
        );

        $name = array_splice(
            array: $args,
            offset: 1,
            length: count($args) - 1
        );

        Status::set(
            status: $flags[0],
            type: (int) $flags[1],
            name: Helpers::implodeContent($name)
        );

        return true;
    }

    /**
     * @return Closure
     */
    private function terminate(): Closure
    {
        return fn () => Shitbot::discord()->close();
    }
}
