<?php

namespace ShitwareLtd\Shitbot\Support;

use Throwable;

class Helpers
{
    /**
     * @param  string  $endpoint
     * @param  bool  $asArray
     * @return array|string
     */
    public static function getContents(string $endpoint, bool $asArray = true): mixed
    {
        $response = file_get_contents(
            filename: $endpoint,
            context: stream_context_create([
                'http' => ['ignore_errors' => true],
            ])
        );

        try {
            return $asArray
                ? json_decode(
                    json: $response,
                    associative: true
                )
                : $response;
        } catch (Throwable) {
            return false;
        }
    }
}
