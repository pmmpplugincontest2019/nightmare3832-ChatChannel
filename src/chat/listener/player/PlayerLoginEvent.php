<?php

namespace chat\listener\player;

use chat\player\PlayerDataManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent as RawPlayerLoginEvent;

class PlayerLoginEvent implements Listener{
    public function __construct(){
    }

    public function onPlayerLoginEvent(RawPlayerLoginEvent $event){
        $player = $event->getPlayer();
        PlayerDataManager::createPlayerData($player);
    }
}