<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
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

        $expenses = Bank::for($entity->author)->expenses();

        $reply = "You have spent: **$$expenses->total**".PHP_EOL.PHP_EOL;
        $reply .= 'Here is an overview of your expenses:'.PHP_EOL;

        foreach ($expenses->breakdown as $item => $total) {
            $reply .= "> [ $item ]: **$$total**".PHP_EOL;
        }

        $entity->reply($reply);

        $this->hitCooldown($entity->author);
    }
}
