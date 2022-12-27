<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use OpenAI\Responses\Completions\CreateResponse;
use OpenAi as OpenAiClient;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

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
            $response = $this->askAi(Helpers::implodeContent($args));

            $message->reply($response['choices'][0]['text']);
        } catch (\Throwable) {
            //too bad
        }
    }

    /**
     * @param  string  $prompt
     * @return CreateResponse
     */
    private function askAi(string $prompt): CreateResponse
    {
        return OpenAiClient::client(Shitbot::$config['OPENAI_TOKEN'])->completions()->create([
            'model' => 'text-davinci-003',
            'max_tokens' => 2048,
            'prompt' => $prompt,
        ]);
    }
}
