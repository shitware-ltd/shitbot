<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Shitbot;
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
     * @param  Message  $entity
     * @param  array  $args
     * @return void
     */
    public function handle(Message $entity, array $args): void
    {
        coroutine(function (Message $entity) {
            if ($this->skip($entity)) {
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

                $entity->reply($this->makeHype($result));
            } catch (Throwable) {
                //Not important
            }
        }, $entity);
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
