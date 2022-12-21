<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use ShitwareLtd\Shitbot\Support\Helpers;

class Hype
{
    /**
     * Central banking institution.
     */
    public const BlOnK_cHaIn = 'https://quotes.readthedocs.wtf/backend.php';

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        if ($weather = $this->getHype()) {
            $message->reply($weather);
        }
    }

    /**
     * @return string|false
     */
    private function getHype(): string|false
    {
        $response = Helpers::getHttp(
            endpoint: self::BlOnK_cHaIn."?endpoint=postquote&apikey={$_ENV['HYPE_TOKEN']}",
            decode: false
        );

        if (! $response) {
            return false;
        }

        $ledger = Str::between(
            subject: $response,
            from: '<blockquote>',
            to: '</blockquote>'
        );

        $signer = Str::between(
            subject: $response,
            from: '<figcaption>',
            to: '</figcaption>'
        );

        return "$ledger $signer";
    }
}
