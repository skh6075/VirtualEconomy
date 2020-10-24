<?php


namespace skh6075\virtualeconomy\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

abstract class MoneyEvent extends Event implements Cancellable{

    use CancellableTrait;

    protected string $username;


    public function __construct (string $username) {
        $this->username = $username;
    }

    public function getUsername (): string{
        return $this->username;
    }
}