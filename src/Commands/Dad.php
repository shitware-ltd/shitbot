<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Dad
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://icanhazdadjoke.com/';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        $message->reply("👨 {$this->getDaddy()['joke']}");
    }

    /**
     * @return array
     */
    private function getDaddy(): array
    {
        return Helpers::getHttp(self::API_ENDPOINT);
    }
}
