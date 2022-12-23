<?php

namespace ShitwareLtd\Shitbot;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use ShitwareLtd\Shitbot\Support\Helpers;

class MessageHandler
{
    /**
     * @param  Message  $message
     */
    public function __construct(
        private readonly Message $message
    ){}

    /**
     * @return void
     */
    public function __invoke(): void
    {
        if ($this->message->author->bot) {
            return;
        }

        $content = Str::lower($this->message->content);

        $this->reactToGoodTimes($content);
        $this->reactToBadWords($content);
        $this->reactToTrongate($content);
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToGoodTimes(string $content): void
    {
        if (Str::contains(
            haystack: $content,
            needles: ['nice', 'awesome', 'sweet', 'cool', 'pog', 'yeet', 'neat']
        ) && Helpers::gamble()) {
            $this->message->react(
                collect([
                    ':BeanLike:955772154367078410',
                    ':DanSureCan:1054559650030309408',
                    ':FeelsJorqensenMan:948839460173414420',
                    ':FeelsTippinMan:945779696870785044',
                    ':StanManCan:986743587041599568',
                    ':mochoman:908433686523940884',
                ])->random()
            );
        }
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToBadWords(string $content): void
    {
        if (Str::contains(
            haystack: $content,
            needles: ['fuck', 'asshole', 'bitch', 'cunt', 'shit', 'pussy']
        ) && Helpers::gamble()) {
            $this->message->react(
                collect([
                    ':eyesshaking:930095703097745408',
                    ':GullScream:1008189021505212416',
                    ':bsod:903869566533394462',
                    ':getsomehelp:903876935707410505',
                    ':smh:903877109825540186',
                    ':codeHard:888730726822981652',
                    ':BeanThis:953772597110276147',
                    ':madbean:897099906584567818',
                    ':beaned:867568151252041758',
                ])->random()
            );
        }
    }

    /**
     * @param  string  $content
     * @return void
     */
    private function reactToTrongate(string $content): void
    {
        if (Str::contains(
            haystack: $content,
            needles: 'trongate'
        )) {
            $this->message->react(':trongate:1030233313266389062');
        }
    }
}
