<?php

namespace ShitwareLtd\Shitbot\Support;

use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\TypingStart;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

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
     * @param  Message|TypingStart  $part
     * @return bool
     */
    public static function shouldProceed(Message|TypingStart $part): bool
    {
        if ($part instanceof Message) {
            return ! $part->author->bot
                && $part->guild !== null;
        }

        return ! $part->user->bot
            && $part->guild !== null;
    }

    /**
     * @param  string  $message
     * @param  int  $max
     * @return array
     */
    public static function splitMessage(string $message, int $max = 1950): array
    {
        if (Str::length($message) <= $max) {
            return [$message];
        }

        $result = [];

        $currentString = '';

        foreach (explode(separator: ' ', string: $message) as $word) {
            if (Str::length($currentString . $word) > $max) {
                $result[] = $currentString;
                $currentString = $word;
            } else {
                $currentString .= " $word";
            }
        }

        if (! empty($currentString)) {
            $result[] = $currentString;
        }

        return $result;
    }
}
