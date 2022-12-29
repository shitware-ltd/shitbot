<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Dad extends Command
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://icanhazdadjoke.com/';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!daddy';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        if ($joke = $this->getDaddy()) {
            $message->reply("👨 {$joke['joke']}");
        }
    }

    /**
     * @return array|null
     */
    private function getDaddy(): array|null
    {
        return Helpers::httpGet(self::API_ENDPOINT);
    }
}
