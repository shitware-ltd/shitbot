<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Chuck extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!chuck';
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
                $response = yield Shitbot::browser()->get('https://api.chucknorris.io/jokes/random');

                $result = Helpers::json($response);

                $entity->reply("💀 {$result['value']}");
            } catch (Throwable) {
                //Not important
            }
        }, $entity);
    }
}
