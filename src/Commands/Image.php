<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
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
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        coroutine(function (Message $message) {
            if ($this->skip($message)) {
                return;
            }

            try {
                $response = file_get_contents('https://source.unsplash.com/random');

                $message->channel->sendMessage(
                    MessageBuilder::new()
                        ->setReplyTo($message)
                        ->addFileFromContent(
                            filename: 'random_'.uniqid(more_entropy: true).'.jpg',
                            content: (string) $response
                        )
                );

                $this->hitCooldown($message->author);
            } catch (Throwable) {
                //Not important
            }
        }, $message);
    }
}
