<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
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
     * @return int
     */
    public function cooldown(): int
    {
        return 5000;
    }

    /**
     * @param  Message  $entity
     * @param  array  $args
     * @return void
     */
    public function handle(Interaction|Message $entity, array $args): void
    {
        coroutine(function (Message $entity, array $args) {
            if ($this->skip($entity)) {
                return;
            }

            $search = Helpers::implodeContent($args);

            $query = http_build_query([
                'key' => Shitbot::config('YOUTUBE_TOKEN'),
                'maxResults' => 1,
                'q' => $search,
                'part' => 'id',
                'type' => 'video',
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->get("https://www.googleapis.com/youtube/v3/search?$query");

                $result = Helpers::json($response);

                if (count($result['items'])) {
                    $reply = "I found the following video for `$search`".PHP_EOL;
                    $reply .= "> https://youtu.be/{$result['items'][0]['id']['videoId']}";

                    $entity->reply($reply);

                    $this->hitCooldown($entity->author);

                    return;
                }
            } catch (Throwable) {
                //Not important
            }

            $entity->reply("I found no videos for `$search`");
        }, $entity, $args);
    }
}
