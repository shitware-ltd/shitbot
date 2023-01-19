<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Wiki extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!wiki';
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
    public function handle(Message $entity, array $args): void
    {
        coroutine(function (Message $entity, array $args) {
            if ($this->skip($entity)) {
                return;
            }

            $search = Helpers::implodeContent($args);

            $query = http_build_query([
                'limit' => 5,
                'search' => $search,
                'action' => 'opensearch',
                'namespace' => 0,
                'format' => 'json',
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->get("https://en.wikipedia.org/w/api.php?$query");

                $result = Helpers::json($response);

                if (count($result[1] ?? [])) {
                    $reply = "I found the following article(s) for `$search`".PHP_EOL;

                    foreach ($result[1] as $key => $value) {
                        $reply .= "> `$value` - <{$result[3][$key]}>".PHP_EOL;
                    }

                    $entity->reply($reply);

                    $this->hitCooldown($entity->author);

                    return;
                }
            } catch (Throwable) {
                //Not important
            }

            $entity->reply("I found no results for `$search`");
        }, $entity, $args);
    }
}
