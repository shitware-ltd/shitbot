<?php

namespace ShitwareLtd\Shitbot;

use Discord\Parts\Channel\Message;

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

        if (strtolower($this->message->content) === 'nice') {
            $this->message->react('👍');
        }
    }
}
