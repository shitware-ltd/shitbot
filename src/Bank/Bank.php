<?php

namespace ShitwareLtd\Shitbot\Bank;

use Discord\Parts\User\User;
use ShitwareLtd\Shitbot\Support\Helpers;

class Bank
{
    /**
     * @param  User  $user
     */
    public function __construct(
        private readonly User $user
    ){}

    /**
     * @param  User  $user
     * @return Bank
     */
    public static function for(User $user): Bank
    {
        return new self($user);
    }

    /**
     * @param  Item  $item
     * @param  int  $units
     * @return void
     */
    public function charge(Item $item, int $units): void
    {
        $bank = $this->bank();

        $bank[$item->value] = ($bank[$item->value] ?? 0) + $units;

        file_put_contents(
            filename: $this->path(),
            data: json_encode($bank)
        );
    }

    /**
     * @return Expenses
     */
    public function expenses(): Expenses
    {
        return new Expenses($this->bank());
    }

    /**
     * @return array
     */
    private function bank(): array
    {
        if (! file_exists($this->path())) {
            return [];
        }

        return Helpers::getContents($this->path());
    }

    /**
     * @return string
     */
    private function path(): string
    {
        return __DIR__.'/../../bank/'.$this->user->id.'.json';
    }
}