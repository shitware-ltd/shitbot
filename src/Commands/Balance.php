<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Bank\Bank;

class Balance extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!balance';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 5000;
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

        $expenses = Bank::for($message->author)->expenses();

        $reply = "You have spent: **$$expenses->total**".PHP_EOL.PHP_EOL;
        $reply .= 'Here is an overview of your expenses:'.PHP_EOL;

        foreach ($expenses->breakdown as $item => $total) {
            $reply .= "> [ $item ]: **$$total**".PHP_EOL;
        }

        $message->reply($reply);

        $this->hitCooldown($message->author);
    }
}
