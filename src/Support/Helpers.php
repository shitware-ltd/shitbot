<?php

namespace ShitwareLtd\Shitbot\Support;

use GuzzleHttp\Client;
use Throwable;

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
     * @param  string  $endpoint
     * @param  bool  $decode
     * @param  bool  $allowFail
     * @return array|string|null
     */
    public static function httpGet(
        string $endpoint,
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
            $response = $client->get($endpoint);

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
