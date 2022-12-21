<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;
use ShitwareLtd\Shitbot\Support\Helpers;

class YoMomma
{
    /**
     * Location of our yo-momma jokes!
     */
    public const JOKES_FILE = __DIR__ . '/../../assets/mom-jokes.json';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        $message->reply("👩 {$this->getYoMomma()}");
        $message->react("👩");
    }

    /**
     * Pick a random joke from our yo-momma jokes file.
     *
     * @return string
     */
    private function getYoMomma(): string
    {
        return Collection::make(
            Helpers::getContents(self::JOKES_FILE)
        )->random();
    }
}
