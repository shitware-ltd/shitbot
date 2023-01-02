<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use React\EventLoop\Loop;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Art extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!art';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 120000;
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

            $prompt = Helpers::implodeContent($args);

            if (Str::length($prompt) < 25) {
                $message->reply('Please be more descriptive.');

                return;
            }

            $message->channel->broadcastTyping();

            $typing = Loop::addPeriodicTimer(
                interval: 5,
                callback: fn () => $message->channel->broadcastTyping()
            );

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()
                    ->withRejectErrorResponse(false)
                    ->post(
                        url: 'https://api.openai.com/v1/images/generations',
                        headers: [
                            'Authorization' => 'Bearer '.Shitbot::config('OPENAI_TOKEN'),
                            'Content-Type' => 'application/json',
                        ],
                        body: json_encode([
                            'n' => 1,
                            'prompt' => $prompt,
                            'response_format' => 'b64_json',
                            'size' => '1024x1024',
                        ])
                    );

                $result = Helpers::json($response);

                if ($response->getStatusCode() >= 400) {
                    $message->reply(
                        $this->formatError($result['error']['message'])
                    );
                } else {
                    $message->channel->sendMessage(
                        MessageBuilder::new()
                            ->setReplyTo($message)
                            ->addFileFromContent(
                                filename: 'dalle_'.uniqid(more_entropy: true).'.png',
                                content: base64_decode($result['data'][0]['b64_json'])
                            )
                    );

                    $this->hitCooldown($message);
                }
            } catch (Throwable $e) {
                $message->reply(
                    $this->formatError($e->getMessage())
                );
            }

            Loop::cancelTimer($typing);
        }, $message, $args);
    }
}
