<?php


namespace skh6075\virtualeconomy\command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skh6075\virtualeconomy\VirtualEconomy;

class PayCommand extends Command{

    protected VirtualEconomy $plugin;


    public function __construct (VirtualEconomy $plugin) {
        $this->plugin = $plugin;

        parent::__construct (
            $this->plugin->getBaseLang()->translate("pay.command.name", [], false),
            $this->plugin->getBaseLang()->translate("pay.command.description", [], false)
        );
        $this->setPermission("pay.command.permission");
    }

    public function execute (CommandSender $player, string $label, array $args): bool{
        if ($player instanceof Player) {
            if ($player->hasPermission($this->getPermission())) {
                $username = array_shift ($args) ?? "";
                $amount = array_shift ($args) ?? 0;

                if (trim($username) !== "" and is_numeric($amount)) {
                    if (($target = $player->getServer()->getPlayer($username)) instanceof Player) {
                        $target = strtolower($target->getName());
                    }
                    if ($amount <= 0) {
                        $player->sendMessage ($this->plugin->getBaseLang()->translate("pay.command.param.amount.small"));
                        return false;
                    }
                    if ($this->plugin->myMoney($player) < $amount) {
                        $player->sendMessage ($this->plugin->getBaseLang()->translate("pay.command.param.amount.money"));
                        return false;
                    }
                    $this->plugin->addMoney($username, $amount);
                    $this->plugin->reduceMoney($player, $amount);
                    $player->sendMessage ($this->plugin->getBaseLang()->translate("pay.command.success", [
                        "%username%" => $username,
                        "%amount%" => $amount,
                        "%unit%" => $this->plugin->getMoneyUnit()
                    ]));
                    if ($target instanceof Player) {
                        $target->sendMessage ($this->plugin->getBaseLang()->translate("pay.command.transfer.target", [
                            "%username%" => $player->getName(),
                            "%amount%" => $amount,
                            "%unit%" => $this->plugin->getMoneyUnit()
                        ]));
                    }
                } else {
                    $player->sendMessage($this->plugin->getBaseLang()->translate("pay.command.help"));
                }
            }
        }
        return true;
    }
}