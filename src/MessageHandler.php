<?php

namespace ShitwareLtd\Shitbot;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;

class MessageHandler
{
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
        if ($this->message->author->bot) {
            return;
        }

        $content = Str::lower($this->message->content);

        if ($content === 'nice') {
            $this->message->react('👍');
        }

        if (Str::contains(
            haystack: $content,
            needles: ['fuck', 'asshole', 'bitch', 'cunt']
        )) {
            $this->message->react('🖕');
        }
    }
}
