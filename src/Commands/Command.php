<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\User;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

/**
 * @method handle(Message|Interaction $entity, array $args)
 */
abstract class Command
{
    /**
     * @var array
     */
    private array $cooldowns = [];

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
     *
     * @throws NoPermissionsException
     */
    protected function skip(Interaction|Message $entity): bool
    {
        if (Shitbot::paused()
            || Helpers::isBotOrDirectMessage($entity)) {
            return true;
        }

        $cooldown = $this->currentCooldown(
            Helpers::getUser($entity)
        );

        if ($cooldown > 0) {
            $this->sendSlowdown(
                entity: $entity,
                cooldown: $cooldown
            );

            return true;
        }

        return false;
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
     * @param  Interaction|Message  $entity
     * @param  string  $error
     * @return void
     *
     * @throws NoPermissionsException
     */
    protected function sendError(Interaction|Message $entity, string $error): void
    {
        $message = Helpers::getMessage($entity);
        $builder = MessageBuilder::new();
        $text = <<<EOT
        You broke me. Please try again.
        ```diff
        - $error
        ```
        EOT;

        if ($entity instanceof Message) {
            $builder->setReplyTo($message)->setContent($text);
        } else {
            $builder->setContent("<@{$entity->user->id}>, $text");
        }

        $message->channel->sendMessage($builder);
    }

    /**
     * @param  Interaction|Message  $entity
     * @param  int|float  $cooldown
     * @return void
     *
     * @throws NoPermissionsException
     */
    private function sendSlowdown(Interaction|Message $entity, int|float $cooldown): void
    {
        $text = "Slow down turbo, $cooldown second(s) until you can use `{$this->trigger()}` again ⏳";
        $message = Helpers::getMessage($entity);
        $builder = MessageBuilder::new();

        if ($entity instanceof Message) {
            $builder->setReplyTo($message)->setContent($text);
        } else {
            $builder->setContent("<@{$entity->user->id}>, $text");
        }

        $message->channel->sendMessage($builder);
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
