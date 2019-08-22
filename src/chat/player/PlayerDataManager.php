<?php

namespace chat\player;

use chat\Main;
use pocketmine\Player;

class PlayerDataManager{

    public static $data = [];

    public static function createPlayerData(Player $player): PlayerData{
        $id = $player->getXuid();
        if(isset(self::$data[$id])) self::$data[$id]->setPlayer($player);else
            self::$data[$id] = new PlayerData($player);
        return self::$data[$id];
    }

    public static function getPlayerDataByPlayer(Player $player): ?PlayerData{
        $id = $player->getXuid();
        if(empty(self::$data[$id])) return null;
        return self::$data[$id];
    }

    public static function getPlayerDataByName(string $name): ?PlayerData{
        $player = Main::getInstance()->getServer()->getPlayer($name);
        if(!($player instanceof Player)) return null;
        $id = $player->getXuid();
        if(empty(self::$data[$id])) return null;
        return self::$data[$id];
    }

    public static function getPlayerDataById($id): ?PlayerData{
        if(empty(self::$data[$id])) return null;
        return self::$data[$id];
    }


}