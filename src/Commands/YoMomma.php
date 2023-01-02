<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Collection;
use ShitwareLtd\Shitbot\Support\Helpers;

class YoMomma extends Command
{
    /**
     * Location of our yo-momma jokes!
     */
    public const JOKES_FILE = __DIR__ . '/../../assets/mom-jokes.json';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!yomomma';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        if ($this->skip($message)) {
            return;
        }

        $message->reply("👩 {$this->getYoMomma()}");
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
