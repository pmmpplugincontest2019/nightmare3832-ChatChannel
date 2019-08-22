<?php

namespace chat\listener\player;

use chat\player\PlayerDataManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent as RawPlayerQuitEvent;

class PlayerQuitEvent implements Listener{
    public function __construct(){
    }

    public function onPlayerLoginEvent(RawPlayerQuitEvent $event){
        $player = $event->getPlayer();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($player);
        $playerData->saveSettings();
    }
}