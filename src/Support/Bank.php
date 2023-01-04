<?php

namespace ShitwareLtd\Shitbot\Support;

use Discord\Parts\User\User;

class Bank
{
    private $costs = [
        'text-davinci-003' => 0.00002,
        'image_generation' => 0.02
    ];

    public function __construct(private User $author)
    {
        if (! file_exists($this->getBankPath())) {
            file_put_contents($this->getBankPath(), json_encode([]));
        }
    }

    public function registerExpense(string $type, int $amount): static
    {
        $bankData = $this->getBankData();

        if (! isset($bankData[$type])) {
            $bankData[$type] = 0;
        }

        $bankData[$type] += $amount;

        $this->updateExpenses($bankData);

        return $this;
    }

    private function getBankData()
    {
        return json_decode(
            file_get_contents(
                self::getBankPath()
            ),
            true
        );
    }

    private function updateExpenses(mixed $transactions): static
    {
        file_put_contents(
            self::getBankPath(),
            json_encode($transactions)
        );

        return $this;
    }

    private function getBankPath(): string
    {
        return __DIR__.'/../Bank/'.$this->author->id.'.json';
    }

    public function getTotalExpenses(): array
    {
        $data = $this->getBankData();
        
        $balanceOverview = [];

        foreach ($data as $type => $usage) {
            $balanceOverview[$type]['usage'] = $usage;
            $balanceOverview[$type]['total_cost'] = $usage * $this->costs[$type];
        }

        $totalCost = array_sum(
            array_map(
                fn ($expense) => $expense['total_cost'],
                $balanceOverview
            )
        );

        return [
            $totalCost,
            $balanceOverview
        ];
    }
}