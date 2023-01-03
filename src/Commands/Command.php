<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

abstract class Command
{
    /**
     * @var array
     */
    private array $cooldowns = [];

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return  void
     */
    abstract public function handle(Message $message, array $args): void;

    /**
     * @return string
     */
    abstract public function trigger(): string;

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 0;
    }

    /**
     * @param  Message  $message
     * @param  string|null  $flag
     * @return bool
     */
    protected function skip(Message $message, ?string $flag = null): bool
    {
        if (Helpers::isBotOrDirectMessage($message)) {
            return true;
        }

        if ($flag
            && Shitbot::config($flag) === true
            && ! Helpers::isOwner($message)) {
            $message->react('a:nonono:903831572065697875');

            return true;
        }

        $cooldown = $this->currentCooldown($message);

        if ($cooldown > 0) {
            $message->reply("Slow down turbo, $cooldown second(s) until you can use `{$this->trigger()}` again ⏳");

            return true;
        }

        return false;
    }

    /**
     * @param  string  $message
     * @return string
     */
    protected function formatError(string $message): string
    {
        $reply = 'You broke me. Please try again.'.PHP_EOL;
        $reply .= '```diff'.PHP_EOL;
        $reply .= "- $message".PHP_EOL;
        $reply .= '```';

        return $reply;
    }

    /**
     * @param  Message  $message
     * @return void
     */
    protected function hitCooldown(Message $message): void
    {
        if ($this->cooldown() === 0 || Helpers::isOwner($message)) {
            return;
        }

        $this->cooldowns[$message->author->id] = $this->now() + $this->cooldown();
    }

    /**
     * @param  Message  $message
     * @return float|int
     */
    private function currentCooldown(Message $message): float|int
    {
        if (! isset($this->cooldowns[$message->author->id])
            || $this->cooldown() === 0) {
            return 0;
        }

        $time = $this->now();

        if ($this->cooldowns[$message->author->id] < $time) {
            unset($this->cooldowns[$message->author->id]);

            return 0;
        }

        return ceil(($this->cooldowns[$message->author->id] - $time) / 1000);
    }

    /**
     * @return float
     */
    private function now(): float
    {
     return round(microtime(true) * 1000);
    }
}
