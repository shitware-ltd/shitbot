<?php

namespace ShitwareLtd\Shitbot\EventHandlers;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\WebSockets\TypingStart as Typing;
use Illuminate\Support\Str;
use ShitwareLtd\Shitbot\Support\Emoji;
use ShitwareLtd\Shitbot\Support\Helpers;

class TypingStart
{
    /**
     * @param  Typing  $typing
     */
    public function __construct(
        private readonly Typing $typing
    ){}

    /**
     * @return void
     * @throws NoPermissionsException
     */
    public function __invoke(): void
    {
        if (! Helpers::shouldProceed($this->typing)) {
            return;
        }

        if (rand(min: 0, max: 100) === 69) {
            $emojis = '';

            for ($x = 0; $x < rand(min: 10, max: 25); $x++) {
                $pick = Emoji::get();

                if (Str::length($pick) > 1) {
                    $pick = "<$pick>";
                }

                $emojis .= "$pick ";
            }

            $this->typing->channel->sendMessage("$emojis <@{$this->typing->user->id}>!");
        }
    }
}
