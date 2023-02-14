<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Str;

class ⛰📄✂ extends Command
{
    /**
     * Game rules!
     */
    public const 🎮 = [
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
     * @param  Message  $💬
     * @param  array  $💰
     * @return void
     *
     * @throws NoPermissionsException
     */
    public function handle(Message $💬, array $💰): void
    {
        if ($this->skip($💬)) {
            return;
        }

        if (! is_null($👆 = $this->👆👇($💰))) {
            $💬->reply($this->💬🎮(
                💬: $💬,
                👆: $👆
            ));

            return;
        }

        $💬->reply('Please select a valid choice, i.e. ( !rps rock|paper|scissors )');
    }

    /**
     * @param  Message  $💬
     * @param  string  $👆
     * @return string
     */
    private function 💬🎮(Message $💬, string $👆): string
    {
        $🤖 = $this->🤖();

        if (empty($👆)) {
            return '> **I choose '.self::🎮[$🤖]['emoji'].'**';
        }

        $reply = $this->💬🎲(
            🤖: $🤖,
            😀: $👆,
            🧑: $💬->author->username
        ).PHP_EOL;

        $reply .= $this->💯💬(
            🤖: $🤖,
            😀: $👆,
            🧑: $💬->author->username
        ).PHP_EOL;

        return $reply;
    }

    /**
     * @param  string  $🤖
     * @param  string  $😀
     * @param  string  $🧑
     * @return string
     */
    private function 💬🎲(
        string $🤖,
        string $😀,
        string $🧑
    ): string {
        $reply = '> I picked '.self::🎮[$🤖]['emoji'].PHP_EOL;
        $reply .= "> $🧑 picked ".self::🎮[$😀]['emoji'].PHP_EOL;

        return $reply;
    }

    /**
     * @param  string  $🤖
     * @param  string  $😀
     * @param  string  $🧑
     * @return string
     */
    private function 💯💬(
        string $🤖,
        string $😀,
        string $🧑
    ): string {
        if ($🤖 === $😀) {
            return "**Seems we had a tie $🧑!**";
        }

        if (self::🎮[$🤖]['weakness'] === $😀) {
            return "**$🧑 wins!**";
        }

        return "**I win! $🧑 loses!**";
    }

    /**
     * @param  array  $⁉
     * @return string|null
     */
    private function 👆👇(array $⁉): ?string
    {
        $👆 = Str::lower($⁉[0] ?? '');

        if (empty($👆)) {
            return '';
        }

        if (in_array(
            needle: $👆,
            haystack: array_keys(self::🎮)
        )) {
            return $👆;
        }

        return null;
    }

    /**
     * @return string
     */
    private function 🤖(): string
    {
        $🎲 = rand(min: 1, max: 99);

        if ($🎲 < 34) {
            return 'rock';
        }

        if ($🎲 < 67) {
            return 'paper';
        }

        return 'scissors';
    }
}