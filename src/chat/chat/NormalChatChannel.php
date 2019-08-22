<?php

namespace chat\chat;

use chat\Main;
use chat\player\PlayerData;

class NormalChatChannel extends ChatChannel{

    public function chat(PlayerData $playerData, string $msg): void{
        $main = Main::getInstance();
        if(!ChatManager::canChat($playerData)){
            $playerData->getPlayer()->sendMessage($main->getMessage("chat.warn"));
            return;
        }
        foreach($this->getMember() as $id => $data){
            if($this->isMute($data, $playerData->getName())) continue;
            $data->getPlayer()->sendMessage($main->getMessage("chat.display", [
                'channel' => $this->getId(),
                'name' => $playerData->getName(),
                'message' => $msg
            ]));
            Main::getInstance()->getLogger()->info("[".$this->getId()."]".$playerData->getName().": ".$msg);
        }
    }
}
