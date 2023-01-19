<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Ip extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!ip';
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

            if (empty($ip = Helpers::implodeContent($args))) {
                return;
            }

            $query = http_build_query([
                'key' => Shitbot::config('IP_TOKEN'),
                'fields' => 33292287,
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->get("https://pro.ip-api.com/json/$ip?$query");

                $result = Helpers::json($response);

                $reply = '```json'.PHP_EOL;
                $reply .= json_encode(
                    value: $result,
                    flags: JSON_PRETTY_PRINT
                ).PHP_EOL;
                $reply .= '```';

                $entity->reply($reply);

                $this->hitCooldown($entity->author);
            } catch (Throwable) {
                //Not important
            }
        }, $entity, $args);
    }
}
