<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Insult
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://evilinsult.com/generate_insult.php?lang=en&type=json';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        if ($insult = $this->getInsult()) {
            $message->reply("{$message->author->username}, {$insult['insult']}");
            $message->react('🖕');
        }
    }

    /**
     * @return array|null
     */
    private function getInsult(): array|null
    {
        return Helpers::httpGet(self::API_ENDPOINT);
    }
}
