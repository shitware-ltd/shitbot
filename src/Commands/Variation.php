<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Attachment;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use ShitwareLtd\Shitbot\Bank\Bank;
use ShitwareLtd\Shitbot\Bank\Item;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Helpers;
use Throwable;

use function React\Async\coroutine;

class Variation extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!variation';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 120000;
    }

    /**
     * @param  Message|Interaction  $entity
     * @param  array  $args
     * @return void
     */
    public function handle(Message|Interaction $entity, array $args): void
    {
        coroutine(function (Message|Interaction $entity) {
            if ($this->skip($entity)) {
                return;
            }

            if ($entity instanceof Message
                && ! $this->passesInitialChecks($entity)) {
                return;
            }

            $message = Helpers::getMessage($entity);
            $user = Helpers::getUser($entity);

            $this->hitCooldown($user);

            $message->channel->broadcastTyping();

            $typing = Loop::addPeriodicTimer(
                interval: 4,
                callback: fn () => $message->channel->broadcastTyping()
            );

            try {
                /** @var Attachment $attachment */
                $attachment = $message->attachments->first();

                /** @var ResponseInterface $fetchImage */
                $fetchImage = yield Shitbot::browser()->get($attachment->url);

                $image = $fetchImage->getBody()->getContents();
                $boundary = uniqid();

                /** @var ResponseInterface $response */
                $response = yield Shitbot::browser()
                    ->withTimeout(60.0)
                    ->withRejectErrorResponse(false)
                    ->post(
                        url: 'https://api.openai.com/v1/images/variations',
                        headers: [
                            'Authorization' => 'Bearer '.Shitbot::config('OPENAI_TOKEN'),
                            'Content-Type' => 'multipart/form-data; boundary="'.$boundary.'"',
                        ],
                        body: $this->buildMultipartBody(
                            boundary: $boundary,
                            image: $image
                        )
                    );

                $result = Helpers::json($response);

                if ($response->getStatusCode() === 200) {
                    $message->channel->sendMessage(
                        MessageBuilder::new()
                            ->setReplyTo($message)
                            ->addFileFromContent(
                                filename: 'dalle_'.uniqid(more_entropy: true).'.png',
                                content: base64_decode($result['data'][0]['b64_json'])
                            )
                    );

                    Bank::for($user)->charge(
                        item: Item::Dalle2,
                        units: 1
                    );

                    $this->hitCooldown($user);
                } else {
                    $this->clearCooldown($user);

                    $this->sendError(
                        entity: $entity,
                        error: $result['error']['message']
                    );
                }
            } catch (Throwable $e) {
                $this->clearCooldown($user);

                $this->sendError(
                    entity: $entity,
                    error: $e->getMessage()
                );
            }

            Loop::cancelTimer($typing);
        }, $entity);
    }

    /**
     * @param  Message  $message
     * @return bool
     */
    private function passesInitialChecks(Message $message): bool
    {
        if (! $message->attachments->count()
            || $message->attachments->first()?->size >= 4194400) {
            $message->reply('You must attach an image to use as the basis for the variation. Must be a valid PNG file, less than 4MB, and square.');

            return false;
        }

        return true;
    }

    /**
     * @param  string  $boundary
     * @param  string  $image
     * @return string
     */
    private function buildMultipartBody(string $boundary, string $image): string
    {
        return <<<EOT
        --$boundary
        Content-Disposition: form-data; name="n"
        
        1
        --$boundary
        Content-Disposition: form-data; name="size"
        
        1024x1024
        --$boundary
        Content-Disposition: form-data; name="response_format"
        
        b64_json
        --$boundary
        Content-Disposition: form-data; name="image"; filename="image.png"
        Content-Transfer-Encoding: binary
        
        $image
        --$boundary--
        EOT;
    }
}
