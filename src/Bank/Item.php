<?php

namespace ShitwareLtd\Shitbot\Bank;

enum Item: string
{
    case Davinci003 = 'text-davinci-003';
    case Dalle2 = 'dalle-2';

    /**
     * @return float
     */
    public function unitPrice(): float
    {
        return match ($this) {
            self::Davinci003 => 0.00002,
            self::Dalle2 => 0.02,
        };
    }
}
