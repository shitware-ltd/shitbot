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
        $message->reply($this->message());
    }

    /**
     * @return string
     */
    private function message(): string
    {
        return <<<EOT
        If you want to play with me, try:
        > `!help`: Well...you are already here, so you got this one down.
        > `!daddy`: Dad jokes brighten everyone's day up.
        > `!hype`: You never know what you'll get, but it's POG.
        > `!insult`: Only if you dare. I can be meaner than the Bean.
        > `!joke`: Basic setup and punchline jokes.
        > `!weather {location}`: I will gaze into the sky for you.
        > `!wiki {search}`: As if googling was hard enough.
        > `!yomomma`: Mom's are great. But not your mom.
        EOT;
    }
}
