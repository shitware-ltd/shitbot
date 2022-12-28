<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use GuzzleHttp\Client as GuzzleClient;
use OpenAI\Client;
use OpenAI\Responses\Completions\CreateResponse;
use OpenAI\Transporters\HttpTransporter;
use OpenAI\ValueObjects\ApiToken;
use OpenAI\ValueObjects\Transporter\BaseUri;
use OpenAI\ValueObjects\Transporter\Headers;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

class OpenAi
{
    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        try {
            $response = $this->askAi(
                Helpers::implodeContent($args)
            );

            $message->reply($response['choices'][0]['text']);
        } catch (Throwable) {
            $message->reply('You broke me. Please try again.');
        }
    }

    /**
     * @param  string  $prompt
     * @return CreateResponse
     */
    private function askAi(string $prompt): CreateResponse
    {
        return $this->client()->completions()->create([
            'model' => 'text-davinci-003',
            'max_tokens' => 2048,
            'prompt' => $prompt,
        ]);
    }

    /**
     * @return Client
     */
    private function client(): Client
    {
        return new Client(new HttpTransporter(
            client: new GuzzleClient([
                'connect_timeout' => 10,
                'timeout' => 20,
            ]),
            baseUri: BaseUri::from('api.openai.com/v1'),
            headers:  Headers::withAuthorization(ApiToken::from(Shitbot::$config['OPENAI_TOKEN']))
        ));
    }
}
