<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Support\Helpers;

class Image
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://source.unsplash.com/random';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        if ($image = $this->getImage()) {
            $message->channel->sendMessage(
                MessageBuilder::new()
                    ->setReplyTo($message)
                    ->addFileFromContent(
                        filename: 'random.jpg',
                        content: $image
                    )
            );
        }
    }

    /**
     * @return string|null
     */
    private function getImage(): string|null
    {
        return Helpers::httpGet(
            endpoint: self::API_ENDPOINT,
            decode: false
        );
    }
}
