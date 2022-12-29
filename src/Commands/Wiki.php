<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Wiki extends Command
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://en.wikipedia.org/w/api.php';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!wiki';
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

        $search = Helpers::implodeContent($args);

        $results = $this->getWiki($search);

        if (count($results ?? []) && count($results[1])) {
            $reply = "I found the following article(s) for `$search`".PHP_EOL;

            foreach ($results[1] as $key => $value) {
                $reply .= "> `$value` - <{$results[3][$key]}>".PHP_EOL;
            }

            $message->reply($reply);

            return;
        }

        $message->reply("I found no results for `$search`");
    }

    /**
     * @param  string  $search
     * @return array|null
     */
    private function getWiki(string $search): array|null
    {
        return Helpers::httpGet(
            endpoint: self::API_ENDPOINT,
            query: [
                'limit' => 5,
                'search' => $search,
                'action' => 'opensearch',
                'namespace' => 0,
                'format' => 'json',
            ]
        );
    }
}
