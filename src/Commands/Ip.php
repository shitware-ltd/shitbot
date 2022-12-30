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

            if (empty($ip = Helpers::implodeContent($args))) {
                return;
            }

            $query = http_build_query([
                'key' => Shitbot::$config['IP_TOKEN'],
                'fields' => 33292287,
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Helpers::browser()->get("https://pro.ip-api.com/json/$ip?$query");

                $result = json_decode(
                    json: $response->getBody()->getContents(),
                    associative: true
                );

                $reply = '```json'.PHP_EOL;

                $reply .= json_encode(
                        value: $result,
                        flags: JSON_PRETTY_PRINT
                    ).PHP_EOL;

                $reply .= '```';

                $message->reply($reply);
            } catch (Throwable) {
                //Not important
            }
        }, $message, $args);
    }
}
