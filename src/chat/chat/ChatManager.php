<?php

namespace chat\chat;

use chat\Main;
use chat\player\PlayerData;

class ChatManager{

    public static $channels = [];

    public static function createChannel(string $id): ChatChannel{
        $channel = new NormalChatChannel($id);
        self::$channels[$id] = $channel;
        Main::getInstance()->channelConfig->set($id, $channel->getSettings());
        return $channel;
    }

    public static function createGlobalChannel(string $id): ChatChannel{
        $channel = new GlobalChatChannel($id);
        //self::$channels[$id] = $channel;
        return $channel;
    }

    public static function deleteChannel(string $id) : void{
        foreach(self::$channels[$id]->getMember() as $playerData2){
            $playerData2->getPlayer()->sendMessage(Main::getInstance()->getMessage("message.deleted", ["name" => $id]));
            self::$channels[$id]->quitChannel($playerData2);
            $playerData2->chatChannel = self::$channels[$id];
        }
        unset(self::$channels[$id]);
        Main::getInstance()->channelConfig->remove($id);
    }

    public static function joinChannel(string $id, PlayerData $playerData){
        self::$channels[$id]->joinChannel($playerData);
        $playerData->chatChannel = self::$channels[$id];
        return self::$channels[$id];
    }

    public static function quitChannel(string $id, PlayerData $playerData){
        self::$channels[$id]->quitChannel($playerData);
        $playerData->chatChannel = null;
        return self::$channels[$id];
    }

    public static function canChat(PlayerData $playerData) : bool{
        $add = floor((time() - $playerData->lastChat) / 5);
        $playerData->chatPoint += $add;
        $maxchatpoint = $playerData->getMaxChatPoint();
        if($playerData->chatPoint > $maxchatpoint) $playerData->chatPoint = $maxchatpoint;
        if($playerData->chatPoint > 0){
            $playerData->chatPoint--;
            $playerData->lastChat = time();
            return true;
        }
        return false;
    }
}