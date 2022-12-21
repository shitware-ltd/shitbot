<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;

class Help
{
    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        $message->reply('Use: `!daddy` `!help` `!hype` `!joke` `!weather {location}` `!wiki {search}` `!yomomma`');
    }
}
