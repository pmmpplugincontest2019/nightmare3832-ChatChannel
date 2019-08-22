<?php

namespace chat\player;

use chat\chat\ChatManager;
use chat\Main;
use pocketmine\Player;

class PlayerData{

    private $id;

    private $player;

    public $chatChannel;

    public $lastChat = 0;
    public $chatPoint = 3;

    public $isGlobal = false;

    const SETTING_GLOBAL = 0;

    public function __construct(Player $player){
        $this->id = $player->getXuid();
        $this->player = $player;
        $this->chatChannel = null;
        $pc = Main::getInstance()->playerConfig;
        if(!$pc->exists($player->getName())) $pc->set($player->getName(), self::getDefaultSettings());
        $settings = $pc->get($player->getName());
        $this->isGlobal = $settings[self::SETTING_GLOBAL];
    }

    public function getId(): string{
        return $this->id;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function setPlayer(Player $player){
        $this->player = $player;
    }

    public function getName(): string{
        return $this->getPlayer()->getName();
    }

    public function getLanguage(): string{
        return $this->getPlayer()->getLocale();
    }

    public function getMaxChatPoint() : int{
        return $this->player->isOp() ? Main::getInstance()->setting->get("chat.speed.restriction.visitor") : Main::getInstance()->setting->get("chat.speed.restriction.op");
    }

    public function chat(string $msg){
        if($this->chatChannel != null) $this->chatChannel->chat($this, $msg);
        else Main::getInstance()->globalChannel->chat($this, $msg);
    }

    public function saveSettings() : void{
        Main::getInstance()->playerConfig->set($this->getName(), $this->getSettings());
    }

    public function getSettings() : array{
        return [
            self::SETTING_GLOBAL => $this->isGlobal
        ];
    }

    public static function getDefaultSettings() : array{
        return [
            self::SETTING_GLOBAL => false
        ];
    }
}