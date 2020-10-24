<?php

namespace skh6075\virtualeconomy\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use skh6075\virtualeconomy\VirtualEconomy;

class EventListener implements Listener{

    private VirtualEconomy $plugin;


    public function __construct (VirtualEconomy $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @priority HIGHEST
     *
     * @param PlayerJoinEvent $event
     */
    public function handlePlayerJoin (PlayerJoinEvent $event): void{
        $player = $event->getPlayer();

        if (!$this->plugin->isAccountPlayer($player)) {
            $this->plugin->setMoney($player, $this->plugin->getDefaultMoney());
        }
    }
}