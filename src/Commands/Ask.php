<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Facades\Log;
use React\EventLoop\Loop;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Bank;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Ask extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!ask';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 20000;
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
                flag: 'OWNER_ONLY_ASK'
            )) {
                return;
            }

            $message->channel->broadcastTyping();

            $typing = Loop::addPeriodicTimer(
                interval: 5,
                callback: fn () => $message->channel->broadcastTyping()
            );

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()->post(
                    url: 'https://api.openai.com/v1/completions',
                    headers: [
                        'Authorization' => 'Bearer '.Shitbot::config('OPENAI_TOKEN'),
                        'Content-Type' => 'application/json',
                    ],
                    body: json_encode([
                        'max_tokens' => 3072,
                        'model' => 'text-davinci-003',
                        'n' => 1,
                        'prompt' => Helpers::implodeContent($args),
                        'temperature' => 1,
                    ])
                );

                $response = Helpers::json($response);
                $result = $response['choices'][0]['text'];

                (new Bank($message->author))
                    ->registerExpense(
                        type: 'text-davinci-003',
                        amount: $response['usage']['total_tokens']
                    );

                foreach (Helpers::splitMessage($result) as $key => $chunk) {
                    if ($key === 0) {
                        $message->reply($chunk);
                    } else {
                        $message->channel->sendMessage($chunk);
                    }
                }

                $this->hitCooldown($message);
            } catch (Throwable $e) {
                $message->reply(
                    $this->formatError($e->getMessage())
                );
            }

            Loop::cancelTimer($typing);
        }, $message, $args);
    }
}
