<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Hype extends Command
{
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
     */
    public function handle(Message $message, array $args): void
    {
        coroutine(function (Message $message) {
            if ($this->bailForBotOrDirectMessage($message)) {
                return;
            }

            $query = http_build_query([
                'apikey' => Shitbot::config('HYPE_TOKEN'),
                'endpoint' => 'postquote',
            ]);

            try {
                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()
                    ->withRejectErrorResponse(false)
                    ->get("https://quotes.readthedocs.wtf/backend.php?$query");

                $result = $response->getBody()->getContents();

                $message->reply($this->makeHype($result));
            } catch (Throwable) {
                //Not important
            }
        }, $message);
    }

    /**
     * @param  string  $hype
     * @return string
     */
    private function makeHype(string $hype): string
    {
        $ledger = Str::between(
            subject: $hype,
            from: '<blockquote>',
            to: '</blockquote>'
        );

        $signer = Str::between(
            subject: $hype,
            from: '<figcaption>',
            to: '</figcaption>'
        );

        return strip_tags("$ledger $signer");
    }
}
