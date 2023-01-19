<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Insult extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!insult';
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

            $query = http_build_query([
                'lang' => 'en',
                'type' => 'json',
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->get("https://evilinsult.com/generate_insult.php?$query");

                $result = Helpers::json($response);

                if (! $entity->mentions->count()) {
                    $entity->channel->sendMessage("<@{$entity->author->id}>, {$result['insult']}");

                    return;
                }

                $mentions = implode(
                    separator: ', ',
                    array: $entity->mentions
                        ->map(fn (User $user): string => "<@$user->id>")
                        ->toArray()
                );

                $entity->channel->sendMessage("$mentions, {$result['insult']}");
            } catch (Throwable) {
                //Not important
            }
        }, $entity);
    }
}
