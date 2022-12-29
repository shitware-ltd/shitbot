<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

class Ip extends Command
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://pro.ip-api.com/json/';

    public const FIELDS = [
        'status',
        'message',
        'query',
        'continent',
        'continentCode',
        'country',
        'countryCode',
        'region',
        'regionName',
        'city',
        'district',
        'zip',
        'lat',
        'lon',
        'timezone',
        'currency',
        'isp',
        'org',
        'as',
        'mobile',
        'proxy',
        'hosting',
    ];

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!ip';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        if ($this->bailForBotOrDirectMessage($message)) {
            return;
        }

        if (empty($lookup = Helpers::implodeContent($args))) {
            return;
        }

        if ($ip = $this->getIp($lookup)) {
            $reply = '```json'.PHP_EOL;

            $reply .= json_encode(
                value: $ip,
                flags: JSON_PRETTY_PRINT
            ).PHP_EOL;

            $reply .= '```';

            $message->reply($reply);
        }
    }

    /**
     * @param  string  $ip
     * @return array|null
     */
    private function getIp(string $ip): array|null
    {
        return Helpers::httpGet(
            endpoint: self::API_ENDPOINT.$ip,
            query: [
                'key' => Shitbot::$config['IP_TOKEN'],
                'fields' => implode(
                    separator: ',',
                    array: self::FIELDS
                ),
            ]
        );
    }
}
