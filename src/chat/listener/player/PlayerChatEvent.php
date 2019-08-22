<?php

namespace chat\listener\player;

use chat\Main;
use chat\player\PlayerDataManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent as RawPlayerChatEvent;
use pocketmine\Player;

class PlayerChatEvent implements Listener{
    public function __construct(){
    }

    public function onPlayerChatEvent(RawPlayerChatEvent $event){
        $player = $event->getPlayer();
        $msg = $event->getMessage();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($player);
        $playerData->chat($msg);
        $event->setCancelled();
    }
}