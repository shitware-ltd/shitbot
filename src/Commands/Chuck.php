<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Chuck extends Command
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://api.chucknorris.io/jokes/random';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!chuck';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        if ($this->bailForBotOrDirectMessage($message)) {
            return;
        }

        if ($chuck = $this->getChuck()) {
            $message->reply("💀 {$chuck['value']}");
        }
    }

    /**
     * @return array|null
     */
    private function getChuck(): array|null
    {
        return Helpers::httpGet(self::API_ENDPOINT);
    }
}
