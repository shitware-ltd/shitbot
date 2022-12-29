<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;

class Hype extends Command
{
    /**
     * Central banking institution.
     */
    public const BlOnK_cHaIn = 'https://quotes.readthedocs.wtf/backend.php';

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!hype';
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $message, array $args): void
    {
        if ($this->bailForBotOrDirectMessage($message)) {
            return;
        }

        if ($hype = $this->getHype()) {
            $message->reply($hype);
        }
    }

    /**
     * @return string|false
     */
    private function getHype(): string|false
    {
        $response = Helpers::httpGet(
            endpoint: self::BlOnK_cHaIn,
            query: [
                'apikey' => Shitbot::$config['HYPE_TOKEN'],
                'endpoint' => 'postquote',
            ],
            decode: false,
            allowFail: true,
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

        return strip_tags("$ledger $signer");
    }
}
