<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\User;
use ShitwareLtd\Shitbot\Support\Helpers;

class Flame
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://evilinsult.com/generate_insult.php';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        if (! $message->mentions->count()) {
            $message->reply('You must be dumb...you did not tell me **who** to flame.');

            return;
        }

        $mentions = implode(
            separator: ', ',
            array: $message->mentions
                ->map(fn (User $user): string => "<@$user->id>")
                ->toArray()
        );

        if ($insult = $this->getInsult()) {
            $message->channel->sendMessage("$mentions, {$insult['insult']}");
        }
    }

    /**
     * @return array|null
     */
    private function getInsult(): array|null
    {
        return Helpers::httpGet(
            endpoint: self::API_ENDPOINT,
            query: [
                'lang' => 'en',
                'type' => 'json',
            ]
        );
    }
}
