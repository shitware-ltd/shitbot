<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Str;

class RockPaperScissors extends Command
{
    /**
     * Game rules!
     */
    public const Game = [
        'rock' => [
            'weakness' => 'paper',
            'emoji' => '⛰',
        ],
        'paper' => [
            'weakness' => 'scissors',
            'emoji' => '📄',
        ],
        'scissors' => [
            'weakness' => 'rock',
            'emoji' => '✂',
        ],
    ];

    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!rps';
    }

    /**
     * @param  Message  $entity
     * @param  array  $args
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Interaction|Message $entity, array $args): void
    {
        if ($this->skip($entity)) {
            return;
        }

        if (! is_null($choice = $this->getChoice($args))) {
            $entity->reply($this->makeGameMessage(
                message: $entity,
                choice: $choice
            ));

            return;
        }

        $entity->reply('Please select a valid choice, i.e. ( !rps rock|paper|scissors )');
    }

    /**
     * @param  Message  $message
     * @param  string  $choice
     * @return string
     */
    private function makeGameMessage(Message $message, string $choice): string
    {
        $botChoice = $this->getBotChoice();

        if (empty($choice)) {
            return '> **I choose '.self::Game[$botChoice]['emoji'].'**';
        }

        $reply = $this->getChoiceRollMessage(
            bot: $botChoice,
            user: $choice,
            userName: $message->author->username
        ).PHP_EOL;

        $reply .= $this->getWinningMessage(
            bot: $botChoice,
            user: $choice,
            userName: $message->author->username
        ).PHP_EOL;

        return $reply;
    }

    /**
     * @param  string  $bot
     * @param  string  $user
     * @param  string  $userName
     * @return string
     */
    private function getChoiceRollMessage(
        string $bot,
        string $user,
        string $userName
    ): string {
        $reply = '> I picked '.self::Game[$bot]['emoji'].PHP_EOL;
        $reply .= "> $userName picked ".self::Game[$user]['emoji'].PHP_EOL;

        return $reply;
    }

    /**
     * @param  string  $bot
     * @param  string  $user
     * @param  string  $userName
     * @return string
     */
    private function getWinningMessage(
        string $bot,
        string $user,
        string $userName
    ): string {
        if ($bot === $user) {
            return "**Seems we had a tie $userName!**";
        }

        if (self::Game[$bot]['weakness'] === $user) {
            return "**$userName wins!**";
        }

        return "**I win! $userName loses!**";
    }

    /**
     * @param  array  $args
     * @return string|null
     */
    private function getChoice(array $args): ?string
    {
        $choice = Str::lower($args[0] ?? '');

        if (empty($choice)) {
            return '';
        }

        if (in_array(
            needle: $choice,
            haystack: array_keys(self::Game)
        )) {
            return $choice;
        }

        return null;
    }

    /**
     * @return string
     */
    private function getBotChoice(): string
    {
        $roll = rand(min: 1, max: 99);

        if ($roll < 34) {
            return 'rock';
        }

        if ($roll < 67) {
            return 'paper';
        }

        return 'scissors';
    }
}
