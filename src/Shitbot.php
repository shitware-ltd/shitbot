<?php

namespace ShitwareLtd\Shitbot;

use Discord\DiscordCommandClient;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\Parts\User\Activity;
use Discord\Parts\WebSockets\TypingStart;
use Discord\WebSockets\Event;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
     * @var array
     */
    private static array $emojis = [
        'cool' => [
            ':BeanLike:955772154367078410',
            ':DanSureCan:1054559650030309408',
            ':FeelsJorqensenMan:948839460173414420',
            ':FeelsTippinMan:945779696870785044',
            ':StanManCan:986743587041599568',
            'a:mochoman:908433686523940884',
            'a:cooldoge:903865400914239508',
            'a:cheers:903865719400321024',
            ':OkFam:1034988675240566854',
            'a:coolblink:903877070336167956',
            'a:potatocheer:915906109917757471',
            'a:MarioLuigiDancing:930208778354319370',
            '😎',
            '🆒',
        ],
        'funny' => [
            ':KekwCamera:1031729132607909939',
            'a:MeLike:1029201658061803560',
            'a:kekamid:903876917575442432',
            'a:potatospin:913606622310445088',
            'a:drilldo:903834334044229693',
            ':KekwCry:1055646524840886292',
            'a:KekwRave:1055646475918516324',
            '😂',
            '🤣',
            '😁',
        ],
        'think' => [
            'a:BlobEat:903866622345900052',
            ':ChiefThonk:1029200771096510534',
            'a:NeonThink:930095718771863552',
            ':ThinkGator:933287413449650206',
            ':watts:903836408639262790',
            ':SuperCereal:985316620928942231',
            ':virus:821890127252946964',
            'a:nervouscursor:903876962328670239',
            '🤔',
            '💭',
        ],
        'rage' => [
            'a:eyesshaking:930095703097745408',
            'a:GullScream:1008189021505212416',
            ':bsod:903869566533394462',
            'a:getsomehelp:903876935707410505',
            'a:smh:903877109825540186',
            'a:codeHard:888730726822981652',
            ':BeanThis:953772597110276147',
            'a:madbean:897099906584567818',
            ':beaned:867568151252041758',
            'a:yeangry:903877015604703232',
            'a:pain:903876735056085023',
            'a:codeRee:888733167530434580',
            'a:Kaboom:903867872344957008',
            'a:duckno:903876825925681153',
            'a:kermitgun:930095685158719509',
            '🖕',
            '💢',
            '😡',
            '😠',
        ],
    ];

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
     * @param  string|null  $flavor
     * @return string
     */
    public static function emoji(?string $flavor = null): string
    {
        return Collection::make(
            match ($flavor) {
                'cool' => static::$emojis['cool'],
                'funny' => static::$emojis['funny'],
                'think' => static::$emojis['think'],
                'rage' => static::$emojis['rage'],
                default => Arr::flatten(static::$emojis),
            }
        )->random();
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
            event: Event::MESSAGE_CREATE,
            listener: $this->handleMessage(...)
        );

        $client->on(
            event: Event::TYPING_START,
            listener: $this->handleTyping(...)
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

    /**
     * @param  TypingStart  $typing
     * @return void
     * @throws NoPermissionsException
     */
    private function handleTyping(TypingStart $typing): void
    {
        (new TypingHandler($typing))();
    }
}
