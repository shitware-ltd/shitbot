<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Chuck
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://api.chucknorris.io/jokes/random';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
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
