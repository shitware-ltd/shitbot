<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException as ๐งจ;
use Discord\Parts\Channel\Message as ๐ญ;
use Illuminate\Support\Str as ๐งถ;

class โฐ๐โ extends Command
{
    /**
     * Game rules!
     */
    public const ๐ฎ = [
        'rock' => [
            '๐ฉ' => 'paper',
            'โข' => 'โฐ',
        ],
        'paper' => [
            '๐ฉ' => 'scissors',
            'โข' => '๐',
        ],
        'scissors' => [
            '๐ฉ' => 'rock',
            'โข' => 'โ',
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
     * @param  ๐ญ  $๐ฌ
     * @param  array  $๐ฐ
     * @return void
     *
     * @throws ๐งจ
     */
    public function handle(๐ญ $๐ฌ, array $๐ฐ): void
    {
        if ($this->skip($๐ฌ)) {
            return;
        }

        if (! is_null($๐ = $this->๐๐($๐ฐ))) {
            $๐ฌ->reply($this->๐ฌ๐ฎ(
                ๐ฌ: $๐ฌ,
                ๐: $๐
            ));

            return;
        }

        $๐ฌ->reply('Please select a valid choice, i.e. ( !rps rock|paper|scissors )');
    }

    /**
     * @param  ๐ญ  $๐ฌ
     * @param  string  $๐
     * @return string
     */
    private function ๐ฌ๐ฎ(๐ญ $๐ฌ, string $๐): string
    {
        $๐ค = $this->๐ค();

        if (empty($๐)) {
            return '> **I choose '.self::๐ฎ[$๐ค]['๐ฉ'].'**';
        }

        $๐ = $this->๐ฌ๐ฒ(
            ๐ค: $๐ค,
            ๐: $๐,
            ๐ง: $๐ฌ->author->username
        ).PHP_EOL;

        $๐ .= $this->๐ฏ๐ฌ(
            ๐ค: $๐ค,
            ๐: $๐,
            ๐ง: $๐ฌ->author->username
        ).PHP_EOL;

        return $๐;
    }

    /**
     * @param  string  $๐ค
     * @param  string  $๐
     * @param  string  $๐ง
     * @return string
     */
    private function ๐ฌ๐ฒ(
        string $๐ค,
        string $๐,
        string $๐ง
    ): string {
        $๐ฆ = '> I picked '.self::๐ฎ[$๐ค]['โข'].PHP_EOL;
        $๐ฆ .= "> $๐ง picked ".self::๐ฎ[$๐]['โข'].PHP_EOL;

        return $๐ฆ;
    }

    /**
     * @param  string  $๐ค
     * @param  string  $๐
     * @param  string  $๐ง
     * @return string
     */
    private function ๐ฏ๐ฌ(
        string $๐ค,
        string $๐,
        string $๐ง
    ): string {
        if ($๐ค === $๐) {
            return "**Seems we had a tie $๐ง!**";
        }

        if (self::๐ฎ[$๐ค]['๐ฉ'] === $๐) {
            return "**$๐ง wins!**";
        }

        return "**I win! $๐ง loses!**";
    }

    /**
     * @param  array  $โ
     * @return string|null
     */
    private function ๐๐(array $โ): ?string
    {
        $๐ = ๐งถ::lower($โ[0] ?? '');

        if (empty($๐)) {
            return '';
        }

        if (in_array(
            needle: $๐,
            haystack: array_keys(self::๐ฎ)
        )) {
            return $๐;
        }

        return null;
    }

    /**
     * @return string
     */
    private function ๐ค(): string
    {
        $๐ฒ = rand(min: 1, max: 99);

        if ($๐ฒ < 34) {
            return 'rock';
        }

        if ($๐ฒ < 67) {
            return 'paper';
        }

        return 'scissors';
    }
}