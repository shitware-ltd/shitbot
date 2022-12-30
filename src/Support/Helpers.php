<?php

namespace ShitwareLtd\Shitbot\Support;

use Discord\Parts\Channel\Message;
use GuzzleHttp\Client;
use React\Http\Browser;
use ShitwareLtd\Shitbot\Shitbot;
use Throwable;

class Helpers
{
    /**
     * Read some json.
     *
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
     * Less is more.
     *
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
     * 50% chance to win prizes.
     *
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
        return (new Browser(loop: Shitbot::$loop))
            ->withTimeout(20.0)
            ->withHeader(
                header: 'Accept',
                value: 'application/json'
            );
    }

    /**
     * The best http client.
     *
     * @param  string  $endpoint
     * @param  array  $query
     * @param  bool  $decode
     * @param  bool  $allowFail
     * @return array|string|null
     */
    public static function httpGet(
        string $endpoint,
        array $query = [],
        bool $decode = true,
        bool $allowFail = false
    ): array|string|null {
        $client = new Client([
            'connect_timeout' => 10,
            'http_errors' => false,
            'timeout' => 15,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        try {
            $response = $client->get(
                uri: $endpoint,
                options: count($query) ? ['query' => $query] : []
            );

            if ($response->getStatusCode() >= 400 && ! $allowFail) {
                return null;
            }

            return $decode
                ? json_decode(
                    json: (string) $response->getBody(),
                    associative: true
                )
                : (string) $response->getBody();

        } catch (Throwable) {
            return null;
        }
    }
}
