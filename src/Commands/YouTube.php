<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class YouTube extends Command
{
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
     */
    public function handle(Message $message, array $args): void
    {
        coroutine(function (Message $message, array $args) {
            if ($this->bailForBotOrDirectMessage($message)) {
                return;
            }

            $search = Helpers::implodeContent($args);

            $query = http_build_query([
                'key' => Shitbot::$config['YOUTUBE_TOKEN'],
                'maxResults' => 1,
                'q' => $search,
                'part' => 'id',
                'type' => 'video',
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Helpers::browser()->get("https://www.googleapis.com/youtube/v3/search?$query");

                $result = json_decode(
                    json: $response->getBody()->getContents(),
                    associative: true
                );

                if (count($result['items'])) {
                    $reply = "I found the following video for `$search`".PHP_EOL;
                    $reply .= "> https://youtu.be/{$result['items'][0]['id']['videoId']}";

                    $message->reply($reply);

                    return;
                }
            } catch (Throwable) {
                //Not important
            }

            $message->reply("I found no videos for `$search`");
        }, $message, $args);
    }
}
