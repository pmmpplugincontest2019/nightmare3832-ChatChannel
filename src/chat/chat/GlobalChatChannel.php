<?php

namespace chat\chat;

use chat\Main;
use chat\player\PlayerData;
use chat\player\PlayerDataManager;

class GlobalChatChannel extends ChatChannel{

    public function chat(PlayerData $playerData, string $msg): void{
        $main = Main::getInstance();
        if(!ChatManager::canChat($playerData)){
            $playerData->getPlayer()->sendMessage($main->getMessage("chat.warn"));
            return;
        }
        foreach($main->getServer()->getOnlinePlayers() as $player){
            if(!PlayerDataManager::getPlayerDataByPlayer($player)->isGlobal && PlayerDataManager::getPlayerDataByPlayer($player)->chatChannel != null) continue;
            $player->getPlayer()->sendMessage($main->getMessage("chat.display", [
                'channel' => $this->getId(),
                'name' => $playerData->getName(),
                'message' => $msg
            ]));
            Main::getInstance()->getLogger()->info("[".$this->getId()."]".$playerData->getName().": ".$msg);
        }
    }
}
