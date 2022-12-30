<?php

namespace ShitwareLtd\Shitbot\Support;

use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;
use ShitwareLtd\Shitbot\Shitbot;

class Helpers
{
    /**
     * @param  string  $path
     * @return array
     */
    public static function getContents(string $path): array
    {
        return json_decode(
            json: file_get_contents($path),
            associative: true
        );
    }

    /**
     * @param  array  $args
     * @return string
     */
    public static function implodeContent(array $args): string
    {
        return implode(
            separator: " ",
            array: $args
        );
    }

    /**
     * @param  ResponseInterface  $response
     * @return array
     */
    public static function json(ResponseInterface $response): array
    {
        return json_decode(
            json: $response->getBody()->getContents(),
            associative: true
        );
    }

    /**
     * @return bool
     */
    public static function gamble(): bool
    {
        return rand(min: 1, max: 999) < 500;
    }

    /**
     * @param  Message  $message
     * @return bool
     */
    public static function shouldProceed(Message $message): bool
    {
        return ! $message->author->bot
            && $message->guild !== null;
    }

    /**
     * @return Browser
     */
    public static function browser(): Browser
    {
        return (new Browser(loop: Shitbot::loop()))
            ->withTimeout(20.0)
            ->withHeader(
                header: 'Accept',
                value: 'application/json'
            );
    }
}
