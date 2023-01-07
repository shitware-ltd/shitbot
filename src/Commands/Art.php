<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use ShitwareLtd\Shitbot\Bank\Bank;
use ShitwareLtd\Shitbot\Bank\Item;
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
            if ($this->skip(
                message: $message,
                flag: 'OWNER_ONLY_ART'
            )) {
                return;
            }

            $message->channel->broadcastTyping();

            $typing = Loop::addPeriodicTimer(
                interval: 4,
                callback: fn () => $message->channel->broadcastTyping()
            );

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()
                    ->withTimeout(60.0)
                    ->withRejectErrorResponse(false)
                    ->post(
                        url: 'https://api.openai.com/v1/images/generations',
                        headers: [
                            'Authorization' => 'Bearer '.Shitbot::config('OPENAI_TOKEN'),
                            'Content-Type' => 'application/json',
                        ],
                        body: json_encode([
                            'n' => 1,
                            'prompt' => Helpers::implodeContent($args),
                            'response_format' => 'b64_json',
                            'size' => '1024x1024',
                        ])
                    );

                $result = Helpers::json($response);

                if ($response->getStatusCode() < 300) {
                    $message->channel->sendMessage(
                        MessageBuilder::new()
                            ->setReplyTo($message)
                            ->addFileFromContent(
                                filename: 'dalle_'.uniqid(more_entropy: true).'.png',
                                content: base64_decode($result['data'][0]['b64_json'])
                            )
                    );

                    Bank::for($message->author)->charge(
                        item: Item::Dalle2,
                        units: 1
                    );

                    $this->hitCooldown($message);
                } else {
                    $message->reply($this->formatError(
                        $result['error']['message']
                    ));
                }
            } catch (Throwable $e) {
                $message->reply($this->formatError(
                    $e->getMessage()
                ));
            }

            Loop::cancelTimer($typing);
        }, $message, $args);
    }
}
