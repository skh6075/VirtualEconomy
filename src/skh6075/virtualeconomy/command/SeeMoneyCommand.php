<?php


namespace skh6075\virtualeconomy\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skh6075\virtualeconomy\VirtualEconomy;

class SeeMoneyCommand extends Command{

    protected VirtualEconomy $plugin;


    public function __construct (VirtualEconomy $plugin) {
        $this->plugin = $plugin;

        parent::__construct (
            $this->plugin->getBaseLang()->translate("seemoney.command.name", [], false),
            $this->plugin->getBaseLang()->translate("seemoney.command.description", [], false)
        );
        $this->setPermission("seemoney.command.permission");
    }

    public function execute (CommandSender $player, string $label, array $args): bool{
        if ($player instanceof Player) {
            if ($player->hasPermission($this->getPermission())) {
                $username = array_shift($args) ?? "";
                if (trim($username) !== "") {
                    if (($target = $player->getServer()->getPlayer($username)) instanceof Player) {
                        $username = strtolower ($target->getName());
                    }
                    if ($this->plugin->isAccountPlayer($username)) {
                        $player->sendMessage($this->plugin->getBaseLang()->translate("seemoney.command.success", [
                            "%username%" => $username,
                            "%money%" => $this->plugin->myMoney($username),
                            "%unit%" => $this->plugin->getMoneyUnit()
                        ]));
                    } else {
                        $player->sendMessage($this->plugin->getBaseLang()->translate("seemoney.command.fail", [
                            "%username%" => $username
                        ]));
                    }
                } else {
                    $player->sendMessage($this->plugin->getBaseLang()->translate("seemoney.command.help"));
                }
            }
        }
        return true;
    }
}