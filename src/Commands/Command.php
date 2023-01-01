<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

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

    /**
     * @param  Throwable  $e
     * @return string
     */
    protected function formatError(Throwable $e): string
    {
        $reply = 'You broke me. Please try again.'.PHP_EOL;
        $reply .= '```diff'.PHP_EOL;
        $reply .= "- {$e->getMessage()}".PHP_EOL;
        $reply .= '```';

        return $reply;
    }
}
