<?php

namespace ShitwareLtd\Shitbot\EventHandlers;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Emoji;
use ShitwareLtd\Shitbot\Support\Helpers;

class MessageCreate
{
    private const MATCHES = [
        'cool' => ['nice', 'awesome', 'sweet', 'cool', 'pog', 'yeet', 'neat', 'badass', 'whoa', 'wow'],
        'funny' => ['lmao', 'lmfao', 'rofl', 'kek', 'cringe', 'funny', 'hah', 'lolol', 'xd'],
        'think' => ['hmm', 'huh', 'interesting', 'think', 'thonk', 'curious', 'why', 'how', 'who', 'wonder'],
        'rage' => ['fuck', 'asshole', 'bitch', 'cunt', 'shit', 'pussy', 'dildo', 'dick', 'dumb', 'twat', 'piss', 'bastard', 'prick', 'wanker', 'cock'],
        'drugs' => ['drunk', 'tipzy', 'weed', '420', 'high', 'stoned', 'alcohol', 'smashed', 'crack', 'meth', 'drinking', 'beer', 'wine', 'drug'],
        'sheep' => ['filament']
        'trongate' => ['trongate', 'connelly', 'dc', 'havc'],
    ];

    /**
     * @param  Message  $message
     */
    public function __construct(
        private readonly Message $message
    ){}

    /**
     * @return void
     */
    public function __invoke(): void
    {
        if (Shitbot::paused()
            || Helpers::isBotOrDirectMessage($this->message)) {
            return;
        }

        $content = Str::lower($this->message->content);

        foreach (self::MATCHES as $emoji => $matches) {
            if (Str::contains(haystack: $content, needles: $matches) && Helpers::gamble()) {
                $this->message->react(Emoji::get($emoji));
            }
        }
    }
}
