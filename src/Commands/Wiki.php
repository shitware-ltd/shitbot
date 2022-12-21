<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;
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

        if ($results && count($formatted = $this->formatResults($results))) {
            $message->reply("I found the following article(s) for ( $search ) :");

            foreach ($formatted as $result) {
                $message->channel->sendMessage($result);
            }
        }
    }

    /**
     * @param  string  $search
     * @return array
     */
    private function getWiki(string $search): array
    {
        return Helpers::getHttp(self::API_ENDPOINT."?limit=3&action=opensearch&namespace=0&format=json&search=$search");
    }

    /**
     * Format wiki results. Index 1 contains titles, index 3 contains the links.
     *
     * @param  array  $results
     * @return array
     */
    private function formatResults(array $results): array
    {
        return Collection::make($results[1])
            ->map(fn ($value, $key) => $value.' - '.$results[3][$key])
            ->all();
    }
}
