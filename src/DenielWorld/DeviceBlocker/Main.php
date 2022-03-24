<?php

namespace DenielWorld\DeviceBlocker;

use pocketmine\utils\TextFormat as TF;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\event\player\PlayerLoginEvent;

class Main extends PluginBase implements Listener{

    public $deviceOS = [];

    protected function onEnable() :void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        if(!file_exists("config.yml")){
            $this->saveResource("config.yml");
        }
    }

    public static function replaceText($string, $player){
        $msg = str_replace("{player}", $player, $string);
        return $msg;
    }

    public function onPacketReceive(DataPacketReceiveEvent $event) : void {
        //$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if($event->getPacket() instanceof LoginPacket) {
            //var_dump(JwtUtils::parse($event->getPacket()->clientDataJwt)[1]);
            $this->deviceOS[JwtUtils::parse($event->getPacket()->clientDataJwt)[1]["ThirdPartyName"]] = JwtUtils::parse($event->getPacket()->clientDataJwt)[1]["DeviceOS"];
        }
    }

    public function onLogin(PlayerLoginEvent $event) {
        $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if(!$event->getPlayer()->isOnline()) {
            unset($this->deviceOS[$event->getPlayer()->getName()]);
            return;
        }
        if($this->deviceOS[$event->getPlayer()->getName()] == 7){
            if($cfg->get("w10") == true){
                $this->getServer()->broadcastMessage(self::replaceText(TF::colorize($cfg->get("w10-quit-message")), $event->getPlayer()->getName()));
                $event->getPlayer()->kick(TF::colorize($cfg->get("w10-kick-reason")), false);
                unset($this->deviceOS[$event->getPlayer()->getName()]);
            }
        }
        elseif($this->deviceOS[$event->getPlayer()->getName()] == 1){
            if($cfg->get("android") == true){
                $this->getServer()->broadcastMessage(self::replaceText(TF::colorize($cfg->get("android-quit-message")), $event->getPlayer()->getName()));
                $event->getPlayer()->kick(TF::colorize($cfg->get("android-kick-reason")), false);
                unset($this->deviceOS[$event->getPlayer()->getName()]);
            }
        }
        elseif($this->deviceOS[$event->getPlayer()->getName()] == 2){
            if($cfg->get("ios") == true){
                $this->getServer()->broadcastMessage(self::replaceText(TF::colorize($cfg->get("ios-quit-message")), $event->getPlayer()->getName()));
                $event->getPlayer()->kick(TF::colorize($cfg->get("ios-kick-reason")), false);
                unset($this->deviceOS[$event->getPlayer()->getName()]);
            }
        }
    }
}
