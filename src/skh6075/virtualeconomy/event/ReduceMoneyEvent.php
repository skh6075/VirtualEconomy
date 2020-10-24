<?php

namespace skh6075\virtualeconomy\event;

class ReduceMoneyEvent extends MoneyEvent{

    private int $amount;

    private string $reason;


    public function __construct (string $username, int $amount, string $reason) {
        parent::__construct ($username);

        $this->amount = $amount;
        $this->reason = $reason;
    }

    public function getAmount (): int{
        return $this->amount;
    }

    public function getReason (): string{
        return $this->reason;
    }
}