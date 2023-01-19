<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
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
     * @param  Message  $entity
     * @param  array  $args
     * @return void
     */
    public function handle(Message $entity, array $args): void
    {
        coroutine(function (Message $entity) {
            if ($this->skip($entity)) {
                return;
            }

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->get('https://icanhazdadjoke.com/');

                $result = Helpers::json($response);

                $entity->reply("👨 {$result['joke']}");
            } catch (Throwable) {
                //Not important
            }
        }, $entity);
    }
}
