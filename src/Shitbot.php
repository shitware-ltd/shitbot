<?php

namespace ShitwareLtd\Shitbot;

use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Activity;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use ShitwareLtd\Shitbot\Commands\Command;
use ShitwareLtd\Shitbot\Commands\Ip;
use ShitwareLtd\Shitbot\Commands\OpenAi;
use ShitwareLtd\Shitbot\Commands\Chuck;
use ShitwareLtd\Shitbot\Commands\Dad;
use ShitwareLtd\Shitbot\Commands\Help;
use ShitwareLtd\Shitbot\Commands\Hype;
use ShitwareLtd\Shitbot\Commands\Image;
use ShitwareLtd\Shitbot\Commands\Insult;
use ShitwareLtd\Shitbot\Commands\Joke;
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
    private static array $config = [];

    /**
     * @var LoopInterface|null
     */
    private static ?LoopInterface $loop = null;

    /**
     * @var array<Command>
     */
    private array $commands = [
        Chuck::class,
        Dad::class,
        Help::class,
        Hype::class,
        Image::class,
        Insult::class,
        Ip::class,
        Joke::class,
        OpenAi::class,
        RockPaperScissors::class,
        Weather::class,
        Wiki::class,
        YoMomma::class,
        YouTube::class,
    ];

    /**
     * @param  DiscordCommandClient  $client
     */
    public function __construct(
        private readonly DiscordCommandClient $client
    ){
        static::$config = [
            'WEATHER_TOKEN' => $_ENV['WEATHER_TOKEN'],
            'HYPE_TOKEN' => $_ENV['HYPE_TOKEN'],
            'YOUTUBE_TOKEN' => $_ENV['YOUTUBE_TOKEN'],
            'OPENAI_TOKEN' => $_ENV['OPENAI_TOKEN'],
            'IP_TOKEN' => $_ENV['IP_TOKEN'],
        ];

        static::$loop = $this->client->getLoop();
    }

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
     * @param  string  $key
     * @return mixed
     */
    public static function config(string $key): mixed
    {
        return static::$config[$key] ?? null;
    }

    /**
     * @return Browser
     */
    public static function browser(): Browser
    {
        return (new Browser(loop: static::$loop))
            ->withTimeout(20.0)
            ->withHeader(
                header: 'Accept',
                value: 'application/json'
            );
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function boot(): void
    {
        foreach ($this->commands as $command) {
            $command = new $command();

            $this->client->registerCommand(
                command: $command->trigger(),
                callable: [$command, 'handle'],
                options: [
                    'cooldown' => $command->cooldown(),
                    'cooldownMessage' => "Slow down turbo, %d second(s) until you can use `{$command->trigger()}` again ⏳",
                ]
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
        $client->updatePresence(
            activity: new Activity(
                discord: $client,
                attributes: [
                    'type' => Activity::TYPE_WATCHING,
                    'name' => 'your moms OnlyFans. 👍',
                ]
            ),
            status: Activity::STATUS_DND
        );

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
