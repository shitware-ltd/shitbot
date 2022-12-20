<?php

namespace ShitwareLtd\Shitbot;

use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Commands\Hype;
use ShitwareLtd\Shitbot\Commands\Weather;
use Throwable;

class ShitbotProvider
{
    /**
     * @param  DiscordCommandClient  $client
     */
    public function __construct(
        private readonly DiscordCommandClient $client
    ){}

    /**
     * @return void
     */
    public static function boot(): void
    {
        try {
            (new self(
                new DiscordCommandClient([
                    'token' => $_ENV['BOT_TOKEN'],
                    'prefix' => false,
                    'caseInsensitiveCommands' => true,
                    'defaultHelpCommand' => false,
                ])
            ))->run();
        } catch (Throwable) {
            //Bad time.
        }
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function run(): void
    {
        $this->client->registerCommand(
            command: 'weather',
            callable: [new Weather(), 'handle']
        );

        $this->client->registerCommand(
            command: 'hype',
            callable: [new Hype(), 'handle']
        );

        $this->client->on(
            event: 'ready',
            listener: $this->isReady(...)
        );

        $this->client->run();
    }

    /**
     * @param  DiscordCommandClient  $client
     * @return void
     */
    private function isReady(DiscordCommandClient $client): void
    {
        $client->on(
            event: 'message',
            listener: $this->handleMessage(...)
        );
    }

    /**
     * @param  Message  $message
     * @return void
     */
    private function handleMessage(Message $message): void
    {
        (new MessageHandler($message))();
    }
}
