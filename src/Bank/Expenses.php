<?php

namespace ShitwareLtd\Shitbot\Bank;

class Expenses
{
    /**
     * @var float|int
     */
    public readonly float|int $total;

    /**
     * @var array
     */
    public readonly array $breakdown;

    /**
     * @param  array  $expenses
     */
    public function __construct(array $expenses)
    {
        $breakdown = [];

        foreach (Item::cases() as $item) {
            $breakdown[$item->name] = ($expenses[$item->value] ?? 0) * $item->unitPrice();
        }

        $this->breakdown = $breakdown;
        $this->total = array_sum($breakdown);
    }
}
