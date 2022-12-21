<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;
use ShitwareLtd\Shitbot\Support\Helpers;

class Joke
{
    /**
     * Location of our jokes!
     */
    const JOKES_FILE = __DIR__.'/../../assets/jokes.json';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        $joke = $this->getJoke();

        $message->reply($joke['setup']);

        $message->channel->sendMessage($joke['punchline']);
    }

    /**
     * Pick a random joke from our yo-momma jokes file.
     *
     * @return array
     */
    private function getJoke(): array
    {
        return Collection::make(
            Helpers::getContents(self::JOKES_FILE)
        )->random();
    }
}
