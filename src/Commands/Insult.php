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
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        coroutine(function (Message $message) {
            if ($this->skip($message)) {
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

                if (! $message->mentions->count()) {
                    $message->channel->sendMessage("<@{$message->author->id}>, {$result['insult']}");

                    return;
                }

                $mentions = implode(
                    separator: ', ',
                    array: $message->mentions
                        ->map(fn (User $user): string => "<@$user->id>")
                        ->toArray()
                );

                $message->channel->sendMessage("$mentions, {$result['insult']}");
            } catch (Throwable) {
                //Not important
            }
        }, $message);
    }
}
