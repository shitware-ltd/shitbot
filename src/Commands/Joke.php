<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;
use ShitwareLtd\Shitbot\Support\Helpers;

class Joke
{
    /**
     * Location of our jokes!
     */
    public const JOKES_FILE = __DIR__ . '/../../assets/jokes.json';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        $joke = $this->getJoke();

        $reply = $joke['setup'].PHP_EOL;
        $reply .= "||{$joke['punchline']}||";

        $message->reply($reply);
    }

    /**
     * Pick a random joke from our jokes file.
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
