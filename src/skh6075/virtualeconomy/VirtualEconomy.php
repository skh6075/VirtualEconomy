<?php


namespace skh6075\virtualeconomy;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use skh6075\virtualeconomy\command\MyMoneyCommand;
use skh6075\virtualeconomy\command\PayCommand;
use skh6075\virtualeconomy\command\SeeMoneyCommand;
use skh6075\virtualeconomy\event\AddMoneyEvent;
use skh6075\virtualeconomy\event\ReduceMoneyEvent;
use skh6075\virtualeconomy\lang\PluginLang;
use skh6075\virtualeconomy\listener\EventListener;


function convertName ($player): string{
    return strtolower ($player instanceof Player ? $player->getName() : $player);
}

class VirtualEconomy extends PluginBase{

    /** @var ?VirtualEconomy */
    private static ?VirtualEconomy $instance = null;

    /** @var PluginLang */
    private PluginLang $language;

    /** @var array */
    private array $setting = [];

    /** @var array */
    private array $money = [];


    public static function getInstance (): ?VirtualEconomy{
        return self::$instance;
    }

    protected function onLoad (): void{
        if (self::$instance === null) {
            self::$instance = $this;
        }
    }

    protected function onEnable (): void{
        $this->saveResource ("setting.json");
        $this->saveResource ("money.json");

        $this->setting = json_decode (file_get_contents ($this->getDataFolder() . "setting.json"), true);
        $this->money = json_decode (file_get_contents ($this->getDataFolder() . "money.json"), true);

        $this->language = new PluginLang();
        $this->language
            ->setLang($lang = $this->setting ["lang"])
            ->setTranslates(yaml_parse(file_get_contents($this->getDataFolder() . "lang/" . $lang . ".yml")));

        $this->getServer()->getCommandMap()->registerAll(strtolower($this->getName()), [
            new MyMoneyCommand($this),
            new SeeMoneyCommand($this),
            new PayCommand($this)
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    protected function onDisable (): void{
        file_put_contents ($this->getDataFolder() . "setting.json", json_encode ($this->setting, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        file_put_contents ($this->getDataFolder() . "money.json", json_encode ($this->money, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function getBaseLang (): PluginLang{
        return $this->language;
    }

    public function getDefaultMoney (): int{
        return $this->setting ["default-money"];
    }

    public function getMoneyUnit (): string{
        return $this->setting ["unit"];
    }

    public function isAccountPlayer ($player): bool{
        return isset ($this->money [convertName($player)]);
    }

    public function myMoney ($player): int{
        return $this->money [convertName($player)] ?? $this->getDefaultMoney();
    }

    /**
     * @param $player
     * @param int $amount
     * @param string $reason
     */
    public function addMoney ($player, int $amount, string $reason = ""): void{
        $event = new AddMoneyEvent(convertName($player), $amount, $reason);
        $event->call();

        if (!$event->isCancelled()) {
            if (!isset ($this->money [$event->getUsername()])) {
                $this->money [$event->getUsername()] = $this->getDefaultMoney();
            }
            $this->money [$event->getUsername()] += $event->getAmount();
        }
    }

    /**
     * @param $player
     * @param int $amount
     * @param string $reason
     */
    public function reduceMoney ($player, int $amount, string $reason = ""): void{
        $event = new ReduceMoneyEvent(convertName($player), $amount, $reason);
        $event->call();

        if (!$event->isCancelled()) {
            if (!isset ($this->money [$event->getUsername()])) {
                $this->money [$event->getUsername()] = $this->getDefaultMoney();
            }
            $this->money [$event->getUsername()] -= $event->getAmount();
            if ($this->myMoney($event->getUsername()) < 0) {
                $this->money [$event->getUsername()] = 0;
            }
        }
    }

    public function setMoney ($player, int $amount): void{
        $this->money [convertName($player)] = $amount;
    }
}