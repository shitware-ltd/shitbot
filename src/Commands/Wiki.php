<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Wiki
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://en.wikipedia.org/w/api.php';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        $search = implode(
            separator: " ",
            array: $args
        );

        $results = $this->getWiki($search);

        if (count($results ?? [])) {
            $message->reply("I found the following article(s) for `$search`");

            foreach ($results[1] as $key => $value) {
                $message->channel->sendMessage("`$value` - <{$results[3][$key]}>");
            }
        } else {
            $message->reply("I found no results for `$search`");
        }
    }

    /**
     * @param  string  $search
     * @return array|null
     */
    private function getWiki(string $search): array|null
    {
        return Helpers::httpGet(self::API_ENDPOINT."?limit=3&action=opensearch&namespace=0&format=json&search=$search");
    }
}
