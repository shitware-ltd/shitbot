<?php

namespace ShitwareLtd\Shitbot\Support;

use GuzzleHttp\Client;
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
            'http_errors' => false,
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
