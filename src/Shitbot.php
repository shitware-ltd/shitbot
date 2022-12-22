<?php

namespace ShitwareLtd\Shitbot;

use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Message;
use ShitwareLtd\Shitbot\Commands\Dad;
use ShitwareLtd\Shitbot\Commands\Help;
use ShitwareLtd\Shitbot\Commands\Hype;
use ShitwareLtd\Shitbot\Commands\Insult;
use ShitwareLtd\Shitbot\Commands\Joke;
//use ShitwareLtd\Shitbot\Commands\Test;
use ShitwareLtd\Shitbot\Commands\RockPaperScissors;
use ShitwareLtd\Shitbot\Commands\Weather;
use ShitwareLtd\Shitbot\Commands\Wiki;
use ShitwareLtd\Shitbot\Commands\YoMomma;
use ShitwareLtd\Shitbot\Commands\YouTube;
use Throwable;

class Shitbot
{
    /**
     * @var array
     */
    private array $commands = [
        Dad::class => '!daddy',
        Help::class => '!help',
        Hype::class => '!hype',
        Insult::class => '!insult',
        Joke::class => '!joke',
        RockPaperScissors::class => '!rps',
        Weather::class => '!weather',
        Wiki::class => '!wiki',
        YoMomma::class => '!yomomma',
        YouTube::class => '!yt',
//        Test::class => '!test',
    ];

    /**
     * @param  DiscordCommandClient  $client
     */
    public function __construct(
        private readonly DiscordCommandClient $client
    ){}

    /**
     * @return void
     */
    public static function run(): void
    {
        try {
            (new self(
                new DiscordCommandClient([
                    'token' => $_ENV['BOT_TOKEN'],
                    'prefix' => false,
                    'caseInsensitiveCommands' => true,
                    'defaultHelpCommand' => false,
                ])
            ))->boot();
        } catch (Throwable) {
            exit(1);
        }
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function boot(): void
    {
        foreach ($this->commands as $command => $trigger) {
            $this->client->registerCommand(
                command: $trigger,
                callable: [new $command(), 'handle']
            );
        }

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
