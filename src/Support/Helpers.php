<?php

namespace ShitwareLtd\Shitbot\Support;

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
     * @return array|string|null
     */
    public static function getHttp(string $endpoint, bool $decode = true): array|string|null
    {
        $curl = curl_init($endpoint);

        curl_setopt(handle: $curl, option: CURLOPT_URL, value: $endpoint);
        curl_setopt(handle: $curl, option: CURLOPT_RETURNTRANSFER, value: true);
        curl_setopt(handle: $curl, option: CURLOPT_HTTPHEADER, value: [
            'Accept: application/json',
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $decode
            ? json_decode(
                json: $response,
                associative: true
            )
            : $response;
    }
}
