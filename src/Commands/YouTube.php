<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

class YouTube extends Command
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://www.googleapis.com/youtube/v3/search';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!yt';
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
                'key' => Shitbot::$config['YOUTUBE_TOKEN'],
                'maxResults' => 1,
                'q' => $search,
                'part' => 'id',
                'type' => 'video',
            ]
        );
    }
}
