<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\User;
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
     * @param  Interaction|Message  $entity
     * @return bool
     */
    protected function skip(Interaction|Message $entity): bool
    {
        if (Shitbot::paused()
            || Helpers::isBotOrDirectMessage($entity)) {
            var_dump('FAILED');
            return true;
        }

        $user = $entity instanceof Message
            ? $entity->author
            : $entity->user;
        var_dump($user);

        $cooldown = $this->currentCooldown($user);

        if ($cooldown > 0) {
            $entity->reply("Slow down turbo, $cooldown second(s) until you can use `{$this->trigger()}` again ⏳");

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
     * @param  User  $user
     * @return void
     */
    protected function hitCooldown(User $user): void
    {
        if ($this->cooldown() === 0 || Helpers::isOwner($user)) {
            return;
        }

        $this->cooldowns[$user->id] = $this->now() + $this->cooldown();
    }

    /**
     * @param  User  $user
     * @return void
     */
    protected function clearCooldown(User $user):  void
    {
        if (isset($this->cooldowns[$user->id])) {
            unset($this->cooldowns[$user->id]);
        }
    }

    /**
     * @param  User  $user
     * @return float|int
     */
    private function currentCooldown(User $user): float|int
    {
        if (! isset($this->cooldowns[$user->id])
            || $this->cooldown() === 0) {
            return 0;
        }

        $time = $this->now();

        if ($this->cooldowns[$user->id] < $time) {
            unset($this->cooldowns[$user->id]);

            return 0;
        }

        return ceil(($this->cooldowns[$user->id] - $time) / 1000);
    }

    /**
     * @return float
     */
    private function now(): float
    {
     return round(microtime(true) * 1000);
    }
}
