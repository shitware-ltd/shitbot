<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class YouTube
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://www.googleapis.com/youtube/v3/search';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        $search = Helpers::implodeContent($args);

        $result = $this->getVideo($search);

        if ($result && count($result['items'])) {
            $reply = "I found the following video for `$search`".PHP_EOL;
            $reply .= "> https://youtu.be/{$result['items'][0]['id']['videoId']}";

            $message->reply($reply);

            return;
        }

        $message->reply("I found no videos for `$search`");
    }

    /**
     * @param  string  $search
     * @return array|null
     */
    private function getVideo(string $search): array|null
    {
        return Helpers::httpGet(
            endpoint: self::API_ENDPOINT,
            query: [
                'key' => $_ENV['YOUTUBE_TOKEN'],
                'maxResults' => 1,
                'q' => $search,
                'part' => 'id',
                'type' => 'video',
            ]
        );
    }
}
