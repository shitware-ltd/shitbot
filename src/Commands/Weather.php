<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Weather extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!weather';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 5000;
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        coroutine(function (Message $message, array $args) {
            if ($this->skip($message)) {
                return;
            }

            $location = Helpers::implodeContent($args);

            $query = http_build_query([
                'key' => Shitbot::config('WEATHER_TOKEN'),
                'q' => $location,
                'aqi' => 'no',
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->get("https://api.weatherapi.com/v1/current.json?$query");

                $result = Helpers::json($response);

                $message->reply($this->makeWeather($result));

                $this->hitCooldown($message);

                return;
            } catch (Throwable) {
                //Not important
            }

            $message->reply("No results for `$location`");
        }, $message, $args);
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
        $localTime = Carbon::parse($response['location']['localtime'])->toDayDateTimeString();
        $tempF = $response['current']['temp_f'];
        $tempC = $response['current']['temp_c'];
        $condition = Str::lower($response['current']['condition']['text']);
        $windMph = $response['current']['wind_mph'];
        $windKph = $response['current']['wind_kph'];
        $windDirection = $response['current']['wind_dir'];
        $gustMph = $response['current']['gust_mph'];
        $gustKph = $response['current']['gust_kph'];
        $humidity = $response['current']['humidity'];
        $precipIn = $response['current']['precip_in'];
        $precipMm = $response['current']['precip_mm'];
        $cloud = $response['current']['cloud'];
        $uv = $response['current']['uv'];

        return <<<EOT
        > **$name, $region, $country:**
        > Local time is **$localTime**. 
        > Conditions are **$tempF °F** / **$tempC °C** and **$condition**.
        > Winds out of the **$windDirection** at **$windMph mph** / **$windKph kph**, with gusts up to **$gustMph mph** / **$gustKph kph**. 
        > Humidity is **$humidity%** with precipitation at **$precipIn in** / **$precipMm mm**.
        > Cloud coverage is at **$cloud%** with a UV index of **$uv**.
        EOT;
    }
}
