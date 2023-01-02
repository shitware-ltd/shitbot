<?php

namespace ShitwareLtd\Shitbot;

use Discord\Builders\MessageBuilder;
use Discord\DiscordCommandClient;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Command\Command as SlashCommand;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\Activity;
use Discord\Parts\WebSockets\TypingStart as Typing;
use Discord\WebSockets\Event;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use ShitwareLtd\Shitbot\Commands\Art;
use ShitwareLtd\Shitbot\Commands\Ask;
use ShitwareLtd\Shitbot\Commands\Chuck;
use ShitwareLtd\Shitbot\Commands\Command;
use ShitwareLtd\Shitbot\Commands\Dad;
use ShitwareLtd\Shitbot\Commands\Help;
use ShitwareLtd\Shitbot\Commands\Hype;
use ShitwareLtd\Shitbot\Commands\Image;
use ShitwareLtd\Shitbot\Commands\Insult;
use ShitwareLtd\Shitbot\Commands\Ip;
use ShitwareLtd\Shitbot\Commands\Joke;
use ShitwareLtd\Shitbot\Commands\RockPaperScissors;
use ShitwareLtd\Shitbot\Commands\Weather;
use ShitwareLtd\Shitbot\Commands\Wiki;
use ShitwareLtd\Shitbot\Commands\YoMomma;
use ShitwareLtd\Shitbot\Commands\YouTube;
use ShitwareLtd\Shitbot\EventHandlers\MessageCreate;
use ShitwareLtd\Shitbot\EventHandlers\TypingStart;
use Throwable;

class Shitbot
{
    /**
     * @var array
     */
    private static array $config = [];

    /**
     * @var array
     */
    private static array $owners = [];

    /**
     * @var LoopInterface|null
     */
    private static ?LoopInterface $loop = null;

    /**
     * @var array<Command>
     */
    private array $prefixCommands = [
        Art::class,
        Ask::class,
        Chuck::class,
        Dad::class,
        Help::class,
        Hype::class,
        Image::class,
        Insult::class,
        Ip::class,
        Joke::class,
        RockPaperScissors::class,
        Weather::class,
        Wiki::class,
        YoMomma::class,
        YouTube::class,
    ];

    /**
     * @param  DiscordCommandClient  $client
     * @param  bool  $installingAppCommands
     */
    public function __construct(
        private readonly DiscordCommandClient $client,
        private readonly bool $installingAppCommands
    ){
        static::$config = [
            'WEATHER_TOKEN' => $_ENV['WEATHER_TOKEN'] ?? 'token',
            'HYPE_TOKEN' => $_ENV['HYPE_TOKEN'] ?? 'token',
            'YOUTUBE_TOKEN' => $_ENV['YOUTUBE_TOKEN'] ?? 'token',
            'OPENAI_TOKEN' => $_ENV['OPENAI_TOKEN'] ?? 'token',
            'IP_TOKEN' => $_ENV['IP_TOKEN'] ?? 'token',
            'BOT_ACTIVITY_STATUS' => $_ENV['BOT_ACTIVITY_STATUS'] ?? 'online',
            'BOT_ACTIVITY_TYPE' => $_ENV['BOT_ACTIVITY_TYPE'] ?? null,
            'BOT_ACTIVITY_NAME' => $_ENV['BOT_ACTIVITY_NAME'] ?? null,
        ];

        static::$owners = empty($_ENV['OWNER_IDS'])
            ? []
            : explode(
                separator: ',',
                string: $_ENV['OWNER_IDS']
            );

        static::$loop = $this->client->getLoop();
    }

    /**
     * @param  bool  $asInstall
     * @return void
     */
    public static function run(bool $asInstall = false): void
    {
        try {
            static::new($asInstall)->boot();
        } catch (Throwable) {
            exit(1);
        }
    }

    /**
     * @param  string  $key
     * @return string|null
     */
    public static function config(string $key): ?string
    {
        return static::$config[$key] ?? null;
    }

    /**
     * @return array
     */
    public static function owners(): array
    {
        return static::$owners;
    }

    /**
     * @return Browser
     */
    public static function browser(): Browser
    {
        return (new Browser(loop: static::$loop))
            ->withTimeout(45.0)
            ->withHeader(
                header: 'Accept',
                value: 'application/json'
            );
    }

    /**
     * @return void
     *
     * @throws Throwable
     */
    public function boot(): void
    {
        if (! $this->installingAppCommands) {
            $this->registerPrefixCommands();
        }

        $this->client->on(
            event: 'ready',
            listener: $this->isReady(...)
        );

        $this->client->run();
    }

    /**
     * @return void
     *
     * @throws Throwable
     */
    private function registerPrefixCommands(): void
    {
        foreach ($this->prefixCommands as $command) {
            $command = new $command();

            $this->client->registerCommand(
                command: $command->trigger(),
                callable: [$command, 'handle']
            );
        }
    }

    /**
     * @param  DiscordCommandClient  $client
     * @return void
     */
    private function isReady(DiscordCommandClient $client): void
    {
        if ($this->installingAppCommands) {
            $this->installAppCommands($client);

            return;
        }

        $this->setActiveStatus($client);

        $client->on(
            event: Event::MESSAGE_CREATE,
            listener: $this->handleMessage(...)
        );

        $client->on(
            event: Event::TYPING_START,
            listener: $this->handleTyping(...)
        );

        $client->listenCommand(
            name: 'ping',
            callback: $this->pong(...)
        );
    }

    /**
     * @param  DiscordCommandClient  $client
     * @return void
     */
    private function setActiveStatus(DiscordCommandClient $client): void
    {
        $activity = null;
        $type = static::config('BOT_ACTIVITY_TYPE');
        $name = static::config('BOT_ACTIVITY_NAME');

        if ($type && $name) {
            $activity = new Activity(
                discord: $client,
                attributes: [
                    'type' => (int) $type,
                    'name' => $name,
                    'details' => $name,
                ]
            );
        }

        $client->updatePresence(
            activity: $activity,
            status: static::config('BOT_ACTIVITY_STATUS')
        );
    }

    /**
     * @param  Message  $message
     * @return void
     */
    private function handleMessage(Message $message): void
    {
        (new MessageCreate($message))();
    }

    /**
     * @param  Typing  $typing
     * @return void
     * @throws NoPermissionsException
     */
    private function handleTyping(Typing $typing): void
    {
        (new TypingStart($typing))();
    }

    /**
     * @param  Interaction  $interaction
     * @return void
     */
    private function pong(Interaction $interaction): void
    {
        $interaction->respondWithMessage(
            builder: MessageBuilder::new()->setContent('Yea yea...PONG. Shitbot at your service. 💦'),
            ephemeral: true
        );
    }

    /**
     * @param  DiscordCommandClient  $client
     * @return void
     */
    private function installAppCommands(DiscordCommandClient $client): void
    {
        $command = new SlashCommand(
            discord: $client,
            attributes: ['name' => 'ping', 'description' => 'See if I am alive and well.']
        );

        try {
            $client->application
                ->commands
                ->save($command)
                ->then(
                    onFulfilled: function () use ($client) {
                        echo PHP_EOL.'Installed PING!'.PHP_EOL;
                        $client->close();
                    },
                    onRejected: function (Throwable $e) use ($client) {
                        echo PHP_EOL.'OH NO: '.$e->getMessage().PHP_EOL;
                        $client->close();
                    }
                );
        } catch (Throwable) {
            $client->close();
        }
    }

    /**
     * @throws Throwable
     */
    private static function new(bool $asInstall): Shitbot
    {
        return new self(
            client: new DiscordCommandClient([
                'token' => $_ENV['BOT_TOKEN'] ?? 'token',
                'prefix' => false,
                'caseInsensitiveCommands' => true,
                'defaultHelpCommand' => false,
            ]),
            installingAppCommands: $asInstall
        );
    }
}
