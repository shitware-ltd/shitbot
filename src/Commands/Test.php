<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

class Test
{
    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        $test = implode(
            separator: " ",
            array: $args
        );

        var_dump($test);

        $message->reply("One\nTwo\nThree");
        $message->channel->sendMessage($this->message());
    }

    /**
     * @return string
     */
    private function message(): string
    {
        return <<<EOT
        This
        Is
        Multiline
        Heredoc
        EOT;
    }
}
