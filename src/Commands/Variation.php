<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Attachment;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Psr\Http\Message\ResponseInterface;
use ShitwareLtd\Shitbot\Bank\Bank;
use ShitwareLtd\Shitbot\Bank\Item;
use ShitwareLtd\Shitbot\Shitbot;
use ShitwareLtd\Shitbot\Support\Emoji;
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

            $typing = $this->startTyping($message);

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
                        $this->buildMessage(
                            entity: $entity,
                            result: $result
                        )
                    )->then(
                        $this->autoExpireComponents(...)
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

            $this->stopTyping($typing);
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
     * @param  Message|Interaction  $entity
     * @param  array  $result
     * @return MessageBuilder
     */
    private function buildMessage(Message|Interaction $entity, array $result): MessageBuilder
    {
        $builder = MessageBuilder::new();

        iF ($entity instanceof Message) {
            $builder->setReplyTo($entity)->addComponent(
                $this->buildActionRow()
            );
        } else {
            $builder->setContent("<@{$entity->user->id}>, here is your variation:");
        }

        return $builder->addFileFromContent(
            filename: 'dalle_'.uniqid(more_entropy: true).'.png',
            content: base64_decode($result['data'][0]['b64_json'])
        );
    }

    /**
     * @return ActionRow
     */
    private function buildActionRow(): ActionRow
    {
        $retry = Button::new(Button::STYLE_SUCCESS)
            ->setLabel('Retry Variation')
            ->setEmoji(Emoji::get())
            ->setListener(
                callback: fn (Interaction $interaction) => Shitbot::command(Variation::class)->handle(
                    entity: $interaction,
                    args: []
                ),
                discord: Shitbot::discord()
            );

        $this->autoExpireListener($retry);

        return ActionRow::new()->addComponent($retry);
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
