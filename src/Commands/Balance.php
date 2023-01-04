<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Bank;
use ShitwareLtd\Shitbot\Support\Helpers;

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

        [ $totalCost, $costOverview ] = (new Bank($message->author))
            ->getTotalExpenses();

        $reply = "
            **<@{$message->author->id}>'s balance:**
Your current balance is: **$ -{$totalCost}**
            
**Here is an overview of your expenses:**";

        foreach ($costOverview as $category => $cost) {
            $reply .= "
{$category}: $ " . $cost['total_cost'];
        }

        $message->reply($reply);

        $this->hitCooldown($message);
    }
}
