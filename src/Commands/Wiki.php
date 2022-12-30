<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
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
                'limit' => 5,
                'search' => $search,
                'action' => 'opensearch',
                'namespace' => 0,
                'format' => 'json',
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Helpers::browser()->get("https://en.wikipedia.org/w/api.php?$query");

                $result = Helpers::json($response);

                if (count($result[1] ?? [])) {
                    $reply = "I found the following article(s) for `$search`".PHP_EOL;

                    foreach ($result[1] as $key => $value) {
                        $reply .= "> `$value` - <{$result[3][$key]}>".PHP_EOL;
                    }

                    $message->reply($reply);

                    return;
                }
            } catch (Throwable) {
                //Not important
            }

            $message->reply("I found no results for `$search`");
        }, $message, $args);
    }
}
