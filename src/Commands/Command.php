<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

abstract class Command
{
    /**
     * @param  Message  $message
     * @param  array  $args
     * @return  void
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

    /**
     * @param  Message  $message
     * @return bool
     *
     * @throws NoPermissionsException
     */
    protected function bailForBotOrDirectMessage(Message $message): bool
    {
        if (Helpers::shouldProceed($message)) {
            return false;
        }

        if (! $message->author->bot
            && $message->guild === null) {
            $message->channel->sendMessage('You have no power to command me here 🖕');
        }

        return true;
    }
}
