<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Bank\Bank;
use ShitwareLtd\Shitbot\Bank\Item;
use ShitwareLtd\Shitbot\Shitbot;
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
     * @param  Message  $entity
     * @param  array  $args
     * @return void
     */
    public function handle(Interaction|Message $entity, array $args): void
    {
        coroutine(function (Message $entity, array $args) {
            if ($this->skip($entity)) {
                return;
            }

            $this->hitCooldown($entity->author);

            $typing = $this->startTyping($entity);

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()
                    ->withTimeout(90.0)
                    ->post(
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

                foreach (Helpers::splitMessage($response['choices'][0]['text']) as $key => $chunk) {
                    if ($key === 0) {
                        $entity->reply($chunk);
                    } else {
                        $entity->channel->sendMessage($chunk);
                    }
                }

                Bank::for($entity->author)->charge(
                    item: Item::Davinci003,
                    units: $response['usage']['total_tokens']
                );

                $this->hitCooldown($entity->author);
            } catch (Throwable $e) {
                $this->clearCooldown($entity->author);

                $this->sendError(
                    entity: $entity,
                    error: $e->getMessage()
                );
            }

            $this->stopTyping($typing);
        }, $entity, $args);
    }
}
