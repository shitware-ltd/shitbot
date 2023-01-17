<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;

class Help extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!help';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 15000;
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

        $message->reply($this->message());

        $this->hitCooldown($message);
    }

    /**
     * @return string
     */
    private function message(): string
    {
        return <<<EOT
        If you want to play with me, try:
        > `!help`: Well...you are already here, so you got this one down.
        > `!art {prompt}`: Ask the A.I. overlords to make a magical image for you. **[DALLE-2]**
        > `!ask {prompt}`: Ask the A.I. overlords something magnificent. **[GPT-3 text-davinci-003]**
        > `!balance`: Show how much money from API usage you've spent.
        > `!chuck`: Chuck Norris is still alive.
        > `!daddy`: Dad jokes brighten everyone's day up.
        > `!hype`: You never know what you'll get, but it's POG.
        > `!image`: Random images can be nice.
        > `!insult`: Only if you dare. I can be meaner than the Bean. @mention users if you prefer I flame them.
        > `!ip {ip}`: Obtain details about the supplied IP address.
        > `!joke`: Basic setup and punchline jokes.
        > `!rps {rock|paper|scissors}`: Play the most basic game on earth.
        > `!uptime`: Reports how long the bot has been online as well as current and peak memory usage.
        > `!weather {location}`: I will gaze into the sky for you.
        > `!wiki {search}`: As if googling was hard enough.
        > `!yomomma`: Mom's are great. But not your mom.
        > `!yt {search}`: Find the perfect YouTube video.
        EOT;
    }
}
