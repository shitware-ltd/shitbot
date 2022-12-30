<?php

namespace ShitwareLtd\Shitbot;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\WebSockets\TypingStart;

class TypingHandler
{
    /**
     * @param  TypingStart  $typing
     */
    public function __construct(
        private readonly TypingStart $typing
    ){}

    /**
     * @return void
     * @throws NoPermissionsException
     */
    public function __invoke(): void
    {
        if (rand(min: 0, max: 999) === 69) {
            $emojis = '';

            for ($x = 0; $x < rand(min: 5, max: 15); $x++) {
                $pick = Shitbot::emoji();

                if (mb_strlen($pick) > 1) {
                    $pick = "<$pick>";
                }

                $emojis .= "$pick ";
            }

            $this->typing->channel->sendMessage("$emojis <@{$this->typing->user->id}>!");
        }
    }
}
