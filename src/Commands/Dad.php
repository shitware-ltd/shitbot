<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Dad extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!daddy';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        coroutine(function (Message $message) {
            if ($this->bailForBotOrDirectMessage($message)) {
                return;
            }

            try {
                /** @var ResponseInterface $response */
                $response = yield Helpers::browser()->get('https://icanhazdadjoke.com/');

                $result = json_decode(
                    json: $response->getBody()->getContents(),
                    associative: true
                );

                $message->reply("👨 {$result['joke']}");
            } catch (Throwable) {
                //Not important
            }
        }, $message);
    }
}
