<?php

namespace ShitwareLtd\Shitbot\Support;

use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\TypingStart;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
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
     * @param  Message|TypingStart  $part
     * @return bool
     */
    public static function isBotOrDirectMessage(Message|TypingStart $part): bool
    {
        if ($part instanceof Message) {
            return $part->guild === null
                || ($part->author->bot && $part->attachments->count() === 0);
        }

        return $part->user->bot
            || $part->guild === null;
    }

    /**
     * @param  Message  $message
     * @return bool
     */
    public static function isOwner(Message $message): bool
    {
        return in_array(
            needle: $message->author->id,
            haystack: Shitbot::owners()
        );
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

    /**
     * @param  int|null  $bytes
     * @return string
     */
    public static function bytesToHuman(?int $bytes): string
    {
        if (is_null($bytes) || $bytes <= 0) {
            return 'unknown';
        }

        $base = log($bytes) / log(1024);
        $floor = floor($base);
        $type = match ((int) $floor) {
            0 => 'bytes',
            1 => 'KB',
            2 => 'MB',
            3 => 'GB',
            4 => 'TB',
            5 => 'PB',
            default => 'unknown',
        };
        $rounded = round(
            num: pow(
                num: 1024,
                exponent: $base - $floor
            ),
            precision: 2
        );

        return "$rounded $type";
    }
}
