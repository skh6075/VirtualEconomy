<?php


namespace skh6075\virtualeconomy\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skh6075\virtualeconomy\VirtualEconomy;

class MyMoneyCommand extends Command{

    protected VirtualEconomy $plugin;


    public function __construct (VirtualEconomy $plugin) {
        $this->plugin = $plugin;

        parent::__construct (
            $this->plugin->getBaseLang()->translate("mymoney.command.name", [], false),
            $this->plugin->getBaseLang()->translate("mymoney.command.description", [], false)
        );
        $this->setPermission("mymoney.command.permission");
    }

    public function execute (CommandSender $player, string $label, array $args): bool{
        if ($player instanceof Player) {
            if ($player->hasPermission ($this->getPermission())) {
                $player->sendMessage($this->plugin->getBaseLang()->translate("mymoney.command.success" . [
                    "%money%" => $this->plugin->myMoney($player),
                    "%unit%" => $this->plugin->getMoneyUnit()
                ]));
            }
        }
        return true;
    }
}