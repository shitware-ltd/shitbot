<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

class Weather extends Command
{
    /**
     * Endpoint we gather data from.
     */
    public const API_ENDPOINT = 'https://api.weatherapi.com/v1/current.json';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!weather';
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

        $location = Helpers::implodeContent($args);

        if ($weather = $this->getWeather($location)) {
            $message->reply($this->makeWeather($weather));

            return;
        }

        $message->reply("No results for `$location`");
    }

    /**
     * @param  string  $location
     * @return array|null
     */
    private function getWeather(string $location): array|null
    {
        return Helpers::httpGet(
            endpoint: self::API_ENDPOINT,
            query: [
                'key' => Shitbot::$config['WEATHER_TOKEN'],
                'q' => $location,
                'aqi' => 'no',
            ]
        );
    }

    /**
     * @param  array  $response
     * @return string
     */
    private function makeWeather(array $response): string
    {
        $name = $response['location']['name'];
        $region = $response['location']['region'];
        $country = $response['location']['country'];
        $tempF = $response['current']['temp_f'];
        $tempC = $response['current']['temp_c'];
        $condition = Str::lower($response['current']['condition']['text']);
        $wind = $response['current']['wind_mph'];
        $windDirection = $response['current']['wind_dir'];
        $humidity = $response['current']['humidity'];

        return "Currently in $name, $region, $country, it is {$tempF}°F ({$tempC}°C) and $condition. Winds out of the $windDirection at {$wind}mph. Humidity is $humidity%";
    }
}
