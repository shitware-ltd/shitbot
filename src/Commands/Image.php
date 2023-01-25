<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Throwable;

use function React\Async\coroutine;

class Image extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!image';
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
     */
    public function handle(Interaction|Message $entity, array $args): void
    {
        coroutine(function (Message $entity) {
            if ($this->skip($entity)) {
                return;
            }

            try {
                $response = file_get_contents('https://source.unsplash.com/random');

                $entity->channel->sendMessage(
                    MessageBuilder::new()
                        ->setReplyTo($entity)
                        ->addFileFromContent(
                            filename: 'random_'.uniqid(more_entropy: true).'.jpg',
                            content: (string) $response
                        )
                );

                $this->hitCooldown($entity->author);
            } catch (Throwable) {
                //Not important
            }
        }, $entity);
    }
}
