<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;
use ShitwareLtd\Shitbot\Support\Helpers;

class Joke extends Command
{
    /**
     * Location of our jokes!
     */
    public const JOKES_FILE = __DIR__ . '/../../assets/jokes.json';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!joke';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        if ($this->skip($message)) {
            return;
        }

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
