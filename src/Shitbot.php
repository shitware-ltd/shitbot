<?php

namespace ShitwareLtd\Shitbot;

use Discord\Builders\MessageBuilder;
use Discord\DiscordCommandClient;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Command\Command as SlashCommand;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\WebSockets\TypingStart as Typing;
use Discord\WebSockets\Event;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use ShitwareLtd\Shitbot\Commands\Admin;
use ShitwareLtd\Shitbot\Commands\Art;
use ShitwareLtd\Shitbot\Commands\Ask;
use ShitwareLtd\Shitbot\Commands\Balance;
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
use ShitwareLtd\Shitbot\Commands\Uptime;
use ShitwareLtd\Shitbot\Commands\Variation;
use ShitwareLtd\Shitbot\Commands\Weather;
use ShitwareLtd\Shitbot\Commands\Wiki;
use ShitwareLtd\Shitbot\Commands\YoMomma;
use ShitwareLtd\Shitbot\Commands\YouTube;
use ShitwareLtd\Shitbot\EventHandlers\MessageCreate;
use ShitwareLtd\Shitbot\EventHandlers\TypingStart;
use ShitwareLtd\Shitbot\Support\Status;
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
     * @var bool
     */
    private static bool $paused = false;

    /**
     * @var DiscordCommandClient|null
     */
    private static ?DiscordCommandClient $discord = null;

    /**
     * @var LoopInterface|null
     */
    private static ?LoopInterface $loop = null;

    /**
     * @var array
     */
    private static array $commandInstances = [];

    /**
     * @var array<Command>
     */
    private array $prefixCommands = [
        Admin::class,
        Art::class,
        Ask::class,
        Balance::class,
        Chuck::class,
        Dad::class,
        Help::class,
        Hype::class,
        Image::class,
        Insult::class,
        Ip::class,
        Joke::class,
        RockPaperScissors::class,
        Uptime::class,
        Variation::class,
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
        DiscordCommandClient $client,
        private readonly bool $installingAppCommands
    ){
        static::$discord = $client;
        static::$loop = $client->getLoop();
        $this->setConfig();
        $this->setOwners();
    }

    /**
     * @param  bool  $asInstall
     * @return void
     */
    public static function run(bool $asInstall = false): void
    {
        try {
            static::new($asInstall)();
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
     * @return DiscordCommandClient|null
     */
    public static function discord(): ?DiscordCommandClient
    {
        return static::$discord;
    }

    /**
     * @param  bool|null  $paused
     * @return bool
     */
    public static function paused(?bool $paused = null): bool
    {
        if ($paused !== null) {
            static::$paused = $paused;
        }

        return static::$paused;
    }

    /**
     * @return array
     */
    public static function owners(): array
    {
        return static::$owners;
    }

    /**
     * @param  string  $commandClass
     * @return Command
     * @throws \Exception
     */
    public static function commandInstances(string $commandClass): Command
    {
        if (! isset(static::$commandInstances[$commandClass])) {
            throw new \Exception('You fucked.');
        }

        return static::$commandInstances[$commandClass];
    }

    /**
     * @return Browser
     */
    public static function browser(): Browser
    {
        return (new Browser(loop: static::$loop))
            ->withTimeout(30.0)
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
    public function __invoke(): void
    {
        if (! $this->installingAppCommands) {
            $this->registerPrefixCommands();
        }

        static::$discord->on(
            event: 'ready',
            listener: $this->isReady(...)
        );

        static::$discord->run();
    }

    /**
     * @return void
     *
     * @throws Throwable
     */
    private function registerPrefixCommands(): void
    {
        foreach ($this->prefixCommands as $command) {
            $commandInstance = new $command();

            static::$commandInstances[$command] = $commandInstance;

            static::$discord->registerCommand(
                command: $commandInstance->trigger(),
                callable: [$commandInstance, 'handle']
            );
        }
    }

    /**
     * @return void
     */
    private function isReady(): void
    {
        if ($this->installingAppCommands) {
            $this->installAppCommands();

            return;
        }

        static::$discord->on(
            event: Event::MESSAGE_CREATE,
            listener: $this->handleMessage(...)
        );

        static::$discord->on(
            event: Event::TYPING_START,
            listener: $this->handleTyping(...)
        );

        static::$discord->listenCommand(
            name: 'ping',
            callback: $this->pong(...)
        );

        Status::setDefault();
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
     * @todo extract to handler class.
     * @param  Interaction  $interaction
     * @return void
     */
    private function pong(Interaction $interaction): void
    {
        $interaction->respondWithMessage(
            builder: MessageBuilder::new()->setContent(
                static::$paused
                    ? 'I am resting. 💤'
                    : 'Shitbot at your service. 💦'
            ),
            ephemeral: true
        );
    }

    /**
     * @todo extract to installer class.
     * @return void
     */
    private function installAppCommands(): void
    {
        $command = new SlashCommand(
            discord: static::$discord,
            attributes: ['name' => 'ping', 'description' => 'See if I am alive and well.']
        );

        try {
            static::$discord->application
                ->commands
                ->save($command)
                ->then(
                    onFulfilled: function () {
                        echo PHP_EOL.'Installed PING!'.PHP_EOL;
                        static::$discord->close();
                    },
                    onRejected: function (Throwable $e) {
                        echo PHP_EOL.'OH NO: '.$e->getMessage().PHP_EOL;
                        static::$discord->close();
                    }
                );
        } catch (Throwable) {
            static::$discord->close();
        }
    }

    /**
     * @return void
     */
    private function setConfig(): void
    {
        static::$config = [
            'WEATHER_TOKEN' => $_ENV['WEATHER_TOKEN'] ?? 'token',
            'HYPE_TOKEN' => $_ENV['HYPE_TOKEN'] ?? 'token',
            'YOUTUBE_TOKEN' => $_ENV['YOUTUBE_TOKEN'] ?? 'token',
            'OPENAI_TOKEN' => $_ENV['OPENAI_TOKEN'] ?? 'token',
            'IP_TOKEN' => $_ENV['IP_TOKEN'] ?? 'token',
            'BOT_ACTIVITY_STATUS' => $_ENV['BOT_ACTIVITY_STATUS'] ?? 'online',
            'BOT_ACTIVITY_URL' => $_ENV['BOT_ACTIVITY_URL'] ?? null,
            'BOT_ACTIVITY_NAME' => $_ENV['BOT_ACTIVITY_NAME'] ?? null,
            'BOT_ACTIVITY_TYPE' => empty($_ENV['BOT_ACTIVITY_TYPE'])
                ? null
                : (int) $_ENV['BOT_ACTIVITY_TYPE'],
        ];
    }

    /**
     * @return void
     */
    private function setOwners(): void
    {
        static::$owners = empty($_ENV['OWNER_IDS'])
            ? []
            : explode(
                separator: ',',
                string: $_ENV['OWNER_IDS']
            );
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
