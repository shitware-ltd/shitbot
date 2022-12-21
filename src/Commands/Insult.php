<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Insult
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://evilinsult.com/generate_insult.php';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        if ($insult = $this->getInsult()) {
            $message->reply("{$message->author->username}, {$insult['insult']}");
        }
    }

    /**
     * @return array|null
     */
    private function getInsult(): array|null
    {
        return Helpers::httpGet(
            endpoint: self::API_ENDPOINT,
            query: [
                'lang' => 'en',
                'type' => 'json',
            ]
        );
    }
}
