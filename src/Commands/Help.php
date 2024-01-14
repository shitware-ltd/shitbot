<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
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
     * @param  Message  $entity
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $entity, array $args): void
    {
        if ($this->skip($entity)) {
            return;
        }

        $entity->reply($this->message());

        $this->hitCooldown($entity->author);
    }

    /**
     * @return string
     */
    private function message(): string
    {
        return <<<EOT
        If you want to play with me, try:
        > `!help`: Well...you are already here, so you got this one down.
        > `!art {prompt}`: Ask the A.I. overlords to make a magical image for you. **[DALLE-3]**
        > `!ask {prompt}`: Ask the A.I. overlords something magnificent. **[gpt-3.5-turbo-instruct]**
        > `!balance`: Show how much money from API usage you've spent.
        > `!chuck`: Chuck Norris is still alive.
        > `!daddy`: Dad jokes brighten everyone's day up.
        > `!image`: Random images can be nice.
        > `!insult`: Only if you dare. I can be meaner than the Bean. @mention users if you prefer I flame them.
        > `!ip {ip}`: Obtain details about the supplied IP address.
        > `!joke`: Basic setup and punchline jokes.
        > `!rps {rock|paper|scissors}`: Play the most basic game on earth.
        > `!uptime`: Reports how long the bot has been online as well as current and peak memory usage.
        > `!variation`: Ask the A.I. overlords to reimagine your attached image. Must be a valid PNG file, less than 4MB, and square. **[DALLE-2]**
        > `!weather {location}`: I will gaze into the sky for you.
        > `!wiki {search}`: As if googling was hard enough.
        > `!yomomma`: Mom's are great. But not your mom.
        > `!yt {search}`: Find the perfect YouTube video.
        EOT;
    }
}
