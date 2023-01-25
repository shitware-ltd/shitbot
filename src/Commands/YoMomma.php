<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
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
     * @param  Message  $entity
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Interaction|Message $entity, array $args): void
    {
        if ($this->skip($entity)) {
            return;
        }

        $entity->reply("👩 {$this->getYoMomma()}");
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
